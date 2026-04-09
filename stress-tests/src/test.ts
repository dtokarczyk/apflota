import http from 'k6/http';
import { sleep, check } from 'k6';
import { Options } from 'k6/options';

/**
 * Stress test: simulate ~2000 user sessions over 10 minutes.
 *
 * Each iteration represents a single user visiting 3 pages
 * with realistic think time between page loads.
 *
 * Arrival rate: ~3.33 new sessions/second = ~2000 sessions/10 minutes
 */
export const options: Options = {
  scenarios: {
    user_visits: {
      executor: 'ramping-arrival-rate',
      startRate: 0,
      timeUnit: '10s',
      preAllocatedVUs: 50,
      maxVUs: 200,
      stages: [
        { duration: '1m', target: 33 },
        { duration: '8m', target: 33 },
        { duration: '1m', target: 0 },
      ],
    },
  },
  thresholds: {
    http_req_duration: ['p(95)<3000'],
    http_req_failed: ['rate<0.05'],
  },
};

const PAGES: string[] = [
  'https://apflota.pl/kalkulator/',
  'https://apflota.pl/kalkulator/toyota-rav4-25-plug-in-hybrid-306km-aut-4x4-dynamic/',
  'https://apflota.pl/o-nas/',
];

export default function (): void {
  for (const url of PAGES) {
    const res = http.get(url, {
      headers: {
        'User-Agent':
          'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        Accept: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'Accept-Language': 'pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7',
      },
    });

    check(res, {
      'status 200': (r) => r.status === 200,
      'response body not empty': (r) => typeof r.body === 'string' && r.body.length > 0,
    });

    // 2-5s think time between pages (simulates real user behavior)
    sleep(Math.random() * 3 + 2);
  }
}
