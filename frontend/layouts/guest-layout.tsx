import { usePage } from '@inertiajs/react';
import type { PropsWithChildren } from 'react';

import { FlashStack } from '@/components/flash-stack';
import { GuestBreadcrumb, type BreadcrumbItem } from '@/components/guest-breadcrumb';
import { GuestFooter, type FooterSection } from '@/components/guest-footer';
import { GuestHeader, type NavigationItem } from '@/components/guest-header';
import { useAppName } from '@/hooks/use-app-name';
import type { SharedPageProps } from '@/types/inertia';

type GuestLayoutProps = PropsWithChildren<{
  breadcrumbs?: BreadcrumbItem[];
  description?: string;
  eyebrow?: string;
  footerSections?: FooterSection[];
  navigation?: NavigationItem[];
  primaryAction?: {
    href: string;
    label: string;
  };
  secondaryAction?: {
    href: string;
    label: string;
  };
  title: string;
}>;

const DEFAULT_NAVIGATION: NavigationItem[] = [
  { href: '/', label: 'Home' },
  { href: '/portfolio', label: 'Portfolio' },
  { href: '/progetti', label: 'Progetti' },
  { href: '/blog', label: 'Blog' },
  { href: '/contatti', label: 'Contatti' },
];

const DEFAULT_FOOTER_SECTIONS: FooterSection[] = [
  {
    title: 'Explore',
    links: [
      { href: '/portfolio', label: 'Portfolio' },
      { href: '/progetti', label: 'Case studies' },
      { href: '/blog', label: 'Articoli' },
    ],
  },
  {
    title: 'Guest flows',
    links: [
      { href: '/login', label: 'Login' },
      { href: '/sign-up', label: 'Sign up' },
      { href: '/forgot', label: 'Recupera password' },
    ],
  },
  {
    title: 'System',
    links: [
      { href: '/sitemap.xml', label: 'Sitemap' },
      { href: '/cookie', label: 'Cookie policy' },
      { href: '/laws', label: 'Note legali' },
    ],
  },
];

export function GuestLayout({
  breadcrumbs,
  eyebrow = 'Guest layout',
  description,
  footerSections = DEFAULT_FOOTER_SECTIONS,
  navigation = DEFAULT_NAVIGATION,
  primaryAction = { href: '/contatti', label: 'Contattami' },
  secondaryAction = { href: '/login', label: 'Area admin' },
  title,
  children,
}: GuestLayoutProps) {
  const page = usePage<SharedPageProps>();
  const appName = useAppName();
  const sharedNavigation = page.props.navigation?.main;
  const resolvedNavigation =
    navigation.length > 0
      ? navigation
      : Array.isArray(sharedNavigation) && sharedNavigation.length > 0
        ? sharedNavigation
            .filter(
              (
                item,
              ): item is { external?: boolean; href: string; label: string } =>
                typeof item === 'object' &&
                item !== null &&
                typeof item.href === 'string' &&
                typeof item.label === 'string',
            )
            .map((item) => ({
              href: item.href,
              label: item.label,
            }))
        : DEFAULT_NAVIGATION;

  return (
    <div className="guest-shell">
      <GuestHeader
        currentPath={page.url}
        navigation={resolvedNavigation}
        primaryAction={primaryAction}
        secondaryAction={secondaryAction}
      />

      <section className="guest-shell__hero">
        <div className="guest-shell__meta">
          <GuestBreadcrumb items={breadcrumbs} />
          <p className="guest-shell__eyebrow">{eyebrow}</p>
          <h1 className="guest-shell__title">{title}</h1>
          {description ? (
            <p className="guest-shell__description">{description}</p>
          ) : null}
        </div>

        <div className="guest-shell__highlight">
          <span className="guest-shell__highlight-badge">Shared shell</span>
          <p className="guest-shell__highlight-copy">
            {appName} raccoglie portfolio, articoli e accesso area riservata in
            un&apos;esperienza coerente, con navigazione e call to action unificate
            su desktop e mobile.
          </p>
        </div>
      </section>

      <FlashStack />

      <section className="guest-shell__surface">{children}</section>

      <GuestFooter appName={appName} sections={footerSections} />
    </div>
  );
}
