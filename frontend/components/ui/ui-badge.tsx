import type { PropsWithChildren } from 'react';

import { cn } from '@/lib/cn';

type UiBadgeProps = PropsWithChildren<{
  tone?: 'brand' | 'muted';
}>;

export function UiBadge({ children, tone = 'brand' }: UiBadgeProps) {
  return (
    <span
      className={cn(
        'inline-flex items-center justify-center rounded-full px-2.5 py-1 text-xs font-semibold',
        tone === 'brand' && 'bg-brand-100 text-brand-700',
        tone === 'muted' && 'bg-slate-100 text-slate-600',
      )}
    >
      {children}
    </span>
  );
}
