import { useState, useEffect, useRef, memo } from 'react';
import { useSpring, useTrail, animated } from '@react-spring/web';
import { submitQuiz, setCookie } from '../api';
import type { Question, SubmitResponse } from '../api';

interface Props {
  session: {
    sessionId: string;
    questions: Question[];
    serverStartedAt: string;
  };
  onComplete: (result: SubmitResponse) => void;
}

function formatTime(ms: number): string {
  const totalSeconds = Math.floor(ms / 1000);
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  const millis = ms % 1000;
  return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}.${String(millis).padStart(3, '0')}`;
}

/** Own state + rAF so parent QuizPage does not re-render every frame (breaks useTrail opacity). */
const QuizTimer = memo(function QuizTimer() {
  const [elapsed, setElapsed] = useState(0);
  const startTimeRef = useRef(Date.now());
  const rafRef = useRef(0);

  useEffect(() => {
    startTimeRef.current = Date.now();
    const tick = () => {
      setElapsed(Date.now() - startTimeRef.current);
      rafRef.current = requestAnimationFrame(tick);
    };
    rafRef.current = requestAnimationFrame(tick);
    return () => cancelAnimationFrame(rafRef.current);
  }, []);

  return (
    <span className="text-3xl font-mono font-bold text-primary tabular-nums">
      {formatTime(elapsed)}
    </span>
  );
});

interface QuestionSlideProps {
  question: Question;
  selectedOption: string | null;
  submitting: boolean;
  onSelect: (option: string, question: Question) => void;
}

function QuestionSlide({
  question,
  selectedOption,
  submitting,
  onSelect,
}: QuestionSlideProps) {
  const questionSpring = useSpring({
    from: { opacity: 0, transform: 'translateY(-18px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: { tension: 210, friction: 22 },
  });

  const optionTrail = useTrail(question.options.length, {
    from: { opacity: 0, transform: 'translateY(10px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    delay: 200,
    config: { tension: 260, friction: 24 },
  });

  return (
    <div className="flex-1 min-h-0 overflow-y-auto overscroll-y-contain">
      <animated.h3
        style={questionSpring}
        className="text-lg font-bold text-center text-neutral-900 mb-6 leading-snug"
      >
        {question.question}
      </animated.h3>

      <div className="flex flex-col gap-3 pb-1">
        {optionTrail.map((trailStyle, i) => {
          const opt = question.options[i];
          const isSelected = selectedOption === opt;

          return (
            <animated.button
              key={`${question.id}-${opt}`}
              style={trailStyle}
              onClick={() => onSelect(opt, question)}
              disabled={submitting || selectedOption !== null}
              className={`w-full py-3.5 px-4 rounded-xl text-left text-base font-medium transition-colors border shadow-sm ${
                isSelected
                  ? 'bg-primary text-white border-primary'
                  : 'bg-neutral-100 text-neutral-900 border-neutral-300 active:bg-neutral-200'
              } disabled:cursor-default`}
            >
              {opt}
            </animated.button>
          );
        })}
      </div>
    </div>
  );
}

export default function QuizPage({ session, onComplete }: Props) {
  const [currentIndex, setCurrentIndex] = useState(0);
  const [answers, setAnswers] = useState<Record<string, string>>({});
  const [submitting, setSubmitting] = useState(false);
  const [selectedOption, setSelectedOption] = useState<string | null>(null);
  const startTimeRef = useRef(Date.now());

  useEffect(() => {
    startTimeRef.current = Date.now();
  }, []);

  const question = session.questions[currentIndex];

  const handleSelect = async (option: string, q: Question) => {
    if (submitting || selectedOption) return;

    setSelectedOption(option);

    const newAnswers = { ...answers, [String(q.id)]: option };
    setAnswers(newAnswers);

    const qIndex = session.questions.findIndex((x) => x.id === q.id);
    const isLast = qIndex >= 0 && qIndex === session.questions.length - 1;

    setTimeout(async () => {
      setSelectedOption(null);

      if (isLast) {
        setSubmitting(true);
        const clientFinished = new Date().toISOString();
        const clientStarted = new Date(startTimeRef.current).toISOString();

        try {
          const result = await submitQuiz({
            sessionId: session.sessionId,
            answers: newAnswers,
            clientStartedAt: clientStarted,
            clientFinishedAt: clientFinished,
          });

          setCookie('quiz_completed', 'true', 7);
          onComplete(result);
        } catch {
          setSubmitting(false);
        }
      } else {
        setCurrentIndex((i) => i + 1);
      }
    }, 300);
  };

  if (!question) return null;

  return (
    <div className="flex-1 flex flex-col px-6 pb-6 pt-3 min-h-0">
      <div className="text-center mb-2 pt-10">
        <QuizTimer />
      </div>

      <div className="text-center text-sm text-gray-500 mb-4">
        {currentIndex + 1} / {session.questions.length}
      </div>

      <QuestionSlide
        key={question.id}
        question={question}
        selectedOption={selectedOption}
        submitting={submitting}
        onSelect={handleSelect}
      />

      {submitting && (
        <p className="text-center text-sm text-gray-400 mt-4">Zapisywanie...</p>
      )}
    </div>
  );
}
