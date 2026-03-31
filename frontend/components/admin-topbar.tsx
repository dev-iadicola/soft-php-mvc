import { Bell02 } from '@untitledui/icons/Bell02';

import { cn } from '@/lib/cn';

type PageAction = {
  href: string;
  label: string;
  variant?: 'ghost' | 'primary';
};

type AdminTopbarProps = {
  notificationCount?: number;
  onOpenSidebar: () => void;
  pageActions?: PageAction[];
  userEmail?: string;
};

export function AdminTopbar({
  notificationCount = 0,
  onOpenSidebar,
  pageActions = [],
  userEmail = 'admin@example.com',
}: AdminTopbarProps) {
  return (
    <div className="admin-topbar">
      <button
        type="button"
        className="admin-topbar__menu-button"
        aria-label="Open admin navigation"
        onClick={onOpenSidebar}
      >
        <span />
        <span />
        <span />
      </button>

      <div className="admin-topbar__actions">
        {pageActions.map((action) => (
          <a
            key={action.href}
            className={cn('admin-topbar__action', {
              'admin-topbar__action--ghost': action.variant !== 'primary',
              'admin-topbar__action--primary': action.variant === 'primary',
            })}
            href={action.href}
          >
            {action.label}
          </a>
        ))}

        <button
          type="button"
          className="admin-topbar__notification"
          aria-label="Notifications"
        >
          <span className="admin-topbar__notification-icon" aria-hidden="true">
            <Bell02 size={18} />
          </span>
          {notificationCount > 0 ? (
            <span className="admin-topbar__notification-badge">
              {notificationCount}
            </span>
          ) : null}
        </button>

        <div className="admin-topbar__user">
          <span className="admin-topbar__user-avatar">
            {userEmail.slice(0, 1).toUpperCase()}
          </span>
          <div>
            <p className="admin-topbar__user-label">Admin user</p>
            <p className="admin-topbar__user-email">{userEmail}</p>
          </div>
        </div>
      </div>
    </div>
  );
}
