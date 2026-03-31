import { Head, usePage } from '@inertiajs/react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type CountSeries = Array<{
  count: number;
  date?: string;
  device?: string;
  month?: string;
  url?: string;
  week?: string;
  browser?: string;
}>;

type VisitorsProps = SharedPageProps & {
  visitorsPage: {
    dailyVisits: Array<{ count: number; date: string }>;
    metrics: {
      todayUnique: number;
      todayVisits: number;
      totalVisits: number;
      uniqueVisitors: number;
    };
    monthlyVisits: Array<{ count: number; month: string }>;
    recentVisits: Array<{
      createdAt: string;
      id: number;
      ip: string;
      url: string;
      userAgent: string;
    }>;
    topBrowsers: Array<{ browser: string; count: number }>;
    topDevices: Array<{ count: number; device: string }>;
    topPages: Array<{ count: number; url: string }>;
    weeklyVisits: Array<{ count: number; week: string }>;
  };
};

function PercentageList({
  items,
  labelKey,
}: {
  items: CountSeries;
  labelKey: 'browser' | 'date' | 'device' | 'month' | 'url' | 'week';
}) {
  const max = Math.max(...items.map((item) => item.count), 1);

  return (
    <div className="admin-chart-list">
      {items.map((item) => (
        <div
          key={`${labelKey}-${String(item[labelKey] ?? '')}`}
          className="admin-chart-list__row"
        >
          <span>{item[labelKey] ?? '-'}</span>
          <div className="admin-chart-list__bar">
            <span style={{ width: `${Math.max((item.count / max) * 100, 8)}%` }} />
          </div>
          <strong>{item.count}</strong>
        </div>
      ))}
    </div>
  );
}

export default function AdminVisitorsPage() {
  const page = usePage<VisitorsProps>();
  const visitorsPage = page.props.visitorsPage;

  return (
    <>
      <Head title="Statistiche visitatori" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Visitors' },
        ]}
        title="Statistiche visitatori"
        description="Panoramica estesa del traffico con trend, browser, dispositivi, pagine top e visite recenti."
      >
        <div className="admin-preview-grid">
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Visite totali</span>
            <strong className="admin-stat-card__value">
              {visitorsPage.metrics.totalVisits}
            </strong>
          </div>
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Visitatori unici</span>
            <strong className="admin-stat-card__value">
              {visitorsPage.metrics.uniqueVisitors}
            </strong>
          </div>
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Visite oggi</span>
            <strong className="admin-stat-card__value">
              {visitorsPage.metrics.todayVisits}
            </strong>
          </div>
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Unici oggi</span>
            <strong className="admin-stat-card__value">
              {visitorsPage.metrics.todayUnique}
            </strong>
          </div>
        </div>

        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Traffic</p>
                <h2 className="admin-panel__title">Visite giornaliere</h2>
              </div>
            </div>

            <PercentageList items={visitorsPage.dailyVisits} labelKey="date" />
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Monthly</p>
                <h2 className="admin-panel__title">Visite mensili</h2>
              </div>
            </div>

            <PercentageList items={visitorsPage.monthlyVisits} labelKey="month" />
          </section>
        </div>

        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Weekly</p>
                <h2 className="admin-panel__title">Visite settimanali</h2>
              </div>
            </div>

            <PercentageList items={visitorsPage.weeklyVisits} labelKey="week" />
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Audience</p>
                <h2 className="admin-panel__title">Browser e dispositivi</h2>
              </div>
            </div>

            <div className="admin-split-panel">
              <div>
                <h3 className="admin-subtitle">Browser</h3>
                <PercentageList items={visitorsPage.topBrowsers} labelKey="browser" />
              </div>
              <div>
                <h3 className="admin-subtitle">Dispositivi</h3>
                <PercentageList items={visitorsPage.topDevices} labelKey="device" />
              </div>
            </div>
          </section>
        </div>

        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Top pages</p>
                <h2 className="admin-panel__title">Pagine più visitate</h2>
              </div>
            </div>

            <PercentageList items={visitorsPage.topPages} labelKey="url" />
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Recent</p>
                <h2 className="admin-panel__title">Visite recenti</h2>
              </div>
            </div>

            <div className="admin-table">
              <div className="admin-table__head">
                <span>IP</span>
                <span>Pagina</span>
                <span>User agent</span>
                <span>Data</span>
              </div>

              {visitorsPage.recentVisits.map((visit) => (
                <div key={visit.id} className="admin-table__row">
                  <span>{visit.ip || '-'}</span>
                  <span>{visit.url || '-'}</span>
                  <span>{visit.userAgent || '-'}</span>
                  <span>{visit.createdAt || '-'}</span>
                </div>
              ))}

              {visitorsPage.recentVisits.length === 0 ? (
                <div className="admin-empty-state">
                  <strong>Nessuna visita registrata</strong>
                  <p>I dati appariranno qui appena il tracking raccoglierà eventi.</p>
                </div>
              ) : null}
            </div>
          </section>
        </div>
      </AdminLayout>
    </>
  );
}
