import { Head, usePage } from '@inertiajs/react';

import type { SharedPageProps } from '@/types/inertia';

export function SeoHead() {
  const { props } = usePage<SharedPageProps>();
  const seo = props.seo ?? {};
  const title = props.meta?.title ?? seo.title ?? 'Iadicola // dev';
  const canonical = seo.canonical ?? props.routing?.canonical ?? undefined;
  const structuredData = Array.isArray(seo.structured_data)
    ? seo.structured_data
    : seo.structured_data
      ? [seo.structured_data]
      : [];

  return (
    <Head title={title ?? undefined}>
      {seo.description ? <meta name="description" content={seo.description} /> : null}
      {seo.robots ? <meta name="robots" content={seo.robots} /> : null}
      {canonical ? <link rel="canonical" href={canonical} /> : null}

      <meta property="og:type" content={seo.type ?? 'website'} />
      {title ? <meta property="og:title" content={title} /> : null}
      {seo.description ? <meta property="og:description" content={seo.description} /> : null}
      {canonical ? <meta property="og:url" content={canonical} /> : null}
      {seo.image ? <meta property="og:image" content={seo.image} /> : null}
      {seo.site_name ? <meta property="og:site_name" content={seo.site_name} /> : null}
      {seo.published_time ? (
        <meta property="article:published_time" content={seo.published_time} />
      ) : null}
      {seo.modified_time ? (
        <meta property="article:modified_time" content={seo.modified_time} />
      ) : null}

      <meta
        name="twitter:card"
        content={seo.twitter_card ?? (seo.image ? 'summary_large_image' : 'summary')}
      />
      {title ? <meta name="twitter:title" content={title} /> : null}
      {seo.description ? <meta name="twitter:description" content={seo.description} /> : null}
      {seo.image ? <meta name="twitter:image" content={seo.image} /> : null}

      {structuredData.map((entry, index) => (
        <script
          key={`structured-data-${index}`}
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify(entry),
          }}
        />
      ))}
    </Head>
  );
}
