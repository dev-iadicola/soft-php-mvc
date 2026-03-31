import type { ComponentType } from 'react';

import FallbackPage from '@/pages/fallback-page';

type PageModule = {
  default?: ComponentType<unknown>;
};

const pages = import.meta.glob('../pages/**/*.tsx', {
  eager: true,
}) as Record<string, PageModule>;

function missingPageComponent(name: string): ComponentType<unknown> {
  return function MissingPageComponent() {
    return <FallbackPage componentName={name} />;
  };
}

export function resolvePageComponent(name: string): ComponentType<unknown> {
  const path = `../pages/${name}.tsx`;
  const module = pages[path];

  if (module?.default) {
    return module.default;
  }

  return missingPageComponent(name);
}
