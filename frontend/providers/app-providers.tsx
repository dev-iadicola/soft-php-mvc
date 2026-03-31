import { router } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import { RouterProvider } from 'react-aria-components';

export function AppProviders({ children }: PropsWithChildren) {
  return (
    <RouterProvider
      navigate={(path) => {
        router.visit(String(path));
      }}
      useHref={(href) => String(href)}
    >
      {children}
    </RouterProvider>
  );
}
