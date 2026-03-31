import { usePage } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import { PublicProjectCard } from '@/components/public/public-project-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { SeoHead } from '@/components/seo-head';
import { UiBadge } from '@/components/ui/ui-badge';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type ProjectShowProps = SharedPageProps & {
  page: {
    project: {
      createdAt: string | null;
      description: string | null;
      endedAt: string | null;
      gallery: Array<{ path: string }>;
      id: number;
      img: string | null;
      link: string | null;
      overview: string | null;
      partner: { name: string; website: string | null } | null;
      slug: string;
      startedAt: string | null;
      technologies: Array<{ id: number; icon: string | null; name: string }>;
      title: string;
      website: string | null;
    };
    relatedProjects: Array<{
      endedAt: string | null;
      id: number;
      img: string | null;
      link: string | null;
      overview: string | null;
      partnerName: string | null;
      slug: string;
      startedAt: string | null;
      technologies: Array<{ id: number; icon: string | null; name: string }>;
      title: string;
      website: string | null;
    }>;
  };
};

export default function PublicProjectShowPage() {
  const { props } = usePage<ProjectShowProps>();
  const { project, relatedProjects } = props.page;
  const [pageIndex, setPageIndex] = useState(0);

  const descriptionPages = useMemo(() => {
    const raw = project.description ?? '';
    if (raw.length <= 1400) {
      return [raw];
    }

    const chunks: string[] = [];
    for (let index = 0; index < raw.length; index += 1400) {
      chunks.push(raw.slice(index, index + 1400));
    }
    return chunks;
  }, [project.description]);

  return (
    <>
      <SeoHead />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { href: '/progetti', label: 'Progetti' },
          { label: project.title },
        ]}
        eyebrow="Project detail"
        title={project.title}
        description={project.overview?.replace(/<[^>]+>/g, '').slice(0, 160) ?? 'Dettaglio progetto'}
        primaryAction={project.website ? { href: project.website, label: 'Apri live' } : { href: '/progetti', label: 'Torna ai progetti' }}
        secondaryAction={project.link ? { href: project.link, label: 'Source' } : { href: '/portfolio', label: 'Portfolio' }}
      >
        <div className="space-y-8">
          <section className="ui-surface grid gap-6 p-6">
            {project.img ? (
              <img
                src={project.img}
                alt={project.title}
                className="h-72 w-full rounded-3xl border border-slate-200 object-contain bg-slate-50 p-6"
              />
            ) : null}

            <div className="flex flex-wrap gap-2">
              {project.startedAt ? <UiBadge>Inizio: {project.startedAt}</UiBadge> : null}
              {project.endedAt ? <UiBadge tone="muted">Fine: {project.endedAt}</UiBadge> : null}
              {project.partner ? <UiBadge>{project.partner.name}</UiBadge> : null}
              {project.technologies.map((technology) => (
                <UiBadge key={technology.id}>{technology.name}</UiBadge>
              ))}
            </div>

            {project.overview ? (
              <div
                className="prose prose-slate max-w-none"
                dangerouslySetInnerHTML={{ __html: project.overview }}
              />
            ) : null}

            {descriptionPages[pageIndex] ? (
              <div
                className="prose prose-slate max-w-none"
                dangerouslySetInnerHTML={{ __html: descriptionPages[pageIndex] }}
              />
            ) : null}

            {descriptionPages.length > 1 ? (
              <div className="flex flex-wrap items-center justify-between gap-4 rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <span className="text-sm text-slate-600">
                  Pagina {pageIndex + 1} di {descriptionPages.length}
                </span>
                <div className="flex gap-3">
                  <button
                    type="button"
                    onClick={() => setPageIndex((value) => Math.max(0, value - 1))}
                    disabled={pageIndex === 0}
                    className="inline-flex min-h-10 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700 disabled:opacity-40"
                  >
                    Precedente
                  </button>
                  <button
                    type="button"
                    onClick={() =>
                      setPageIndex((value) => Math.min(descriptionPages.length - 1, value + 1))
                    }
                    disabled={pageIndex >= descriptionPages.length - 1}
                    className="inline-flex min-h-10 items-center justify-center rounded-full border border-brand-700 bg-brand-700 px-4 text-sm font-semibold text-white disabled:opacity-40"
                  >
                    Successiva
                  </button>
                </div>
              </div>
            ) : null}

            {project.gallery.length > 0 ? (
              <div className="space-y-4">
                <PublicSectionHeader eyebrow="Gallery" title="Screenshot" />
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                  {project.gallery.map((media) => (
                    <a
                      key={media.path}
                      href={media.path}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="block overflow-hidden rounded-3xl border border-slate-200 bg-slate-50"
                    >
                      <img src={media.path} alt={project.title} className="h-40 w-full object-cover" />
                    </a>
                  ))}
                </div>
              </div>
            ) : null}
          </section>

          {relatedProjects.length > 0 ? (
            <section className="space-y-4">
              <PublicSectionHeader eyebrow="Related" title="Altri progetti" />
              <div className="grid gap-6 lg:grid-cols-2">
                {relatedProjects.slice(0, 4).map((item) => (
                  <PublicProjectCard key={item.id} project={item} />
                ))}
              </div>
            </section>
          ) : null}
        </div>
      </GuestLayout>
    </>
  );
}
