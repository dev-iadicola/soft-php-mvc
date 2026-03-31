import { Head, router, usePage } from '@inertiajs/react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type SessionsProps = SharedPageProps & {
  sessionsPage: {
    currentSessionId: string;
    sessions: Array<{
      createdAt: string;
      id: string;
      ip: string;
      lastActivity: string;
      userAgent: string;
    }>;
    user: {
      email: string;
    };
  };
};

export default function AdminSessionsPage() {
  const page = usePage<SessionsProps>();
  const sessionsPage = page.props.sessionsPage;

  return (
    <>
      <Head title="Sessioni attive" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { href: '/admin/security', label: 'Sicurezza' },
          { label: 'Sessioni attive' },
        ]}
        title="Sessioni attive"
        description={`Account: ${sessionsPage.user.email}`}
      >
        <section className="admin-panel">
          <div className="admin-panel__header">
            <div>
              <p className="admin-panel__eyebrow">Sessions</p>
              <h2 className="admin-panel__title">{sessionsPage.sessions.length} sessioni trovate</h2>
            </div>
          </div>

          <div className="admin-session-list">
            {sessionsPage.sessions.map((session) => (
              <article key={session.id} className="admin-session-card">
                <div className="admin-session-card__header">
                  <code>{session.id}</code>
                  {session.id === sessionsPage.currentSessionId ? <span>Corrente</span> : null}
                </div>
                <p>IP: {session.ip}</p>
                <p>Browser: {session.userAgent}</p>
                <p>Ultima attività: {session.lastActivity}</p>
                <p>Creata il: {session.createdAt}</p>
                <button
                  type="button"
                  className="admin-form-actions__button admin-form-actions__button--ghost"
                  onClick={() => router.post(`/admin/sessions/${encodeURIComponent(session.id)}/terminate`)}
                >
                  Termina sessione
                </button>
              </article>
            ))}
          </div>
        </section>
      </AdminLayout>
    </>
  );
}
