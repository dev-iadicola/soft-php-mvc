export type AdminBreadcrumbItem = {
  href?: string;
  label: string;
};

type AdminBreadcrumbProps = {
  items?: AdminBreadcrumbItem[];
};

export function AdminBreadcrumb({ items = [] }: AdminBreadcrumbProps) {
  if (items.length === 0) {
    return null;
  }

  return (
    <nav aria-label="Admin breadcrumb" className="admin-breadcrumb">
      <ol className="admin-breadcrumb__list">
        {items.map((item, index) => {
          const isLast = index === items.length - 1;

          return (
            <li key={`${item.label}-${index}`} className="admin-breadcrumb__item">
              {item.href && !isLast ? (
                <a className="admin-breadcrumb__link" href={item.href}>
                  {item.label}
                </a>
              ) : (
                <span className="admin-breadcrumb__current">{item.label}</span>
              )}
            </li>
          );
        })}
      </ol>
    </nav>
  );
}
