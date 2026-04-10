export interface Question {
  id: number;
  question: string;
  options: string[];
}

export interface StartResponse {
  sessionId: string;
  serverStartedAt: string;
  questions: Question[];
}

export interface QuizDetail {
  id: number;
  question: string;
  userAnswer: string;
  correctAnswer: string;
  isCorrect: boolean;
}

export interface SubmitResponse {
  correctCount: number;
  totalQuestions: number;
  serverTimeSeconds: number;
  clientTimeSeconds: number;
  details: QuizDetail[];
}

export interface ContestStatusResponse {
  status: 'upcoming' | 'active' | 'finished';
  startsAt: string;
  endsAt: string;
  now: string;
}

const BASE = '/api';

export async function getContestStatus(force?: string): Promise<ContestStatusResponse> {
  const params = force ? `?force=${force}` : '';
  const res = await fetch(`${BASE}/quiz/status${params}`);

  if (!res.ok) {
    const body = await res.json().catch(() => ({}));
    throw new Error(body.message || `Błąd ${res.status}`);
  }

  return res.json();
}

export async function startQuiz(data: {
  email: string;
  consentRegulations: boolean;
  consentMarketing: boolean;
}): Promise<StartResponse> {
  const res = await fetch(`${BASE}/quiz/start`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });

  if (!res.ok) {
    const body = await res.json().catch(() => ({}));
    throw new Error(body.message || `Błąd ${res.status}`);
  }

  return res.json();
}

export async function submitQuiz(data: {
  sessionId: string;
  answers: Record<string, string>;
  clientStartedAt: string;
  clientFinishedAt: string;
}): Promise<SubmitResponse> {
  const res = await fetch(`${BASE}/quiz/submit`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data),
  });

  if (!res.ok) {
    const body = await res.json().catch(() => ({}));
    throw new Error(body.message || `Błąd ${res.status}`);
  }

  return res.json();
}

export function setCookie(name: string, value: string, days: number) {
  const d = new Date();
  d.setTime(d.getTime() + days * 24 * 60 * 60 * 1000);
  const secure = window.location.protocol === 'https:' ? ';Secure' : '';
  document.cookie = `${name}=${value};expires=${d.toUTCString()};path=/;SameSite=Lax${secure}`;
}

export function getCookie(name: string): string | null {
  const match = document.cookie.match(new RegExp(`(^| )${name}=([^;]+)`));
  return match ? match[2] : null;
}
