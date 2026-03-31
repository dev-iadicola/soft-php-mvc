import { Head, usePage } from '@inertiajs/react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type PreviewStat = {
  label: string;
  value: string;
};

type AdminPreviewProps = SharedPageProps & {
  preview: {
    actions: Array<{
      href: string;
      label: string;
      variant?: 'ghost' | 'primary';
    }>;
    description: string;
    stats: PreviewStat[];
    title: string;
  };
};

export default function AdminPreviewDashboard() {
  const page = usePage<AdminPreviewProps>();
  const preview = page.props.preview;

  return (
    <>
      <Head title="Admin React Preview" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'React preview' },
        ]}
        title={preview.title}
        description={preview.description}
        pageActions={preview.actions}
        notificationCount={4}
      >
        <div className="admin-preview-grid">
          {preview.stats.map((stat) => (
            <section key={stat.label} className="admin-stat-card">
              <span className="admin-stat-card__label">{stat.label}</span>
              <strong className="admin-stat-card__value">{stat.value}</strong>
            </section>
          ))}
        </div>

        <section className="admin-panel">
          <div className="admin-panel__header">
            <div>
              <p className="admin-panel__eyebrow">Layout capabilities</p>
              <h2 className="admin-panel__title">What this shell already covers</h2>
            </div>
          </div>

          <ul className="admin-panel__list">
            <li>Sidebar desktop con sezioni e stato attivo della route corrente</li>
            <li>Drawer mobile con backdrop e chiusura esplicita</li>
            <li>Topbar con notification bell, user snapshot e page actions</li>
            <li>Breadcrumb e area contenuto pronta per CRUD e dashboard reali</li>
          </ul>
        </section>
      </AdminLayout>
    </>
  );
}
