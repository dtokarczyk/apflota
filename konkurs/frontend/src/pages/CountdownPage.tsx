import { useState, useEffect } from 'react';
import { useTransition, animated, config } from '@react-spring/web';

interface Props {
  onFinish: () => void;
}

const COUNTDOWN_FROM = 5;

export default function CountdownPage({ onFinish }: Props) {
  const [count, setCount] = useState(COUNTDOWN_FROM);

  useEffect(() => {
    if (count <= 0) return;

    const timer = setTimeout(() => {
      setCount((c) => c - 1);
    }, 1000);

    return () => clearTimeout(timer);
  }, [count]);

  useEffect(() => {
    if (count === 0) {
      const timer = setTimeout(onFinish, 800);
      return () => clearTimeout(timer);
    }
  }, [count, onFinish]);

  const display = count > 0 ? String(count) : 'START!';

  const transitions = useTransition(display, {
    from: { opacity: 0, transform: 'scale(0.3)' },
    enter: { opacity: 1, transform: 'scale(1)' },
    leave: { opacity: 0, transform: 'scale(1.8)' },
    config: config.stiff,
    exitBeforeEnter: true,
  });

  return (
    <div className="flex-1 flex flex-col items-center justify-center px-6 min-h-0 py-6">
      <p className="text-sm text-gray-400 mb-6">Przygotuj się!</p>
      <div className="relative w-40 h-40 flex items-center justify-center">
        {transitions((style, item) => (
          <animated.div
            style={style}
            className="absolute text-7xl font-bold text-primary"
          >
            {item}
          </animated.div>
        ))}
      </div>
    </div>
  );
}
