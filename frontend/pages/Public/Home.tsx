import { Head, Link, usePage } from '@inertiajs/react';

import { PublicArticleCard } from '@/components/public/public-article-card';
import { PublicProjectCard } from '@/components/public/public-project-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { UiBadge } from '@/components/ui/ui-badge';
import { UiCard } from '@/components/ui/ui-card';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type HomeProps = SharedPageProps & {
  page: {
    articles: Array<{
      createdAt: string | null;
      id: number;
      img: string | null;
      link: string | null;
      overview: string | null;
      slug: string;
      subtitle: string | null;
      tags: Array<{ id: number; name: string; slug: string }>;
      title: string;
    }>;
    certificates: Array<{
      certifiedAt: string | null;
      company: string | null;
      id: number;
      img: string | null;
      title: string;
    }>;
    profiles: Array<{
      avatar: string | null;
      bio: string | null;
      githubUrl: string | null;
      id: number;
      linkedinUrl: string | null;
      name: string;
      tagline: string | null;
      twitterUrl: string | null;
      welcomeMessage: string | null;
    }>;
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
    skills: Array<{
      description: string | null;
      id: number;
      title: string;
    }>;
    technologies: Array<{
      icon: string | null;
      id: number;
      name: string;
    }>;
  };
};

export default function PublicHomePage() {
  const { props } = usePage<HomeProps>();
  const page = props.page;
  const profile = page.profiles[0] ?? null;

  return (
    <>
      <Head title="Home" />

      <GuestLayout
        eyebrow="Portfolio"
        title={profile?.name ? `${profile.name}, sviluppatore web` : 'Portfolio web developer'}
        description={profile?.tagline ?? profile?.welcomeMessage ?? 'Progetti, articoli e stack tecnico in un unico frontend React.'}
        primaryAction={{ href: '/progetti', label: 'Esplora i progetti' }}
        secondaryAction={{ href: '/blog', label: 'Leggi il blog' }}
      >
        <div className="space-y-8">
          {profile ? (
            <UiCard
              eyebrow="Whoami"
              title={profile.name}
              description={profile.welcomeMessage ?? undefined}
            >
              <div className="grid gap-6 md:grid-cols-[auto_minmax(0,1fr)] md:items-start">
                {profile.avatar ? (
                  <img
                    src={profile.avatar}
                    alt={profile.name}
                    className="size-24 rounded-full border border-brand-200 object-cover"
                  />
                ) : null}
                <div className="space-y-4">
                  {profile.bio ? (
                    <div
                      className="prose prose-slate max-w-none text-sm"
                      dangerouslySetInnerHTML={{ __html: profile.bio }}
                    />
                  ) : null}
                  <div className="flex flex-wrap gap-3">
                    {profile.githubUrl ? <a href={profile.githubUrl} target="_blank" rel="noopener noreferrer" className="text-sm font-medium text-brand-700">GitHub</a> : null}
                    {profile.linkedinUrl ? <a href={profile.linkedinUrl} target="_blank" rel="noopener noreferrer" className="text-sm font-medium text-brand-700">LinkedIn</a> : null}
                    {profile.twitterUrl ? <a href={profile.twitterUrl} target="_blank" rel="noopener noreferrer" className="text-sm font-medium text-brand-700">Twitter</a> : null}
                  </div>
                </div>
              </div>
            </UiCard>
          ) : null}

          <section className="space-y-4">
            <PublicSectionHeader
              eyebrow="Projects"
              title="Progetti recenti"
              description="Una selezione dei lavori più rappresentativi già presenti nel portfolio."
            />
            <div className="grid gap-6 lg:grid-cols-2">
              {page.projects.slice(0, 4).map((project) => (
                <PublicProjectCard key={project.id} project={project} />
              ))}
            </div>
          </section>

          <section className="space-y-4">
            <PublicSectionHeader
              eyebrow="Skills"
              title="Competenze"
              description="Competenze operative e tecnologie che uso nei progetti e nelle collaborazioni."
            />
            <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              {page.skills.map((skill) => (
                <UiCard key={skill.id} eyebrow="Skill" title={skill.title}>
                  <p className="text-sm leading-7 text-slate-600">{skill.description}</p>
                </UiCard>
              ))}
            </div>
            <div className="flex flex-wrap gap-2">
              {page.technologies.map((technology) => (
                <UiBadge key={technology.id}>{technology.name}</UiBadge>
              ))}
            </div>
          </section>

          <section className="space-y-4">
            <PublicSectionHeader
              eyebrow="Blog"
              title="Ultimi articoli"
              description="Guide, note tecniche e approfondimenti già disponibili nella sezione blog."
            />
            <div className="grid gap-6 lg:grid-cols-2">
              {page.articles.slice(0, 4).map((article) => (
                <PublicArticleCard key={article.id} article={article} />
              ))}
            </div>
          </section>

          <section className="space-y-4">
            <PublicSectionHeader
              eyebrow="Certificates"
              title="Certificazioni"
              description="Tracce rapide delle certificazioni e percorsi professionali presenti nel portfolio."
            />
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

          <div className="flex flex-wrap gap-3">
            <Link href="/portfolio" className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700">
              Portfolio completo
            </Link>
            <Link href="/tech-stack" className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700">
              Tech stack
            </Link>
          </div>
        </div>
      </GuestLayout>
    </>
  );
}
