import { Head, useForm, usePage } from '@inertiajs/react';

import { AdminLayout } from '@/layouts/admin-layout';
import { UiButton } from '@/components/ui/ui-button';
import { UiField } from '@/components/ui/ui-field';
import { UiInput } from '@/components/ui/ui-input';
import type { SharedPageProps } from '@/types/inertia';

type SecurityProps = SharedPageProps & {
  security: {
    provisioningUri?: string | null;
    setupSecret?: string | null;
    user: {
      email: string;
      twoFactorEnabled: boolean;
    };
  };
};

export default function AdminSecurityPage() {
  const page = usePage<SecurityProps>();
  const security = page.props.security;
  const enableForm = useForm({ code: '' });

  return (
    <>
      <Head title="Sicurezza account" />

      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Sicurezza account' },
        ]}
        title="Sicurezza account"
        description="Gestisci 2FA, setup iniziale e protezione del tuo accesso admin."
      >
        <div className="admin-dashboard-grid">
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Account</p>
                <h2 className="admin-panel__title">{security.user.email}</h2>
              </div>
            </div>

            <div className="admin-security-status">
              <span className="admin-security-status__badge">
                {security.user.twoFactorEnabled ? '2FA attiva' : '2FA non attiva'}
              </span>
              <p className="admin-panel__description">
                {security.user.twoFactorEnabled
                  ? 'Il login richiederà password e codice TOTP.'
                  : 'Configura la 2FA per richiedere un codice TOTP dopo il login.'}
              </p>
            </div>
          </section>

          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Sessions</p>
                <h2 className="admin-panel__title">Accessi collegati</h2>
              </div>
            </div>

            <a href="/admin/sessions" className="admin-inline-link">
              Vai alla gestione sessioni attive
            </a>
          </section>
        </div>

        {security.user.twoFactorEnabled ? (
          <section className="admin-panel">
            <div className="admin-panel__header">
              <div>
                <p className="admin-panel__eyebrow">Two-factor</p>
                <h2 className="admin-panel__title">2FA attualmente attiva</h2>
              </div>
            </div>

            <form
              onSubmit={(event) => {
                event.preventDefault();
                enableForm.post('/admin/security/two-factor/disable');
              }}
            >
              <UiButton type="submit" tone="secondary">
                Disattiva 2FA
              </UiButton>
            </form>
          </section>
        ) : (
          <div className="admin-dashboard-grid">
            <section className="admin-panel">
              <div className="admin-panel__header">
                <div>
                  <p className="admin-panel__eyebrow">Setup</p>
                  <h2 className="admin-panel__title">Secret manuale</h2>
                </div>
              </div>

              <code className="admin-code-block">{security.setupSecret ?? 'N/D'}</code>
              <p className="admin-panel__description">
                QR code dedicato separato in task specifico. Intanto la configurazione
                manuale resta disponibile e completa.
              </p>
              <p className="admin-panel__description">{security.provisioningUri}</p>
            </section>

            <section className="admin-panel">
              <div className="admin-panel__header">
                <div>
                  <p className="admin-panel__eyebrow">Activation</p>
                  <h2 className="admin-panel__title">Conferma codice TOTP</h2>
                </div>
              </div>

              <form
                className="auth-form"
                onSubmit={(event) => {
                  event.preventDefault();
                  enableForm.post('/admin/security/two-factor/enable');
                }}
              >
                <UiField
                  htmlFor="security-code"
                  label="Codice TOTP"
                  hint="Inserisci il codice a 6 cifre generato dalla tua app di autenticazione."
                >
                  <UiInput
                    id="security-code"
                    type="text"
                    inputMode="numeric"
                    pattern="[0-9]{6}"
                    maxLength={6}
                    value={enableForm.data.code}
                    onChange={(event) => enableForm.setData('code', event.target.value)}
                    placeholder="123456"
                    required
                  />
                </UiField>

                <UiButton type="submit" tone="primary" isDisabled={enableForm.processing}>
                  {enableForm.processing ? 'Attivazione...' : 'Abilita 2FA'}
                </UiButton>
              </form>
            </section>
          </div>
        )}
      </AdminLayout>
    </>
  );
}
