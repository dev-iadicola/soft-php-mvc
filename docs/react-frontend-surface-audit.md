# React + Inertia Frontend Surface Audit

## Obiettivo

Mappare le superfici frontend reali del progetto per preparare la migrazione progressiva a `React + TypeScript + Inertia`.

Questo documento non definisce ancora l'architettura finale; serve a capire:

- quali pagine esistono davvero
- quali layout e componenti sono condivisi
- quali dipendenze JS/CSS sono attive oggi
- quali interazioni sono server-driven e quali sono gia asincrone
- quali aree hanno il maggiore accoppiamento con view PHP e script inline

## Sintesi

Il frontend attuale e diviso in quattro famiglie principali:

1. layout e shell condivise
2. pagine pubbliche
3. pagine auth/guest
4. pagine admin

La superficie pubblica e abbastanza compatta e adatta a un porting progressivo.

La superficie admin e molto piu ampia, con forte uso di:

- script inline
- librerie CDN
- jQuery / Bootstrap
- componenti page-specific
- fetch mirate per interazioni asincrone

Questo conferma che la migrazione va fatta per slice funzionali, non per semplice conversione file-per-file.

## 1. Layout e Shell Condivise

### Layout esistenti

- `views/layouts/default.php`
- `views/layouts/admin.php`
- `views/layouts/coming-soon.php`
- `views/layouts/raw.php`

### Shell pubblica corrente

`default.php` contiene:

- meta SEO base
- menu mobile custom
- navbar pubblica
- footer
- popup cookie
- flash messages
- asset CSS globali

Dipendenze principali:

- Bootstrap 5 CDN
- Font Awesome 4.7 CDN
- Devicon CDN
- `assets/lib.js`
- jQuery CDN

### Shell admin corrente

`admin.php` contiene:

- sidebar
- topbar
- breadcrumb
- notification bell
- user dropdown
- flash messages
- footer admin
- funzioni JS globali per sidebar, dropdown, toast, sortable e toggle active

Dipendenze principali:

- Bootstrap 4 JS
- jQuery
- Popper
- Lucide via CDN
- Quill
- SortableJS
- CKEditor glue script
- Chart.js

### Componenti condivisi

Pubblici/shared:

- `views/components/footer.php`
- `views/components/pagination.php`
- `views/components/popup-cookie.php`
- `views/session/messages.php`

Admin/shared:

- `views/components/admin/sidebar.php`
- `views/components/admin/notification-bell.php`
- `views/components/admin/article-form.php`
- `views/components/admin/profile-form.php`
- `views/components/admin/skill-form.php`

## 2. Pagine Pubbliche

### Pagine marketing/contenuto

- `/` -> `views/pages/home.php`
- `/portfolio` -> `views/pages/portfolio.php`
- `/progetti` -> `views/pages/progetti.php`
- `/progetti/{slug}` -> `views/pages/progetto.php`
- `/tech-stack` -> `views/pages/technology.php`
- `/certificati` -> `views/pages/corsi.php`
- `/contatti` -> `views/pages/contatti.php`
- `/blog` -> `views/pages/blog.php`
- `/sitemap.xml` -> `views/pages/sitemap.php` con layout `raw`

### Pagine legali / informative

- `/cookie` -> `views/pages/laws/law.php`
- `/laws` -> `views/pages/laws/law.php`
- `/law` -> `views/pages/cookie-law.php`
- fallback/errore -> `views/pages/error.php`, `views/pages/errors/ops.php`
- manutenzione -> `views/pages/coming-soon.php`

### Osservazioni

- Il blog nel branch corrente espone la lista, ma non risulta una view dettaglio articolo interna dedicata.
- Le pagine pubbliche dipendono ancora molto dal layout `default.php`.
- SEO e meta tag sono gia server-driven, quindi il porting dovra preservare questo comportamento.
- La paginazione pubblica e gia centralizzata in `views/components/pagination.php`, ottimo candidato per una prima conversione in componente React.

## 3. Pagine Auth / Guest

### Superficie auth attuale

- `/login` -> `views/pages/Auth/login.php`
- `/sign-up` -> `views/pages/Auth/sign-up.php`
- `/forgot` -> `views/pages/Auth/forgot.php`
- `/validate-pin/{token}` -> `views/pages/Auth/validate-token.php`
- `/two-factor` -> `views/pages/Auth/two-factor.php`

### Caratteristiche

- tutte vivono ancora dentro la shell `default`
- hanno script inline dedicati
- usano form classici server-posted
- dipendono da flash message, old input, CSRF e redirect server-side

### Implicazioni per React

Le pagine auth sono buone candidate per il futuro `GuestLayout`, ma non sono il primo slice da migrare se prima non esiste gia un adapter Inertia solido.

## 4. Pagine Admin

### Dashboard e sistema

- `/admin/dashboard` -> `views/pages/admin/dashboard.php`
- `/admin/visitors` -> `views/pages/admin/visitors.php`
- `/admin/logs` -> `views/pages/admin/logs.php`
- `/admin/terminal` -> `views/pages/admin/terminal.php`
- `/admin/settings` -> `views/pages/admin/settings.php`

