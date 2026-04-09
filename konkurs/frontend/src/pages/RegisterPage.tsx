import { useState } from 'react';
import { useSpring, useTrail, animated, config } from '@react-spring/web';
import { startQuiz } from '../api';
import type { Question } from '../api';

interface Props {
  onQuizReady: (session: {
    sessionId: string;
    questions: Question[];
    serverStartedAt: string;
  }) => void;
}

export default function RegisterPage({ onQuizReady }: Props) {
  const [email, setEmail] = useState('');
  const [consentReg, setConsentReg] = useState(false);
  const [consentMkt, setConsentMkt] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const slideUp = useSpring({
    from: { opacity: 0, transform: 'translateY(60px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: config.gentle,
  });

  const fields = [0, 1, 2, 3];
  const trail = useTrail(fields.length, {
    from: { opacity: 0, transform: 'translateY(20px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    delay: 200,
    config: config.gentle,
  });

  const handleSubmit = async () => {
    setError('');

    if (!email.trim()) {
      setError('Podaj adres e-mail');
      return;
    }
    if (!consentReg) {
      setError('Akceptacja regulaminu jest wymagana');
      return;
    }

    setLoading(true);
    try {
      const res = await startQuiz({
        email: email.trim(),
        consentRegulations: consentReg,
        consentMarketing: consentMkt,
      });
      onQuizReady(res);
    } catch (err: unknown) {
      setError(err instanceof Error ? err.message : 'Wystąpił błąd');
    } finally {
      setLoading(false);
    }
  };

  return (
    <animated.div
      style={slideUp}
      className="flex-1 flex flex-col justify-center px-6 py-6 min-h-0"
    >
      <h2 className="text-2xl font-bold text-center mb-8">Dane uczestnika</h2>

      <div className="flex flex-col gap-5">
        <animated.div style={trail[0]}>
          <input
            type="email"
            placeholder="Twój adres e-mail"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="w-full px-4 py-3 bg-muted rounded-xl text-base outline-none focus:ring-2 focus:ring-primary"
            autoComplete="email"
          />
        </animated.div>

        <animated.label style={trail[1]} className="flex items-start gap-3 cursor-pointer">
          <input
            type="checkbox"
            checked={consentReg}
            onChange={(e) => setConsentReg(e.target.checked)}
            className="mt-1 w-5 h-5 accent-primary shrink-0"
          />
          <span className="text-sm leading-snug">
            Akceptuję{' '}
            <a href="#" className="text-primary underline">
              regulamin konkursu
            </a>{' '}
            *
          </span>
        </animated.label>

        <animated.label style={trail[2]} className="flex items-start gap-3 cursor-pointer">
          <input
            type="checkbox"
            checked={consentMkt}
            onChange={(e) => setConsentMkt(e.target.checked)}
            className="mt-1 w-5 h-5 accent-primary shrink-0"
          />
          <span className="text-sm leading-snug">
            Wyrażam zgodę na komunikację marketingową
          </span>
        </animated.label>

        {error && (
          <p className="text-red-600 text-sm text-center">{error}</p>
        )}

        <animated.div style={trail[3]}>
          <button
            onClick={handleSubmit}
            disabled={loading}
            className="w-full py-4 bg-primary text-white text-lg font-bold rounded-2xl active:scale-95 transition-transform disabled:opacity-50"
          >
            {loading ? 'Ładowanie...' : 'ROZPOCZNIJ QUIZ'}
          </button>
        </animated.div>
      </div>
    </animated.div>
  );
}
