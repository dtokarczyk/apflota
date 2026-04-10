import { CookiePreferences } from './CookieConsentContext';

const GOOGLE_ANALYTICS_ID = 'G-22Q62DDSNX';
const GOOGLE_ANALYTICS_SCRIPT_ID = 'google-analytics-script';
const FACEBOOK_PIXEL_ID = '994901926272394';
const FACEBOOK_PIXEL_SCRIPT_ID = 'facebook-pixel-script';

type FacebookPixelFn = ((...args: unknown[]) => void) & {
  callMethod?: (...args: unknown[]) => void;
  queue?: unknown[][];
  loaded?: boolean;
  version?: string;
  push?: (...args: unknown[]) => void;
};

declare global {
  interface Window {
    dataLayer?: unknown[];
    gtag?: (...args: unknown[]) => void;
    fbq?: FacebookPixelFn;
    _fbq?: FacebookPixelFn;
    __facebookPixelInitialized?: boolean;
  }
}

// ---------------------------------------------------------------------------
// gtag helper
// ---------------------------------------------------------------------------

function gtag(...args: unknown[]) {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push(args);
}

// ---------------------------------------------------------------------------
// Google Analytics z Consent Mode v2
// Ładowane ZAWSZE (poza mechanizmem cookie-consent).
// Domyślnie consent = denied — dane zbierane bez cookies.
// Po decyzji użytkownika wysyłamy `consent update`.
// ---------------------------------------------------------------------------

let gaLoaded = false;

export function initGoogleAnalytics() {
  if (gaLoaded) return;
  gaLoaded = true;

  // Expose gtag globally for consent update calls
  window.dataLayer = window.dataLayer || [];
  window.gtag = gtag;

  // Consent Mode v2 — default denied for all
  gtag('consent', 'default', {
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    analytics_storage: 'denied',
    wait_for_update: 500,
  });

  // Load gtag.js script
  const script = document.createElement('script');
  script.id = GOOGLE_ANALYTICS_SCRIPT_ID;
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${GOOGLE_ANALYTICS_ID}`;
  document.head.appendChild(script);

  gtag('js', new Date());
  gtag('config', GOOGLE_ANALYTICS_ID);
}

// ---------------------------------------------------------------------------
// Consent update — wywoływane po akceptacji / odrzuceniu cookies
// ---------------------------------------------------------------------------

function updateGoogleConsent(prefs: CookiePreferences) {
  if (!window.gtag) return;

  window.gtag('consent', 'update', {
    ad_storage: prefs.marketing ? 'granted' : 'denied',
    ad_user_data: prefs.marketing ? 'granted' : 'denied',
    ad_personalization: prefs.marketing ? 'granted' : 'denied',
    analytics_storage: prefs.analytics ? 'granted' : 'denied',
  });
}

// ---------------------------------------------------------------------------
// Facebook Pixel — ładowany TYLKO po zgodzie marketingowej
// ---------------------------------------------------------------------------

function initFacebookPixel() {
  if (!window.fbq) {
    const fbq = function (...args: unknown[]) {
      if (fbq.callMethod) {
        fbq.callMethod(...args);
        return;
      }
      fbq.queue?.push(args);
    } as FacebookPixelFn;

    fbq.queue = [];
    fbq.loaded = true;
    fbq.version = '2.0';
    fbq.push = (...args: unknown[]) => {
      fbq(...args);
    };

    window.fbq = fbq;
    window._fbq = fbq;
  }

  if (!document.getElementById(FACEBOOK_PIXEL_SCRIPT_ID)) {
    const script = document.createElement('script');
    script.id = FACEBOOK_PIXEL_SCRIPT_ID;
    script.async = true;
    script.src = 'https://connect.facebook.net/en_US/fbevents.js';
    document.head.appendChild(script);
  }

  if (!window.__facebookPixelInitialized) {
    window.fbq('init', FACEBOOK_PIXEL_ID);
    window.__facebookPixelInitialized = true;
  }

  window.fbq('track', 'PageView');
}

// ---------------------------------------------------------------------------
// Wywoływane po zapisie preferencji cookies
// ---------------------------------------------------------------------------

export function applyCookieConsent(prefs: CookiePreferences) {
  // Zawsze aktualizuj Google Consent Mode
  updateGoogleConsent(prefs);

  // FB Pixel tylko pod zgodą marketingową
  if (prefs.marketing) {
    initFacebookPixel();
  }
}
