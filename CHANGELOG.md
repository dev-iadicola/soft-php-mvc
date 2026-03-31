# Changelog

## feature/react/public-seo-parity

### Parita SEO per le pagine pubbliche React
- Estesa la risposta HTML Inertia iniziale per renderizzare lato server description, canonical, Open Graph, Twitter Card, favicon e JSON-LD partendo dai props SEO condivisi
- Uniformato il titolo documento tra primo render server-side e navigazioni client-side React, evitando suffissi incoerenti tra HTML iniziale e pagina idratata
- Aggiunto `SeoHead` riusabile per le pagine pubbliche React, cosi canonical, OG, Twitter e structured data restano allineati anche nelle navigazioni Inertia senza reload completo
- Arricchiti i controller pubblici con metadati SEO piu coerenti per home, portfolio, tech stack, progetti e articoli, inclusi `Article`/`CollectionPage`/`CreativeWork` in JSON-LD dove rilevante

---

## feature/react/public-pages-porting

### Porting React delle pagine pubbliche principali
- Portati su Inertia i controller pubblici di home, portfolio, progetti, dettaglio progetto, blog, dettaglio articolo e tech stack, mantenendo URL e servizi applicativi esistenti
- Introdotto `PublicPageSerializer` per serializzare in modo stabile profilo, skill, tecnologie, progetti, articoli e paginazione senza accoppiare il core framework ai payload dell'app
- Aggiunte le nuove pagine React pubbliche con `GuestLayout` condiviso, filtri blog/progetti, card riusabili e viste dedicate per dettaglio articolo e dettaglio progetto
- Preparato il frontend pubblico alla successiva fase SEO, separando chiaramente contratto props, shell guest e contenuto delle singole pagine

---

## feature/react/untitledui-integration

### Base tecnica per Untitled UI nel frontend React
- Integrata la toolchain frontend necessaria per una base compatibile con Untitled UI (`tailwindcss`, plugin Vite, `react-aria-components`, `@untitledui/icons`, `tailwind-merge`)
- Introdotti provider e primitive source-owned (`AppProviders`, `UiButton`, `UiBadge`, `UiCard`) per usare pattern Untitled UI senza accoppiare direttamente le pagine alla libreria esterna
- Aggiunta una preview `/react-preview/untitled` per validare Tailwind, React Aria, icone Untitled UI e convenzioni di design system già dentro il bootstrap Inertia
- Risolto il `419` dei form React/Inertia inviando sempre il token CSRF negli header client e accettando il token anche lato middleware via `X-CSRF-TOKEN` / `X-XSRF-TOKEN`
- Centralizzata nel bootstrap Inertia la costruzione degli header di sicurezza e dei token client-side tramite helper dedicato, cosi CSRF e futuri header condivisi non restano duplicati nei singoli form React
- Ripulito il copy tecnico del `GuestLayout`, sostituendo il messaggio di bootstrap React con un testo neutro adatto al frontend reale

---

## feature/react/admin-secondary-areas

### Porting React delle aree admin secondarie
- Portate su Inertia le pagine admin di contatti, template email, tech stack e statistiche visitatori con serializzazione esplicita lato controller
- Aggiunte nuove pagine React gestionali per inbox messaggi, editing template, gestione tecnologie e dashboard visitatori senza dipendere piu dalle view legacy PHP di quelle sezioni
- Estesa la shell admin React con nuovi entry di navigazione e componenti/stili riusabili per liste record, pannelli secondari, placeholder editor e tabelle dati
- Corretto il resolver asset Inertia per usare `/assets/build` come public path reale della build Vite, eliminando i 404 su CSS e JS delle pagine React

---

## feature/react/admin-critical-flows

### Porting React dei flussi admin critici
- Portate su Inertia le pagine critiche di autenticazione admin (`/login`, `/sign-up`, `/two-factor`) con nuove pagine React guest e submit via `useForm`
- Portata su React la dashboard admin reale con metriche serializzate lato controller, trend visite e lista dei messaggi recenti
- Portate su React anche le pagine sicurezza account e sessioni attive, mantenendo attivazione/disattivazione 2FA e terminazione sessioni dentro la nuova shell admin

---

## feature/react/admin-form-strategy

### Strategia base per i form admin React
- Introdotti componenti riusabili per form admin (`AdminFormShell`, sezioni, field wrapper, input/select/textarea) per evitare di ridisegnare ogni CRUD da zero
- Aggiunta una preview dedicata `Admin/FormStrategyPreview` con `useForm`, error bag, aside impostazioni, indicatori di completion e preparazione a upload/editor rich-text
- Aggiornata la preview React admin con il nuovo entry della strategia form, cosi il prossimo branch dei flussi critici può riusare subito la stessa base UI

---

## feature/react/admin-layout-shell

### Shell admin React condivisa
- Evoluto `AdminLayout` in una shell reale con sidebar sezionata, drawer mobile, topbar, notification bell placeholder, user snapshot, breadcrumb e page actions
- Aggiunta una preview admin Inertia dedicata con `Admin/PreviewDashboard` e route `/react-preview/admin` per verificare la shell senza toccare ancora i CRUD reali
- Aggiornato il fallback React per distinguere automaticamente tra pagine guest e admin, applicando il layout corretto già dalla fase di bootstrap

---

## feature/react/public-props-contract

