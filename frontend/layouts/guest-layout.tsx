import type { PropsWithChildren } from 'react';

import { BrandMark } from '@/components/brand-mark';
import { FlashStack } from '@/components/flash-stack';

type GuestLayoutProps = PropsWithChildren<{
  title: string;
  eyebrow?: string;
  description?: string;
}>;

export function GuestLayout({
  title,
  eyebrow = 'Guest layout',
  description,
  children,
}: GuestLayoutProps) {
  return (
    <div className="guest-shell">
      <div className="guest-shell__hero">
        <BrandMark />
        <div className="guest-shell__copy">
          <p className="guest-shell__eyebrow">{eyebrow}</p>
          <h1 className="guest-shell__title">{title}</h1>
          {description ? (
            <p className="guest-shell__description">{description}</p>
          ) : null}
        </div>
      </div>

      <FlashStack />

      <section className="guest-shell__surface">{children}</section>
    </div>
  );
}
