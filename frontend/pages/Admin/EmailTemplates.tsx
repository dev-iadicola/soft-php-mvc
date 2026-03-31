import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type EmailTemplate = {
  body: string;
  id: number;
  isActive: boolean;
  slug: string;
  subject: string;
  updatedAt: string;
};

type EmailTemplatesProps = SharedPageProps & {
  emailTemplatesPage: {
    current: EmailTemplate | null;
    placeholders: Array<{
      description: string;
      label: string;
    }>;
    templates: EmailTemplate[];
  };
};

export default function AdminEmailTemplatesPage() {
  const page = usePage<EmailTemplatesProps>();
  const emailTemplatesPage = page.props.emailTemplatesPage;
  const current = emailTemplatesPage.current;
  const form = useForm({
    body: current?.body ?? '',
    is_active: current?.isActive ?? false,
    subject: current?.subject ?? '',
  });

  useEffect(() => {
    form.setData({
      body: current?.body ?? '',
      is_active: current?.isActive ?? false,
      subject: current?.subject ?? '',
    });
  }, [current?.id]);

  return (
    <>
      <Head title="Template email" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Template email' },
        ]}
        title="Template email"
        description="Gestione dei template automatici con placeholder e stato attivo/disattivo."
      >
        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Catalog</p>
                <h2 className="admin-panel__title">Template disponibili</h2>
              </div>
            </div>

            <div className="admin-record-list">
              {emailTemplatesPage.templates.map((template) => (
                <article
                  key={template.id}
                  className={`admin-record-card${
                    current?.id === template.id ? ' admin-record-card--active' : ''
                  }`}
                >
                  <div className="admin-record-card__header">
                    <div>
                      <strong>{template.slug}</strong>
                      <p>{template.subject}</p>
                    </div>
                    <span
                      className={`admin-badge${
                        template.isActive ? '' : ' admin-badge--neutral'
                      }`}
                    >
                      {template.isActive ? 'Attivo' : 'Disattivo'}
                    </span>
                  </div>

                  <div className="admin-record-card__meta">
                    <span>Ultima modifica</span>
                    <span>{template.updatedAt || 'N/D'}</span>
                  </div>

                  <Link
                    href={`/admin/email-templates/${template.id}/edit`}
                    className="admin-inline-link"
                  >
                    Modifica template
                  </Link>
                </article>
              ))}

              {emailTemplatesPage.templates.length === 0 ? (
                <div className="admin-empty-state">
                  <strong>Nessun template configurato</strong>
                  <p>Esegui migration e seeder per inizializzare i template automatici.</p>
                </div>
              ) : null}
            </div>
          </section>

          <section className="admin-panel">
            {current ? (
              <form
                className="admin-form-card"
                onSubmit={(event) => {
                  event.preventDefault();
                  form.post(`/admin/email-templates/${current.id}`);
                }}
              >
                <div className="admin-panel__header">
                  <div>
                    <p className="admin-panel__eyebrow">Editing</p>
                    <h2 className="admin-panel__title">{current.slug}</h2>
                  </div>
                </div>

                <label className="admin-filter-field" htmlFor="template-subject">
                  <span>Oggetto</span>
                  <input
                    id="template-subject"
                    className="admin-input"
                    value={form.data.subject}
                    onChange={(event) => form.setData('subject', event.target.value)}
                    placeholder="Oggetto email"
                    required
                  />
                </label>

                <label className="admin-filter-field" htmlFor="template-body">
                  <span>Corpo HTML</span>
                  <textarea
                    id="template-body"
                    className="admin-input admin-textarea"
                    rows={14}
                    value={form.data.body}
                    onChange={(event) => form.setData('body', event.target.value)}
                    placeholder="<p>Ciao {nome}, ...</p>"
                    required
                  />
                </label>

                <label className="admin-checkbox-row">
                  <input
                    type="checkbox"
                    checked={form.data.is_active}
                    onChange={(event) => form.setData('is_active', event.target.checked)}
                  />
                  <span>Template attivo</span>
                </label>

                <div className="admin-placeholder-grid">
                  {emailTemplatesPage.placeholders.map((placeholder) => (
                    <button
                      key={placeholder.label}
                      type="button"
                      className="admin-placeholder-chip"
                      onClick={() =>
                        form.setData(
                          'body',
                          `${form.data.body}${form.data.body ? ' ' : ''}${placeholder.label}`,
                        )
                      }
                    >
                      <strong>{placeholder.label}</strong>
                      <span>{placeholder.description}</span>
                    </button>
                  ))}
                </div>

                <div className="admin-form-actions">
                  <button
                    type="submit"
                    className="admin-form-actions__button"
                    disabled={form.processing}
                  >
                    {form.processing ? 'Salvataggio...' : 'Salva template'}
                  </button>
                  <Link
                    href="/admin/email-templates"
                    className="admin-form-actions__button admin-form-actions__button--ghost"
                  >
                    Annulla
                  </Link>
                </div>
              </form>
            ) : (
              <div className="admin-empty-state admin-empty-state--tall">
                <strong>Seleziona un template</strong>
                <p>
                  Scegli una riga dalla colonna di sinistra per modificare oggetto,
                  corpo HTML e stato di invio automatico.
                </p>
              </div>
            )}
          </section>
        </div>
      </AdminLayout>
    </>
  );
}
