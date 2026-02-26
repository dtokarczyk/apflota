import { useState } from 'react';
import { validateImport, runImport } from '../api';

export default function CsvImport({ onImported }) {
  const [file, setFile] = useState(null);
  const [result, setResult] = useState(null);
  const [loading, setLoading] = useState(false);
  const [importing, setImporting] = useState(false);
  const [mode, setMode] = useState('replace');

  const handleFileChange = (e) => {
    const f = e.target.files?.[0];
    setFile(f);
    setResult(null);
    if (f) {
      setLoading(true);
      validateImport(f)
        .then((data) => {
          setResult(data);
        })
        .catch((err) => {
          setResult({ error: err.message });
        })
        .finally(() => setLoading(false));
    }
  };

  const handleImportValid = () => {
    if (!file || !result?.valid_rows?.length) return;
    setImporting(true);
    runImport(result.valid_rows, mode, file.name)
      .then((data) => {
        if (data.success) {
          setFile(null);
          setResult(null);
          if (onImported) onImported();
        } else {
          setResult((r) => ({ ...r, importError: data.error_message || 'Import failed' }));
        }
      })
      .catch((err) => {
        setResult((r) => ({ ...r, importError: err.message }));
      })
      .finally(() => setImporting(false));
  };

  return (
    <div>
      <div style={{ marginBottom: '16px' }}>
        <label>
          Plik CSV:{' '}
          <input type="file" accept=".csv" onChange={handleFileChange} disabled={loading} />
        </label>
        <label style={{ marginLeft: '16px' }}>
          Tryb:{' '}
          <select value={mode} onChange={(e) => setMode(e.target.value)}>
            <option value="replace">Nadpisz wszystkie dane</option>
            <option value="append">Dodaj do istniejących</option>
          </select>
        </label>
      </div>
      <p style={{ marginTop: '4px', marginBottom: '16px', color: '#646970', fontSize: '13px' }}>
        <strong>Nadpisz</strong> - przed importem usuwa wszystkie stawki; w bazie zostaną tylko dane z tego pliku. 
      </p>
      <p style={{ marginTop: '4px', marginBottom: '16px', color: '#646970', fontSize: '13px' }}>
        <strong>Dodaj do istniejących</strong> - dopisuje wiersze do obecnych stawek bez usuwania ich (może powstać duplikat tego samego wariantu).
      </p>

      {loading && <p>Walidacja…</p>}

      {result?.error && <p style={{ color: '#b32d2e' }}>{result.error}</p>}

      {result && !result.error && (
        <>
          <p>
            Poprawne wiersze: <strong>{result.valid_count}</strong>
            {result.error_count > 0 && (
              <>
                , Błędne: <strong>{result.error_count}</strong>
              </>
            )}
          </p>

          {result.errors?.length > 0 && (
            <div style={{ marginBottom: '12px', maxHeight: '200px', overflow: 'auto' }}>
              <strong>Błędy:</strong>
              <ul style={{ margin: '4px 0', paddingLeft: '20px' }}>
                {result.errors.slice(0, 50).map((err, i) => (
                  <li key={i}>{err}</li>
                ))}
                {result.errors.length > 50 && (
                  <li>… i {result.errors.length - 50} innych</li>
                )}
              </ul>
            </div>
          )}

          {result.preview?.length > 0 && (
            <div style={{ marginBottom: '12px', overflow: 'auto' }}>
              <strong>Podgląd (pierwsze wiersze):</strong>
              <table className="wp-list-table widefat fixed striped" style={{ marginTop: '8px' }}>
                <thead>
                  <tr>
                    <th>car_id</th>
                    <th>idv</th>
                    <th>month</th>
                    <th>km</th>
                    <th>percent</th>
                    <th>fee</th>
                    <th>rate</th>
                  </tr>
                </thead>
                <tbody>
                  {result.preview.map((row, i) => (
                    <tr key={i}>
                      <td>{row.car_id}</td>
                      <td>{row.idv}</td>
                      <td>{row.month}</td>
                      <td>{row.km}</td>
                      <td>{row.percent}</td>
                      <td>{row.fee}</td>
                      <td>{row.rate}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          )}

          {result.importError && (
            <p style={{ color: '#b32d2e', marginBottom: '12px' }}>{result.importError}</p>
          )}

          <div>
            {result.valid_count > 0 && (
              <button
                type="button"
                className="button button-primary"
                onClick={handleImportValid}
                disabled={importing}
              >
                {importing ? 'Importowanie…' : `Importuj tylko poprawne (${result.valid_count} wierszy)`}
              </button>
            )}
            <button
              type="button"
              className="button"
              style={{ marginLeft: '8px' }}
              onClick={() => {
                setFile(null);
                setResult(null);
              }}
            >
              Anuluj
            </button>
          </div>
        </>
      )}
    </div>
  );
}
