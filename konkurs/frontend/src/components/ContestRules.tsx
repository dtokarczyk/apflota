interface Props {
  className?: string;
}

const rules = [
  'Jak najszybciej odpowiedz poprawnie na 10 pytań',
  'Zgarnij auto z pełnym bakiem na cały weekend!',
  'Konkurs trwa od godz. 18:00 do 23:00 w dniu 13.04.2026',
  'Wyniki ogłosimy zwycięzcom drogą mailową',
];

export default function ContestRules({ className = '' }: Props) {
  return (
    <div
      className={`w-full rounded-[20px] border border-primary/10 bg-white/95 p-5 shadow-[0_18px_50px_rgba(17,24,39,0.08)] backdrop-blur ${className}`}
    >
      <div className="mb-4 flex items-center justify-between gap-3">
        <h3 className="text-xs font-bold uppercase tracking-[0.24em] text-primary">
          Zasady udziału
        </h3>
        <span className="rounded-full bg-primary/8 px-3 py-1 text-[11px] font-semibold text-primary">
          4 kroki
        </span>
      </div>

      <ol className="space-y-3">
        {rules.map((rule, i) => (
          <li key={i} className="flex items-start gap-3 rounded-xl bg-neutral-50 px-3 py-3">
            <span className="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
              {i + 1}
            </span>
            <span className="text-sm leading-relaxed text-gray-700">{rule}</span>
          </li>
        ))}
      </ol>
    </div>
  );
}
