import { useState, useEffect, useCallback } from 'react';
import { getCookie, getContestStatus } from './api';
import type { Question, SubmitResponse } from './api';
import WelcomePage from './pages/WelcomePage';
import RegisterPage from './pages/RegisterPage';
import CountdownPage from './pages/CountdownPage';
import QuizPage from './pages/QuizPage';
import ResultPage from './pages/ResultPage';
import AlreadyPlayedPage from './pages/AlreadyPlayedPage';
import ContestCountdownPage from './pages/ContestCountdownPage';
import ContestEndedPage from './pages/ContestEndedPage';
import ApflotaLogo from './components/ApflotaLogo';
import { CookieConsentProvider, CookieBanner } from './components/CookieConsent';

type Screen =
  | 'loading'
  | 'contest-countdown'
  | 'contest-ended'
  | 'welcome'
  | 'register'
  | 'countdown'
  | 'quiz'
  | 'result'
  | 'already-played';

interface QuizSession {
  sessionId: string;
  questions: Question[];
  serverStartedAt: string;
}

function getForceParam(): string | undefined {
  const params = new URLSearchParams(window.location.search);
  return params.get('force') || undefined;
}

export default function App() {
  const [screen, setScreen] = useState<Screen>('loading');
  const [session, setSession] = useState<QuizSession | null>(null);
  const [result, setResult] = useState<SubmitResponse | null>(null);
  const [startsAt, setStartsAt] = useState<string>('');

  const goToActiveFlow = useCallback(() => {
    const alreadyPlayed = getCookie('quiz_completed') === 'true';
    setScreen(alreadyPlayed ? 'already-played' : 'welcome');
  }, []);

  useEffect(() => {
    const force = getForceParam();
    getContestStatus(force)
      .then((data) => {
        setStartsAt(data.startsAt);
        switch (data.status) {
          case 'upcoming':
            setScreen('contest-countdown');
            break;
          case 'active':
            goToActiveFlow();
            break;
          case 'finished':
            setScreen('contest-ended');
            break;
        }
      })
      .catch(() => {
        goToActiveFlow();
      });
  }, [goToActiveFlow]);

  const barLogoScreens: Screen[] = [
    'register',
    'countdown',
    'result',
    'already-played',
    'contest-countdown',
    'contest-ended',
  ];

  return (
    <CookieConsentProvider>
      <div className="max-w-[430px] mx-auto min-h-dvh flex flex-col relative">
        {barLogoScreens.includes(screen) && (
          <header className="pt-5 pb-1 px-6 shrink-0">
            <ApflotaLogo variant="header" />
          </header>
        )}

        <div className="flex-1 flex flex-col min-h-0 relative">
          {screen === 'quiz' && (
            <div
              className="absolute top-4 left-0 right-0 z-10 flex justify-center pointer-events-none px-6"
              aria-hidden
            >
              <ApflotaLogo variant="subtle" />
            </div>
          )}

          {screen === 'loading' && (
            <div className="flex-1 flex items-center justify-center">
              <ApflotaLogo variant="header" />
            </div>
          )}

          {screen === 'contest-countdown' && startsAt && (
            <ContestCountdownPage startsAt={startsAt} onActive={goToActiveFlow} />
          )}

          {screen === 'contest-ended' && <ContestEndedPage />}

          {screen === 'welcome' && (
            <WelcomePage onStart={() => setScreen('register')} />
          )}

          {screen === 'register' && (
            <RegisterPage
              onQuizReady={(s) => {
                setSession(s);
                setScreen('countdown');
              }}
            />
          )}

          {screen === 'countdown' && (
            <CountdownPage onFinish={() => setScreen('quiz')} />
          )}

          {screen === 'quiz' && session && (
            <QuizPage
              session={session}
              onComplete={(r) => {
                setResult(r);
                setScreen('result');
              }}
            />
          )}

          {screen === 'result' && result && (
            <ResultPage result={result} />
          )}

          {screen === 'already-played' && <AlreadyPlayedPage />}
        </div>
      </div>
      <CookieBanner />
    </CookieConsentProvider>
  );
}
