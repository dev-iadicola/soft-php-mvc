import type { ReactNode } from 'react';
import {
  Button as AriaButton,
  composeRenderProps,
  type ButtonProps as AriaButtonProps,
} from 'react-aria-components';

import { cn } from '@/lib/cn';

type UiButtonProps = Omit<AriaButtonProps, 'children'> & {
  children: ReactNode;
  leadingIcon?: ReactNode;
  trailingIcon?: ReactNode;
  tone?: 'ghost' | 'primary' | 'secondary';
};

export function UiButton({
  children,
  className,
  leadingIcon,
  tone = 'secondary',
  trailingIcon,
  ...props
}: UiButtonProps) {
  return (
    <AriaButton
      {...props}
      className={composeRenderProps(className, (resolvedClassName) =>
        cn(
          'inline-flex min-h-11 items-center justify-center gap-2 rounded-full border px-4 text-sm font-semibold transition',
          'focus:outline-none focus:ring-4 focus:ring-brand-100',
          'disabled:cursor-not-allowed disabled:opacity-50',
          tone === 'primary' &&
            'border-brand-700 bg-brand-700 text-white hover:border-brand-600 hover:bg-brand-600',
          tone === 'secondary' &&
            'border-slate-200 bg-white text-slate-700 hover:border-brand-200 hover:text-brand-700',
          tone === 'ghost' &&
            'border-transparent bg-transparent text-slate-600 hover:bg-slate-100 hover:text-slate-900',
          resolvedClassName,
        ),
      )}
    >
      <>
        {leadingIcon ? <span className="size-4">{leadingIcon}</span> : null}
        <span>{children}</span>
        {trailingIcon ? <span className="size-4">{trailingIcon}</span> : null}
      </>
    </AriaButton>
  );
}
