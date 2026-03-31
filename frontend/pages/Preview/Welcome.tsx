import { Head, usePage } from '@inertiajs/react';

import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type PreviewPayload = {
  description: string;
  highlights: string[];
  next_step: string;
  title: string;
};

type PreviewPageProps = SharedPageProps & {
  preview: PreviewPayload;
};

export default function WelcomePreviewPage() {
  const page = usePage<PreviewPageProps>();
  const preview = page.props.preview;
  const userEmail = page.props.auth?.user?.email ?? 'guest';

  return (
    <>
      <Head title="React Preview" />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { label: 'React preview' },
        ]}
        eyebrow="Inertia preview"
        title={preview.title}
        description={preview.description}
      >
        <div className="preview-grid">
          <section className="preview-card">
            <span className="preview-card__eyebrow">Current request</span>
            <h2 className="preview-card__title">{page.url}</h2>
            <p className="preview-card__copy">
              Il client React sta ricevendo la pagina Inertia dal backend PHP e
              la monta usando gli asset buildati con Vite.
            </p>
          </section>

          <section className="preview-card">
            <span className="preview-card__eyebrow">Auth snapshot</span>
            <h2 className="preview-card__title">{userEmail}</h2>
            <p className="preview-card__copy">
              Le shared props sono già disponibili nel bootstrap iniziale,
              inclusi auth, flash message e meta base.
            </p>
          </section>
        </div>

        <section className="preview-card preview-card--stacked">
          <span className="preview-card__eyebrow">What is ready</span>
          <ul className="preview-list">
            {preview.highlights.map((item) => (
              <li key={item} className="preview-list__item">
                {item}
              </li>
            ))}
          </ul>
        </section>

        <section className="preview-card preview-card--accent">
          <span className="preview-card__eyebrow">Next step</span>
          <p className="preview-card__copy">{preview.next_step}</p>
        </section>
      </GuestLayout>
    </>
  );
}