### Account e sicurezza

- `/admin/edit-profile` -> `views/pages/admin/edit-profile.php`
- `/admin/password` -> `views/pages/admin/change-password.php`
- `/admin/security` -> `views/pages/admin/security.php`
- `/admin/sessions` -> `views/pages/admin/sessions.php`

### Gestione contenuti e portfolio

- `/admin/home` -> `views/pages/admin/portfolio/home.php`
- `/admin/project` -> `views/pages/admin/portfolio/project.php`
- `/admin/technology` -> `views/pages/admin/portfolio/technology.php`
- `/admin/partner` -> `views/pages/admin/portfolio/partner.php`
- `/admin/corsi` -> `views/pages/admin/portfolio/corsi.php`
- `/admin/contact-hero` -> `views/pages/admin/portfolio/contact-hero.php`
- `/admin/footer-links` -> `views/pages/admin/portfolio/footer-links.php`
- `/admin/email-templates` -> `views/pages/admin/portfolio/email-templates.php`
- `/admin/contatti` -> `views/pages/admin/portfolio/messaggi.php`
- `/admin/laws` -> `views/pages/admin/laws/index.php`

### Sotto-superfici riutilizzate

- form articolo
- form profilo
- form skill
- sidebar
- notification bell

### Osservazioni

- L'admin e la parte piu costosa da migrare.
- Esistono molte pagine CRUD che condividono pattern simili ma non ancora componentizzati lato frontend.
- La shell admin e gia forte e riconoscibile: ottima base per `AdminLayout`.
- Diversi CRUD admin hanno gia endpoint JSON o fetch mirate, utili per future interazioni React incrementali.

## 5. Interazioni Asincrone e JS Page-Specific

### Interazioni async gia presenti

- polling notifiche
- sort order via `PATCH /admin/sort-order`
- toggle active via `PATCH /admin/toggle-active`
- conteggi notifiche via JSON

### Pagine con forte JS embedded

- dashboard admin -> Chart.js
- visitors admin -> Chart.js
- logs admin -> Leaflet + jQuery
- security admin -> QR code via CDN
- home/project/technology/partner/footer-links -> sortable / helper JS
- email templates -> Quill
- auth pages -> script inline specifici

### Conseguenza

Il porting non va pensato come semplice traduzione delle view PHP in JSX. In piu aree bisognera:

- estrarre logica client
- isolare librerie dipendenti dalla pagina
- ridefinire component boundaries

## 6. Dipendenze Frontend Correnti

### Asset locali

- `assets/style.css`
- `assets/admin.css`
- `assets/cards.css`
- `assets/colors.css`
- `assets/effect.css`
- `assets/lib.js`
- `assets/js/typewrite.js`

### CDN / librerie esterne oggi in uso

- Bootstrap 5 pubblico
- Bootstrap 4 admin
- jQuery
- Popper
- Font Awesome 4.7
- Lucide
- Devicon
- Quill
- SortableJS
- Chart.js
- Leaflet
- Lottie / DotLottie
- QRCode library via CDN

### Implicazioni

L'attuale frontend non e ancora centralizzato in una toolchain unica. La migrazione React dovra assorbire gradualmente questi asset, evitando un secondo layer di caos.

## 7. Accoppiamenti Critici da Tenere d'Occhio

### Accoppiamenti shell

- `default.php` include menu, footer, popup cookie e flash
- `admin.php` include molte interazioni JS globali

### Accoppiamenti helper-driven

- uso diffuso di `view(...)`
- uso di `route(...)`, `csrf_token()`, `assets()`, `old()`, `errors()`
- SEO costruita lato PHP prima del render

### Accoppiamenti layout/page

- diverse pagine admin contano su JS globale dichiarato nel layout
- alcune view si aspettano funzioni come `initSortable()` definite in `admin.php`

Questi accoppiamenti vanno esplicitati nel branch `architecture-rfc`, per evitare un bootstrap React che rompa funzioni implicite oggi date per scontate.

## 8. Slice di Porting Naturali

### Slice 1 - shell e convenzioni

- `GuestLayout`
- `AdminLayout`
- flash stack
- shared props globali

### Slice 2 - pagine pubbliche a basso rischio

- blog list
- tech stack
- certificati

### Slice 3 - pubblico core

- home
- portfolio
- progetti list/dettaglio
- contatti

### Slice 4 - auth guest

- login
- sign-up
- forgot/reset
- two-factor challenge

### Slice 5 - admin core

- dashboard
- account/security
- notifiche

### Slice 6 - admin CRUD complessi

- articoli
- progetti
- media
- email templates
- contatti

## Conclusione

L'inventario conferma che:

- il pubblico puo essere portato in modo abbastanza progressivo
- auth e guest sono gestibili ma richiedono shared props e form strategy ben definiti
- l'admin richiede una migrazione per cluster funzionali, non per singola pagina isolata

Il branch successivo corretto dopo questo audit e `feature/react/architecture-rfc`, che dovra trasformare questa mappa in decisioni architetturali:

- contratto props
- adapter Inertia
- convenzioni layout
- strategia asset
- ordine ufficiale del primo MVP tecnico
