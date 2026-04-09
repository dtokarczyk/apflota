import { useSpring, animated, config } from '@react-spring/web';
import ContestRules from '../components/ContestRules';

export default function ContestEndedPage() {
  const fadeIn = useSpring({
    from: { opacity: 0, transform: 'translateY(30px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: config.gentle,
  });

  return (
    <div className="flex-1 flex flex-col items-center justify-center px-6 py-8 min-h-0 text-center">
      <animated.div style={fadeIn} className="flex flex-col items-center gap-5">
        <div className="text-6xl">🏁</div>
        <h2 className="text-2xl font-bold">Konkurs się zakończył</h2>
        <p className="text-gray-500 text-sm leading-relaxed max-w-[280px]">
          Dziękujemy za zainteresowanie konkursem AP FLOTA. Czas na zgłoszenia już minął.
        </p>
        <ContestRules className="text-left mt-2" />
        <a
          href="https://www.apflota.pl?utm_source=konkurs"
          className="text-primary underline text-sm mt-3 inline-block"
          target="_blank"
          rel="noopener noreferrer"
        >
          www.apflota.pl
        </a>
      </animated.div>
    </div>
  );
}
