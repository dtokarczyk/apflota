/**
 * REST API helpers for Calculator admin (uses wp-api and nonce from wpLocalizeScript).
 */

const getConfig = () => {
  if (typeof wiCalcAdmin !== 'undefined') {
    return { baseUrl: wiCalcAdmin.apiUrl, nonce: wiCalcAdmin.nonce };
  }
  return { baseUrl: '/wp-json/wi-calc/v1', nonce: '' };
};

const headers = () => {
  const { nonce } = getConfig();
  return {
    'Content-Type': 'application/json',
    'X-WP-Nonce': nonce,
  };
};

export async function fetchCars() {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/cars`, { headers: headers() });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function fetchRates(params = {}) {
  const { baseUrl } = getConfig();
  const q = new URLSearchParams(params).toString();
  const url = q ? `${baseUrl}/rates?${q}` : `${baseUrl}/rates`;
  const res = await fetch(url, { headers: headers() });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function updateRate(id, data) {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/rates/${id}`, {
    method: 'PUT',
    headers: headers(),
    body: JSON.stringify(data),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function createRate(data) {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/rates`, {
    method: 'POST',
    headers: headers(),
    body: JSON.stringify(data),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function deleteRate(id) {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/rates/${id}`, {
    method: 'DELETE',
    headers: headers(),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function validateImport(file) {
  const { baseUrl } = getConfig();
  const form = new FormData();
  form.append('file', file);
  const res = await fetch(`${baseUrl}/import/validate`, {
    method: 'POST',
    headers: { 'X-WP-Nonce': getConfig().nonce },
    body: form,
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function runImport(validRows, mode, originalName) {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/import`, {
    method: 'POST',
    headers: headers(),
    body: JSON.stringify({
      valid_rows: validRows,
      mode: mode || 'replace',
      original_name: originalName || '',
    }),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function fetchUploads() {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/uploads`, { headers: headers() });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function fetchMigrations() {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/migrations`, { headers: headers() });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

export async function runMigrations() {
  const { baseUrl } = getConfig();
  const res = await fetch(`${baseUrl}/migrations/run`, {
    method: 'POST',
    headers: headers(),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}
