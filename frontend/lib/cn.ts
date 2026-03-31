import { twMerge } from 'tailwind-merge';

type ClassDictionary = Record<string, boolean | null | undefined>;
type ClassArray = ClassValue[];
type ClassValue =
  | ClassArray
  | ClassDictionary
  | number
  | string
  | false
  | null
  | undefined;

function flatten(value: ClassValue): string[] {
  if (!value) {
    return [];
  }

  if (typeof value === 'string' || typeof value === 'number') {
    return [String(value)];
  }

  if (Array.isArray(value)) {
    return value.flatMap((item) => flatten(item));
  }

  return Object.entries(value)
    .filter(([, isEnabled]) => Boolean(isEnabled))
    .map(([className]) => className);
}

export function cn(...values: ClassValue[]): string {
  return twMerge(
    values
      .flatMap((value) => flatten(value))
      .join(' ')
      .trim(),
  );
}
