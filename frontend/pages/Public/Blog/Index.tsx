import { router, usePage } from '@inertiajs/react';
import { useState } from 'react';

import { PublicArticleCard } from '@/components/public/public-article-card';
import { PublicSectionHeader } from '@/components/public/public-section-header';
import { SeoHead } from '@/components/seo-head';
import { UiButton } from '@/components/ui/ui-button';
import { UiEmptyState } from '@/components/ui/ui-empty-state';
import { UiInput } from '@/components/ui/ui-input';
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
      <SeoHead />

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
            <UiInput
              value={search}
              onChange={(event) => setSearch(event.target.value)}
              onKeyDown={(event) => {
                if (event.key === 'Enter') {
                  event.preventDefault();
                  submitSearch();
                }
              }}
              className="min-w-[18rem] flex-1 rounded-full"
              placeholder="Cerca per titolo..."
            />
            <UiButton onPress={submitSearch} tone="primary">
              Cerca
            </UiButton>
            {(page.filters.search || page.filters.tag) ? (
              <UiButton onPress={() => router.get('/blog')}>
                Reset
              </UiButton>
            ) : null}
          </div>

          <div className="flex flex-wrap gap-2">
            {page.tags.map((tag) => {
              const active = page.filters.tag === tag.slug;
              return (
                <UiButton
                  key={tag.id}
                  onPress={() =>
                    router.get('/blog', {
                      ...(page.filters.search ? { search: page.filters.search } : {}),
                      tag: tag.slug,
                    })
                  }
                  tone={active ? 'primary' : 'secondary'}
                  className={
                    active
                      ? undefined
                      : 'hover:border-brand-200 hover:text-brand-700'
                  }
                >
                  #{tag.name}
                </UiButton>
              );
            })}
          </div>

          {page.pagination.items.length === 0 ? (
            <UiEmptyState
              title="Nessun articolo trovato"
              description="Prova a cambiare ricerca o a rimuovere i filtri attivi."
            />
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
                <UiButton
                  key={pageNumber}
                  onPress={() =>
                    router.get('/blog', {
                      ...(page.filters.search ? { search: page.filters.search } : {}),
                      ...(page.filters.tag ? { tag: page.filters.tag } : {}),
                      page: pageNumber,
                    })
                  }
                  tone={pageNumber === page.pagination.currentPage ? 'primary' : 'secondary'}
                  className="size-10 px-0"
                >
                  {pageNumber}
                </UiButton>
              ))}
            </div>
          ) : null}
        </div>
      </GuestLayout>
    </>
  );
}
