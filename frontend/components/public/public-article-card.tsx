import { Link } from '@inertiajs/react';

import { UiBadge } from '@/components/ui/ui-badge';
import { UiCard } from '@/components/ui/ui-card';

type Tag = {
  id: number;
  name: string;
  slug: string;
};

type ArticleCard = {
  createdAt: string | null;
  id: number;
  img: string | null;
  link: string | null;
  overview: string | null;
  slug: string;
  subtitle: string | null;
  tags: Tag[];
  title: string;
};

type PublicArticleCardProps = {
  article: ArticleCard;
};

export function PublicArticleCard({ article }: PublicArticleCardProps) {
  const excerpt = article.overview
    ? article.overview.replace(/<[^>]+>/g, '').slice(0, 180)
    : null;

  return (
    <UiCard
      eyebrow="Article"
      title={article.title}
      description={article.subtitle ?? article.createdAt ?? undefined}
    >
      <div className="space-y-4">
        {article.img ? (
          <Link href={`/blog/${article.slug}`} className="block overflow-hidden rounded-3xl border border-slate-200 bg-slate-50">
            <img
              src={article.img}
              alt={article.title}
              className="h-48 w-full object-cover transition duration-200 hover:scale-[1.02]"
            />
          </Link>
        ) : null}

        {excerpt ? <p className="text-sm leading-7 text-slate-600">{excerpt}...</p> : null}

        <div className="flex flex-wrap gap-2">
          {article.tags.map((tag) => (
            <UiBadge key={tag.id}>#{tag.name}</UiBadge>
          ))}
        </div>

        <div className="flex flex-wrap gap-3">
          <Link
            href={`/blog/${article.slug}`}
            className="inline-flex min-h-11 items-center justify-center rounded-full border border-brand-700 bg-brand-700 px-4 text-sm font-semibold text-white"
          >
            Apri articolo
          </Link>
          {article.link ? (
            <a
              href={article.link}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
            >
              Fonte originale
            </a>
          ) : null}
        </div>
      </div>
    </UiCard>
  );
}
