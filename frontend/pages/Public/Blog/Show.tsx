import { usePage } from '@inertiajs/react';

import { PublicArticleCard } from '@/components/public/public-article-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { SeoHead } from '@/components/seo-head';
import { UiBadge } from '@/components/ui/ui-badge';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type BlogShowProps = SharedPageProps & {
  page: {
    article: {
      createdAt: string | null;
      id: number;
      img: string | null;
      link: string | null;
      overview: string | null;
      slug: string;
      subtitle: string | null;
      tags: Array<{ id: number; name: string; slug: string }>;
      title: string;
    };
    relatedArticles: Array<{
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
  };
};

export default function PublicBlogShowPage() {
  const { props } = usePage<BlogShowProps>();
  const { article, relatedArticles } = props.page;

  return (
    <>
      <SeoHead />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { href: '/blog', label: 'Blog' },
          { label: article.title },
        ]}
        eyebrow="Article detail"
        title={article.title}
        description={article.subtitle ?? article.createdAt ?? 'Dettaglio articolo'}
      >
        <div className="space-y-8">
          <section className="ui-surface grid gap-6 p-6">
            {article.img ? (
              <img
                src={article.img}
                alt={article.title}
                className="h-80 w-full rounded-3xl border border-slate-200 object-cover"
              />
            ) : null}

            <div className="flex flex-wrap gap-2">
              {article.createdAt ? <UiBadge tone="muted">{article.createdAt}</UiBadge> : null}
              {article.tags.map((tag) => (
                <UiBadge key={tag.id}>#{tag.name}</UiBadge>
              ))}
            </div>

            {article.overview ? (
              <div
                className="prose prose-slate max-w-none"
                dangerouslySetInnerHTML={{ __html: article.overview }}
              />
            ) : null}

            {article.link ? (
              <div>
                <a
                  href={article.link}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
                >
                  Fonte originale
                </a>
              </div>
            ) : null}
          </section>

          {relatedArticles.length > 0 ? (
            <section className="space-y-4">
              <PublicSectionHeader eyebrow="Related" title="Articoli correlati" />
              <div className="grid gap-6 lg:grid-cols-2">
                {relatedArticles.map((item) => (
                  <PublicArticleCard key={item.id} article={item} />
                ))}
              </div>
            </section>
          ) : null}
        </div>
      </GuestLayout>
    </>
  );
}
