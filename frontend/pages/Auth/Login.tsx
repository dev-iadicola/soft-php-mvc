import { Head, useForm, usePage } from '@inertiajs/react';

import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type AuthPageProps = SharedPageProps & {
  authPage: {
    description: string;
    links?: Array<{ href: string; label: string }>;
    title: string;
  };
};

export default function LoginPage() {
  const page = usePage<AuthPageProps>();
  const authPage = page.props.authPage;
  const form = useForm({
    email: '',
    password: '',
  });

  return (
    <>
      <Head title="Login" />

      <GuestLayout
        eyebrow="Admin auth"
        title={authPage.title}
        description={authPage.description}
        primaryAction={{ href: '/sign-up', label: 'Primo accesso' }}
        secondaryAction={{ href: '/', label: 'Torna al sito' }}
      >
        <div className="auth-shell">
          <section className="auth-card">
            <div className="auth-card__header">
              <p className="auth-card__eyebrow">Secure access</p>
              <h2 className="auth-card__title">Entra nel pannello</h2>
              <p className="auth-card__copy">
                Usa email e password per accedere. Se il tuo account ha la 2FA attiva,
                verrai reindirizzato alla verifica TOTP.
              </p>
            </div>

            <form
              className="auth-form"
              onSubmit={(event) => {
                event.preventDefault();
                form.post('/login');
              }}
            >
              <label className="auth-form__field" htmlFor="email">
                <span>Email</span>
                <input
                  id="email"
                  type="email"
                  value={form.data.email}
                  onChange={(event) => form.setData('email', event.target.value)}
                  placeholder="admin@example.com"
                  autoComplete="email"
                  required
                />
              </label>

              <label className="auth-form__field" htmlFor="password">
                <span>Password</span>
                <input
                  id="password"
                  type="password"
                  value={form.data.password}
                  onChange={(event) => form.setData('password', event.target.value)}
                  placeholder="Inserisci la tua password"
                  autoComplete="current-password"
                  required
                />
              </label>

              <div className="auth-form__links">
                {authPage.links?.map((link) => (
                  <a key={link.href} href={link.href}>
                    {link.label}
                  </a>
                ))}
              </div>

              <button type="submit" className="auth-form__submit" disabled={form.processing}>
                {form.processing ? 'Accesso in corso...' : 'Accedi'}
              </button>
            </form>
          </section>
        </div>
      </GuestLayout>
    </>
  );
}
