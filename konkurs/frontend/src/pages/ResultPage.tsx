import { useSpring, useTrail, animated, config } from '@react-spring/web';
import type { SubmitResponse } from '../api';

interface Props {
  result: SubmitResponse;
}

export default function ResultPage({ result }: Props) {
  const headerSpring = useSpring({
    from: { opacity: 0, transform: 'scale(0.6)' },
    to: { opacity: 1, transform: 'scale(1)' },
    config: config.wobbly,
  });

  const countSpring = useSpring({
    from: { val: 0 },
    to: { val: result.correctCount },
    config: { duration: 1000 },
  });

  const trail = useTrail(result.details.length, {
    from: { opacity: 0, transform: 'translateY(20px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    delay: 600,
    config: config.gentle,
  });

  return (
    <div className="flex-1 flex flex-col px-6 py-6 min-h-0 overflow-y-auto">
      <animated.div style={headerSpring} className="text-center mb-8">
        <h2 className="text-2xl font-bold mb-4">Twój wynik</h2>
        <div className="text-6xl font-bold text-primary mb-2">
          <animated.span>
            {countSpring.val.to((v) => Math.floor(v))}
          </animated.span>
          <span className="text-3xl text-gray-400">/{result.totalQuestions}</span>
        </div>
        <p className="text-sm text-gray-500 mt-2">
          Czas: {result.clientTimeSeconds.toFixed(2)}s
        </p>
        <p className="text-sm text-gray-600 mt-4 leading-relaxed">
          Jeśli Twój wynik będzie najlepszy spośród wszystkich uczestników konkursu
          - skontaktujemy się z Tobą. Udanego wydarzenia!
        </p>
      </animated.div>

      <div className="flex flex-col gap-3 pb-8">
        {trail.map((style, i) => {
          const detail = result.details[i];
          return (
            <animated.div
              key={detail.id}
              style={style}
              className={`p-4 rounded-xl border-2 ${detail.isCorrect
                ? 'border-green-500 bg-green-50'
                : 'border-red-400 bg-red-50'
                }`}
            >
              <p className="text-sm font-bold mb-1">
                {i + 1}. {detail.question}
              </p>
              {!detail.isCorrect && (
                <p className="text-xs text-red-600">
                  Twoja odpowiedź: {detail.userAnswer}
                </p>
              )}
              <p className={`text-xs ${detail.isCorrect ? 'text-green-700' : 'text-green-600'}`}>
                Poprawna: {detail.correctAnswer}
              </p>
            </animated.div>
          );
        })}
      </div>
    </div>
  );
}
