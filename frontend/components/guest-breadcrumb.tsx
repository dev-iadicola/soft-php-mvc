import { cn } from '@/lib/cn';

export type BreadcrumbItem = {
  href?: string;
  label: string;
};

type GuestBreadcrumbProps = {
  items?: BreadcrumbItem[];
};

export function GuestBreadcrumb({ items = [] }: GuestBreadcrumbProps) {
  if (items.length === 0) {
    return null;
  }

  return (
    <nav aria-label="Breadcrumb" className="guest-breadcrumb">
      <ol className="guest-breadcrumb__list">
        {items.map((item, index) => {
          const isLast = index === items.length - 1;

          return (
            <li key={`${item.label}-${index}`} className="guest-breadcrumb__item">
              {item.href && !isLast ? (
                <a className="guest-breadcrumb__link" href={item.href}>
                  {item.label}
                </a>
              ) : (
                <span
                  className={cn('guest-breadcrumb__current', {
                    'guest-breadcrumb__current--muted': !isLast,
                  })}
                >
                  {item.label}
                </span>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
