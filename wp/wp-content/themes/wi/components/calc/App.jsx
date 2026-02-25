import { useState } from 'react';
import DataGrid from './components/DataGrid';
import CsvImport from './components/CsvImport';
import UploadHistory from './components/UploadHistory';

const TABS = [
  { id: 'data', label: 'Dane kalkulatora' },
  { id: 'import', label: 'Import CSV' },
  { id: 'history', label: 'Historia importów' },
];

export default function App() {
  const [activeTab, setActiveTab] = useState('data');

  return (
    <div className="wi-calc-admin-wrap" style={{ padding: '20px 0' }}>
      <h1 style={{ marginBottom: '16px' }}>Kalkulator</h1>

      <div className="nav-tab-wrapper" style={{ marginBottom: '16px' }}>
        {TABS.map((tab) => (
          <button
            key={tab.id}
            type="button"
            className={`nav-tab ${activeTab === tab.id ? 'nav-tab-active' : ''}`}
            onClick={() => setActiveTab(tab.id)}
          >
            {tab.label}
          </button>
        ))}
      </div>

      {activeTab === 'data' && <DataGrid />}
      {activeTab === 'import' && <CsvImport onImported={() => setActiveTab('history')} />}
      {activeTab === 'history' && <UploadHistory />}
    </div>
  );
}
