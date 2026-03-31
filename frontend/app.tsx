import { createInertiaApp } from '@inertiajs/react';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import { resolvePageComponent } from '@/lib/page-resolver';
import '@/styles/app.css';

const mountElement = document.getElementById('app');

if (mountElement === null) {
  throw new Error('Missing #app root element for React frontend bootstrap.');
}

void createInertiaApp({
  resolve: (name) => resolvePageComponent(name),
  title: (title) => (title ? `${title} | Soft MVC` : 'Soft MVC'),
  setup({ App, el, props }) {
    createRoot(el).render(
      <StrictMode>
        <App {...props} />
      </StrictMode>,
    );
  },
});
