import type { InputHTMLAttributes } from 'react';

import { cn } from '@/lib/cn';

type UiInputProps = InputHTMLAttributes<HTMLInputElement>;

export function UiInput({ className, ...props }: UiInputProps) {
  return <input {...props} className={cn('ui-input', className)} />;
}
