import { usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';
import { useState } from 'react';

import { AdminBreadcrumb, type AdminBreadcrumbItem } from '@/components/admin-breadcrumb';
import { AdminSidebar, type AdminNavSection } from '@/components/admin-sidebar';
import { AdminTopbar } from '@/components/admin-topbar';
import { BrandMark } from '@/components/brand-mark';
import { FlashStack } from '@/components/flash-stack';
import { cn } from '@/lib/cn';
import type { SharedPageProps } from '@/types/inertia';

type AdminLayoutProps = PropsWithChildren<{
  breadcrumbs?: AdminBreadcrumbItem[];
  description?: string;
  notificationCount?: number;
  pageActions?: Array<{
    href: string;
    label: string;
    variant?: 'ghost' | 'primary';
  }>;
  sections?: AdminNavSection[];
  title: string;
}>;

const DEFAULT_SECTIONS: AdminNavSection[] = [
  {
    title: 'Overview',
    items: [
      { href: '/admin/dashboard', label: 'Dashboard' },
      { href: '/react-preview/admin', label: 'React preview' },
      { href: '/react-preview/admin/forms', label: 'Form strategy' },
      { href: '/admin/notifications/count', label: 'Notifications API' },
    ],
  },
  {
    title: 'Content',
    items: [
      { href: '/admin/home', label: 'Articles' },
      { href: '/admin/project', label: 'Projects' },
      { href: '/admin/technology', label: 'Tech stack' },
    ],
  },
  {
    title: 'System',
    items: [
      { href: '/admin/edit-profile', label: 'Account' },
      { href: '/admin/security', label: 'Security' },
      { href: '/admin/settings', label: 'Settings' },
    ],
  },
];

export function AdminLayout({
  breadcrumbs,
  description,
  notificationCount = 3,
  pageActions = [],
  sections = DEFAULT_SECTIONS,
  title,
  children,
}: AdminLayoutProps) {
  const page = usePage<SharedPageProps>();
  const [isSidebarOpen, setIsSidebarOpen] = useState(false);
  const userEmail = page.props.auth?.user?.email ?? 'admin@example.com';

  return (
    <div className="app-shell app-shell--admin">
      <div
        className={cn('app-shell__backdrop', {
          'app-shell__backdrop--open': isSidebarOpen,
        })}
        onClick={() => setIsSidebarOpen(false)}
      />

      <aside
        className={cn('app-sidebar', {
          'app-sidebar--open': isSidebarOpen,
        })}
      >
        <div className="app-sidebar__brand">
          <a className="app-sidebar__brand-link" href="/admin/dashboard">
            <BrandMark />
          </a>

          <button
            type="button"
            className="app-sidebar__close"
            aria-label="Close admin navigation"
            onClick={() => setIsSidebarOpen(false)}
          >
            Close
          </button>
        </div>

        <div className="app-sidebar__intro">
          <p className="app-sidebar__label">Admin shell</p>
          <p className="app-sidebar__copy">
            Sidebar React condivisa pronta per dashboard, CRUD e flussi account.
          </p>
        </div>

        <AdminSidebar currentPath={page.url} sections={sections} />
      </aside>

      <main className="app-main">
        <AdminTopbar
          notificationCount={notificationCount}
          onOpenSidebar={() => setIsSidebarOpen(true)}
          pageActions={pageActions}
          userEmail={userEmail}
        />

        <header className="app-header">
          <div>
            <AdminBreadcrumb items={breadcrumbs} />
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
