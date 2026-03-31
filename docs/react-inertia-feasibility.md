# React + TypeScript + Inertia Feasibility Assessment

## Obiettivo

Valutare se il framework e l'applicazione attuale possono supportare una migrazione progressiva a `React + TypeScript + Inertia`, mantenendo:

- routing PHP custom
- SEO server-driven
- sessione, flash message e CSRF
- coesistenza tra view PHP legacy e nuove pagine Inertia
- separazione tra shell pubblica/guest e shell admin

## Esito

La migrazione e fattibile, ma va affrontata come integrazione incrementale e non come riscrittura totale immediata.

Il progetto ha gia una base abbastanza favorevole:

- bootstrap applicativo centralizzato in `Mvc`
- `Response` unico e condiviso
- router basato su attributi, gia separato in loader, matcher e dispatcher
- layout pubblici e admin gia distinti
- sessione, flash e CSRF gia presenti

Il lavoro principale non sta nel routing, ma in questi punti:

- introdurre un adapter Inertia sul layer HTTP
- creare una pipeline asset moderna (`Vite` + manifest)
- definire un contratto stabile di props serializzate tra backend e React
- ridurre accoppiamenti attuali a view PHP, script inline e helper globali

## Segnali Positivi

### 1. Il core HTTP e abbastanza estendibile

`Mvc` centralizza `Request`, `Response`, `Router` e `View`, quindi l'integrazione di una response Inertia puo essere fatta senza stravolgere il bootstrap.

Punti utili:

- `app/Core/Mvc.php`
- `app/Core/Http/Response.php`
- `app/Core/Http/Router.php`

`Response` gestisce gia:

- contenuto HTML
- JSON
- header custom
- redirect

Questa e una base utile per aggiungere:

- `X-Inertia`
- `X-Inertia-Version`
- JSON page object Inertia
- redirect compatibili con richieste Inertia

### 2. Il router custom non impedisce Inertia

Il router usa attributi PHP e reflection, ma non dipende dal motore view. Questo rende praticabile introdurre un nuovo tipo di risposta senza cambiare la definizione delle rotte.

Punti utili:

- `app/Core/Http/RouteLoader.php`
- `app/Core/Http/RouteDispatcher.php`
- `app/Core/Http/ControllerParameterResolver.php`

La risoluzione dei controller e gia sufficientemente semplice per ospitare un adapter Inertia.

### 3. Sessione, flash e CSRF esistono gia

Il framework possiede gia i mattoni che Inertia richiede lato server:

- sessione singleton
- flash messages
- old input / validation errors
- token CSRF disponibile anche come meta tag

Punti utili:

- `app/Core/Services/SessionStorage.php`
- `app/Core/Services/CsrfService.php`
- `app/Middleware/CsrfMiddleware.php`
- `utils/helpers.php`

Questo abbassa molto il rischio sul porting dei form.

### 4. Esiste gia una distinzione reale tra frontend pubblico e admin

La migrazione a due shell React distinte e coerente con la struttura attuale.

Punti utili:

- `views/layouts/default.php`
- `views/layouts/admin.php`

Questo supporta bene la futura separazione:

- `GuestLayout`
- `AdminLayout`

### 5. Il progetto ha gia endpoint JSON utili

Esistono gia endpoint JSON in admin, quindi il progetto non e rigidamente legato al solo rendering HTML.

Esempi:

- notifiche
- toggle active
- sort order

Questo aiuta per interazioni progressive nel frontend React.

## Rischi e Vincoli

### 1. Non esiste ancora una pipeline frontend moderna

Al momento non c'e:

- `package.json`
- `vite.config.*`
- build manifest
- entrypoint frontend

Inoltre l'helper `assets()` punta staticamente a `/assets/...`, quindi andra introdotto un resolver capace di leggere il manifest di Vite in produzione e il dev server in sviluppo.

Punti coinvolti:

- `utils/helpers.php`
- `views/layouts/default.php`
- `views/layouts/admin.php`

### 2. Il framework oggi e response-mutation driven

I controller spesso chiamano `view(...)` o `response()->redirect(...)`, che mutano la response globale; il `Router` oggi non usa il valore ritornato dal dispatcher.

Questo non blocca Inertia, ma significa che l'adapter va progettato nello stesso stile del framework, oppure va rifinita la semantica di dispatch prima dell'integrazione.

Punti coinvolti:

- `app/Core/Facade/View.php`
- `app/Core/Http/RouteDispatcher.php`
- `app/Core/Http/Router.php`

### 3. Flash e errori non sono ancora completamente unificati

Coesistono chiavi flash dirette (`success`, `error`, `warning`) e storage `_flash` per old input / validation errors.

Prima di una integrazione Inertia robusta conviene definire un contratto unico per:

- flash messages
- validation errors
- old input
- shared props globali

Punti coinvolti:

- `app/Core/Services/SessionStorage.php`
- `app/Core/Http/Response.php`
- `views/session/messages.php`

### 4. Le view attuali contengono molti script inline e CDN

Il layout pubblico e quello admin dipendono ancora molto da:

- CDN esterne
- script inline
- jQuery
- Bootstrap
- librerie agganciate direttamente alla pagina

Questo aumenta il lavoro di estrazione in componenti React e rende importante una migrazione graduale.

### 5. Manca ancora un contratto di serializzazione per i model verso il frontend

Il backend ha un ORM typed e ricco, ma non esiste ancora una convenzione chiara per esporre al frontend:

- model singoli
- collezioni
- paginazioni
- oggetti SEO
- breadcrumb
- auth user
- notification counts

Serve un layer esplicito di props/DTO, altrimenti la logica rischia di restare sparsa.

## Decisione Raccomandata

La migrazione dovrebbe essere:

- ibrida
- progressiva
- guidata dal backend
- page-by-page

Non e raccomandata una riscrittura big bang.

Ordine suggerito:

1. feasibility assessment
2. audit delle superfici frontend attuali
3. RFC architetturale
4. strategia di migrazione
5. adapter backend Inertia
6. toolchain frontend
7. bootstrap React + TypeScript
8. prima pagina Inertia isolata

## Perimetro consigliato del primo MVP tecnico

Il primo MVP Inertia dovrebbe fermarsi a:

- introdurre il protocollo Inertia nel backend
- installare la toolchain frontend minima
- creare `AppShell`, `GuestLayout` e `AdminLayout` base
- pubblicare una sola pagina dimostrativa Inertia
- mantenere intatte tutte le view PHP esistenti

Non dovrebbe ancora includere:

- porting completo delle pagine pubbliche
- CRUD admin complessi
- editor rich-text
- upload media
- sostituzione totale di jQuery/Bootstrap

## Impatto su SEO

La migrazione e compatibile con i requisiti SEO del progetto, a patto di mantenere:

- URL invariati
- meta server-driven
- canonical coerenti
- JSON-LD server-provided o comunque deterministico
- rendering iniziale corretto della pagina Inertia

Il rischio SEO maggiore non viene da Inertia in se, ma da un eventuale passaggio affrettato a un frontend che sposti troppo la responsabilita nel client.

## Conclusione

`React + TypeScript + Inertia` e una direzione sostenibile per questo progetto.

La base del framework non obbliga a una riscrittura del router o del core MVC; il lavoro vero consiste nel costruire bene:

- adapter HTTP Inertia
- pipeline assets
- shared props
- convenzioni layout
- confine tra legacy PHP views e nuove pagine React

La raccomandazione finale e quindi:

- procedere con il branch successivo `feature/react/frontend-surface-audit`
- non installare ancora tutte le librerie finali
- rinviare il bootstrap tecnico al termine dell'RFC
