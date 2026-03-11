# TODO - Potenziale del Portfolio

---

> **Nota importante:** eseguire tutti i task seguendo le best practices moderne, costruendo soluzioni pulite e professionali, con attenzione a buoni design pattern, manutenibilita, chiarezza e assenza di codice spaghetti. L'implementazione deve essere dettagliata ma rigorosa.
>
> **Nota di processo:** a ogni nuova sessione eseguire sempre una code review iniziale del lavoro recente e tracciare osservazioni, problemi, refactor proposti e decisioni in `refactoring.md`.
>
> **Vincolo operativo:** e vietato cancellare task gia presenti da `todo.md`; i task possono essere solo aggiunti, aggiornati o marcati come completati.

## Refactoring: da fillable a incapsulamento (proprieta tipizzate)

> **Obiettivo:** Sostituire `protected array $fillable` con proprieta reali tipizzate (`protected ?string $title = null`)
> nei Model, mantenendo compatibilita con il QueryBuilder, l'hydrator e le viste.

### Fase 1 - Base Model e Attributes trait
- [x] Aggiungere metodo `columns(): array` nel Model base che usa Reflection per leggere le proprieta pubbliche/protette dichiarate nel model figlio (escludendo `$table`, `$timestamps`, `$fillable`, `$attributes`, `$primaryKey`)
- [x] Modificare `setAttribute()` per scrivere direttamente nella proprieta tipizzata (se esiste) invece che in `$attributes`
- [x] Modificare `getAttribute()` per leggere dalla proprieta tipizzata (se esiste) invece che da `$attributes`
- [x] Aggiornare `jsonSerialize()` per serializzare le proprieta reali tramite `columns()`
- [x] Aggiornare il trait `Attributes`: `__get()` e `__set()` devono dare priorita alle proprieta dichiarate

### Fase 2 - ActiveQueryFactory
- [x] In `ActiveQueryFactory::for()`, derivare il fillable da `$model->columns()` invece che da `$model->fillable`
- [x] Rendere `$fillable` deprecato/opzionale nel Model base (fallback su `columns()` se vuoto)

### Fase 3 - ModelHydrator
- [x] In `ModelHydrator::one()`, usare le proprieta tipizzate: settare via `$model->setAttribute()` che ora scrive nelle proprieta reali
- [x] Verificare che il clone funzioni correttamente con le proprieta dichiarate

### Fase 4 - Refactoring dei 13 Model

- [x] **Article** — Aggiungere: `?int $id`, `?string $title`, `?string $subtitle`, `?string $overview`, `?string $img`, `?string $link`, `?string $created_at`
- [x] **Certificate** — Aggiungere: `?int $id`, `?string $title`, `?string $overview`, `?int $certified`, `?string $link`, `?string $ente`
- [x] **Contatti** — Aggiungere: `?string $nome`, `?string $email`, `?string $messaggio`, `?string $created_at`, `?string $typologie`
- [x] **Curriculum** — Aggiungere: `?int $id`, `?string $title`, `?string $img`, `int $download = 0`
- [x] **Law** — Aggiungere: `?int $id`, `?string $title`, `?string $testo`
- [x] **LogTrace** — Aggiungere: `?int $user_id`, `?string $last_log`, `?string $indirizzo`, `?string $device`
- [x] **Partner** — Aggiungere: `?int $id`, `?string $name`, `?string $website`
- [x] **Profile** — Aggiungere: `?int $id`, `?string $name`, `?string $tagline`, `?string $welcome_message`, `?bool $selected`
- [x] **Project** — Aggiungere: `?int $technology_id`, `?int $partner_id`, `?string $title`, `?string $overview`, `?string $description`, `?string $link`, `?string $img`, `?string $website`
- [x] **Skill** — Aggiungere: `?int $id`, `?string $title`, `?string $description`
- [x] **Technology** — Aggiungere: `?int $id`, `?string $name`
- [x] **Token** — Aggiungere: `?string $email`, `?string $token`, `?bool $used`, `?string $created_at`, `?string $expiry_date`
- [x] **User** — Aggiungere: `?string $email`, `?string $password`, `?string $token`, `?string $indirizzo`, `?string $last_log`, `?int $log_id`, `?string $created_at`
- [x] Per ogni model: rimuovere `protected array $fillable` dopo aver aggiunto le proprieta

