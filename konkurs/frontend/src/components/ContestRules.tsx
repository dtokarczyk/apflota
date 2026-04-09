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
    <div className={`w-full max-w-[320px] ${className}`}>
      <h3 className="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">
        Zasady udziału
      </h3>
      <ol className="list-decimal list-inside space-y-1.5 text-sm text-gray-600 leading-relaxed">
        {rules.map((rule, i) => (
          <li key={i}>{rule}</li>
        ))}
      </ol>
    </div>
  );
}