### Shared props pubbliche per React
- Esteso `SharedProps` con contratto iniziale per il frontend pubblico: `site`, `navigation`, `routing`, `seo` e metadata base coerenti con l'URL corrente
- Aggiornato `GuestLayout` per consumare la navigazione condivisa lato server quando disponibile, mantenendo fallback locale compatibile durante il porting graduale
- Aggiunti test sul pacchetto di shared props guest per consolidare canonical, current path e navigazione principale del layout pubblico

---

## feature/react/guest-layout-shell

### Shell guest/pubblica condivisa
- Evoluto `GuestLayout` in una shell React completa con header pubblico, CTA, drawer mobile, breadcrumb, hero strutturato e footer condiviso
- Estratti componenti dedicati per navigazione guest, breadcrumb e footer, cosi da poter riusare la stessa shell su pagine pubbliche, blog e flussi guest come login o sign-up
- Aggiornata la pagina preview Inertia per usare la nuova shell e verificare stato attivo, compatibilita mobile e composizione hero/contenuto senza toccare ancora il contratto server delle shared props

---

## feature/react/inertia-first-page

### Prima pagina Inertia reale
- Aggiunto il caricamento degli asset buildati dal manifest Vite dentro la risposta HTML Inertia, cosi la prima visita può montare davvero React senza script hardcoded
- Introdotta una route di preview React/Inertia dedicata con pagina `Preview/Welcome` per validare end-to-end shared props, manifest assets e coesistenza con le view PHP legacy
- Aggiunti test sul render HTML Inertia e sul resolver del manifest asset per consolidare la base della prima integrazione

---

## feature/react/react-ts-bootstrap

### Deploy frontend compilato in CI
- Aggiornato `.github/workflows/main.yml` per installare le dipendenze frontend in GitHub Actions, eseguire la build Vite prima del deploy e inviare al server solo gli asset compilati necessari a runtime
- Esclusi dal deploy i file di toolchain/frontend non necessari in produzione, come `frontend/`, `package*.json`, `tsconfig*` e `vite.config.ts`

### Bootstrap iniziale React + TypeScript
- Aggiunto il nuovo entrypoint `frontend/app.tsx` con bootstrap Inertia client-side e resolver pagine iniziale
- Introdotta la struttura base `frontend/` con `pages`, `components`, `layouts`, `hooks`, `lib`, `styles` e `types` per avviare il porting in modo ordinato
- Definiti alias `@/*`, utility `cn(...)`, layout placeholder `GuestLayout` e `AdminLayout`, piu una fallback page per componenti Inertia non ancora migrate
- Rifinito lo script `npm run build` per validare TypeScript senza emettere artefatti transitori nel repository durante la build CI

---

## feature/react/frontend-toolchain

### Toolchain frontend React + TypeScript + Vite
- Aggiunti `package.json`, `tsconfig.json`, `tsconfig.app.json`, `tsconfig.node.json` e `vite.config.ts` come base del nuovo frontend React
- Definiti script Node standard per `dev`, `build` e `preview`
- Configurato Vite per produrre build versionate in `assets/build` con entrypoint frontend dedicato e manifest abilitato
- Aggiornato `.gitignore` per escludere `node_modules`, cache Vite e gli output build generati localmente
- Aggiornato il `Dockerfile` per includere `nodejs` e `npm` nel container applicativo, cosi la toolchain React puo essere usata anche tramite `dock`

---

## feature/react/inertia-backend-adapter

### Infrastruttura backend minima per Inertia
- Introdotti `InertiaPage`, `SharedProps`, `InertiaResponseFactory` e la facade `Inertia` per preparare il protocollo Inertia nel framework senza toccare ancora la toolchain frontend
- Aggiunto helper globale `inertia()` e config `config/inertia.php` con versione e root element iniziali
- `Response` ora espone getter per status code e headers, utili per testing e per il nuovo adapter response-oriented
- Aggiunti test dedicati per serializzazione del page object, merge delle shared props e comportamento HTML/JSON delle prime risposte Inertia

---

## feature/react/migration-strategy

### Strategia operativa di migrazione React + Inertia
- Formalizzato l'ordine di esecuzione delle fasi: MVP tecnico Inertia, shell React condivise, pubblico a basso rischio, auth guest, admin core e solo dopo CRUD complessi
- Definiti criteri di ingresso/uscita per ogni fase, insieme a regole di esecuzione per mantenere la migrazione incrementale, reversibile e deployabile
- Chiariti i principali stop/go della roadmap: niente big bang, niente porting simultaneo di aree critiche, niente bootstrap caricato di librerie premature
- Confermato `feature/react/inertia-backend-adapter` come primo branch tecnico che dovra toccare davvero il framework per introdurre Inertia

---

## feature/react/architecture-rfc

### Decisioni architetturali per React + TypeScript + Inertia
- Formalizzata la strategia di migrazione ibrida: coesistenza esplicita tra view PHP legacy e nuove pagine Inertia, senza riscrivere il router custom
- Definita l'integrazione di Inertia come adapter sul layer HTTP, basata sulla `Response` condivisa e non su una rottura del flusso attuale controller/router
- Scelti i capisaldi del nuovo frontend: struttura dedicata `frontend/`, due layout React distinti (`GuestLayout` e `AdminLayout`), shared props centralizzate e output build in `assets/build`
- Confermata la convenzione `cn(...)`, il bisogno di un resolver Vite separato da `assets()` e l'uso di payload/DTO stabilizzati per serializzare i dati verso React

---

## feature/react/frontend-surface-audit

