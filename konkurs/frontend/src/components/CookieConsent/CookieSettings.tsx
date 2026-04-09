import { useState } from 'react';
import { useCookieConsent } from './CookieConsentContext';
import type { CookiePreferences } from './CookieConsentContext';

interface CategoryDef {
  key: keyof CookiePreferences;
  label: string;
  description: string;
  locked?: boolean;
}

const categories: CategoryDef[] = [
  {
    key: 'necessary',
    label: 'Niezbędne',
    description:
      'Cookies niezbędne do prawidłowego działania strony. Nie można ich wyłączyć.',
    locked: true,
  },
  {
    key: 'analytics',
    label: 'Pomiarowe',
    description:
      'Zbieramy dane za pomocą Google Analytics w celu analizy ruchu na stronie i poprawy jakości usług.',
  },
  {
    key: 'marketing',
    label: 'Marketingowe',
    description:
      'Zbieramy dane za pomocą Google Ads i Facebook Ads w celu remarketingu i personalizacji reklam.',
  },
];

export default function CookieSettings() {
  const { preferences, savePreferences, closeSettings } = useCookieConsent();

  const [draft, setDraft] = useState<CookiePreferences>({ ...preferences });

  function toggle(key: keyof CookiePreferences) {
    if (key === 'necessary') return;
    setDraft((prev) => ({ ...prev, [key]: !prev[key] }));
  }

  return (
    <div className="fixed inset-0 z-50 flex items-end justify-center bg-black/40">
      <div className="mx-auto w-full max-w-[430px] rounded-t-2xl bg-white p-5 pb-8 max-h-[85dvh] overflow-y-auto">
        <div className="flex items-center justify-between mb-4">
          <h3 className="text-base font-bold text-gray-900">
            Ustawienia cookies
          </h3>
          <button
            onClick={closeSettings}
            className="text-gray-400 hover:text-gray-600 transition-colors p-1"
            aria-label="Zamknij"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              className="h-5 w-5"
              viewBox="0 0 20 20"
              fill="currentColor"
            >
              <path
                fillRule="evenodd"
                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                clipRule="evenodd"
              />
            </svg>
          </button>
        </div>

        <p className="text-sm text-gray-600 mb-5 leading-relaxed">
          Dostosuj, które kategorie plików cookies chcesz włączyć.
          Cookies niezbędne są zawsze aktywne.
        </p>

        <div className="space-y-4 mb-6">
          {categories.map((cat) => (
            <div
              key={cat.key}
              className="flex items-start gap-3 rounded-xl border border-gray-200 p-4"
            >
              <div className="flex-1 min-w-0">
                <span className="block text-sm font-bold text-gray-900">
                  {cat.label}
                </span>
                <span className="block text-xs text-gray-500 mt-1 leading-relaxed">
                  {cat.description}
                </span>
              </div>
              <button
                type="button"
                role="switch"
                aria-checked={draft[cat.key]}
                disabled={cat.locked}
                onClick={() => toggle(cat.key)}
                className={`
                  relative mt-0.5 inline-flex h-6 w-11 shrink-0 items-center rounded-full
                  transition-colors duration-200 focus-visible:outline-2 focus-visible:outline-offset-2
                  ${cat.locked ? 'cursor-not-allowed opacity-60' : 'cursor-pointer'}
                  ${draft[cat.key] ? 'bg-primary' : 'bg-gray-300'}
                `}
              >
                <span
                  className={`
                    inline-block h-4 w-4 rounded-full bg-white shadow-sm
                    transition-transform duration-200
                    ${draft[cat.key] ? 'translate-x-6' : 'translate-x-1'}
                  `}
                />
              </button>
            </div>
          ))}
        </div>

        <button
          onClick={() => savePreferences(draft)}
          className="w-full rounded-xl bg-primary py-3 px-4 text-sm font-bold text-white transition-colors hover:bg-primary-dark active:bg-primary-dark"
        >
          Zapisz ustawienia
        </button>
      </div>
    </div>
  );
}
