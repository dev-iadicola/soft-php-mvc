import type { PropsWithChildren } from 'react';

type UiStatCardProps = PropsWithChildren<{
  description?: string;
  label: string;
  value: string;
}>;

export function UiStatCard({
  children,
  description,
  label,
  value,
}: UiStatCardProps) {
  return (
    <div className="ui-stat-card">
      <span className="ui-stat-card__label">{label}</span>
      <strong className="ui-stat-card__value">{value}</strong>
      {description ? <p className="ui-stat-card__description">{description}</p> : null}
      {children}
    </div>
  );
}
