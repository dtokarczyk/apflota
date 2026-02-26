import { useState, useEffect } from 'react';
import { fetchUploads, downloadUpload } from '../api';

export default function UploadHistory() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);
  const [downloadingId, setDownloadingId] = useState(null);

  useEffect(() => {
    fetchUploads()
      .then((data) => setItems(data.items || []))
      .catch(() => setItems([]))
      .finally(() => setLoading(false));
  }, []);

  const handleDownload = (e, row) => {
    e.preventDefault();
    if (!row.file_path) {
      alert('Ten import nie ma zapisanego pliku (zapis plików działa od nowych importów).');
      return;
    }
    setDownloadingId(row.id);
    downloadUpload(row.id, row.original_name || row.filename)
      .catch(() => alert('Nie udało się pobrać pliku.'))
      .finally(() => setDownloadingId(null));
  };

  if (loading) return <p>Ładowanie…</p>;

  if (items.length === 0) {
    return <p>Brak historii importów.</p>;
  }

  return (
    <table className="wp-list-table widefat fixed striped">
      <thead>
        <tr>
          <th>Data</th>
          <th>Nazwa pliku</th>
          <th>Wiersze</th>
          <th>Auta</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        {items.map((row) => {
          const name = row.original_name || row.filename || '—';
          const canDownload = Boolean(row.file_path);
          const isDownloading = downloadingId === row.id;
          return (
            <tr key={row.id}>
              <td>{row.created_at ? new Date(row.created_at).toLocaleString() : '—'}</td>
              <td>
                <a
                  href="#"
                  onClick={(e) => handleDownload(e, row)}
                  style={{
                    color: '#2271b1',
                    textDecoration: 'underline',
                    cursor: isDownloading ? 'wait' : 'pointer',
                  }}
                  title={canDownload ? 'Pobierz plik' : 'Plik nie był zapisany (import sprzed zapisu plików)'}
                >
                  {isDownloading ? 'Pobieranie…' : name}
                </a>
              </td>
              <td>{row.rows_imported ?? '—'}</td>
              <td>{row.cars_affected ?? '—'}</td>
              <td>
                {row.status}
                {row.error_message && (
                  <span title={row.error_message} style={{ color: '#b32d2e', marginLeft: '4px' }}>
                    (błąd)
                  </span>
                )}
              </td>
            </tr>
          );
        })}
      </tbody>
    </table>
  );
}
