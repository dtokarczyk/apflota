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

// eslint-disable-next-line @typescript-eslint/no-unused-vars
function gtag(..._args: unknown[]) {
  window.dataLayer = window.dataLayer || [];
  // eslint-disable-next-line prefer-rest-params
  window.dataLayer.push(arguments);
}

// ---------------------------------------------------------------------------
// Google Analytics z Consent Mode v2
// Ładowane ZAWSZE (poza mechanizmem cookie-consent).
// Domyślnie consent = denied — dane zbierane bez cookies.
// Po decyzji użytkownika wysyłamy `consent update`.
// ---------------------------------------------------------------------------

let gaLoaded = false;
let gaConfigured = false;

function trackGooglePageView() {
  window.gtag?.('event', 'page_view', {
    page_title: document.title,
    page_location: window.location.href,
    page_path: `${window.location.pathname}${window.location.search}`,
  });
}

function configureGoogleAnalytics() {
  if (gaConfigured || !window.gtag) return;

  gaConfigured = true;
  window.gtag('js', new Date());
  window.gtag('config', GOOGLE_ANALYTICS_ID, { send_page_view: false });
  trackGooglePageView();
}

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
  const existingScript = document.getElementById(
    GOOGLE_ANALYTICS_SCRIPT_ID,
  ) as HTMLScriptElement | null;

  if (existingScript?.dataset.loaded === 'true') {
    configureGoogleAnalytics();
    return;
  }

  const handleLoad = () => {
    if (existingScript) {
      existingScript.dataset.loaded = 'true';
    }
    configureGoogleAnalytics();
  };

  if (existingScript) {
    existingScript.addEventListener('load', handleLoad, { once: true });
    return;
  }

  const script = document.createElement('script');
  script.id = GOOGLE_ANALYTICS_SCRIPT_ID;
  script.async = true;
  script.src = `https://www.googletagmanager.com/gtag/js?id=${GOOGLE_ANALYTICS_ID}`;
  script.addEventListener(
    'load',
    () => {
      script.dataset.loaded = 'true';
      configureGoogleAnalytics();
    },
    { once: true },
  );
  document.head.appendChild(script);
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

  const trackFacebookPageView = () => {
    if (!window.__facebookPixelInitialized) {
      window.fbq?.('init', FACEBOOK_PIXEL_ID);
      window.__facebookPixelInitialized = true;
    }

    window.fbq?.('track', 'PageView');
  };

  const existingScript = document.getElementById(
    FACEBOOK_PIXEL_SCRIPT_ID,
  ) as HTMLScriptElement | null;

  if (existingScript?.dataset.loaded === 'true') {
    trackFacebookPageView();
    return;
  }

  if (existingScript) {
    existingScript.addEventListener(
      'load',
      () => {
        existingScript.dataset.loaded = 'true';
        trackFacebookPageView();
      },
      { once: true },
    );
    return;
  }

  const script = document.createElement('script');
  script.id = FACEBOOK_PIXEL_SCRIPT_ID;
  script.async = true;
  script.src = 'https://connect.facebook.net/en_US/fbevents.js';
  script.addEventListener(
    'load',
    () => {
      script.dataset.loaded = 'true';
      trackFacebookPageView();
    },
    { once: true },
  );
  document.head.appendChild(script);
}

// ---------------------------------------------------------------------------
// Wywoływane po zapisie preferencji cookies
// ---------------------------------------------------------------------------

export function applyCookieConsent(prefs: CookiePreferences) {
  // Zawsze aktualizuj Google Consent Mode
  updateGoogleConsent(prefs);

  // Wyślij page_view po udzieleniu zgody analitycznej (pełny hit z cookies)
  if (prefs.analytics) {
    trackGooglePageView();
  }

  // FB Pixel tylko pod zgodą marketingową
  if (prefs.marketing) {
    initFacebookPixel();
  }
}
