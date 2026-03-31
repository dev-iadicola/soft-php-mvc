import type { TextareaHTMLAttributes } from 'react';

import { cn } from '@/lib/cn';

type AdminTextareaProps = TextareaHTMLAttributes<HTMLTextAreaElement> & {
  invalid?: boolean;
};

export function AdminTextarea({
  className,
  invalid = false,
  ...props
}: AdminTextareaProps) {
  return (
    <textarea
      {...props}
      className={cn('admin-input', 'admin-textarea', className, {
        'admin-input--invalid': invalid,
      })}
    />
  );
}
