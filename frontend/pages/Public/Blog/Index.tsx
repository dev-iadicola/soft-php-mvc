import { router, Head, usePage } from '@inertiajs/react';
import { useState } from 'react';

import { PublicArticleCard } from '@/components/public/public-article-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type BlogIndexProps = SharedPageProps & {
  page: {
    filters: {
      search: string;
      tag: string;
    };
    pagination: {
      currentPage: number;
      hasNext: boolean;
      hasPages: boolean;
      hasPrevious: boolean;
      items: Array<{
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
      nextPage: number;
      pageRange: number[];
      previousPage: number;
      totalItems: number;
      totalPages: number;
    };
    tags: Array<{ id: number; name: string; slug: string }>;
  };
};

export default function PublicBlogIndexPage() {
  const { props } = usePage<BlogIndexProps>();
  const page = props.page;
  const [search, setSearch] = useState(page.filters.search);

  const submitSearch = () => {
    router.get('/blog', {
      ...(search ? { search } : {}),
      ...(page.filters.tag ? { tag: page.filters.tag } : {}),
    });
  };

  return (
    <>
      <Head title="Blog" />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { label: 'Blog' },
        ]}
        eyebrow="Blog"
        title="Articoli"
        description="Guide, note tecniche e contenuti editoriali serviti ora tramite pagine React pubbliche."
      >
        <div className="space-y-6">
          <PublicSectionHeader
            eyebrow="Search"
            title="Cerca e filtra"
            description="Combina ricerca testuale e tag per trovare più velocemente gli articoli."
          />

          <div className="flex flex-wrap gap-3">
            <input
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter') {
                  event.preventDefault();
                  submitSearch();
                }
              }}
              className="min-h-11 min-w-[18rem] flex-1 rounded-full border border-slate-200 bg-white px-4 text-sm text-slate-800"
              placeholder="Cerca per titolo..."
            />
            <button
              type="button"
              onClick={submitSearch}
              className="inline-flex min-h-11 items-center justify-center rounded-full border border-brand-700 bg-brand-700 px-4 text-sm font-semibold text-white"
            >
              Cerca
            </button>
            {(page.filters.search || page.filters.tag) ? (
              <button
                type="button"
                onClick={() => router.get('/blog')}
                className="inline-flex min-h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-4 text-sm font-semibold text-slate-700"
              >
                Reset
              </button>
            ) : null}
          </div>

          <div className="flex flex-wrap gap-2">
            {page.tags.map((tag) => {
              const active = page.filters.tag === tag.slug;
              return (
                <button
                  key={tag.id}
                  type="button"
                  onClick={() =>
                    router.get('/blog', {
                      ...(page.filters.search ? { search: page.filters.search } : {}),
                      tag: tag.slug,
                    })
                  }
                  className={`inline-flex min-h-10 items-center justify-center rounded-full border px-4 text-sm font-semibold ${
                    active
                      ? 'border-brand-700 bg-brand-700 text-white'
                      : 'border-slate-200 bg-white text-slate-700'
                  }`}
                >
                  #{tag.name}
                </button>
              );
            })}
          </div>

          {page.pagination.items.length === 0 ? (
            <div className="ui-surface p-8 text-center text-sm text-slate-600">
              Nessun articolo trovato.
            </div>
          ) : (
            <div className="grid gap-6 lg:grid-cols-2">
              {page.pagination.items.map((article) => (
                <PublicArticleCard key={article.id} article={article} />
              ))}
            </div>
          )}

          {page.pagination.hasPages ? (
            <div className="flex flex-wrap items-center justify-center gap-2">
              {page.pagination.pageRange.map((pageNumber) => (
                <button
                  key={pageNumber}
                  type="button"
                  onClick={() =>
                    router.get('/blog', {
                      ...(page.filters.search ? { search: page.filters.search } : {}),
                      ...(page.filters.tag ? { tag: page.filters.tag } : {}),
                      page: pageNumber,
                    })
                  }
                  className={`inline-flex size-10 items-center justify-center rounded-full border text-sm font-semibold ${
                    pageNumber === page.pagination.currentPage
                      ? 'border-brand-700 bg-brand-700 text-white'
                      : 'border-slate-200 bg-white text-slate-700'
                  }`}
                >
                  {pageNumber}
                </button>
              ))}
            </div>
          ) : null}
        </div>
      </GuestLayout>
    </>
  );
}
