import type { PropsWithChildren, ReactNode } from 'react';

import { cn } from '@/lib/cn';

type UiCardProps = PropsWithChildren<{
  description?: string;
  eyebrow?: string;
  title: string;
  toolbar?: ReactNode;
}>;

export function UiCard({
  children,
  description,
  eyebrow,
  title,
  toolbar,
}: UiCardProps) {
  return (
    <section className="ui-surface flex flex-col gap-4 p-6">
      <div className="flex flex-wrap items-start justify-between gap-4">
        <div className="space-y-2">
          {eyebrow ? (
            <p className="text-xs font-semibold tracking-[0.16em] text-brand-700 uppercase">
              {eyebrow}
            </p>
          ) : null}
          <div className="space-y-1">
            <h2 className="text-xl font-semibold text-slate-950">{title}</h2>
            {description ? <p className="text-sm text-slate-600">{description}</p> : null}
          </div>
        </div>
        {toolbar ? <div>{toolbar}</div> : null}
      </div>

      {children}
    </section>
  );
}
