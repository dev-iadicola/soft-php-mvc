import { Link } from '@inertiajs/react';

import { UiBadge } from '@/components/ui/ui-badge';
import { UiCard } from '@/components/ui/ui-card';

type Technology = {
  id: number;
  icon: string | null;
  name: string;
};

type ProjectCard = {
  endedAt: string | null;
  id: number;
  img: string | null;
  link: string | null;
  overview: string | null;
  partnerName: string | null;
  slug: string;
  startedAt: string | null;
  technologies: Technology[];
  title: string;
  website: string | null;
};

type PublicProjectCardProps = {
  project: ProjectCard;
};

export function PublicProjectCard({ project }: PublicProjectCardProps) {
  return (
    <UiCard
      eyebrow="Project"
      title={project.title}
      description={project.partnerName ?? project.technologies.map((item) => item.name).join(' · ')}
    >
      <div className="space-y-4">
        {project.img ? (
          <Link href={`/progetti/${project.slug}`} className="block overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
            <img
              src={project.img}
              alt={project.title}
              className="h-52 w-full object-contain p-4 transition duration-200 hover:scale-[1.02]"
            />
          </Link>
        ) : null}

        {project.overview ? (
          <div
            className="text-sm leading-7 text-slate-600"
            dangerouslySetInnerHTML={{ __html: project.overview }}
          />
        ) : null}

        <div className="flex flex-wrap gap-2">
          {project.technologies.map((technology) => (
            <UiBadge key={technology.id}>{technology.name}</UiBadge>
          ))}
          {project.startedAt || project.endedAt ? (
            <UiBadge tone="muted">
              {[project.startedAt, project.endedAt].filter(Boolean).join(' → ')}
            </UiBadge>
          ) : null}
        </div>

        <div className="flex flex-wrap gap-3">
          <Link
            href={`/progetti/${project.slug}`}
            className="inline-flex min-h-11 items-center justify-center rounded-full border border-brand-700 bg-brand-700 px-4 text-sm font-semibold text-white"
          >
            Apri progetto
          </Link>
          {project.link ? (
            <a
              href={project.link}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
            >
              Source
            </a>
          ) : null}
          {project.website ? (
            <a
              href={project.website}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
            >
              Live
            </a>
          ) : null}
        </div>
      </div>
    </UiCard>
  );
}
