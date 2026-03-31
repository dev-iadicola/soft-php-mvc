import { usePage } from '@inertiajs/react';

import { cn } from '@/lib/cn';
import type { SharedPageProps } from '@/types/inertia';

const FLASH_VARIANTS = [
  { key: 'success', label: 'Success', className: 'flash-stack__item--success' },
  { key: 'warning', label: 'Warning', className: 'flash-stack__item--warning' },
  { key: 'error', label: 'Error', className: 'flash-stack__item--error' },
] as const;

export function FlashStack() {
  const page = usePage<SharedPageProps>();
  const flash = page.props.flash ?? {};

  const items = FLASH_VARIANTS
    .map((variant) => ({
      ...variant,
      message: flash[variant.key] ?? null,
    }))
    .filter((variant) => variant.message);

  if (items.length === 0) {
    return null;
  }

  return (
    <div className="flash-stack" aria-live="polite">
      {items.map((item) => (
        <div key={item.key} className={cn('flash-stack__item', item.className)}>
          <span className="flash-stack__label">{item.label}</span>
          <p className="flash-stack__message">{item.message}</p>
        </div>
      ))}
    </div>
  );
}