### Audit superfici frontend da migrare
- Aggiunto il documento `docs/react-frontend-surface-audit.md` con inventario delle superfici pubbliche, auth, admin, layout e componenti condivisi
- Mappate le shell attuali (`default`, `admin`, `coming-soon`, `raw`) e le dipendenze reali a CDN, script inline e asset locali
- Distinte le aree pubbliche, guest/auth e admin, con evidenza dei punti piu costosi da migrare e degli accoppiamenti attuali a helper, layout e JS globale
- Formalizzati i primi slice naturali di porting per preparare il branch successivo `feature/react/architecture-rfc`

---

## feature/react/feasibility-assessment

### Analisi di fattibilita React + TypeScript + Inertia
- Aggiunto il documento `docs/react-inertia-feasibility.md` con valutazione architetturale basata sul framework reale
- Documentati i punti favorevoli alla migrazione: bootstrap centralizzato, `Response` condivisa, router a attributi, sessione/flash/CSRF e shell pubblica/admin gia separate
- Evidenziati i principali vincoli tecnici: assenza di pipeline frontend moderna, helper asset statico, response mutation pattern, script inline/CDN e mancanza di un contratto props/DTO per il frontend
- Formalizzata la raccomandazione di una migrazione ibrida e progressiva, con perimetro chiaro del primo MVP tecnico e ordine suggerito dei branch successivi

---

## feature/admin-responsive-ui

### Redesign completo layout admin
- Riscritto `admin.css` da zero con design system coerente: palette colori semantica (primary, success, warning, danger, info), variabili CSS per spacing, radius, shadow e tipografia
- Sostituito Font Awesome 4.7 con Lucide Icons (SVG, moderno, consistente)
- Aggiunto font Inter tramite Google Fonts come font sans-serif principale dell'admin
- Rimosso font-weight 700 forzato su tutti gli elementi, introdotta scala tipografica chiara (h1-h4, body, caption, label)

### Nuova topbar admin
- Creata topbar fissa con breadcrumb dinamico della pagina corrente, area notifiche e dropdown utente (avatar, profilo, impostazioni, sicurezza, logout)
- Hamburger menu per toggle sidebar su tablet/mobile
- Dropdown utente con link rapidi a profilo, impostazioni, sicurezza e logout

### Redesign sidebar
- Sidebar ridisegnata con stile moderno: brand in alto, sezioni titolate (Principale, Gestione Contenuti, Sistema), link con icone Lucide e indicatore attivo (barra laterale colorata)
- Sotto-menu collassabili con chevron animato e auto-apertura in base alla pagina corrente
- Backdrop scuro su mobile con chiusura al tap fuori
- Helper PHP `_sidebarGroupActive()` per gestire lo stato attivo dei gruppi di navigazione
- Rimosso vecchio toggle button `#toggle-sidebar` e meccanismo jQuery per collasso

### Notifiche spostate in topbar
- Campanellino notifiche spostato dalla sidebar alla topbar, con dropdown posizionato sotto il bottone
- Icone notifica aggiornate a Lucide (bell, mail, info)
- Polling 30s mantenuto, stile dropdown aggiornato

### Dashboard ridisegnata
- Stat card riscritte con stile moderno: icona colorata su sfondo tenue, valore grande, label descrittiva, hover con elevazione
- Layout stat card con CSS Grid responsive (`auto-fit, minmax(220px, 1fr)`)
- Grafici Chart.js aggiornati con palette coerente (primary indigo), grid minimali, font Inter
- Lista messaggi ridisegnata: avatar con iniziale, badge tipologia, indicatore non-letto con sfondo warning, layout compatto

### Session messages
- Flash message ridisegnati con stile admin coerente: bordo colorato, icone Lucide (check, alert-circle, alert-triangle), auto-dismiss dopo 5 secondi con fade-out

### Footer admin
- Creato footer admin dedicato e minimale inline nel layout (copyright, link "Visita il sito"), separato dal footer pubblico
- Sticky footer: resta in fondo anche con contenuto corto grazie a flexbox

### Toast system
- Introdotto sistema toast globale `adminToast(message, type)` per feedback azioni (sortable, toggle active, errori)
- Toast con animazione slide-in/fade-out, posizionamento fixed bottom-right

### Pulizia tecnica
- Rimossa dipendenza da Font Awesome 4.7 CDN duplicata
- Rimosso script `window.onload` di warning per protocollo file
- Aggiunto cache-busting automatico (`?v=time()`) sul CSS admin
- Aggiunta pulizia view cache dopo modifiche layout

### Nota design system
- Lo stile admin segue i pattern visivi di UntitledUI (palette, radius, spacing, tipografia) come riferimento per la futura migrazione a React+TypeScript+Inertia

---

## feature/first-user-registration

### Bootstrap primo account admin
- Rimosso il seeder automatico dell'utente admin
- Il login ora reindirizza alla pagina di registrazione iniziale quando non esiste alcun utente
- La registrazione del primo account e stata blindata lato server: se esiste gia un utente, non e possibile crearne un secondo nemmeno via POST diretto
- Introdotto `FirstUserSetupService` con test unitari dedicati per il flusso bootstrap del primo admin

---

## fix/admin-asset-404

### Layout admin
- Rimosso il riferimento al CSS inesistente `favicon.min.css` nel layout admin
- Normalizzato il path del favicon admin per evitare URL asset con slash doppi

---

## feature/migration-raw-sql-fix

