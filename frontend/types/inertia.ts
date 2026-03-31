export type FlashBag = {
  error?: string | null;
  success?: string | null;
  warning?: string | null;
};

export type AuthUser = {
  email?: string | null;
  id?: number | string | null;
};

export type SharedPageProps = {
  app?: {
    csrf_token?: string | null;
    name?: string | null;
    url?: string | null;
  };
  auth?: {
    user?: AuthUser | null;
  };
  flash?: FlashBag;
  meta?: {
    title?: string | null;
  };
  navigation?: {
    main?: Array<{
      external?: boolean;
      href: string;
      label: string;
    }>;
  };
  routing?: {
    canonical?: string | null;
    current?: string | null;
  };
  seo?: {
    canonical?: string | null;
    description?: string | null;
    image?: string | null;
    title?: string | null;
  };
  site?: {
    base_url?: string | null;
    maintenance_page?: string | null;
  };
} & Record<string, unknown>;
