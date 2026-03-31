import { Head, usePage } from '@inertiajs/react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type DashboardProps = SharedPageProps & {
  dashboard: {
    dailyVisits: Array<{ count: number; date: string }>;
    messages: Array<{
      createdAt: string;
      excerpt: string;
      id: number;
      isRead: boolean;
      name: string;
      typology: string;
    }>;
    stats: Array<{ href: string; label: string; value: string }>;
    todayVisits: number;
  };
};

export default function AdminDashboardPage() {
  const page = usePage<DashboardProps>();
  const dashboard = page.props.dashboard;

  const maxVisits = Math.max(...dashboard.dailyVisits.map((day) => day.count), 1);

  return (
    <>
      <Head title="Dashboard admin" />

      <AdminLayout
        breadcrumbs={[{ label: 'Dashboard' }]}
        title="Dashboard"
        description="Panoramica rapida del sito e dei contenuti più recenti."
      >
        <div className="admin-preview-grid">
          {dashboard.stats.map((stat) => (
            <a key={stat.label} href={stat.href} className="admin-stat-card admin-stat-card--link">
              <span className="admin-stat-card__label">{stat.label}</span>
              <strong className="admin-stat-card__value">{stat.value}</strong>
            </a>
          ))}
        </div>

        <div className="admin-dashboard-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Weekly trend</p>
                <h2 className="admin-panel__title">Visite ultimi 7 giorni</h2>
              </div>
            </div>

            <div className="admin-chart-list">
              {dashboard.dailyVisits.map((day) => (
                <div key={day.date} className="admin-chart-list__row">
                  <span>{day.date}</span>
                  <div className="admin-chart-list__bar">
                    <span
                      style={{ width: `${Math.max((day.count / maxVisits) * 100, 8)}%` }}
                    />
                  </div>
                  <strong>{day.count}</strong>
                </div>
              ))}
            </div>
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Today</p>
                <h2 className="admin-panel__title">Visite odierne</h2>
              </div>
            </div>

            <div className="admin-kpi">
              <strong className="admin-kpi__value">{dashboard.todayVisits}</strong>
              <p className="admin-panel__description">
                Snapshot rapido per tenere d’occhio il traffico corrente.
              </p>
            </div>
          </section>
        </div>

        <section className="admin-panel">
          <div className="admin-panel__header">
            <div>
              <p className="admin-panel__eyebrow">Inbox</p>
              <h2 className="admin-panel__title">Messaggi recenti</h2>
            </div>
          </div>

          <div className="admin-message-list">
            {dashboard.messages.map((message) => (
              <a
                key={message.id}
                href={`/admin/contatti/${message.id}`}
                className="admin-message-list__item"
              >
                <div>
                  <strong>{message.name}</strong>
                  <p>{message.excerpt}</p>
                </div>
                <div className="admin-message-list__meta">
                  {message.typology ? <span>{message.typology}</span> : null}
                  <span>{message.createdAt}</span>
                  {!message.isRead ? <em>Nuovo</em> : null}
                </div>
              </a>
            ))}
          </div>
        </section>
      </AdminLayout>
    </>
  );
}
