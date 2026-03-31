import { AdminLayout } from '@/layouts/admin-layout';
import { GuestLayout } from '@/layouts/guest-layout';
import { useAppName } from '@/hooks/use-app-name';

type FallbackPageProps = {
  componentName?: string;
};

export default function FallbackPage({
  componentName = 'Unknown',
}: FallbackPageProps) {
  const appName = useAppName();
  const isAdminComponent = componentName.startsWith('Admin/');

  const content = (
    <div className="placeholder-card">
      <span className="placeholder-card__badge">Bootstrap OK</span>
      <h2 className="placeholder-card__title">
        Nessuna pagina React registrata per <code>{componentName}</code>
      </h2>
      <p className="placeholder-card__copy">
        Questo fallback è intenzionale: il branch corrente prepara struttura,
        tipizzazione e bootstrap. La prima pagina Inertia verrà introdotta nel
        branch successivo.
      </p>
    </div>
  );

  if (isAdminComponent) {
    return (
      <AdminLayout
        breadcrumbs={[
          { href: '/admin/dashboard', label: 'Admin' },
          { label: 'Fallback' },
        ]}
        title={`${appName} admin shell pronta`}
        description="Il bootstrap React sa già distinguere le pagine admin dalle guest e assegnare il layout corretto."
      >
        {content}
      </AdminLayout>
    );
  }

  return (
    <GuestLayout
      eyebrow="Bootstrap React"
      title={`${appName} frontend pronto`}
      description="La toolchain React è attiva e il resolver Inertia è pronto a ricevere le prime pagine migrate."
    >
      {content}
    </GuestLayout>
  );
}