### Robustezza raw migration
- Corretto `Migration::rawSql()` per filtrare statement vuoti e normalizzare gli array SQL
- `executeUp()` e `executeDown()` ora trattano le raw migration senza query come no-op invece di tentare un `CREATE TABLE __raw__` invalido
- Aggiunto test unitario per coprire il caso `rawSql([])` e l'esecuzione con statement vuoti

---

## feature/demo-seeders

### Blog demo data
- Riscritto il seeder articoli con 10 contenuti demo realistici, slug espliciti, date distribuite e mix di formati `pillar` e `quick-note`
- Introdotto stub condiviso `database/seed/stubs/article-demo-content.php` per centralizzare copy, metadata editoriali e cover demo
- Cover articolo generate come SVG data URI differenti e compatibili con il limite del campo `articles.img`
- Aggiunti seeders per `tags` e `article_tag`, derivati automaticamente dal dataset articoli per mantenere coerenza tra contenuti e tassonomia
- Aggiunto test unitario dedicato per verificare quantita, varieta, slug, cover e tag del dataset demo articoli

---

## feature/projects-status-cleanup

### Semplificazione stato progetto
- Rimosso il campo `status` dal form admin dei progetti
- Rimossi badge e label "In corso/Completato" dalla vista pubblica di dettaglio progetto
- Presentazione delle date resa neutra con campi separati "Inizio" e "Fine"
- Eliminato l'enum `ProjectStatus` e la proprietà `status` dal model `Project`

---

## feature/auth-security

### Rate limiting mirato
- Nuova tabella `rate_limits` con tracking per `IP + route` (`attempts`, `last_attempt_at`)
- `RateLimitMiddleware` riscritto su storage persistente e configurazione `settings.rate_limit`
- Middleware non più globale: applicato via attributo alle sole rotte `POST /login` e `POST /contatti`
- Risposta HTTP `429` con messaggio e `retry_after` per richieste JSON, redirect back con errore per form web

### 2FA TOTP
- Migration: colonne `two_factor_secret` e `two_factor_enabled` sulla tabella `users`
- Implementato `TotpService` puro con generazione secret, provisioning URI e verifica codici a 6 cifre
- Nuova pagina admin `/admin/security` per attivare/disattivare la 2FA
- QR code generato come SVG inline lato client a partire da URI `otpauth://...`
- Login aggiornato: dopo password valida, gli utenti con 2FA attiva vengono reindirizzati al form `/two-factor`

### Sessioni attive
- Nuova tabella `sessions` per tracciare sessioni autenticare con `user_id`, IP, user agent e `last_activity`
- Nuovi model/service: `AuthSession` e `AuthSessionService`
- `AuthService` e `AuthMiddleware` aggiornati per validare la sessione attiva dal database e sincronizzare `last_activity`
- Nuova pagina admin `/admin/sessions` con elenco sessioni e azione "Termina"

### Supporto framework
- `SessionStorage` ora espone helper per `session_id`, rigenerazione ID e rimozione multipla chiavi
- Corretto `ActiveQueryFactory` per leggere il flag `timestamps` dai model senza forzare `created_at`/`updated_at`

---

## feature/profile-enhancements

### Campi social
- Migration: colonne `github_url`, `linkedin_url`, `twitter_url` (VARCHAR 255, nullable) su `profile`
- Input URL nel form admin profilo
- Icone social (GitHub, LinkedIn, Twitter) nella sezione hero pubblica, mostrate solo se valorizzate

### Avatar
- Migration: colonna `avatar` (VARCHAR 255, nullable)
- Upload con resize 200x200 via `ImageHelper::processFromString()` nel controller
- Mostrato nella hero pubblica con bordo circolare e fallback se assente

### Bio estesa
- Textarea bio con editor Quill (classe `editor`) nel form create profilo
- Bio renderizzata come HTML nella sezione competenze pubblica

---

## feature/projects-enhancements

