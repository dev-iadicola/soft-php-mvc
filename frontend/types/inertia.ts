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
  };
  auth?: {
    user?: AuthUser | null;
  };
  flash?: FlashBag;
  meta?: {
    title?: string | null;
  };
} & Record<string, unknown>;
