import { useCookieConsent } from './CookieConsentContext';
import CookieSettings from './CookieSettings';

export default function CookieBanner() {
  const { isConsentGiven, isSettingsOpen, acceptAll, rejectAll, openSettings } =
    useCookieConsent();

  if (isConsentGiven && !isSettingsOpen) return null;

  return (
    <>
      {!isConsentGiven && (
        <div className="fixed inset-x-0 bottom-0 z-50 p-4">
          <div className="mx-auto max-w-[430px] rounded-2xl bg-white shadow-[0_-2px_20px_rgba(0,0,0,0.15)] p-5">
            <h3 className="text-base font-bold text-gray-900 mb-2">
              Szanujemy Twoją prywatność
            </h3>
            <p className="text-sm text-gray-600 mb-4 leading-relaxed">
              Używamy plików cookies, aby zapewnić prawidłowe działanie strony,
              analizować ruch oraz personalizować reklamy. Możesz zaakceptować
              wszystkie cookies, odrzucić opcjonalne lub dostosować ustawienia.
            </p>

            <div className="flex flex-col gap-2">
              <button
                onClick={acceptAll}
                className="w-full rounded-xl bg-primary py-3 px-4 text-sm font-bold text-white transition-colors hover:bg-primary-dark active:bg-primary-dark"
              >
                Akceptuj wszystkie
              </button>
              <button
                onClick={rejectAll}
                className="w-full rounded-xl border border-gray-300 bg-white py-3 px-4 text-sm font-bold text-gray-700 transition-colors hover:bg-gray-50 active:bg-gray-100"
              >
                Odrzuć wszystkie
              </button>
              <button
                onClick={openSettings}
                className="w-full py-2 text-sm font-medium text-gray-500 underline underline-offset-2 transition-colors hover:text-gray-700"
              >
                Ustawienia cookies
              </button>
            </div>
          </div>
        </div>
      )}

      {isSettingsOpen && <CookieSettings />}
    </>
  );
}
