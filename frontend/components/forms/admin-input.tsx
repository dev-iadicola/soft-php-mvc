import type { InputHTMLAttributes } from 'react';

import { cn } from '@/lib/cn';

type AdminInputProps = InputHTMLAttributes<HTMLInputElement> & {
  invalid?: boolean;
};

export function AdminInput({ className, invalid = false, ...props }: AdminInputProps) {
  return (
    <input
      {...props}
      className={cn('admin-input', className, {
        'admin-input--invalid': invalid,
      })}
    />
  );
}
