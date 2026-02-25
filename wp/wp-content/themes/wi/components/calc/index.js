/**
 * Calculator admin app entry – mounts React into #wi-calc-admin.
 */

import { createRoot } from 'react-dom/client';
import App from './App';

const el = document.getElementById('wi-calc-admin');
if (el) {
  const root = createRoot(el);
  root.render(<App />);
}
