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

/**
 * Injects external script once. Handles React Strict Mode / HMR when the tag already exists.
 */
function loadScriptOnce(id: string, src: string, onLoaded: () => void): void {
  const existing = document.getElementById(id) as HTMLScriptElement | null;

  if (existing?.dataset.loaded === 'true') {
    onLoaded();
    return;
  }

  const markLoaded = (el: HTMLScriptElement) => {
    el.dataset.loaded = 'true';
    onLoaded();
  };

  if (existing) {
    existing.addEventListener('load', () => markLoaded(existing), { once: true });
    return;
  }

  const script = document.createElement('script');
  script.id = id;
  script.async = true;
  script.src = src;
  script.addEventListener('load', () => markLoaded(script), { once: true });
  document.head.appendChild(script);
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
function gtag(..._args: unknown[]) {
  window.dataLayer = window.dataLayer || [];
  // eslint-disable-next-line prefer-rest-params
  window.dataLayer.push(arguments);
}

// ---------------------------------------------------------------------------
// Google Analytics + Consent Mode v2
// gtag.js loads early, but consent defaults to denied (no analytics cookies until granted).
// Official snippet skips that — it would set cookies before the user chooses.
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

  window.dataLayer = window.dataLayer || [];
  window.gtag = gtag;

  gtag('consent', 'default', {
    ad_storage: 'denied',
    ad_user_data: 'denied',
    ad_personalization: 'denied',
    analytics_storage: 'denied',
    wait_for_update: 500,
  });

  loadScriptOnce(
    GOOGLE_ANALYTICS_SCRIPT_ID,
    `https://www.googletagmanager.com/gtag/js?id=${GOOGLE_ANALYTICS_ID}`,
    configureGoogleAnalytics,
  );
}

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
// Meta Pixel — only after marketing consent (official snippet loads immediately).
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

  loadScriptOnce(
    FACEBOOK_PIXEL_SCRIPT_ID,
    'https://connect.facebook.net/en_US/fbevents.js',
    trackFacebookPageView,
  );
}

export function applyCookieConsent(prefs: CookiePreferences) {
  updateGoogleConsent(prefs);

  if (prefs.analytics) {
    trackGooglePageView();
  }

  if (prefs.marketing) {
    initFacebookPixel();
  }
}
