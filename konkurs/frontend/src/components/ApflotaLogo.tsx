type Props = {
  /** hero: welcome screen; header: top bar; subtle: quiz overlay */
  variant?: 'hero' | 'header' | 'subtle';
  className?: string;
};

const variantClass: Record<NonNullable<Props['variant']>, string> = {
  hero: 'w-[min(92vw,300px)] h-auto',
  header: 'h-7 w-auto',
  subtle: 'h-5 w-auto opacity-45',
};

export default function ApflotaLogo({
  variant = 'header',
  className = '',
}: Props) {
  const center = variant === 'subtle' ? '' : 'mx-auto';
  return (
    <img
      src="/apflota.svg"
      alt="AP Flota"
      className={`block ${center} ${variantClass[variant]} ${className}`.trim()}
      width={206}
      height={24}
      decoding="async"
    />
  );
}
