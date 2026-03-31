import type { PropsWithChildren } from 'react';

type UiFieldProps = PropsWithChildren<{
  error?: string | null;
  hint?: string;
  htmlFor?: string;
  label: string;
  required?: boolean;
}>;

export function UiField({
  children,
  error,
  hint,
  htmlFor,
  label,
  required = false,
}: UiFieldProps) {
  return (
    <label className="ui-field" htmlFor={htmlFor}>
      <span className="ui-field__label">
        {label}
        {required ? ' *' : null}
      </span>
      {children}
      {hint ? <span className="ui-field__hint">{hint}</span> : null}
      {error ? <span className="ui-field__error">{error}</span> : null}
    </label>
  );
}
