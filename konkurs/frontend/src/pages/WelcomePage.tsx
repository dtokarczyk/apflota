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
    <div className="relative flex-1 overflow-hidden px-6 py-8 min-h-0">
      <div className="pointer-events-none absolute inset-x-0 top-0 h-48 bg-[radial-gradient(circle_at_top,_rgba(187,38,8,0.14),_transparent_68%)]" />
      <div className="pointer-events-none absolute -right-16 top-28 h-40 w-40 rounded-full bg-primary/8 blur-3xl" />
      <div className="pointer-events-none absolute -left-12 bottom-32 h-32 w-32 rounded-full bg-neutral-900/6 blur-3xl" />

      <div className="relative flex h-full flex-col justify-between">
        <animated.div style={fadeUp} className="flex flex-col items-center gap-5 pt-4 text-center">
          <ApflotaLogo variant="hero" className="w-[min(78vw,240px)]" />

          <div className="inline-flex items-center rounded-full border border-primary/15 bg-primary/8 px-4 py-1.5 text-xs font-bold uppercase tracking-[0.28em] text-primary">
            Konkurs
          </div>

          <div className="max-w-[340px] space-y-3">
            <h2 className="text-[2rem] font-black uppercase tracking-tight text-neutral-950">
              Wygraj auto
            </h2>
            <p className="text-[1.7rem] font-bold leading-tight text-gray-700">
              Do wygrania: Auto z pełnym bakiem na weekend!
            </p>
          </div>
        </animated.div>

        <animated.div style={fadeUp} className="my-6 flex justify-center">
          <ContestRules className="max-w-[360px] text-left" />
        </animated.div>

        <animated.div style={buttonSpring} className="pb-6">
          <button
            onClick={onStart}
            className="w-full rounded-2xl bg-primary py-4 text-xl font-bold text-white shadow-[0_14px_30px_rgba(187,38,8,0.3)] transition-transform active:scale-95"
          >
            START
          </button>
        </animated.div>
      </div>
    </div>
  );
}
