import { useState, useEffect } from 'react';
import { fetchUploads } from '../api';

export default function UploadHistory() {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchUploads()
      .then((data) => setItems(data.items || []))
      .catch(() => setItems([]))
      .finally(() => setLoading(false));
  }, []);

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
        {items.map((row) => (
          <tr key={row.id}>
            <td>{row.created_at ? new Date(row.created_at).toLocaleString() : '—'}</td>
            <td>{row.original_name || row.filename || '—'}</td>
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
        ))}
      </tbody>
    </table>
  );
}
