import type { PropsWithChildren, ReactNode } from 'react';

type UiEmptyStateProps = PropsWithChildren<{
  actions?: ReactNode;
  description: string;
  title: string;
}>;

export function UiEmptyState({
  actions,
  children,
  description,
  title,
}: UiEmptyStateProps) {
  return (
    <div className="ui-empty-state">
      <div className="space-y-2">
        <h2 className="ui-empty-state__title">{title}</h2>
        <p className="ui-empty-state__description">{description}</p>
      </div>
      {children}
      {actions ? <div className="flex flex-wrap justify-center gap-3">{actions}</div> : null}
    </div>
  );
}
