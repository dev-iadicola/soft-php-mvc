import type { SelectHTMLAttributes } from 'react';

import { cn } from '@/lib/cn';

type AdminSelectProps = SelectHTMLAttributes<HTMLSelectElement> & {
  invalid?: boolean;
};

export function AdminSelect({ className, invalid = false, ...props }: AdminSelectProps) {
  return (
    <select
      {...props}
      className={cn('admin-input', 'admin-select', className, {
        'admin-input--invalid': invalid,
      })}
    />
  );
}
