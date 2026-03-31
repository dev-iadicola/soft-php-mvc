import { createInertiaApp } from '@inertiajs/react';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';

import { buildInertiaSecurityHeaders } from '@/lib/inertia-request-security';
import { resolvePageComponent } from '@/lib/page-resolver';
import { AppProviders } from '@/providers/app-providers';
import '@/styles/tailwind.css';
import '@/styles/app.css';

const mountElement = document.getElementById('app');

if (mountElement === null) {
  throw new Error('Missing #app root element for React frontend bootstrap.');
}

void createInertiaApp({
  resolve: (name) => resolvePageComponent(name),
  title: (title) => title || 'Iadicola // dev',
  defaults: {
    visitOptions: (_href, options) => ({
      ...options,
      headers: buildInertiaSecurityHeaders(options.headers, null),
    }),
  },
  setup({ App, el, props }) {
    const initialCsrfToken =
      typeof props.initialPage.props.app === 'object' &&
      props.initialPage.props.app !== null &&
      'csrf_token' in props.initialPage.props.app
        ? String(props.initialPage.props.app.csrf_token ?? '')
        : null;

    createRoot(el).render(
      <StrictMode>
        <AppProviders>
          <App {...props} />
        </AppProviders>
      </StrictMode>,
    );

    const headers = buildInertiaSecurityHeaders({}, initialCsrfToken);

    if (headers['X-CSRF-TOKEN']) {
      const metaTag = document.querySelector('meta[name="csrf-token"]');
      if (metaTag === null) {
        const createdMeta = document.createElement('meta');
        createdMeta.name = 'csrf-token';
        createdMeta.content = headers['X-CSRF-TOKEN'];
        document.head.appendChild(createdMeta);
      }
    }
  },
});
