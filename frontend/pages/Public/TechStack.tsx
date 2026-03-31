import { Head, usePage } from '@inertiajs/react';

import { PublicSectionHeader } from '@/components/public/public-section-header';
import { UiCard } from '@/components/ui/ui-card';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type TechStackProps = SharedPageProps & {
  page: {
    technologies: Array<{ icon: string | null; id: number; name: string }>;
  };
};

export default function PublicTechStackPage() {
  const { props } = usePage<TechStackProps>();
  const page = props.page;

  return (
    <>
      <Head title="Tech Stack" />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { label: 'Tech Stack' },
        ]}
        eyebrow="Stack"
        title="Tech Stack"
        description="Le tecnologie attive del portfolio servite da pagina React pubblica."
      >
        <div className="space-y-6">
          <PublicSectionHeader
            eyebrow="Technologies"
            title="Tecnologie utilizzate"
            description="Stack tecnico usato nei progetti, nel blog e nei flussi admin."
          />

          <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            {page.technologies.map((technology) => (
              <UiCard key={technology.id} eyebrow="Tech" title={technology.name}>
                <div className="flex items-center gap-3 text-sm text-slate-600">
                  {technology.icon ? (
                    <i className={`${technology.icon} text-2xl text-brand-700`} aria-hidden="true" />
                  ) : (
                    <span className="inline-block size-3 rounded-full bg-brand-700" />
                  )}
                  <span>Disponibile nello stack attivo del portfolio.</span>
                </div>
              </UiCard>
            ))}
          </div>
        </div>
      </GuestLayout>
    </>
  );
}
