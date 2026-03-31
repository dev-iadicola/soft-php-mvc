import { usePage } from '@inertiajs/react';

import type { SharedPageProps } from '@/types/inertia';

export function useAppName(): string {
  const page = usePage<SharedPageProps>();

  return page.props.app?.name ?? 'Soft MVC';
}
