import type { PropsWithChildren, ReactNode } from 'react';

type AdminFormSectionProps = PropsWithChildren<{
  description?: string;
  eyebrow?: string;
  title: string;
  toolbar?: ReactNode;
}>;

export function AdminFormSection({
  children,
  description,
  eyebrow,
  title,
  toolbar,
}: AdminFormSectionProps) {
  return (
    <section className="admin-form-section">
      <div className="admin-form-section__header">
        <div>
          {eyebrow ? <p className="admin-form-section__eyebrow">{eyebrow}</p> : null}
          <h2 className="admin-form-section__title">{title}</h2>
          {description ? (
            <p className="admin-form-section__description">{description}</p>
          ) : null}
        </div>
        {toolbar ? <div className="admin-form-section__toolbar">{toolbar}</div> : null}
      </div>

      <div className="admin-form-section__body">{children}</div>
    </section>
  );
}
