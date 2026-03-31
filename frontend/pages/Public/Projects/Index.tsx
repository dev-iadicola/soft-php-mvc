import { router, usePage } from '@inertiajs/react';

import { PublicProjectCard } from '@/components/public/public-project-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { SeoHead } from '@/components/seo-head';
import { UiBadge } from '@/components/ui/ui-badge';
import { UiButton } from '@/components/ui/ui-button';
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
            <UiButton onPress={() => router.get('/progetti')}>
              Tutti
            </UiButton>
            {page.technologies.map((technology) => {
              const active = page.selectedTechnology === technology.name;

              return (
                <UiButton
                  key={technology.id}
                  onPress={() => router.get('/progetti', { technology: technology.name })}
                  tone={active ? 'primary' : 'secondary'}
                >
                  {technology.name}
                </UiButton>
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
