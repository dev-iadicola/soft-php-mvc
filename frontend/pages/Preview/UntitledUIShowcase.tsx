import { Head } from '@inertiajs/react';
import { ArrowRight } from '@untitledui/icons/ArrowRight';
import { Bell02 } from '@untitledui/icons/Bell02';
import { LayersThree01 } from '@untitledui/icons/LayersThree01';
import { Stars02 } from '@untitledui/icons/Stars02';

import { UiBadge } from '@/components/ui/ui-badge';
import { UiButton } from '@/components/ui/ui-button';
import { UiCard } from '@/components/ui/ui-card';
import { GuestLayout } from '@/layouts/guest-layout';

export default function UntitledUIShowcasePage() {
  return (
    <>
      <Head title="Untitled UI Preview" />

      <GuestLayout
        breadcrumbs={[
          { href: '/', label: 'Home' },
          { href: '/react-preview', label: 'React preview' },
          { label: 'Untitled UI' },
        ]}
        eyebrow="Design system preview"
        title="Untitled UI integration spike"
        description="Prima base tecnica per Tailwind, React Aria e componenti source-owned compatibili con il linguaggio Untitled UI."
        primaryAction={{ href: '/react-preview/admin', label: 'Apri admin preview' }}
        secondaryAction={{ href: '/react-preview', label: 'Torna alla preview base' }}
      >
        <div className="grid gap-6 md:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.9fr)]">
          <UiCard
            eyebrow="Foundation"
            title="Componenti React source-owned"
            description="La libreria esterna resta un riferimento visivo e di pattern, mentre i componenti qui dentro restano controllati dal progetto."
            toolbar={<UiBadge>Untitled UI aligned</UiBadge>}
          >
            <div className="grid gap-4 sm:grid-cols-3">
              <div className="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <Stars02 className="mb-3 size-5 text-brand-700" />
                <h3 className="text-sm font-semibold text-slate-950">Tailwind ready</h3>
                <p className="mt-2 text-sm text-slate-600">
                  Toolchain Vite pronta per utilities, tokens e componenti futuri.
                </p>
              </div>
              <div className="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <LayersThree01 className="mb-3 size-5 text-brand-700" />
                <h3 className="text-sm font-semibold text-slate-950">React Aria ready</h3>
                <p className="mt-2 text-sm text-slate-600">
                  Provider router-aware già collegato a Inertia per future primitive accessibili.
                </p>
              </div>
              <div className="rounded-3xl border border-slate-200 bg-slate-50 p-4">
                <Bell02 className="mb-3 size-5 text-brand-700" />
                <h3 className="text-sm font-semibold text-slate-950">Icons ready</h3>
                <p className="mt-2 text-sm text-slate-600">
                  Set Untitled UI disponibile subito per navigation, badge e page actions.
                </p>
              </div>
            </div>

            <div className="flex flex-wrap gap-3">
              <UiButton tone="primary" trailingIcon={<ArrowRight />}>
                Primary action
              </UiButton>
              <UiButton tone="secondary">Secondary action</UiButton>
              <UiButton tone="ghost">Ghost action</UiButton>
            </div>
          </UiCard>

          <UiCard
            eyebrow="Conventions"
            title="Regole del branch"
            description="Base tecnica pronta per i branch successivi del design system senza toccare il core del framework."
          >
            <div className="space-y-3 text-sm text-slate-600">
              <p>La utility `cn` ora mergea classi Tailwind in modo sicuro.</p>
              <p>Il frontend React mantiene il CSS legacy ma può introdurre nuovi slice in utilities.</p>
              <p>Le primitive future useranno componenti source-owned invece di dipendere direttamente dalla UI library nelle pagine.</p>
            </div>

            <div className="flex flex-wrap gap-2">
              <UiBadge>cn + tailwind-merge</UiBadge>
              <UiBadge tone="muted">RouterProvider</UiBadge>
              <UiBadge tone="muted">Tailwind v4</UiBadge>
            </div>
          </UiCard>
        </div>
      </GuestLayout>
    </>
  );
}
