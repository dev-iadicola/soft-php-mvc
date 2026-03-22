# Changelog

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
