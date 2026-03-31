import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useEffect } from 'react';

import { AdminLayout } from '@/layouts/admin-layout';
import type { SharedPageProps } from '@/types/inertia';

type Technology = {
  icon: string | null;
  id: number;
  isActive: boolean;
  name: string;
  sortOrder: number;
};

type TechnologyProps = SharedPageProps & {
  technologyPage: {
    current: Technology | null;
    technologies: Technology[];
  };
};

const DEVICON_NAMES = [
  'android',
  'angular',
  'apache',
  'aws',
  'azure',
  'bash',
  'bootstrap',
  'csharp',
  'css3',
  'docker',
  'figma',
  'firebase',
  'git',
  'github',
  'go',
  'graphql',
  'html5',
  'java',
  'javascript',
  'jquery',
  'kotlin',
  'kubernetes',
  'laravel',
  'linux',
  'mongodb',
  'mysql',
  'nestjs',
  'nextjs',
  'nginx',
  'nodejs',
  'npm',
  'php',
  'postgresql',
  'python',
  'react',
  'redis',
  'sass',
  'spring',
  'sqlite',
  'storybook',
  'tailwindcss',
  'terraform',
  'typescript',
  'vite',
  'vuejs',
  'webpack',
  'wordpress',
  'yarn',
];

const DEVICON_VARIANTS = ['plain', 'original', 'line'];

const DEVICON_OPTIONS = DEVICON_NAMES.flatMap((name) =>
  DEVICON_VARIANTS.map((variant) => ({
    label: `${name} (${variant})`,
    value: `devicon-${name}-${variant}`,
  })),
);

export default function AdminTechnologyPage() {
  const page = usePage<TechnologyProps>();
  const technologyPage = page.props.technologyPage;
  const current = technologyPage.current;
  const form = useForm({
    icon: current?.icon ?? '',
    name: current?.name ?? '',
  });

  useEffect(() => {
    form.setData({
      icon: current?.icon ?? '',
      name: current?.name ?? '',
    });
  }, [current?.id]);

  return (
    <>
      <Head title="Tech stack" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Tech stack' },
        ]}
        title="Tech stack"
        description="Gestisci tecnologie, icone Devicon e stato di pubblicazione dello stack."
      >
        <div className="admin-secondary-grid">
          <section className="admin-panel">
            <form
              className="admin-form-card"
              onSubmit={(event) => {
                event.preventDefault();

                if (current) {
                  form.patch(`/admin/technology-update/${current.id}`);
                  return;
                }

                form.post('/admin/technology');
              }}
            >
              <div className="admin-panel__header">
                <div>
                  <p className="admin-panel__eyebrow">Editor</p>
                  <h2 className="admin-panel__title">
                    {current ? 'Modifica tecnologia' : 'Nuova tecnologia'}
                  </h2>
                </div>
              </div>

              <label className="admin-filter-field" htmlFor="technology-name">
                <span>Nome tecnologia</span>
                <input
                  id="technology-name"
                  className="admin-input"
                  value={form.data.name}
                  onChange={(event) => form.setData('name', event.target.value)}
                  placeholder="Es. Laravel"
                  maxLength={100}
                  required
                />
              </label>

              <label className="admin-filter-field" htmlFor="technology-icon">
                <span>Classe icona Devicon</span>
                <input
                  id="technology-icon"
                  className="admin-input"
                  list="technology-icons"
                  value={form.data.icon}
                  onChange={(event) => form.setData('icon', event.target.value)}
                  placeholder="devicon-laravel-plain"
                  maxLength={100}
                />
                <datalist id="technology-icons">
                  {DEVICON_OPTIONS.map((option) => (
                    <option key={option.value} value={option.value}>
                      {option.label}
                    </option>
                  ))}
                </datalist>
              </label>

              <div className="admin-icon-preview">
                {form.data.icon ? (
                  <i className={form.data.icon} aria-hidden="true" />
                ) : (
                  <span>Nessuna icona selezionata</span>
                )}
              </div>

              <div className="admin-form-actions">
                <button
                  type="submit"
                  className="admin-form-actions__button"
                  disabled={form.processing}
                >
                  {form.processing
                    ? 'Salvataggio...'
                    : current
                      ? 'Salva modifiche'
                      : 'Aggiungi tecnologia'}
                </button>
                {current ? (
                  <Link
                    href="/admin/technology"
                    className="admin-form-actions__button admin-form-actions__button--ghost"
                  >
                    Annulla
                  </Link>
                ) : null}
              </div>
            </form>
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Stack</p>
                <h2 className="admin-panel__title">
                  {technologyPage.technologies.length} tecnologie configurate
                </h2>
              </div>
            </div>

            <div className="admin-record-list">
              {technologyPage.technologies.map((technology) => (
                <article
                  key={technology.id}
                  className={`admin-record-card${
                    current?.id === technology.id ? ' admin-record-card--active' : ''
                  }${!technology.isActive ? ' admin-record-card--muted' : ''}`}
                >
                  <div className="admin-record-card__header">
                    <div className="admin-record-card__title-row">
                      {technology.icon ? (
                        <i className={technology.icon} aria-hidden="true" />
                      ) : (
                        <span className="admin-icon-fallback">#</span>
                      )}
                      <strong>{technology.name}</strong>
                    </div>
                    <span
                      className={`admin-badge${
                        technology.isActive ? '' : ' admin-badge--neutral'
                      }`}
                    >
                      {technology.isActive ? 'Attivo' : 'Archiviato'}
                    </span>
                  </div>

                  <div className="admin-record-card__meta">
                    <span>ID #{technology.id}</span>
                    <span>Ordine {technology.sortOrder}</span>
                  </div>

                  <div className="admin-inline-actions">
                    <Link
                      href={`/admin/technology-edit/${technology.id}`}
                      className="admin-inline-link"
                    >
                      Modifica
                    </Link>
                    <button
                      type="button"
                      className="admin-inline-button"
                      onClick={() =>
                        router.patch(
                          '/admin/toggle-active',
                          { entity: 'technology', id: technology.id },
                          { preserveScroll: true },
                        )
                      }
                    >
                      {technology.isActive ? 'Archivia' : 'Riattiva'}
                    </button>
                    <button
                      type="button"
                      className="admin-inline-button admin-inline-button--danger"
                      onClick={() => {
                        if (window.confirm(`Eliminare ${technology.name}?`)) {
                          router.delete(`/admin/technology-delete/${technology.id}`);
                        }
                      }}
                    >
                      Elimina
                    </button>
                  </div>
                </article>
              ))}
            </div>
          </section>
        </div>
      </AdminLayout>
    </>
  );
}
