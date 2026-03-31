import type { PropsWithChildren } from 'react';

import { BrandMark } from '@/components/brand-mark';
import { FlashStack } from '@/components/flash-stack';

type AdminLayoutProps = PropsWithChildren<{
  title: string;
  description?: string;
}>;

export function AdminLayout({
  title,
  description,
  children,
}: AdminLayoutProps) {
  return (
    <div className="app-shell app-shell--admin">
      <aside className="app-sidebar">
        <BrandMark />
        <div className="app-sidebar__section">
          <p className="app-sidebar__label">Admin shell</p>
          <p className="app-sidebar__copy">
            Questo layout verrà esteso nei branch successivi con sidebar,
            topbar, notifiche e page actions.
          </p>
        </div>
      </aside>

      <main className="app-main">
        <header className="app-header">
          <div>
            <p className="app-header__eyebrow">Admin layout</p>
            <h1 className="app-header__title">{title}</h1>
            {description ? (
              <p className="app-header__description">{description}</p>
            ) : null}
          </div>
        </header>

        <FlashStack />

        <section className="app-surface">{children}</section>
      </main>
    </div>
  );
}
