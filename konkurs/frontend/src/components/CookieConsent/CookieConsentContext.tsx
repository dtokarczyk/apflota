import { createContext, useContext, useState, useEffect, useCallback, type ReactNode } from 'react';
import { getCookie, setCookie } from '../../api';
import { applyCookieConsent, initGoogleAnalytics } from './cookieScripts';

export interface CookiePreferences {
  necessary: true;
  analytics: boolean;
  marketing: boolean;
}

interface CookieConsentContextValue {
  preferences: CookiePreferences;
  isConsentGiven: boolean;
  isSettingsOpen: boolean;
  acceptAll: () => void;
  rejectAll: () => void;
  savePreferences: (prefs: CookiePreferences) => void;
  openSettings: () => void;
  closeSettings: () => void;
}

const COOKIE_NAME = 'cookie_consent';
const COOKIE_DAYS = 365;

const defaultPreferences: CookiePreferences = {
  necessary: true,
  analytics: false,
  marketing: false,
};

const CookieConsentContext = createContext<CookieConsentContextValue | null>(null);

export function useCookieConsent() {
  const ctx = useContext(CookieConsentContext);
  if (!ctx) throw new Error('useCookieConsent must be used within CookieConsentProvider');
  return ctx;
}

function readStoredPreferences(): CookiePreferences | null {
  const raw = getCookie(COOKIE_NAME);
  if (!raw) return null;
  try {
    const parsed = JSON.parse(decodeURIComponent(raw));
    return {
      necessary: true,
      analytics: !!parsed.analytics,
      marketing: !!parsed.marketing,
    };
  } catch {
    return null;
  }
}

function persistPreferences(prefs: CookiePreferences) {
  setCookie(COOKIE_NAME, encodeURIComponent(JSON.stringify(prefs)), COOKIE_DAYS);
}

export function CookieConsentProvider({ children }: { children: ReactNode }) {
  const [preferences, setPreferences] = useState<CookiePreferences>(defaultPreferences);
  const [isConsentGiven, setIsConsentGiven] = useState(false);
  const [isSettingsOpen, setIsSettingsOpen] = useState(false);

  useEffect(() => {
    // GA ładowane zawsze z Consent Mode v2 (domyślnie denied)
    initGoogleAnalytics();

    const stored = readStoredPreferences();
    if (stored) {
      setPreferences(stored);
      setIsConsentGiven(true);
      applyCookieConsent(stored);
    }
  }, []);

  const save = useCallback((prefs: CookiePreferences) => {
    setPreferences(prefs);
    setIsConsentGiven(true);
    setIsSettingsOpen(false);
    persistPreferences(prefs);
    applyCookieConsent(prefs);
  }, []);

  const acceptAll = useCallback(() => {
    save({ necessary: true, analytics: true, marketing: true });
  }, [save]);

  const rejectAll = useCallback(() => {
    save({ necessary: true, analytics: false, marketing: false });
  }, [save]);

  const savePreferences = useCallback((prefs: CookiePreferences) => {
    save(prefs);
  }, [save]);

  const openSettings = useCallback(() => setIsSettingsOpen(true), []);
  const closeSettings = useCallback(() => setIsSettingsOpen(false), []);

  return (
    <CookieConsentContext.Provider
      value={{
        preferences,
        isConsentGiven,
        isSettingsOpen,
        acceptAll,
        rejectAll,
        savePreferences,
        openSettings,
        closeSettings,
      }}
    >
      {children}
    </CookieConsentContext.Provider>
  );
}
