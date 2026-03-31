import { cn } from '@/lib/cn';

export type AdminNavItem = {
  href: string;
  label: string;
};

export type AdminNavSection = {
  items: AdminNavItem[];
  title: string;
};

type AdminSidebarProps = {
  currentPath: string;
  sections: AdminNavSection[];
};

export function AdminSidebar({ currentPath, sections }: AdminSidebarProps) {
  return (
    <div className="admin-sidebar-shell">
      {sections.map((section) => (
        <section key={section.title} className="admin-sidebar-shell__section">
          <p className="admin-sidebar-shell__label">{section.title}</p>
          <nav className="admin-sidebar-shell__nav" aria-label={section.title}>
            {section.items.map((item) => (
              <a
                key={item.href}
                className={cn('admin-sidebar-shell__link', {
                  'admin-sidebar-shell__link--active': currentPath === item.href,
                })}
                href={item.href}
              >
                {item.label}
              </a>
            ))}
          </nav>
        </section>
      ))}
    </div>
  );
}
