# Changelog

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
