import type { PropsWithChildren, ReactNode } from 'react';

type AdminFormShellProps = PropsWithChildren<{
  aside: ReactNode;
  footer?: ReactNode;
}>;

export function AdminFormShell({ aside, children, footer }: AdminFormShellProps) {
  return (
    <div className="admin-form-shell">
      <div className="admin-form-shell__main">{children}</div>
      <aside className="admin-form-shell__aside">{aside}</aside>
      {footer ? <div className="admin-form-shell__footer">{footer}</div> : null}
    </div>
  );
}
