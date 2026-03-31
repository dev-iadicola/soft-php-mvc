import { router, usePage } from '@inertiajs/react';

import { PublicProjectCard } from '@/components/public/public-project-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { SeoHead } from '@/components/seo-head';
import { UiBadge } from '@/components/ui/ui-badge';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type ProjectsIndexProps = SharedPageProps & {
  page: {
    projects: Array<{
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
    selectedTechnology: string | null;
    technologies: Array<{ icon: string | null; id: number; name: string }>;
  };
};

export default function PublicProjectsIndexPage() {
  const { props } = usePage<ProjectsIndexProps>();
  const page = props.page;

  return (
    <>
      <SeoHead />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { label: 'Progetti' },
        ]}
        eyebrow="Projects"
        title="Progetti"
        description="Filtro per tecnologia e dettaglio dedicato per ogni progetto del portfolio."
      >
        <div className="space-y-6">
          <PublicSectionHeader
            eyebrow="Filters"
            title="Filtra per tecnologia"
            description="Riduci l’elenco dei progetti in base allo stack principale."
          />

          <div className="flex flex-wrap gap-2">
            <button
              type="button"
              onClick={() => router.get('/progetti')}
              className="inline-flex min-h-10 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
            >
              Tutti
            </button>
            {page.technologies.map((technology) => {
              const active = page.selectedTechnology === technology.name;

              return (
                <button
                  key={technology.id}
                  type="button"
                  onClick={() => router.get('/progetti', { technology: technology.name })}
                  className={`inline-flex min-h-10 items-center justify-center rounded-full border px-4 text-sm font-semibold ${
                    active
                      ? 'border-brand-700 bg-brand-700 text-white'
                      : 'border-slate-200 bg-white text-slate-700'
                  }`}
                >
                  {technology.name}
                </button>
              );
            })}
          </div>

          {page.selectedTechnology ? (
            <UiBadge>Filtro attivo: {page.selectedTechnology}</UiBadge>
          ) : null}

          <div className="grid gap-6 lg:grid-cols-2">
            {page.projects.map((project) => (
              <PublicProjectCard key={project.id} project={project} />
            ))}
          </div>
        </div>
      </GuestLayout>
    </>
  );
}
