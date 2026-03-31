import { createInertiaApp } from '@inertiajs/react';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import { resolvePageComponent } from '@/lib/page-resolver';
import { AppProviders } from '@/providers/app-providers';
import '@/styles/tailwind.css';
import '@/styles/app.css';

const mountElement = document.getElementById('app');
const csrfToken =
  document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute('content')
    ?.trim() ?? '';

if (mountElement === null) {
  throw new Error('Missing #app root element for React frontend bootstrap.');
}

void createInertiaApp({
  defaults: {
    visitOptions: (_href, options) => ({
      ...options,
      headers: {
        ...options.headers,
        ...(csrfToken !== '' ? { 'X-CSRF-TOKEN': csrfToken } : {}),
      },
    }),
  },
  resolve: (name) => resolvePageComponent(name),
  title: (title) => (title ? `${title} | Soft MVC` : 'Soft MVC'),
  setup({ App, el, props }) {
    createRoot(el).render(
      <StrictMode>
        <AppProviders>
          <App {...props} />
        </AppProviders>
      </StrictMode>,
    );
  },
});
