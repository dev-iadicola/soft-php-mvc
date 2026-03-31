type HeaderMap = Record<string, string>;

const META_TOKEN_HEADERS: Record<string, string> = {
  'csrf-token': 'X-CSRF-TOKEN',
};

function readMetaToken(name: string): string | null {
  return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content')?.trim() ?? null;
}

export function buildInertiaSecurityHeaders(
  initialHeaders: Record<string, string> = {},
  fallbackCsrfToken?: string | null,
): HeaderMap {
  const headers: HeaderMap = {
    'X-Requested-With': 'XMLHttpRequest',
    ...initialHeaders,
  };

  for (const [metaName, headerName] of Object.entries(META_TOKEN_HEADERS)) {
    const token = readMetaToken(metaName) ?? (metaName === 'csrf-token' ? fallbackCsrfToken ?? null : null);

    if (token && token !== '') {
      headers[headerName] = token;
    }
  }

  return headers;
}
