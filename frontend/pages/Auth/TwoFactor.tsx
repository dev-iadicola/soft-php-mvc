import { Head, useForm, usePage } from '@inertiajs/react';

import { GuestLayout } from '@/layouts/guest-layout';
import type { SharedPageProps } from '@/types/inertia';

type TwoFactorPageProps = SharedPageProps & {
  authPage: {
    description: string;
    title: string;
  };
};

export default function TwoFactorPage() {
  const page = usePage<TwoFactorPageProps>();
  const authPage = page.props.authPage;
  const form = useForm({
    code: '',
  });

  return (
    <>
      <Head title="Verifica 2FA" />

      <GuestLayout
        eyebrow="Two-factor auth"
        title={authPage.title}
        description={authPage.description}
        primaryAction={{ href: '/login', label: 'Torna al login' }}
        secondaryAction={{ href: '/', label: 'Sito pubblico' }}
      >
        <div className="auth-shell">
          <section className="auth-card auth-card--compact">
            <div className="auth-card__header">
              <p className="auth-card__eyebrow">TOTP challenge</p>
              <h2 className="auth-card__title">Conferma il codice</h2>
              <p className="auth-card__copy">
                Inserisci il codice a 6 cifre generato dalla tua app di autenticazione.
              </p>
            </div>

            <form
              className="auth-form"
              onSubmit={(event) => {
                event.preventDefault();
                form.post('/two-factor');
              }}
            >
              <label className="auth-form__field" htmlFor="code">
                <span>Codice TOTP</span>
                <input
                  id="code"
                  type="text"
                  inputMode="numeric"
                  pattern="[0-9]{6}"
                  maxLength={6}
                  value={form.data.code}
                  onChange={(event) => form.setData('code', event.target.value)}
                  placeholder="123456"
                  required
                />
              </label>

              <button type="submit" className="auth-form__submit" disabled={form.processing}>
                {form.processing ? 'Verifica...' : 'Conferma accesso'}
              </button>
            </form>
          </section>
        </div>
      </GuestLayout>
    </>
  );
}
