import type { PropsWithChildren, ReactNode } from 'react';

import { cn } from '@/lib/cn';

type AdminFieldProps = PropsWithChildren<{
  error?: string | null;
  hint?: string;
  id: string;
  label: string;
  required?: boolean;
  toolbar?: ReactNode;
}>;

export function AdminField({
  children,
  error,
  hint,
  id,
  label,
  required = false,
  toolbar,
}: AdminFieldProps) {
  return (
    <label className="admin-form-field" htmlFor={id}>
      <span className="admin-form-field__header">
        <span className="admin-form-field__label">
          {label}
          {required ? <span className="admin-form-field__required">Required</span> : null}
        </span>
        {toolbar ? <span className="admin-form-field__toolbar">{toolbar}</span> : null}
      </span>

      {children}

      {error ? (
        <span className="admin-form-field__error" role="alert">
          {error}
        </span>
      ) : hint ? (
        <span className="admin-form-field__hint">{hint}</span>
      ) : null}
    </label>
  );
}

type FieldControlProps = PropsWithChildren<{
  invalid?: boolean;
}>;

export function FieldControl({ children, invalid = false }: FieldControlProps) {
  return (
    <span
      className={cn('admin-form-field__control', {
        'admin-form-field__control--invalid': invalid,
      })}
    >
      {children}
    </span>
  );
}
