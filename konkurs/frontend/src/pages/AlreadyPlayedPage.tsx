import { useSpring, animated, config } from '@react-spring/web';

export default function AlreadyPlayedPage() {
  const fadeIn = useSpring({
    from: { opacity: 0, transform: 'translateY(30px)' },
    to: { opacity: 1, transform: 'translateY(0px)' },
    config: config.gentle,
  });

  return (
    <div className="flex-1 flex flex-col items-center justify-center px-6 py-8 min-h-0 text-center">
      <animated.div style={fadeIn}>
        <div className="text-6xl mb-6">🏁</div>
        <h2 className="text-2xl font-bold mb-3">Już wziąłeś udział!</h2>
        <p className="text-gray-500 text-sm leading-relaxed max-w-[280px]">
          Dziękujemy za udział w konkursie AP FLOTA. Każdy uczestnik może zagrać tylko raz.
        </p>
        <a
          href="https://www.apflota.pl?utm_source=konkurs"
          className="text-primary underline text-sm mt-5 inline-block"
          target="_blank"
          rel="noopener noreferrer"
        >
          www.apflota.pl
        </a>
      </animated.div>
    </div>
  );
}
