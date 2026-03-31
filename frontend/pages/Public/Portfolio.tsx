import { Head, usePage } from '@inertiajs/react';

import { PublicProjectCard } from '@/components/public/public-project-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { UiBadge } from '@/components/ui/ui-badge';
import { UiCard } from '@/components/ui/ui-card';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type PortfolioProps = SharedPageProps & {
  page: {
    certificates: Array<{ certifiedAt: string | null; company: string | null; id: number; img: string | null; title: string }>;
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
    technologies: Array<{ icon: string | null; id: number; name: string }>;
  };
};

export default function PublicPortfolioPage() {
  const { props } = usePage<PortfolioProps>();
  const page = props.page;

  return (
    <>
      <Head title="Portfolio" />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { label: 'Portfolio' },
        ]}
        eyebrow="Portfolio"
        title="Portfolio completo"
        description="Progetti, certificazioni e stack tecnologico già presenti nel portfolio, ora serviti da pagine React pubbliche."
      >
        <div className="space-y-8">
          <section className="space-y-4">
            <PublicSectionHeader eyebrow="Projects" title="Progetti" />
            <div className="grid gap-6 lg:grid-cols-2">
              {page.projects.map((project) => (
                <PublicProjectCard key={project.id} project={project} />
              ))}
            </div>
          </section>

          <section className="space-y-4">
            <PublicSectionHeader eyebrow="Certificates" title="Certificazioni" />
            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              {page.certificates.map((certificate) => (
                <UiCard
                  key={certificate.id}
                  eyebrow="Certificate"
                  title={certificate.title}
                  description={certificate.company ?? certificate.certifiedAt ?? undefined}
                >
                  {certificate.img ? (
                    <img
                      src={certificate.img}
                      alt={certificate.title}
                      className="h-36 w-full rounded-2xl border border-slate-200 object-contain bg-slate-50 p-4"
                    />
                  ) : null}
                </UiCard>
              ))}
            </div>
          </section>

          <section className="space-y-4">
            <PublicSectionHeader eyebrow="Stack" title="Tecnologie" />
            <div className="flex flex-wrap gap-2">
              {page.technologies.map((technology) => (
                <UiBadge key={technology.id}>{technology.name}</UiBadge>
              ))}
            </div>
          </section>
        </div>
      </GuestLayout>
    </>
  );
}
