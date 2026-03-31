import { Head, useForm } from '@inertiajs/react';

import { AdminField, FieldControl } from '@/components/forms/admin-field';
import { AdminFormSection } from '@/components/forms/admin-form-section';
import { AdminFormShell } from '@/components/forms/admin-form-shell';
import { AdminInput } from '@/components/forms/admin-input';
import { AdminSelect } from '@/components/forms/admin-select';
import { AdminTextarea } from '@/components/forms/admin-textarea';
import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type PreviewOption = {
  label: string;
  value: string;
};

type PreviewFormProps = SharedPageProps & {
  preview: {
    categories: PreviewOption[];
    visibilities: PreviewOption[];
  };
};

type StrategyFormData = {
  category: string;
  cover: File | null;
  excerpt: string;
  overview: string;
  publishAt: string;
  slug: string;
  title: string;
  visibility: string;
};

function countCompletedFields(data: StrategyFormData): number {
  const values = [
    data.title,
    data.slug,
    data.category,
    data.visibility,
    data.excerpt,
    data.overview,
    data.publishAt,
    data.cover ? 'cover' : '',
  ];

  return values.filter((value) => value.trim() !== '').length;
}

export default function AdminFormStrategyPreview({
  preview,
}: PreviewFormProps) {
  const form = useForm<StrategyFormData>({
    category: preview.categories[0]?.value ?? '',
    cover: null,
    excerpt: '',
    overview: '',
    publishAt: '',
    slug: '',
    title: '',
    visibility: preview.visibilities[0]?.value ?? 'draft',
  });

  const completedFields = countCompletedFields(form.data);
  const completion = Math.round((completedFields / 8) * 100);

  const runValidation = () => {
    const errors: Record<string, string> = {};

    if (form.data.title.trim() === '') {
      errors.title = 'Il titolo è obbligatorio per il submit server-side.';
    }

    if (form.data.slug.trim() === '') {
      errors.slug = 'Lo slug serve per URL, canonical e preview SEO.';
    }

    if (form.data.excerpt.trim().length < 24) {
      errors.excerpt = 'L’excerpt dovrebbe essere più descrittivo.';
    }

    if (form.data.overview.trim().length < 80) {
      errors.overview = 'Il contenuto demo è troppo corto per un editor reale.';
    }

    form.setError(errors);
  };

  const resetForm = () => {
    form.reset();
    form.clearErrors();
  };

  return (
    <>
      <Head title="Admin Form Strategy" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { href: '/react-preview/admin', label: 'React preview' },
          { label: 'Form strategy' },
        ]}
        title="Admin form strategy"
        description="Base riusabile per form admin React con error bag, validazione server-side, upload e aside impostazioni."
        notificationCount={4}
        pageActions={[
          { href: '#validate', label: 'Run validation' },
          { href: '#publish', label: 'Preview submit', variant: 'primary' },
        ]}
      >
        <AdminFormShell
          aside={
            <div className="admin-form-aside">
              <AdminFormSection
                eyebrow="Progress"
                title="Completion"
                description="Indicatore utile per editor lunghi e per capire subito se manca qualche blocco importante."
              >
                <div className="admin-form-progress">
                  <div className="admin-form-progress__bar">
                    <span
                      className="admin-form-progress__value"
                      style={{ width: `${completion}%` }}
                    />
                  </div>
                  <strong className="admin-form-progress__label">{completion}% complete</strong>
                </div>
              </AdminFormSection>

              <AdminFormSection
                eyebrow="Server contract"
                title="Validation strategy"
                description="Il server resta la source of truth; `useForm` gestisce pending state, file upload e mapping degli errori."
              >
                <ul className="admin-form-checklist">
                  <li>Error bag mappata per campo singolo</li>
                  <li>Submit reale via Inertia con `forceFormData` per upload</li>
                  <li>CTA sticky con feedback su stato e dirty form</li>
                  <li>Editor rich-text integrabile come campo controllato</li>
                </ul>
              </AdminFormSection>

              <AdminFormSection
                eyebrow="Media"
                title="Cover upload"
                description="Nel branch successivo il form potrà inviare file reali usando lo stesso shell."
              >
                <div className="admin-upload-summary">
                  <strong>{form.data.cover?.name ?? 'Nessun file selezionato'}</strong>
                  <span>
                    {form.data.cover
                      ? `${Math.round(form.data.cover.size / 1024)} KB`
                      : 'Supporto previsto per upload, progress e validazioni MIME.'}
                  </span>
                </div>
              </AdminFormSection>
            </div>
          }
          footer={
            <div className="admin-form-actions">
              <button
                type="button"
                className="admin-form-actions__button admin-form-actions__button--ghost"
                onClick={resetForm}
              >
                Reset draft
              </button>
              <button
                id="validate"
                type="button"
                className="admin-form-actions__button admin-form-actions__button--ghost"
                onClick={runValidation}
              >
                Run validation
              </button>
              <button
                id="publish"
                type="button"
                className="admin-form-actions__button admin-form-actions__button--primary"
                onClick={runValidation}
              >
                Preview submit
              </button>
            </div>
          }
        >
          <form
            className="admin-form-grid"
            onSubmit={(event) => {
              event.preventDefault();
              runValidation();
            }}
          >
            <AdminFormSection
              eyebrow="Editorial"
              title="Main content"
              description="Area principale per i campi a maggior peso editoriale."
            >
              <AdminField
                id="title"
                label="Title"
                required
                error={form.errors.title}
                hint="Il titolo deve essere chiaro, scansionabile e pronto per SEO/social."
              >
                <FieldControl invalid={Boolean(form.errors.title)}>
                  <AdminInput
                    id="title"
                    value={form.data.title}
                    invalid={Boolean(form.errors.title)}
                    placeholder="Esempio: Come strutturare un articolo React con Inertia"
                    onChange={(event) => form.setData('title', event.target.value)}
                  />
                </FieldControl>
              </AdminField>

              <div className="admin-form-grid__columns">
                <AdminField
                  id="slug"
                  label="Slug"
                  required
                  error={form.errors.slug}
                  hint="Usato per URL, canonical e preview snippet."
                >
                  <FieldControl invalid={Boolean(form.errors.slug)}>
                    <AdminInput
                      id="slug"
                      value={form.data.slug}
                      invalid={Boolean(form.errors.slug)}
                      placeholder="react-inertia-form-strategy"
                      onChange={(event) => form.setData('slug', event.target.value)}
                    />
                  </FieldControl>
                </AdminField>

                <AdminField
                  id="publishAt"
                  label="Publish at"
                  hint="Serve per scheduling editoriale e listing ordinato."
                >
                  <FieldControl>
                    <AdminInput
                      id="publishAt"
                      type="date"
                      value={form.data.publishAt}
                      onChange={(event) => form.setData('publishAt', event.target.value)}
                    />
                  </FieldControl>
                </AdminField>
              </div>

              <AdminField
                id="excerpt"
                label="Excerpt"
                required
                error={form.errors.excerpt}
                hint="Estratto breve per listati, meta description e anteprime."
                toolbar={<span>{form.data.excerpt.length}/180</span>}
              >
                <FieldControl invalid={Boolean(form.errors.excerpt)}>
                  <AdminTextarea
                    id="excerpt"
                    rows={4}
                    value={form.data.excerpt}
                    invalid={Boolean(form.errors.excerpt)}
                    placeholder="Riassumi il valore dell’articolo in 1-2 frasi chiare."
                    onChange={(event) => form.setData('excerpt', event.target.value)}
                  />
                </FieldControl>
              </AdminField>

              <AdminField
                id="overview"
                label="Overview"
                required
                error={form.errors.overview}
                hint="Placeholder per il futuro editor rich-text: heading, liste, quote, callout."
              >
                <FieldControl invalid={Boolean(form.errors.overview)}>
                  <AdminTextarea
                    id="overview"
                    rows={8}
                    value={form.data.overview}
                    invalid={Boolean(form.errors.overview)}
                    placeholder="Scrivi qui l’overview dell’articolo. Nel branch futuro questo blocco verrà sostituito dall’editor rich-text."
                    onChange={(event) => form.setData('overview', event.target.value)}
                  />
                </FieldControl>
              </AdminField>
            </AdminFormSection>

            <AdminFormSection
              eyebrow="Settings"
              title="Publishing controls"
              description="Campi che in un CRUD reale vivranno nella sidebar o in meta-box dedicate."
            >
              <div className="admin-form-grid__columns">
                <AdminField
                  id="category"
                  label="Category"
                  hint="Tassonomia editoriale gestita via props server-side."
                >
                  <FieldControl>
                    <AdminSelect
                      id="category"
                      value={form.data.category}
                      onChange={(event) => form.setData('category', event.target.value)}
                    >
                      {preview.categories.map((option) => (
                        <option key={option.value} value={option.value}>
                          {option.label}
                        </option>
                      ))}
                    </AdminSelect>
                  </FieldControl>
                </AdminField>

                <AdminField
                  id="visibility"
                  label="Visibility"
                  hint="Draft, scheduled o published."
                >
                  <FieldControl>
                    <AdminSelect
                      id="visibility"
                      value={form.data.visibility}
                      onChange={(event) => form.setData('visibility', event.target.value)}
                    >
                      {preview.visibilities.map((option) => (
                        <option key={option.value} value={option.value}>
                          {option.label}
                        </option>
                      ))}
                    </AdminSelect>
                  </FieldControl>
                </AdminField>
              </div>

              <AdminField
                id="cover"
                label="Cover image"
                hint="I file vivranno nel form Inertia con `forceFormData` e progress indicator."
              >
                <FieldControl>
                  <AdminInput
                    id="cover"
                    type="file"
                    accept="image/*"
                    onChange={(event) =>
                      form.setData('cover', event.target.files?.[0] ?? null)
                    }
                  />
                </FieldControl>
              </AdminField>
            </AdminFormSection>
          </form>
        </AdminFormShell>
      </AdminLayout>
    </>
  );
}