### Stato progetto con enum
- Enum `ProjectStatus` (`in_progress`, `completed`, `paused`) con metodi `label()`, `color()`, `icon()`
- Migration: colonne `status`, `started_at`, `ended_at` su `projects`
- Model `Project` aggiornato con le nuove proprietà
- Form admin: select status (iterato dall'enum), input date inizio/fine
- Vista pubblica dettaglio: badge status colorato con icona, range date "Mar 2024 — in corso"

### Galleria screenshot
- Upload multiplo galleria nel form admin progetti (`<input type="file" multiple name="gallery[]">`)
- Anteprima immagini esistenti con bottone elimina per ciascuna
- Vista dettaglio pubblica: griglia gallery con immagini cliccabili (link diretto)

### Note
- Drag-and-drop era già integrato (SortableJS + `initSortable`)

---

## feature/blog-system

### Integrazione frontend
- Nuovo `BlogController` con rotta `GET /blog` — paginazione, ricerca per titolo, filtro per tag via query string
- Vista pubblica `blog.php` con griglia card articoli, barra ricerca, chip tag cliccabili, paginazione
- Link "~/blog" aggiunto nella navbar pubblica e nella sitemap
- Multi-select tag nel form create e nel modal edit articolo admin
- `HomeManagerController` aggiornato: passa tag alla vista, sync tag su create/update
- Campo `content` corretto in `overview` nel form create (allineato al model)

### Sistema tag
- Migration: tabelle `tags` (id, name, slug UNIQUE) e `article_tag` (article_id, tag_id) con indici
- Model `Tag` e `ArticleTag`
- `TagService` con CRUD, `getForArticle()`, `syncForArticle()`, `findBySlug()`

### Paginazione
- DTO `PaginationResult` con: items, currentPage, totalPages, totalItems, perPage, helper `hasPages()`, `hasPrevious()`, `hasNext()`, `pageRange()`
- `ArticleService::paginateActive()` con supporto ricerca e filtro tag
- Partial view `components/pagination.php` con navigazione prev/next e numeri pagina, preserva query string

### Ricerca full-text
- `ArticleService::search()` con `WHERE title LIKE ?`
- `ArticleService::paginateActive()` accetta parametro `$search` per filtrare

### Editor rich-text
- Classe `editor` aggiunta alla textarea `overview` nel form create articolo
- Campo `overview` con editor Quill aggiunto nel modal edit articolo
- Corretto nome campo da `content` a `overview` nel form create (allineato al model)

---

## feature/auto-reply

### Sistema auto-reply configurabile
- Migration: creata tabella `email_templates` (id, slug UNIQUE, subject, body TEXT, is_active, updated_at)
- Model `EmailTemplate` con proprietà tipizzate e cast `is_active` → bool
- `EmailTemplateService` con metodi: `getAll()`, `findBySlug()`, `findOrFail()`, `update()`, `render()` (sostituzione placeholder), `sendIfActive()` (invio via BrevoMail se template attivo)
- Seeder con template `contact_auto_reply`: oggetto "Abbiamo ricevuto il tuo messaggio", body HTML con placeholder `{nome}`, `{email}`, `{messaggio}`

### Integrazione automatica
- `ContactService::create()` ora chiama `EmailTemplateService::sendIfActive('contact_auto_reply', ...)` dopo salvataggio e notifica
- L'invio è silenzioso: se il template non esiste, è disattivo, o l'invio fallisce, non blocca il flusso

### Pagina admin
- `EmailTemplateController` con rotte: lista (`GET /admin/email-templates`), edit (`GET /admin/email-templates/{id}/edit`), update (`POST /admin/email-templates/{id}`)
- Vista con tabella template, form modifica con subject/body/toggle attivo, anteprima HTML del template
- Link "Template Email" aggiunto nella sidebar admin sotto Gestione Portfolio

---

## feature/contacts-management

### Toggle letto/non letto
- `ContactService::toggleRead($id)` inverte lo stato `is_read` e restituisce il nuovo valore
- Nuovo endpoint `POST /admin/contatti/{id}/toggle-read` con feedback
- Bottone toggle nella lista messaggi (icona busta aperta/chiusa) e nel dettaglio messaggio

### Risposta diretta via Brevo
- Form textarea + bottone "Invia risposta" nella vista dettaglio messaggio
- Endpoint `POST /admin/contatti/{id}/reply` usa `BrevoMail` per inviare via API Brevo
- Conferma prima dell'invio, gestione errori con try/catch

### Filtri per tipologia
- `ContactService::getDistinctTypologies()` restituisce le tipologie uniche
- `ContactService::getByTypologie()` filtra per tipologia
- Select con auto-submit nel header della lista messaggi
- `ContattiManagerController::index()` accetta query string `?typologie=...`

### Miglioramenti vista messaggi
- Messaggi non letti evidenziati con bordo sinistro giallo e badge "Nuovo"
- XSS protection con `htmlspecialchars()` su tutti gli output utente
- Layout dettaglio messaggio migliorato con bottoni azione raggruppati

---

## feature/upload-media

### Tabella media e sistema polimorfico
- Migration: creata tabella `media` (id, entity_type, entity_id, path, disk, sort_order, created_at) con indice su entity
- Model `Media` con proprietà tipizzate
- `MediaService` con metodi: `attach()`, `getFor()`, `delete()`, `deleteAllFor()`, `reorder()`, `findOrFail()`
- Upload tramite `MediaService::attach('project', $id, $file)` — gestisce naming, path, resize/compress automatico e salvataggio su disco

### ImageHelper
- Creato `App\Core\Helpers\ImageHelper` con GD: `processFromString()`, `resize()`, `compress()`
- Supporto JPEG, PNG, WebP con resize proporzionale (max 1200x1200) e compressione
- Preserva trasparenza per PNG/WebP
- Applicato automaticamente in `MediaService::attach()` per immagini

### Integrazione ProjectManagerController
- Upload multiplo galleria (`<input type="file" name="gallery[]" multiple>`) in store/update
- Immagine principale (`img`) resta come campo singolo, galleria su tabella `media`
- `destroy()` ora pulisce anche i media associati via `MediaService::deleteAllFor()`
- Nuovo endpoint `DELETE /admin/project-media/{mediaId}` per eliminare singole immagini
- Vista edit passa `gallery` con media del progetto

### Config filesystem
- Corretti typo in `config/filesystem.php`: `drive` → `driver`, `locale` → `local`
- Aggiunti commenti per documentare convenzioni dischi public/private

### Test Filesystem
- 7 nuovi test: getPath public vs private, strip leading slash, sovrascrittura file, deleteOrFail su file inesistente/esistente, existsOrFail

### SVG template per seeder
- Creato template `database/seed/stubs/project-svg-template.php` per generare SVG professionali con iniziali e gradient — da usare in `feature/demo-seeders`

---

## feature/admin-notifications

### Sistema notifiche admin
- Migration: creata tabella `notifications` (id, type, title, message, link, is_read, created_at)
- Model `Notification` con proprietà tipizzate e cast `is_read` → bool
- `NotificationService` con metodi: `create()`, `getUnread()`, `countUnread()`, `markAsRead()`, `markAllAsRead()`, `findOrFail()`
- Notifica automatica generata in `ContactService::create()` alla ricezione di un nuovo messaggio contatti

### Controller e endpoint
- `NotificationController` con 3 endpoint:
  - `GET /admin/notifications/count` — JSON `{"count": N}` per polling
  - `POST /admin/notifications/{id}/read` — segna come letta e redirect al link
  - `POST /admin/notifications/read-all` — segna tutte come lette

### Campanellino UI
- Partial `notification-bell.php` incluso nel layout admin globale
- Icona `fa-bell` fixed in alto a destra con badge numerico rosso
- Dropdown al click con lista ultime 10 notifiche non lette
- Ogni notifica cliccabile (segna come letta + naviga alla pagina)
- Bottone "Segna tutte come lette" nell'header del dropdown
- Polling automatico ogni 30 secondi via `fetch()` per aggiornare il badge
- CSS dedicato: animazione pulse sul badge, dropdown con scroll, hover sugli item

---

## feature/admin-dashboard

### Pulizia dashboard
- Rimossi widget placeholder: tabella HTML con dati finti (Mark/Jacob/Larry) e form "Quick Form / Submit Ticket" non funzionante

### Card riepilogo
- Aggiunta card **Progetti Attivi** con conteggio da `ProjectService::getActive()`
- Aggiunta card **Articoli Attivi** con conteggio da `ArticleService::getActive()`
- Card Messaggi ora mostra il conteggio **non letti** invece del totale, con colore `bg-danger` se > 0

### Sistema messaggi letti/non letti
- Migration: aggiunta colonna `is_read` (TINYINT default 0) alla tabella `contatti`
- Model `Contatti`: aggiunta proprietà `bool $is_read` con cast
- `ContactService`: aggiunti metodi `countUnread()` e `markAsRead(int $id)`
- Nuovo endpoint `POST /admin/contatti/{id}/read` per marcare come letto
- Auto-read: apertura dettaglio messaggio (`GET /admin/contatti/{id}`) marca automaticamente come letto
- Dashboard: messaggi non letti evidenziati con bordo sinistro giallo, font bold e badge "Nuovo"
- Ogni messaggio nella dashboard è ora un link cliccabile al dettaglio

---

## feature/seo-metadata

### Rimozione sezione Partners
- Rimosso `PartnersController` e vista `partners.php`
- Rimosso link Partners dalla navbar, footer e sitemap
- Rimosso riferimento a `PartnerService` da `PortfolioController`
- Rimossa riga Partners dal seeder `links_footer`
- Aggiunto filtro nel footer per escludere il link `/partners` dal database

### Slug automatici
- Aggiunta colonna `slug` (VARCHAR 150, nullable) a `projects` e `articles`
- Proprietà `?string $slug` aggiunta ai model `Project` e `Article`
- Generazione automatica dello slug da `Str::slug($title)` in `ProjectService::create/update` e `ArticleService::create/update`
- `ProjectService::findBySlug()` cerca prima per slug, poi fallback per titolo (compatibilità URL precedenti)
- Aggiunto `ArticleService::findBySlug()`
- Aggiornata vista progetti per usare slug negli URL invece di `urlencode($title)`
- Migration per popolare gli slug dei record esistenti

### Meta description e og:image dinamici
- Creato helper `App\Core\Helpers\Seo` con metodo `Seo::make()` per generare array meta tag (title, description, image, url)
- Aggiornato layout `default.php` con supporto a: `<meta name="description">`, Open Graph tags (`og:title`, `og:description`, `og:image`, `og:url`), `<link rel="canonical">`
- Tag `<title>` ora dinamico (pagina + suffisso sito)
- Aggiornati controller pubblici: HomeController, ProgettiController, PortfolioController, TechnologyController
- Per pagine senza SEO esplicito, fallback ai valori default

### Sitemap XML
- Creato `SitemapController` con rotta `GET /sitemap.xml`
- Genera XML con: 7 pagine statiche (home, portfolio, progetti, tech-stack, partners, certificati, contatti), tutti i progetti attivi con slug e lastmod, tutti gli articoli attivi con link esterno

---

## feature/technology-icons

- Aggiunta colonna `icon` (VARCHAR 100, nullable) alla tabella `technology`
- Aggiunta proprietà `?string $icon` al model `Technology`
- Aggiunto CDN Devicon (~130 icone tecnologie) nei layout admin e pubblico
- Aggiunto `<select>` con anteprima live nella vista admin per scegliere l'icona dalla libreria Devicon (3 varianti: plain, original, line)
- Aggiornata la vista pubblica tech stack: mostra l'icona Devicon se presente, altrimenti fallback al dot verde
- Aggiornata validazione nel controller per accettare il campo `icon`

---

## Refactoring: da fillable a incapsulamento (proprieta tipizzate)

### Fase 1 - Base Model e Attributes trait
- Aggiunto metodo `columns(): array` nel Model base con Reflection per leggere le proprieta dichiarate nel model figlio
- Modificato `setAttribute()` per scrivere direttamente nella proprieta tipizzata
- Modificato `getAttribute()` per leggere dalla proprieta tipizzata
- Aggiornato `jsonSerialize()` per serializzare le proprieta reali tramite `columns()`
- Aggiornato il trait `Attributes`: `__get()` e `__set()` con priorita alle proprieta dichiarate

### Fase 2 - ActiveQueryFactory
- In `ActiveQueryFactory::for()`, il fillable viene derivato da `$model->columns()` invece che da `$model->fillable`
- `$fillable` reso deprecato/opzionale nel Model base (fallback su `columns()` se vuoto)

### Fase 3 - ModelHydrator
- In `ModelHydrator::one()`, settaggio via `$model->setAttribute()` che scrive nelle proprieta reali
- Verificato che il clone funzioni correttamente con le proprieta dichiarate

### Fase 4 - Refactoring dei 13 Model
- **Article** — `?int $id`, `?string $title`, `?string $subtitle`, `?string $overview`, `?string $img`, `?string $link`, `?string $created_at`
- **Certificate** — `?int $id`, `?string $title`, `?string $overview`, `?int $certified`, `?string $link`, `?string $ente`
- **Contatti** — `?string $nome`, `?string $email`, `?string $messaggio`, `?string $created_at`, `?string $typologie`
- **Curriculum** — `?int $id`, `?string $title`, `?string $img`, `int $download = 0`
- **Law** — `?int $id`, `?string $title`, `?string $testo`
- **LogTrace** — `?int $user_id`, `?string $last_log`, `?string $indirizzo`, `?string $device`
- **Partner** — `?int $id`, `?string $name`, `?string $website`
- **Profile** — `?int $id`, `?string $name`, `?string $tagline`, `?string $welcome_message`, `?bool $selected`
- **Project** — `?int $technology_id`, `?int $partner_id`, `?string $title`, `?string $overview`, `?string $description`, `?string $link`, `?string $img`, `?string $website`
- **Skill** — `?int $id`, `?string $title`, `?string $description`
- **Technology** — `?int $id`, `?string $name`
- **Token** — `?string $email`, `?string $token`, `?bool $used`, `?string $created_at`, `?string $expiry_date`
- **User** — `?string $email`, `?string $password`, `?string $token`, `?string $indirizzo`, `?string $last_log`, `?int $log_id`, `?string $created_at`
- Rimosso `protected array $fillable` da ogni model

### Fase 5 - Verifica e test
- Verificato `query()->create()`, `query()->update()`, `query()->find()` / `first()` / `get()`, `$model->save()`
- Verificato che le viste continuino a funzionare
- Verificati i metodi statici: `Token::generateToken()`, `LogTrace::ceateLog()`, `User::changePassword()`
- Verificate le relazioni in `Project` (`partner()`, `technology()`)

### Fase 6 - Pulizia
- Rimosso `$fillable` da tutti i model
- Rimosso `$attributes` array dal trait dove non piu necessario
- Aggiornato lo stub delle migration
- Aggiornata documentazione

### Fase 7 - Riconoscimento tabella
- Riconoscimento automatico del nome tabella dal nome del model senza bisogno di `protected string $table`

---

## Da implementare (tabelle non ancora utilizzate)

### Links Footer
- Creato Model `LinkFooter`
- Creato controller admin `LinkFooterMngController` con CRUD completo
- Creata vista admin per gestire i link del footer
- Integrati i link dinamici nel layout footer del sito

### Visitors (Tracking visitatori)
- Creato Model `Visitor` con ip_address, user_agent, url, session_id, created_at
- Aggiornata migration `create_visitors` con campi completi
- Creato `VisitorService` con metodi: getAll, getRecent, getTotalVisits, getTodayVisits, getUniqueVisitors, getTodayUniqueVisitors, getVisitsByDay/Week/Month, getTopBrowsers, getTopDevices, getTopPages
- Implementato `VisitorTrackingMiddleware` che registra IP, user_agent, URL e session_id ad ogni visita pubblica
- Registrato middleware nel gruppo `guest` in `config/middleware.php`
- Creato `VisitorDashboardController` con rotta GET `/admin/visitors`
- Creata vista `views/pages/admin/visitors.php` con KPI card, grafici Chart.js, tabelle
- Aggiunto link "Visitatori" nella sidebar admin
- Aggiornata dashboard principale con dati reali

### Project Technologies (Tabella pivot)
- Creato Model `ProjectTechnology`
- Aggiunta selezione multipla delle tecnologie nella form progetto
- Mostrate le tecnologie associate nella vista pubblica di ogni progetto
- Filtro progetti per tecnologia

### Technology
- Creato controller admin `TechnologyMngController` con CRUD
- Vista admin per aggiungere/modificare/eliminare tecnologie
- Pagina pubblica "Tech Stack" con tutte le tecnologie utilizzate

### Partners
- Creato controller admin `PartnerMngController` con CRUD
- Vista admin per gestire i partner
- Sezione pubblica "Partner / Collaborazioni" con link ai siti web
- Associazione visibile nei progetti (mostrare il partner nella vista progetto)

### Logs
- Aggiunta paginazione alla vista admin dei log
- Filtri per data, utente, dispositivo
- Possibilita di eliminare log vecchi (pulizia)
- Export log in CSV

---

## Framework

### Routing e HTTP
- Aggiornati progressivamente i controller ai nuovi attributi di routing e metadata in stile Spatie

### Fase 1: Stabilizzare il Cuore dell'ORM
- Aggiunto `columns(): array` nel model base come API canonica
- Mantenuto `getPersistableColumns()` come alias compatibile
- Esteso il query builder con supporto a espressioni SQL nel SELECT e GROUP BY:
  - `selectRaw(string $expression): static` per espressioni arbitrarie nel SELECT
  - `groupByRaw(string $expression): static` per espressioni GROUP BY
  - `fetchRows(): array` per risultati raggruppati come array associativi
  - `havingRaw()` per filtri su aggregati
  - 15 test in `AggregateBuilderTest`
  - Rifattorizzate le query raw in `VisitorRepository` e `ProjectService`

### Fase 1b: Funzioni Aggregate nel Query Builder
- `count(string $column = '*'): int`
- `sum(string $column): int|float`
- `avg(string $column): float`
- `max(string $column): mixed`
- `min(string $column): mixed`
- `selectAggregate(string $function, string $column, ?string $alias = null): static`
- `countDistinct(string $column): int`
- `scalar(): mixed` per query che restituiscono un singolo valore
- Compatibilita con `groupBy()` per risultati raggruppati via `fetchRows()`
- 15 test per le funzioni aggregate
- Rifattorizzato `LogRepository` eliminando tutte le query SQL raw

### Fase 1c: Fix trait Relation (belongsToMany / belongsToManyThrough)
- Corretto `belongsToMany()`: il join puntava a `$this->getTable()` (non presente nel FROM) invece che alla tabella del model correlato
- Corretto `belongsToManyThrough()`: stesso bug, stessa correzione
- Rinominati parametri per chiarezza: `$foreignPivotKey` (colonna pivot→this), `$relatedPivotKey` (colonna pivot→related)

### Fase 2: Completare il Refactor dei Model Tipizzati
- Ricontrollati tutti i model e allineati i tipi alle colonne attese
- Migliorata la struttura dei model: il nullable rispecchia lo schema del database
- Corretti mismatch: `User::$created_at`, `Curriculum::$download`, `Token::$used`, `Profile::$selected`, `Certificate::$certified`
- Rimossi i riferimenti residui a `fillable` dalla documentazione ORM
- Aggiornato `ORM.MD` per spiegare i model con proprieta tipizzate e reflection delle colonne
- Documentate le regole per campi nullable e valori di default del database

### Fase 3: Rendere la CLI Davvero Utile
- Implementata la generazione reale dei file in `MakeModelCommand`
- Aggiunti stub per model, controller, middleware, migration e seeder
- Generati placeholder di proprieta tipizzate negli stub dei model
- Aggiunti flag opzionali: `make:model --migration`, `--resource`, `--table=...`
- Aggiunto comando per ispezionare le rotte scoperte
- Aggiunto comando per ispezionare colonne del model e nome tabella inferito
- Migliorata consistenza dell'output CLI e messaggi di errore
- Aggiunti test sui comandi
- Alla generazione di un controller da CLI, viene chiesto quali middleware e prefix configurare

### Fase 4: Rafforzare HTTP e Routing
- Rimossi/isolati i percorsi di routing deprecati in `Router`
- Aggiunti test per caricamento rotte, ereditarieta, stack middleware e matching
- Introdotta cache delle rotte per produzione
- Aggiunto supporto per nomi rotta, URL generation e ispezione delle rotte da CLI
- Standardizzate le firme dei metodi dei controller e l'iniezione di `Request`
- Estratto dal `RouteDispatcher` un resolver dedicato ai parametri del controller
- Consentita l'iniezione di `Request` senza dipendere da `mvc()->request`
- Supportata la risoluzione mista di parametri per nome e per tipo

### Fase 4.b: Refactor Routing in stile Spatie
- Introdotti attributi HTTP separati: `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`
- Mantenuto `#[RouteAttr]` come layer di compatibilita temporaneo
- Aggiunti attributi di gruppo: `#[Prefix]`, `#[Middleware]`, `#[NamePrefix]`
- Il `RouteLoader` ignora i metodi pubblici senza attributi di routing
- Rimossa la rigidita per cui ogni metodo pubblico del controller doveva essere una rotta
- Aggiunta URL generation basata su route name e parametri
- Aggiunto comando CLI `route:list`
- Aggiunti test di compatibilita tra nuovi attributi HTTP e `RouteAttr`
- Aggiunti test su prefix multipli, merge middleware e inheritance dei controller
- Spostati gli attributi `#[Prefix]` e `#[Middleware]` da `AdminController` nei controller concreti admin

### Fase 5: Migliorare Validazione e Gestione della Request
- Creati helper di validazione orientati alla request / classi stile form-request
- Aggiunti accessor tipizzati in `Request`
- Migliorata l'estendibilita del validator per regole custom
- Standardizzato il trasporto degli errori di validazione verso sessione e viste
- Aggiunti test di validazione su edge case e flussi nullable

### Admin Frontend Refresh
- Allineati `Attributes::__get()` e `Attributes::__set()` al nuovo modello con proprieta tipizzate
- Lasciate le colonne DB sconosciute in `$attributes` come fallback
- Corretta la semantica di `save()` per evitare insert/update di tutte le proprieta nullable non toccate
- Aggiunto supporto al dirty-checking esplicito dei model
- Aggiunto hook di casting per booleani, interi, date e colonne JSON
- Aggiunta strategia sicura di mapping proprieta-colonna per nomi riservati come `table`
- Verificato il comportamento dell'hydrator con clone, proprieta tipizzate e colonne mappate
- Scritti test unitari per create, update, find, first, get, hydration e save
- Ricontrollati tutti i model e allineati i tipi
- Migliorata la struttura dei model (nullable rispecchia lo schema DB)
- Rimossi riferimenti residui a `fillable` dalla documentazione ORM
- Aggiornato `ORM.MD`
- Documentate le regole per campi nullable e valori di default

### Fase 8: Refactor Emersi da `refactoring.md`
- Aggiunto `declare(strict_types=1)` ai file framework mancanti
