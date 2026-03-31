import { Head, useForm, usePage } from '@inertiajs/react';

import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type SignUpPageProps = SharedPageProps & {
  authPage: {
    description: string;
    title: string;
  };
};

export default function SignUpPage() {
  const page = usePage<SignUpPageProps>();
  const authPage = page.props.authPage;
  const form = useForm({
    confirmed: '',
    email: '',
    password: '',
  });

  return (
    <>
      <Head title="Sign up iniziale" />

      <GuestLayout
        eyebrow="First account"
        title={authPage.title}
        description={authPage.description}
        primaryAction={{ href: '/login', label: 'Hai già un account?' }}
        secondaryAction={{ href: '/', label: 'Torna al sito' }}
      >
        <div className="auth-shell">
          <section className="auth-card">
            <div className="auth-card__header">
              <p className="auth-card__eyebrow">Bootstrap account</p>
              <h2 className="auth-card__title">Configura il primo admin</h2>
              <p className="auth-card__copy">
                Questo form rimane disponibile solo quando il progetto non ha ancora
                utenti registrati.
              </p>
            </div>

            <form
              className="auth-form"
              onSubmit={(event) => {
                event.preventDefault();
                form.post('/sign-up');
              }}
            >
              <label className="auth-form__field" htmlFor="sign-up-email">
                <span>Email</span>
                <input
                  id="sign-up-email"
                  type="email"
                  value={form.data.email}
                  onChange={(event) => form.setData('email', event.target.value)}
                  placeholder="admin@example.com"
                  autoComplete="email"
                  required
                />
              </label>

              <label className="auth-form__field" htmlFor="sign-up-password">
                <span>Password</span>
                <input
                  id="sign-up-password"
                  type="password"
                  value={form.data.password}
                  onChange={(event) => form.setData('password', event.target.value)}
                  placeholder="Minimo 8 caratteri"
                  autoComplete="new-password"
                  required
                />
              </label>

              <label className="auth-form__field" htmlFor="sign-up-confirmed">
                <span>Ripeti password</span>
                <input
                  id="sign-up-confirmed"
                  type="password"
                  value={form.data.confirmed}
                  onChange={(event) => form.setData('confirmed', event.target.value)}
                  placeholder="Conferma password"
                  autoComplete="new-password"
                  required
                />
              </label>

              <button type="submit" className="auth-form__submit" disabled={form.processing}>
                {form.processing ? 'Creazione account...' : 'Crea account admin'}
              </button>
            </form>
          </section>
        </div>
      </GuestLayout>
    </>
  );
}