### Fase 5 - Verifica e test
- [x] Verificare che `query()->create()` funzioni (i dati vengono filtrati correttamente)
- [x] Verificare che `query()->update()` funzioni
- [x] Verificare che `query()->find()` / `first()` / `get()` idratino correttamente le proprieta
- [x] Verificare che `$model->save()` funzioni (lettura proprieta per l'update)
- [x] Verificare che le viste continuino a funzionare (`$article->title`, `$project->img`, ecc.)
- [x] Verificare i metodi statici: `Token::generateToken()`, `LogTrace::ceateLog()`, `User::changePassword()`
- [x] Verificare le relazioni in `Project` (`partner()`, `technology()`)

### Fase 6 - Pulizia
- [x] Rimuovere `$fillable` da tutti i model
- [x] Rimuovere `$attributes` array dal trait se non piu necessario (o mantenerlo come fallback per campi extra dal DB)
- [x] Aggiornare lo stub delle migration (`App/Core/CLI/Stubs/migration.stub`) se genera model con fillable
- [x] Aggiornare eventuale documentazione

### Fase 7 - Riconoscimento tabella
- [x] Poter avere la possibilita' di riconoscere il nome della tabella dal nome del model, evitando di dover scrivere l'attributo come per esempiop     protected string $table = 'articles';


---

## Da implementare (tabelle non ancora utilizzate)

### Links Footer
- [ ] Creare Model `LinkFooter`
- [ ] Creare controller admin `LinkFooterMngController` con CRUD completo
- [ ] Creare vista admin per gestire i link del footer
- [ ] Integrare i link dinamici nel layout footer del sito (al posto di link statici)

### Visitors (Tracking visitatori)
- [ ] Creare Model `Visitor`
- [ ] Implementare middleware/listener che registra IP e user_agent ad ogni visita
- [ ] Creare dashboard admin con statistiche visitatori (visite uniche, browser, dispositivi)
- [ ] Grafici e report (visite giornaliere, settimanali, mensili)

### Portfolio (Tabella portfolio)
- [ ] Creare Model `Portfolio`
- [ ] Creare controller admin `PortfolioMngController` con CRUD
- [ ] Differenziare "portfolio" (showcase rapido con titolo, overview, link, stato deploy) da "projects" (dettaglio completo con tech e partner)
- [ ] Vista pubblica portfolio con filtro per stato deploy (online/offline)

### Project Technologies (Tabella pivot)
- [ ] Creare Model `ProjectTechnology`
- [ ] Nella form di creazione/modifica progetto, aggiungere selezione multipla delle tecnologie (molti-a-molti)
- [ ] Mostrare le tecnologie associate nella vista pubblica di ogni progetto
- [ ] Filtro progetti per tecnologia

---

## Da completare (parzialmente implementati)

### Technology
- [ ] Creare controller admin `TechnologyMngController` con CRUD
- [ ] Vista admin per aggiungere/modificare/eliminare tecnologie
- [ ] Icone o immagini per ogni tecnologia
- [ ] Pagina pubblica "Tech Stack" con tutte le tecnologie utilizzate

### Partners
- [ ] Creare controller admin `PartnerMngController` con CRUD
- [ ] Vista admin per gestire i partner
- [ ] Sezione pubblica "Partner / Collaborazioni" con link ai siti web
- [ ] Associazione visibile nei progetti (mostrare il partner nella vista progetto)

### Curriculum
- [ ] Creare controller admin `CurriculumMngController` con CRUD
- [ ] Upload e gestione file CV (PDF)
- [ ] Contatore download visibile in admin
- [ ] Bottone download CV nella pagina pubblica del portfolio

### Logs
- [ ] Aggiungere paginazione alla vista admin dei log
- [ ] Filtri per data, utente, dispositivo
- [ ] Possibilita di eliminare log vecchi (pulizia)
- [ ] Export log in CSV

---

## Funzionalita trasversali da sviluppare

### SEO e Metadata
- [ ] Slug automatici per progetti e articoli
- [ ] Meta description e og:image dinamici per ogni pagina
- [ ] Sitemap XML generata automaticamente

### Admin Dashboard
- [ ] Homepage admin con riepilogo: totale progetti, articoli, messaggi non letti, visite
- [ ] Widget statistiche visitatori (se implementato tracking)
- [ ] Notifiche per nuovi messaggi contatti

### Upload e Media
- [ ] Galleria immagini per i progetti (multiple immagini, non solo una)
- [ ] Ottimizzazione immagini (resize, compressione)
- [ ] Gestione file media centralizzata

### Contatti
- [ ] Segnare messaggi come letti/non letti
- [ ] Risposta diretta ai messaggi dall'admin
- [ ] Filtri per tipologia messaggio

### Articoli / Blog
- [ ] Sistema di tag o categorie
- [ ] Paginazione
- [ ] Ricerca full-text
- [ ] Editor rich-text per il contenuto

### Progetti
- [ ] Ordinamento drag-and-drop nell'admin
- [ ] Stato progetto (in corso, completato, in pausa)
- [ ] Data inizio/fine progetto
- [ ] Screenshot multipli

### Profile
- [ ] Aggiungere campi social (GitHub, LinkedIn, Twitter)
- [ ] Avatar/foto profilo
- [ ] Bio estesa con editor rich-text

### Sicurezza e Auth
- [ ] Rate limiting sui form pubblici (contatti, login)
- [ ] 2FA (autenticazione a due fattori)
- [ ] Gestione sessioni attive (visualizza e termina sessioni)

### Performance e UX
- [ ] Cache delle pagine pubbliche
- [ ] Lazy loading immagini
- [ ] Dark/light mode toggle
- [ ] Multilingua (i18n)

---

## Framework

### Scopo

Questa sezione consolida i task di evoluzione del framework direttamente dentro `todo.md`, evitando file di planning duplicati.

### Stato Attuale del Framework

- kernel HTTP con request, response, router, route loader, matcher e dispatcher
- discovery delle rotte tramite attributi
- ORM custom con `Model`, `ActiveQuery`, query builder, hydrator, migration e seeder
- layer di validazione basato su regole
- astrazione filesystem con driver e storage manager
- kernel CLI con generatori e comandi per migration/seeder
- mail layer, session storage, CSRF, middleware e tooling di debug

### Risultati Dell'Esplorazione

#### ORM

- L'ORM ha gia una buona separazione concettuale: builder, executor, hydrator, model.
- Il refactor recente da `fillable` a proprieta tipizzate va nella direzione giusta.
- L'API del model oggi mescola ancora proprieta dichiarate e `$attributes` dinamici.
- `save()` rischia di scrivere uno snapshot completo delle proprieta, incluse quelle ancora `null`.


#### CLI

- `MakeModelCommand` era un placeholder e va consolidato ulteriormente.
- Gli stub coprono meno di quanto il framework oggi esponga.
- La CLI dovrebbe diventare il livello principale di developer experience.

#### Routing e HTTP

- La pipeline delle rotte e ben separata in loader, matcher e dispatcher.
- In `Router` c'e ancora codice deprecato.
- Il route loader usa molta reflection e va coperto con test prima di aggiungere altre feature.
- [x] Aggiornare progressivamente i controller ai nuovi attributi di routing e metadata, con un design piu coerente e dichiarativo in stile Spatie

#### Validazione

- Il validator copre gia molte regole comuni e supporta messaggi custom.
- C'e spazio per integrazione con `Request`, payload validati tipizzati e miglior trasporto degli errori verso controller e viste.

#### Documentazione

- Le convenzioni interne non sono ancora sufficientemente fissate per chi lavora sul framework.

### Obiettivo Strategico

Portare il progetto da MVC custom funzionante a mini-framework coerente, con:

- comportamento dei model prevedibile
- ergonomia migliore per chi sviluppa
- scaffolding CLI piu forte
- copertura automatica piu affidabile
- convenzioni interne chiare

### Roadmap Operativa

#### Fase 1: Stabilizzare il Cuore dell'ORM

- [x] Aggiungere `columns(): array` nel model base come API canonica per leggere le proprieta dichiarate del model
- [x] Mantenere `getPersistableColumns()` come alias compatibile
- [x] Allineare `Attributes::__get()` e `Attributes::__set()` al nuovo modello con proprieta tipizzate
- [x] Lasciare le colonne DB sconosciute in `$attributes` come fallback
- [x] Correggere la semantica di `save()` per evitare che insert e update scrivano tutte le proprieta nullable non toccate
- [x] Aggiungere supporto al dirty-checking esplicito dei model
- [x] Aggiungere hook di casting per booleani, interi, date e colonne JSON
- [x] Aggiungere una strategia sicura di mapping proprieta-colonna per nomi riservati come `table`
- [x] Verificare il comportamento dell'hydrator con clone, proprieta tipizzate e colonne mappate
- [x] Scrivere test unitari per create, update, find, first, get, hydration e save

#### Fase 2: Completare il Refactor dei Model Tipizzati

- [x] Ricontrollare tutti i model e allineare i tipi alle colonne attese
- [x] Migliorare la struttura dei model evitando di rendere tutto nullable: il nullable deve rispecchiare lo schema del database ed essere usato solo dove la colonna e davvero nullable
- [ ] Introdurre nei model un tipo `String` avanzato come value object/wrapper al posto di `string` dove utile, per incapsulare funzionalita di tipizzazione e operazioni string piu evolute in stile Java
- [x] Correggere mismatch come `User::$created_at`, `Curriculum::$download`, `Token::$used`, `Profile::$selected` e `Certificate::$certified`
- [x] Rimuovere i riferimenti residui a `fillable` dalla documentazione ORM
- [x] Aggiornare `ORM.MD` per spiegare i model con proprieta tipizzate e reflection delle colonne
- [x] Documentare le regole per campi nullable e valori di default del database

#### Fase 3: Rendere la CLI Davvero Utile

- [x] Implementare la generazione reale dei file in `MakeModelCommand`
- [x] Aggiungere stub per model, controller, middleware, migration e seeder con le convenzioni attuali
- [x] Generare placeholder di proprieta tipizzate negli stub dei model
- [x] Aggiungere flag opzionali come `make:model --migration`, `make:model --resource` e `make:model --table=...`
- [x] Aggiungere un comando per ispezionare le rotte scoperte
- [x] Aggiungere un comando per ispezionare colonne del model e nome tabella inferito
- [x] Migliorare consistenza dell'output CLI e messaggi di errore
- [x] Aggiungere test sui comandi dove sensato
- [x] Quando un utente genera un controller da CLI, chiedere quali middleware applicare e quale prefix configurare sul controller generato

#### Fase 4: Rafforzare HTTP e Routing

- [x] Rimuovere o isolare i percorsi di routing deprecati in `Router`
- [x] Aggiungere test per caricamento rotte, ereditarieta, stack middleware e matching
- [x] Introdurre cache delle rotte per produzione
- [ ] Migliorare il reporting degli errori quando gli attributi sono malformati
- [x] Aggiungere supporto per nomi rotta, URL generation e ispezione delle rotte da CLI
- [ ] Standardizzare le firme dei metodi dei controller e l'iniezione di `Request`

#### Fase 4.b: Refactor Routing in stile Spatie

- [x] Introdurre attributi HTTP separati: `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]`
- [x] Mantenere `#[RouteAttr]` come layer di compatibilita temporaneo
- [x] Aggiungere attributi di gruppo come `#[Prefix]`, `#[Middleware]` e `#[NamePrefix]`
- [x] Permettere al `RouteLoader` di ignorare i metodi pubblici senza attributi di routing
- [x] Rimuovere la rigidita per cui ogni metodo pubblico del controller deve essere una rotta
- [ ] Rifattorizzare `RouteDefinition` per supportare meglio name prefix, metadata e futura route cache
- [ ] Estendere `RouteCollection` per route names, lookup piu robusto e serializzazione cache-friendly
- [x] Aggiungere URL generation basata su route name e parametri
- [x] Aggiungere un comando CLI `route:list`
- [x] Aggiungere test di compatibilita tra nuovi attributi HTTP e `RouteAttr`
- [x] Aggiungere test su prefix multipli, merge middleware e inheritance dei controller
- [x] Evitare l'uso di `AdminController` come contenitore centralizzato di `#[Prefix]` e `#[Middleware]`, spostando gli attributi nei controller concreti admin come `HomeManagerController`
- [ ] Valutare model binding o parameter binding piu evoluto nel dispatcher
- [ ] Eliminare definitivamente il codice routing deprecato da `Router` dopo la migrazione

#### Fase 5: Migliorare Validazione e Gestione della Request

- [x] Creare helper di validazione orientati alla request o classi stile form-request
- [x] Aggiungere accessor tipizzati in `Request`
- [ ] Supportare payload validati normalizzati per booleani, interi, array e file
- [x] Migliorare l'estendibilita del validator per regole custom
- [x] Standardizzare il trasporto degli errori di validazione verso sessione e viste
- [x] Aggiungere test di validazione su edge case e flussi nullable

#### Fase 6: Migliorare Storage e Upload

- [ ] Rafforzare i test per visibilita, generazione URL, sovrascrittura e cancellazione
- [ ] Standardizzare la gestione degli upload nei controller
- [ ] Aggiungere helper orientati alle immagini per asset di project e article
- [ ] Definire un contratto chiaro tra dischi pubblici e privati

#### Fase 7: Developer Experience e Convenzioni

- [ ] Scrivere una guida interna al lifecycle del framework
- [ ] Documentare convenzioni di naming per model, tabelle, migration, rotte e viste
- [ ] Aggiungere una checklist per nuovi contributi al framework
- [ ] Aggiungere comandi locali di qualita per lint e test
- [ ] Formalizzare la policy sui commenti: commenti descrittivi in inglese solo dove la logica non e ovvia

#### Fase 8: Refactor Emersi da `refactoring.md`
- una volta eseguito tutti i task, ripulire il file refactoring.md

- [ ] Centralizzare directory e path infrastrutturali evitando `__DIR__` e concatenazioni di path sparse nel codice
- [x] Aggiungere `declare(strict_types=1)` ai file framework che ancora non lo dichiarano in modo coerente
- [ ] Ridurre il boilerplate di `ActiveQuery` valutando una delega dinamica comune verso il builder
- [ ] Uniformare naming e firme dei comandi CLI, convergendo verso una API coerente (`execute`, parametri omogenei, convenzioni condivise)
- [ ] Uniformare il comportamento "not found" di `ActiveQuery` scegliendo un solo pattern coerente tra `null`, `false` ed eccezioni
- [ ] Scomporre `Model` estraendo responsabilita dedicate per casting, dirty tracking e schema resolution
- [ ] Valutare lo spostamento della logica di persistenza `save()` fuori da `ActiveQuery` verso un layer piu adatto
- [ ] Rendere sicuro `Attributes::__set()` evitando collisioni tra nomi proprieta e nomi metodo
- [x] Controllare il valore di ritorno di `mkdir()` e `file_put_contents()` nei generatori CLI e gestire correttamente gli errori filesystem
- [x] Estrarre in costanti i formati data hardcoded del model ed eliminare magic strings ripetute
- [x] Preservare stack trace e `previous exception` nelle `ModelException` lanciate da `ActiveQuery`
- [ ] Rendere configurabili le system columns del query builder invece di lasciarle hardcoded
- [x] Rendere `ValidateClassName` riusabile senza path hardcoded a `app/Middleware/`
- [x] Uniformare i path e il case delle directory usate dai comandi generatori CLI (`app/...` vs `App/...`)
- [ ] Correggere `SmtpProvider` per distinguere tra configurazione SMTP assente e configurazione presente ma invalida
- [ ] Allineare i test dei service al driver reale del PDO usato nei test (`sqlite` vs `mysql`) per evitare falsi positivi
- [ ] Rimuovere o riusare coerentemente la dipendenza `Response` da `SmtpProvider` se non serve piu
- [x] Sistemare `RouteDispatcher` valorizzando correttamente le proprieta usate nei messaggi di errore e rimuovendo il blocco commentato legacy
- [ ] Valutare rimozione o integrazione di `RouteRegister` se resta fuori dal flusso reale del router

### Task ad Alto Valore Immediato

1. Chiudere bene il refactor ORM verso le proprieta tipizzate.
2. Aggiungere il comando di ispezione rotte.
3. Aggiungere il comando di ispezione model/colonne.
4. Coprire il route loader con test.
5. Introdurre il refactor del routing verso attributi stile Spatie.
6. Espandere gli stub CLI mancanti.

### Milestone

#### Milestone A: Sicurezza ORM

- [x] `columns()` implementato
- [x] `Attributes` allineato
- [x] `save()` corretto
- [x] test di regressione ORM aggiunti

#### Milestone B: Scaffolding Framework

- [x] generatore model reale
- [ ] stub migliori
- [x] comando di ispezione rotte

#### Milestone C: Affidabilita del Framework

- [ ] test routing
- [ ] test validation
- [ ] test storage ampliati
- [x] documentazione ORM allineata al codice

#### Milestone D: Routing Moderno

- [x] attributi HTTP dedicati
- [x] compatibilita con `RouteAttr`
- [x] `route:list`
- [x] URL generation
- [ ] rimozione pipeline routing deprecated
