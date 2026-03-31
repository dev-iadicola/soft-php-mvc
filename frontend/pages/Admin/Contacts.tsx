import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type ContactMessage = {
  createdAt: string;
  email: string;
  excerpt: string;
  id: number;
  isRead: boolean;
  name: string;
  typology: string;
};

type ContactDetail = {
  createdAt: string;
  email: string;
  id: number;
  isRead: boolean;
  message: string;
  name: string;
  typology: string;
};

type ContactsProps = SharedPageProps & {
  contactsPage: {
    current: ContactDetail | null;
    filter: {
      typologie: string;
    };
    messages: ContactMessage[];
    summary: {
      total: number;
      typologies: string[];
      unread: number;
    };
  };
};

function withFilter(basePath: string, filter: string) {
  if (filter === '') {
    return basePath;
  }

  return `${basePath}?typologie=${encodeURIComponent(filter)}`;
}

export default function AdminContactsPage() {
  const page = usePage<ContactsProps>();
  const contactsPage = page.props.contactsPage;
  const replyForm = useForm({
    reply_body: '',
  });

  useEffect(() => {
    replyForm.setData('reply_body', '');
  }, [contactsPage.current?.id]);

  return (
    <>
      <Head title="Messaggi ricevuti" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Messaggi' },
        ]}
        title="Messaggi ricevuti"
        description="Inbox admin con filtro tipologia, azioni rapide e dettaglio della conversazione."
      >
        <div className="admin-preview-grid">
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Messaggi caricati</span>
            <strong className="admin-stat-card__value">{contactsPage.summary.total}</strong>
          </div>
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Non letti</span>
            <strong className="admin-stat-card__value">{contactsPage.summary.unread}</strong>
          </div>
          <div className="admin-stat-card">
            <span className="admin-stat-card__label">Tipologie</span>
            <strong className="admin-stat-card__value">
              {contactsPage.summary.typologies.length}
            </strong>
          </div>
        </div>

        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Filters</p>
                <h2 className="admin-panel__title">Lista messaggi</h2>
              </div>
            </div>

            <label className="admin-filter-field" htmlFor="contacts-typology">
              <span>Filtra per tipologia</span>
              <select
                id="contacts-typology"
                className="admin-input admin-select"
                value={contactsPage.filter.typologie}
                onChange={(event) =>
                  router.get(
                    '/admin/contatti',
                    event.target.value ? { typologie: event.target.value } : {},
                    { preserveScroll: true, replace: true },
                  )
                }
              >
                <option value="">Tutte</option>
                {contactsPage.summary.typologies.map((typology) => (
                  <option key={typology} value={typology}>
                    {typology}
                  </option>
                ))}
              </select>
            </label>

            <div className="admin-record-list">
              {contactsPage.messages.map((message) => (
                <article
                  key={message.id}
                  className={`admin-record-card${!message.isRead ? ' admin-record-card--accent' : ''}${
                    contactsPage.current?.id === message.id ? ' admin-record-card--active' : ''
                  }`}
                >
                  <div className="admin-record-card__header">
                    <div>
                      <strong>{message.name}</strong>
                      <p>{message.typology || 'Senza tipologia'}</p>
                    </div>
                    {!message.isRead ? <span className="admin-badge">Nuovo</span> : null}
                  </div>

                  <p>{message.excerpt}</p>

                  <div className="admin-record-card__meta">
                    <span>{message.email}</span>
                    <span>{message.createdAt}</span>
                  </div>

                  <div className="admin-inline-actions">
                    <Link
                      href={withFilter(`/admin/contatti/${message.id}`, contactsPage.filter.typologie)}
                      className="admin-inline-link"
                    >
                      Apri messaggio
                    </Link>
                    <button
                      type="button"
                      className="admin-inline-button"
                      onClick={() =>
                        router.post(`/admin/contatti/${message.id}/toggle-read`, {}, { preserveScroll: true })
                      }
                    >
                      {message.isRead ? 'Segna non letto' : 'Segna letto'}
                    </button>
                  </div>
                </article>
              ))}

              {contactsPage.messages.length === 0 ? (
                <div className="admin-empty-state">
                  <strong>Nessun messaggio disponibile</strong>
                  <p>Il filtro corrente non ha prodotto risultati.</p>
                </div>
              ) : null}
            </div>
          </section>

          <section className="admin-panel">
            {contactsPage.current ? (
              <>
                <div className="admin-panel__header">
                  <div>
                    <p className="admin-panel__eyebrow">Conversation</p>
                    <h2 className="admin-panel__title">{contactsPage.current.name}</h2>
                  </div>
                  <span className="admin-badge admin-badge--neutral">
                    {contactsPage.current.typology || 'Generico'}
                  </span>
                </div>

                <div className="admin-record-card admin-record-card--detail">
                  <div className="admin-record-card__meta">
                    <span>{contactsPage.current.email}</span>
                    <span>{contactsPage.current.createdAt}</span>
                  </div>

                  <p className="admin-detail-message">{contactsPage.current.message}</p>

                  <div className="admin-inline-actions">
                    <button
                      type="button"
                      className="admin-inline-button"
                      onClick={() =>
                        router.post(
                          `/admin/contatti/${contactsPage.current?.id}/toggle-read`,
                          {},
                          { preserveScroll: true },
                        )
                      }
                    >
                      {contactsPage.current.isRead ? 'Segna non letto' : 'Segna letto'}
                    </button>
                    <button
                      type="button"
                      className="admin-inline-button admin-inline-button--danger"
                      onClick={() => {
                        if (
                          window.confirm(
                            `Eliminare il messaggio di ${contactsPage.current?.name}?`,
                          )
                        ) {
                          router.delete(`/admin/contatti-delete/${contactsPage.current?.id}/`);
                        }
                      }}
                    >
                      Elimina messaggio
                    </button>
                  </div>
                </div>

                <form
                  className="admin-form-card"
                  onSubmit={(event) => {
                    event.preventDefault();
                    replyForm.post(`/admin/contatti/${contactsPage.current?.id}/reply`);
                  }}
                >
                  <div className="admin-panel__header">
                    <div>
                      <p className="admin-panel__eyebrow">Reply</p>
                      <h2 className="admin-panel__title">Rispondi al mittente</h2>
                    </div>
                  </div>

                  <textarea
                    className="admin-input admin-textarea"
                    rows={8}
                    value={replyForm.data.reply_body}
                    onChange={(event) => replyForm.setData('reply_body', event.target.value)}
                    placeholder="Scrivi la tua risposta..."
                    required
                  />

                  <div className="admin-form-actions">
                    <button
                      type="submit"
                      className="admin-form-actions__button"
                      disabled={replyForm.processing}
                    >
                      {replyForm.processing ? 'Invio...' : 'Invia risposta'}
                    </button>
                  </div>
                </form>
              </>
            ) : (
              <div className="admin-empty-state admin-empty-state--tall">
                <strong>Seleziona un messaggio</strong>
                <p>
                  Apri una conversazione dalla colonna di sinistra per vedere il dettaglio
                  e inviare una risposta.
                </p>
              </div>
            )}
          </section>
        </div>
      </AdminLayout>
    </>
  );
}
