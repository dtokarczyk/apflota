import { useState, useEffect, useCallback } from 'react';
import { ReactGrid, Column, Row, TextCell, NumberCell, HeaderCell } from '@silevis/reactgrid';
import '@silevis/reactgrid/styles.css';
import { fetchRates, updateRate, fetchCars } from '../api';

const COLUMN_IDS = {
  id: 'id',
  car_id: 'car_id',
  idv: 'idv',
  month: 'month',
  km: 'km',
  percent: 'percent',
  fee: 'fee',
  rate: 'rate',
};

const COLUMNS = [
  { columnId: COLUMN_IDS.id, width: 60 },
  { columnId: COLUMN_IDS.car_id, width: 80 },
  { columnId: COLUMN_IDS.idv, width: 100 },
  { columnId: COLUMN_IDS.month, width: 90 },
  { columnId: COLUMN_IDS.km, width: 90 },
  { columnId: COLUMN_IDS.percent, width: 80 },
  { columnId: COLUMN_IDS.fee, width: 110 },
  { columnId: COLUMN_IDS.rate, width: 100 },
];

function rateToRow(rate) {
  return {
    rowId: rate.id,
    cells: [
      { type: 'number', value: rate.id },
      { type: 'text', text: String(rate.car_id || '') },
      { type: 'text', text: String(rate.idv || '') },
      { type: 'number', value: rate.month },
      { type: 'number', value: rate.km },
      { type: 'number', value: rate.percent },
      { type: 'number', value: rate.fee },
      { type: 'number', value: rate.rate },
    ],
  };
}

function buildHeaderRow() {
  return {
    rowId: 'header',
    cells: COLUMNS.map((col) => ({
      type: 'header',
      text: col.columnId.replace('_', ' '),
    })),
  };
}

export default function DataGrid() {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [carIdFilter, setCarIdFilter] = useState('');
  const [carIds, setCarIds] = useState([]);
  const [carTitles, setCarTitles] = useState({});

  useEffect(() => {
    fetchCars()
      .then((data) => {
        const map = {};
        const items = data.items || [];
        items.forEach((item) => {
          map[String(item.car_id)] = item.title || '';
        });
        setCarTitles(map);
        const ids = items.map((i) => i.car_id).sort((a, b) => Number(a) - Number(b));
        setCarIds(ids);
      })
      .catch(() => {
        setCarTitles({});
        setCarIds([]);
      });
  }, []);

  const loadRates = useCallback(async () => {
    setLoading(true);
    try {
      const params = {};
      if (carIdFilter) params.car_id = carIdFilter;
      params.per_page = 10000;
      const data = await fetchRates(params);
      const header = buildHeaderRow();
      const dataRows = data.items.map(rateToRow);
      setRows([header, ...dataRows]);
    } catch (e) {
      setRows([buildHeaderRow()]);
    } finally {
      setLoading(false);
    }
  }, [carIdFilter]);

  useEffect(() => {
    loadRates();
  }, [loadRates]);

  const handleCellsChanged = useCallback(
    async (changes) => {
      const rateRows = rows.slice(1);
      const newRows = [...rows];
      for (const change of changes) {
        if (change.rowId === 'header') continue;
        const rowIndex = newRows.findIndex((r) => r.rowId === change.rowId);
        if (rowIndex < 0) continue;
        const prev = newRows[rowIndex].cells;
        const colIndex = COLUMNS.findIndex((c) => c.columnId === change.columnId);
        if (colIndex < 0) continue;
        const newCells = [...prev];
        newCells[colIndex] = change.newCell;
        newRows[rowIndex] = { ...newRows[rowIndex], cells: newCells };

        const field = change.columnId;
        let value;
        if (change.newCell.type === 'number') value = change.newCell.value;
        else value = change.newCell.text;
        try {
          await updateRate(change.rowId, { [field]: value });
        } catch (err) {
          console.error('Update failed', err);
        }
      }
      setRows(newRows);
    },
    [rows]
  );

  if (loading) {
    return <p>Ładowanie…</p>;
  }

  return (
    <div>
      <div style={{ marginBottom: '12px' }}>
        <label>
          Filtr car_id:{' '}
          <select
            value={carIdFilter}
            onChange={(e) => setCarIdFilter(e.target.value)}
            style={{ minWidth: '100px' }}
          >
            <option value="">Wszystkie</option>
            {carIds.map((id) => {
              const title = carTitles[String(id)];
              const label = title ? `${id} - ${title}` : id;
              return (
                <option key={id} value={id}>
                  {label}
                </option>
              );
            })}
          </select>
        </label>
        <button type="button" className="button" onClick={loadRates} style={{ marginLeft: '8px' }}>
          Odśwież
        </button>
      </div>
      <div className="wi-calc-datagrid-table-wrap">
        <ReactGrid
          columns={COLUMNS}
          rows={rows}
          onCellsChanged={handleCellsChanged}
          enableRangeSelection
        />
      </div>
    </div>
  );
}
