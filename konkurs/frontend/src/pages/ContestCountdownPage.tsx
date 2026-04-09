import { useState, useEffect } from 'react';
import { useSpring, animated, config } from '@react-spring/web';
import ContestRules from '../components/ContestRules';

interface Props {
  startsAt: string;
  onActive: () => void;
}

function getTimeLeft(target: number) {
  const diff = Math.max(0, target - Date.now());
  return {
    days: Math.floor(diff / 86_400_000),
    hours: Math.floor((diff % 86_400_000) / 3_600_000),
    minutes: Math.floor((diff % 3_600_000) / 60_000),
    seconds: Math.floor((diff % 60_000) / 1_000),
    total: diff,
  };
}

export default function ContestCountdownPage({ startsAt, onActive }: Props) {
  const target = new Date(startsAt).getTime();
  const [left, setLeft] = useState(() => getTimeLeft(target));

  useEffect(() => {
    const id = setInterval(() => {
      const next = getTimeLeft(target);
      setLeft(next);
      if (next.total <= 0) {
        clearInterval(id);
        onActive();
      }
    }, 1_000);
    return () => clearInterval(id);
  }, [target, onActive]);

  const fadeIn = useSpring({
    from: { opacity: 0, transform: 'translateY(30px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: config.gentle,
  });

  const pad = (n: number) => String(n).padStart(2, '0');

  return (
    <div className="flex-1 flex flex-col items-center justify-center px-6 py-8 min-h-0 text-center">
      <animated.div style={fadeIn} className="flex flex-col items-center gap-5">
        <div className="text-5xl">🏎️</div>
        <h2 className="text-2xl font-bold">Konkurs wkrótce!</h2>
        <p className="text-lg text-gray-700 font-semibold leading-relaxed">
          Do wygrania: Auto z pełnym bakiem<br />na weekend!
        </p>

        <div className="flex gap-3">
          {[
            { value: left.days, label: 'dni' },
            { value: left.hours, label: 'godz' },
            { value: left.minutes, label: 'min' },
            { value: left.seconds, label: 'sek' },
          ].map(({ value, label }) => (
            <div
              key={label}
              className="flex flex-col items-center bg-muted rounded-xl px-3 py-3 min-w-[60px]"
            >
              <span className="text-2xl font-bold text-primary tabular-nums">
                {pad(value)}
              </span>
              <span className="text-[10px] text-gray-400 uppercase tracking-wide mt-1">
                {label}
              </span>
            </div>
          ))}
        </div>

        <ContestRules className="text-left mt-2" />
      </animated.div>
    </div>
  );
}
