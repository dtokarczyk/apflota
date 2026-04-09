import { useSpring, animated, config } from '@react-spring/web';
import ApflotaLogo from '../components/ApflotaLogo';
import ContestRules from '../components/ContestRules';

interface Props {
  onStart: () => void;
}

export default function WelcomePage({ onStart }: Props) {
  const fadeUp = useSpring({
    from: { opacity: 0, transform: 'translateY(40px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: config.gentle,
  });

  const buttonSpring = useSpring({
    from: { opacity: 0, transform: 'scale(0.8)' },
    to: { opacity: 1, transform: 'scale(1)' },
    delay: 400,
    config: config.wobbly,
  });

  return (
    <div className="flex-1 flex flex-col justify-between px-6 py-10 min-h-0">
      <animated.div style={fadeUp} className="flex flex-col items-center pt-8">
        <ApflotaLogo variant="hero" />
      </animated.div>

      <animated.div style={fadeUp} className="flex flex-col items-center text-center gap-4">
        <h2 className="text-2xl font-bold">KONKURS</h2>
        <p className="text-lg text-gray-700 leading-relaxed font-semibold">
          Do wygrania: Auto z pełnym bakiem<br />na weekend!
        </p>
        <ContestRules className="text-left mt-2" />
      </animated.div>

      <animated.div style={buttonSpring} className="pb-6">
        <button
          onClick={onStart}
          className="w-full py-4 bg-primary text-white text-xl font-bold rounded-2xl active:scale-95 transition-transform"
        >
          START
        </button>
      </animated.div>
    </div>
  );
}
