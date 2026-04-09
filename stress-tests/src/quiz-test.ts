import http from 'k6/http';
import { sleep, check } from 'k6';
import { Options } from 'k6/options';
import { Counter, Trend } from 'k6/metrics';

/**
 * Stress test: Full quiz scenario on konkurs.apflota.pl
 *
 * Simulates real users going through the entire quiz flow:
 *   1. Check contest status
 *   2. Register with email & start quiz
 *   3. Answer questions over ~3 minutes (realistic filling time)
 *   4. Submit answers
 *
 * Each VU = one user completing the full quiz.
 * Think time between questions: ~18s each (10 questions * 18s = ~3 min)
 */

// ── Custom metrics ──────────────────────────────────────────────
const quizStarted = new Counter('quiz_started');
const quizSubmitted = new Counter('quiz_submitted');
const quizScore = new Trend('quiz_score');
const quizServerTime = new Trend('quiz_server_time_seconds');

// ── Configuration ───────────────────────────────────────────────
const BASE_URL = 'https://konkurs.apflota.pl/api/quiz';
const FORCE_CODE = 'kodiwoapflota'; // bypass contest time window

export const options: Options = {
  scenarios: {
    quiz_users: {
      executor: 'ramping-arrival-rate',
      startRate: 0,
      timeUnit: '1m',
      preAllocatedVUs: 700,
      maxVUs: 800,
      stages: [
        { duration: '1m', target: 220 },   // ramp up to ~220 new quizzes/min
        { duration: '8m', target: 220 },   // sustain ~220/min (≈2000 total over 10 min)
        { duration: '1m', target: 0 },     // ramp down
      ],
      gracefulStop: '5m',                  // let in-progress quizzes finish
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<5000'],     // API responses under 5s at p95
    http_req_failed: ['rate<0.10'],        // less than 10% errors (rate limiting expected)
    quiz_started: ['count>0'],
    quiz_submitted: ['count>0'],
  },
};

// ── Helpers ──────────────────────────────────────────────────────
const HEADERS = {
  'Content-Type': 'application/json',
  Accept: 'application/json',
  'User-Agent':
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
};

function generateEmail(vuId: number, iter: number): string {
  const ts = Date.now();
  return `stress-vu${vuId}-${iter}-${ts}@stress-tests.apflota.pl`;
}

/** Simulate user reading question and picking answer: 12-24s per question */
function thinkOnQuestion(): void {
  sleep(12 + Math.random() * 12);
}

/** Small pause between API calls (page transition / countdown) */
function shortPause(): void {
  sleep(1 + Math.random() * 2);
}

// ── Main scenario ───────────────────────────────────────────────
export default function (): void {
  const vuId = __VU;
  const iter = __ITER;

  // ─── Step 1: Check contest status ───────────────────────────
  const statusRes = http.get(`${BASE_URL}/status?force=${FORCE_CODE}`, {
    headers: HEADERS,
  });

  const statusOk = check(statusRes, {
    'status: 200': (r) => r.status === 200,
    'status: contest active': (r) => {
      try {
        const body = JSON.parse(r.body as string);
        return body.status === 'active';
      } catch {
        return false;
      }
    },
  });

  if (!statusOk) {
    console.error(`VU${vuId}: Contest not active, status=${statusRes.status}`);
    return;
  }

  shortPause();

  // ─── Step 2: Register & start quiz ──────────────────────────
  const email = generateEmail(vuId, iter);
  const startPayload = JSON.stringify({
    email,
    consentRegulations: true,
    consentMarketing: Math.random() > 0.5, // 50% opt-in marketing
  });

  const startRes = http.post(`${BASE_URL}/start?force=${FORCE_CODE}`, startPayload, {
    headers: HEADERS,
  });

  const startOk = check(startRes, {
    'start: 201 created': (r) => r.status === 201,
    'start: has sessionId': (r) => {
      try {
        const body = JSON.parse(r.body as string);
        return typeof body.sessionId === 'string' && body.sessionId.length > 0;
      } catch {
        return false;
      }
    },
    'start: has questions': (r) => {
      try {
        const body = JSON.parse(r.body as string);
        return Array.isArray(body.questions) && body.questions.length === 10;
      } catch {
        return false;
      }
    },
  });

  if (!startOk) {
    console.error(
      `VU${vuId}: Failed to start quiz, status=${startRes.status}, body=${startRes.body}`,
    );
    return;
  }

  quizStarted.add(1);

  const startBody = JSON.parse(startRes.body as string);
  const sessionId: string = startBody.sessionId;
  const serverStartedAt: string = startBody.serverStartedAt;
  const questions: Array<{
    id: number;
    question: string;
    options: string[];
  }> = startBody.questions;

  // ─── Step 3: Countdown (3 seconds in real app) ──────────────
  sleep(3);

  // ─── Step 4: Answer questions one by one (~3 min total) ─────
  const clientStartedAt = new Date().toISOString();
  const answers: Record<string, string> = {};

  for (const q of questions) {
    // Simulate reading question + thinking + selecting answer
    thinkOnQuestion();

    // Pick a random option (simulates real user behavior)
    const selectedIndex = Math.floor(Math.random() * q.options.length);
    answers[String(q.id)] = q.options[selectedIndex];
  }

  const clientFinishedAt = new Date().toISOString();

  // Small delay after last answer (UI transition)
  sleep(0.5);

  // ─── Step 5: Submit quiz ────────────────────────────────────
  const submitPayload = JSON.stringify({
    sessionId,
    answers,
    clientStartedAt,
    clientFinishedAt,
  });

  const submitRes = http.post(`${BASE_URL}/submit`, submitPayload, {
    headers: HEADERS,
  });

  const submitOk = check(submitRes, {
    'submit: 201 created': (r) => r.status === 201,
    'submit: has correctCount': (r) => {
      try {
        const body = JSON.parse(r.body as string);
        return typeof body.correctCount === 'number';
      } catch {
        return false;
      }
    },
    'submit: has details': (r) => {
      try {
        const body = JSON.parse(r.body as string);
        return Array.isArray(body.details) && body.details.length === 10;
      } catch {
        return false;
      }
    },
  });

  if (submitOk) {
    const submitBody = JSON.parse(submitRes.body as string);
    quizSubmitted.add(1);
    quizScore.add(submitBody.correctCount);
    quizServerTime.add(submitBody.serverTimeSeconds);
  } else {
    console.error(
      `VU${vuId}: Failed to submit quiz, status=${submitRes.status}, body=${submitRes.body}`,
    );
  }
}
