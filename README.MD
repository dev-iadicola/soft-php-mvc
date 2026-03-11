# Soft PHP MVC

> Framework MVC artigianale sviluppato da **Salvatore Iadicola**, con un ORM proprietario, un motore di template interno e un router personalizzato.
> Scritto interamente in PHP per fini didattici e dimostrativi, con un'architettura estendibile, sicura e conforme ai principi SOLID.

---

## Introduzione

**Soft PHP MVC** è un mini-framework PHP progettato per offrire un ambiente di sviluppo leggero ma completo, integrando:

- un ORM proprietario con typed model, dirty tracking e query builder multi-driver;
- un motore di template PHP personalizzato con sintassi Blade-like;
- un sistema di routing basato su PHP 8 Attributes e Reflection;
- un sistema di validazione completo con 25+ regole;
- un CLI tool (`php soft`) per generazione codice, migrazioni, seeding e test;
- Facade per semplificare le operazioni;
- una gestione delle eccezioni estensibile tramite classi dedicate;
- un'architettura chiara in stile **MVC** (Model-View-Controller).

---

## Requisiti

- **PHP** >= 8.1
- **PDO** abilitato
- **Composer** per autoloading e dipendenze
- **Database MySQL** o **MariaDB** (PostgreSQL supportato a livello di query builder)
- Server locale (Docker, Laragon, XAMPP, o PHP built-in server)

## Installazione

```bash
git clone https://github.com/dev-iadicola/soft-php-mvc.git
cd soft-php-mvc
composer install
cp .env.example .env
# Configura le variabili nel file .env
php soft serve
```

---

## Struttura del Progetto

```
soft-php-mvc/
├── App/
│   ├── Core/                         # Framework core
│   │   ├── CLI/                      # Sistema CLI
│   │   │   ├── Kernel.php            # Registry e dispatcher dei comandi
│   │   │   ├── Commands/             # 18+ comandi (make, migrate, seed, serve, test)
│   │   │   ├── Stubs/               # Template per generazione codice
│   │   │   └── System/Out.php       # Output colorato nel terminale
│   │   ├── Connection/               # Gestione connessione database
│   │   ├── Contract/                 # Interfacce (QueryBuilder, Middleware, Command, Rule)
│   │   ├── Controllers/              # BaseController e Controller astratti
│   │   ├── DataLayer/                # ORM
│   │   │   ├── Model.php            # Base model con typed properties e dirty tracking
│   │   │   ├── Query/               # ActiveQuery, QueryBuilder, Executor, Hydrator
│   │   │   ├── Factory/             # ActiveQueryFactory, QueryBuilderFactory
│   │   │   ├── Runtime/             # ORM singleton (PDO + driver)
│   │   │   ├── Migration/           # Sistema di migrazioni
│   │   │   └── Seeder/              # Sistema di seeding
│   │   ├── Design/                   # Pattern (Singleton, Factory)
│   │   ├── Facade/                   # Auth, Session, View, Storage, RouteHelper
│   │   ├── Helpers/                  # Str, Log, Path
│   │   ├── Http/                     # Request, Response, Router, RouteLoader/Matcher/Dispatcher
│   │   │   └── Attributes/          # PHP 8 Attributes (#[RouteAttr], #[ControllerAttr])
│   │   ├── Middleware.php            # Pipeline middleware
│   │   ├── Provider/                 # DatabaseProvider, SmtpProvider, ErrorProviders
│   │   ├── Services/                 # AuthService, SessionStorage, CsrfService, PathResolver
│   │   ├── Strategy/                 # ITimeoutStrategy e implementazioni
│   │   ├── Traits/                   # Attributes, Relation, Getter, Setter
│   │   ├── Validation/               # Validator e RuleInterface
│   │   ├── View.php                  # Motore di template
│   │   ├── Config.php                # Loader .env e config/
│   │   └── Mvc.php                   # Application bootstrap
│   ├── Controllers/                  # Controller applicativi (Home, Portfolio, Progetti, ecc.)
│   ├── Middleware/                    # Middleware applicativi (Auth, CSRF, CORS, RateLimit, ecc.)
│   ├── Model/                        # 13+ modelli (User, Article, Project, Skill, ecc.)
│   ├── Mail/                         # Classi mailer
│   └── Utils/                        # Utility applicative
├── Database/
│   ├── migration/                    # 15+ file di migrazione
│   └── seed/                         # 10+ seeder
├── config/                           # Configurazione (settings, middleware, controllers, resources, storage)
├── views/                            # Template (layouts, pages, components)
├── assets/                           # CSS, JS, immagini
├── storage/                          # Log, cache, sessioni, file applicativi
├── tests/Unit/                       # Test PHPUnit (QueryBuilder, Model, Helpers, Storage, CLI)
├── utils/helpers.php                 # Funzioni helper globali
├── index.php                         # Entry point
├── composer.json                     # Dipendenze
├── phpunit.xml                       # Configurazione PHPUnit
├── rector.php                        # Configurazione Rector
├── phpstan.neon                      # Configurazione PHPStan livello 5
├── phpstan-baseline.neon             # Baseline errori preesistenti PHPStan
└── ORM.MD                            # Documentazione ORM
```

---

# Architettura Software

Il framework segue un approccio ibrido che unisce la chiarezza dell'**MVC**, la modularità della **Layered Architecture**, e la flessibilità della **Hexagonal Architecture**, guidato dai principi **SOLID**.

## Principi architetturali

### MVC (Model-View-Controller)
- **Model** -> logica di business e interazione con il database tramite ORM proprietario
- **View** -> motore di template PHP personalizzato con sintassi Blade-like
- **Controller** -> riceve la richiesta HTTP, invoca i Model, e restituisce la View o una Response

### Layered Architecture

| Layer | Responsabilita |
|-------|----------------|
| **Core Layer** | Router, Request, Response, Controller, View |
| **Domain Layer** | Models, ORM, QueryBuilder, Validation, Services |
| **Infrastructure Layer** | Database, Sessions, Logging, Autenticazione |
| **Presentation Layer** | Template engine e rendering |

### Hexagonal Architecture (Ports & Adapters)
- I **Controller** agiscono come adapter tra HTTP e il dominio interno
- I **Model** e i **Service** costituiscono il core domain
- Le classi infrastrutturali (`Database`, `SessionStorage`, `Logger`, `MailService`) rappresentano port sostituibili

### SOLID

| Principio | Applicazione |
|-----------|-------------|
| **S** - Single Responsibility | Ogni classe ha un solo scopo (`QueryBuilder`, `Router`, `SessionStorage`) |
| **O** - Open/Closed | Le classi possono essere estese senza modificarne il codice originale |
| **L** - Liskov Substitution | Servizi e adapter rispettano interfacce comuni |
| **I** - Interface Segregation | Interfacce piccole e specifiche (`ITimeoutStrategy`, `MiddlewareInterface`, `RuleInterface`) |
| **D** - Dependency Inversion | Il core dipende da astrazioni, non da implementazioni concrete |

---

# ORM Proprietario

L'ORM usa typed model properties come schema canonico per la persistenza. Non si basa piu su `fillable`.

## Architettura ORM

| Componente | Ruolo |
|-----------|-------|
| **Model** | Metadati tabella, typed properties, casting, dirty tracking |
| **ActiveQuery** | API fluent per query + coordinamento create/update/save |
| **QueryBuilder** | Generazione SQL e gestione binding (MySQL/PostgreSQL) |
| **QueryExecutor** | Esecuzione prepared statement tramite PDO |
| **ModelHydrator** | Conversione righe DB in istanze Model |

## Typed Models

Ogni modello dichiara le colonne come typed properties PHP. Le proprieta di framework (`$table`, `$primaryKey`, `$timestamps`, `$attributes`, `$original`, `$dirty`) sono escluse automaticamente tramite Reflection.

```php
class Article extends Model
{
    protected string $table = 'articles';
    protected int|string|null $id = null;
    protected ?string $title = null;
    protected ?string $subtitle = null;
    protected ?string $created_at = null;
}
```

## Column Mapping

Quando il nome della proprieta PHP non corrisponde alla colonna DB:

```php
protected function columnMap(): array
{
    return ['migrationTable' => 'migration_table'];
}
```

## Casting

```php
protected function casts(): array
{
    return [
        'selected' => 'bool',
        'download' => 'int',
        'meta'     => 'json',
    ];
}
```

Cast supportati: `bool`, `int`, `float`, `string`, `array`/`json`, `date`, `datetime`

## Dirty Tracking e Persistenza

`save()` usa dirty tracking intelligente:
- Nuovi modelli: INSERT solo degli attributi modificati
- Modelli esistenti: UPDATE solo degli attributi dirty
- Modelli non modificati: nessuna query emessa

```php
$article = new Article();
$article->title = 'Typed ORM';
$article->save();  // INSERT solo title + timestamps

$article->title = 'Updated';
$article->save();  // UPDATE solo title
```

API di dirty tracking:
- `isDirty(?string $key): bool`
- `getDirtyAttributes(): array`
- `getAttributesForInsert(): array`
- `getAttributesForUpdate(): array`
- `syncOriginal(): static`

## Query Builder (Fluent API)

```php
// Selezione e filtri
$articles = Article::query()
    ->select(['title', 'created_at'])
    ->where('title', '!=', '')
    ->whereNotNull('subtitle')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Find
$article = Article::find(1);
$article = Article::findOrFail(1);

// Join
$results = Article::query()
    ->leftJoin('users', 'articles.user_id', '=', 'users.id')
    ->where('users.active', true)
    ->get();

// Aggregazione
$count = Article::query()->where('published', true)->count();
$exists = Article::query()->where('slug', 'test')->exists();

// Debug
$sql = Article::query()->where('id', 1)->toSql();
$raw = Article::query()->where('id', 1)->toRawSql();
```

Metodi disponibili: `select`, `distinct`, `where`, `whereNot`, `orWhere`, `whereNull`, `whereNotNull`, `whereIn`, `whereNotIn`, `whereBetween`, `whereNotBetween`, `join`, `leftJoin`, `rightJoin`, `groupBy`, `having`, `orderBy`, `limit`, `offset`, `get`, `first`, `find`, `findOrFail`, `all`, `exists`, `count`, `pluck`, `create`, `update`, `delete`, `save`

## Relazioni

```php
// Nel Model
$user->hasOne(Profile::class, 'user_id');
$user->hasMany(Article::class, 'user_id');
$article->belongsTo(User::class, 'user_id');
$user->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
```

## Driver Supportati

| Driver | INSERT | UPDATE | DELETE | Peculiarita |
|--------|--------|--------|--------|-------------|
| **MySQL** | Standard | Standard | Standard | Timestamps automatici |
| **PostgreSQL** | `RETURNING id` | `RETURNING *` | `RETURNING *` | ILIKE, double-quote identifiers |

---

# Sistema di Routing

Il routing usa PHP 8 Attributes e Reflection per auto-descrivere le rotte direttamente nei controller, senza file di configurazione esterni.

## Componenti

| Componente | Ruolo | Pattern |
|-----------|-------|---------|
| **RouteLoader** | Scansiona i controller via Reflection e legge `#[RouteAttr]` | Attribute + Reflection |
| **RouteRegister** | Archivia le rotte per metodo HTTP | Registry |
| **RouteMatcher** | Converte `/path/{id}` in regex e cattura parametri | Strategy |
| **RouteDispatcher** | Esegue middleware e chiama il controller/action | Dispatcher + Chain of Responsibility |

## Flusso

```text
[HTTP Request]
      |
      v
 RouteLoader    -> Scansiona controller e legge attributi
      |
      v
 RouteRegister  -> Registra rotte per metodo HTTP
      |
      v
 RouteMatcher   -> Converte path in regex, cattura parametri
      |
      v
 RouteDispatcher -> Esegue middleware, chiama controller/action
      |
      v
[HTTP Response]
```

## Esempio

```php
#[ControllerAttr(['auth'])]
class HomeController extends Controller
{
    #[RouteAttr('progetti')]
    public function index()
    {
        $projects = Project::query()->get();
        $this->render(view: 'progetti', variables: compact('projects'));
    }

    #[RouteAttr('progetti/{id}')]
    public function show(Request $request, int $id)
    {
        $project = Project::findOrFail($id);
        view('progetto', compact('project'));
    }

    #[RouteAttr('contatti', 'POST')]
    public function sendForm(Request $request)
    {
        return view('contatti');
    }
}
```

---

# Motore di Template

Il view engine supporta una sintassi Blade-like con direttive personalizzate.

## Sintassi

| Sintassi | Output |
|----------|--------|
| `{{ $variable }}` | Output escaped (XSS-safe via `htmlspecialchars`) |
| `{{{ $variable }}}` | Output non escaped |
| `@include('path.to.partial')` | Include parziale (ricorsivo) |
| `@csrf` | Campo hidden con token CSRF |
| `@delete` / `@patch` / `@put` | Campo hidden per method override |

## Utilizzo

```php
// Dal controller
$this->render(view: 'progetti', variables: compact('projects'));

// Oppure tramite Facade
View::make('progetti', compact('projects'));

// Oppure tramite helper
view('progetti', compact('projects'));
```

---

# Sistema di Sessioni

## Architettura

| Pattern | Ruolo |
|---------|-------|
| **Singleton** | `SessionStorage` garantisce un'unica istanza globale |
| **Strategy** | `ITimeoutStrategy` permette logiche di timeout intercambiabili |
| **Facade** | `Session` e `Auth` semplificano le chiamate |
| **Template Method** | Flusso standard avvio -> verifica -> distruzione |

## Sicurezza

- Cookie HTTP-only (previene accesso JS)
- Strict session mode (previene fixation)
- Cookie secure su HTTPS
- SameSite=Lax (protezione CSRF)
- Verifica consistenza IP
- Tracciamento device (User-Agent)
- Timeout per inattivita

## Utilizzo

```php
use App\Core\Facade\Session;

Session::set('user', ['id' => 1, 'name' => 'Luigi']);
$user = Session::get('user');

// Flash messages (autodistruttivi dopo la lettura)
Session::setFlash('success', 'Operazione completata!');
$message = Session::getFlash('success');
```

---

# Sistema di Autenticazione

## AuthService

| Funzionalita | Descrizione |
|-------------|-------------|
| **Login** | Verifica credenziali, genera token, inizializza sessione |
| **Logout** | Distrugge sessione e invalida token |
| **Verifica** | Controlla autenticazione tramite `Auth::check()` |
| **Recupero utente** | `Auth::user()` restituisce l'utente autenticato |
| **Protezione IP** | Verifica consistenza indirizzo IP nella sessione |
| **Token** | Generazione token crittograficamente sicura via `random_bytes()` |

## Utilizzo

```php
use App\Core\Facade\Auth;

// Login
$user = User::query()->where('email', $email)->first();
Auth::login($user);

// Verifica
if (Auth::check()) {
    $currentUser = Auth::user();
}

// Logout
Auth::logout();
```

---

# Validator

## Utilizzo

La validazione applicativa usa `illuminate/validation`; [`App\Core\Validation\Validator`](/home/luigi/projects/soft-php-mvc/app/Core/Validation/Validator.php) delega a un adapter dedicato, così il progetto non dipende direttamente dalla libreria nei controller.

```php
use App\Core\Validation\Validator;

$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8|max:20',
]);

// Con messaggi personalizzati
$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8|max:20',
], [
    'email.required' => 'Inserisci il tuo indirizzo email.',
    'password.min' => 'La password deve avere almeno 8 caratteri.',
]);

// Con Closure
$validator = Validator::make($_POST, [
    'username' => [
        fn($value) => strlen($value) > 3 ?: 'Il nome utente deve essere lungo almeno 4 caratteri.'
    ]
]);

// Con classi di regole personalizzate
$validator = Validator::make($request->all(), [
    'name' => [new StartsWithUppercase()],
]);

// Verifica
if ($validator->fails()) {
    $errors = $validator->errors();
    $message = $validator->implodeError('<br>');
} else {
    $validated = $validator->validated();
}
```

## Regole Disponibili

| Regola | Descrizione |
|--------|-------------|
| `required` | Campo obbligatorio |
| `nullable` | Campo nullable (salta le altre regole se null) |
| `email` | Indirizzo email valido |
| `string` | Deve essere stringa |
| `numeric` | Valore numerico |
| `integer` | Numero intero |
| `decimal` | Numero decimale |
| `alpha` | Solo lettere |
| `alpha_num` | Lettere e numeri |
| `boolean` | Accetta true/false, 1/0, yes/no |
| `min:x` | Lunghezza minima |
| `max:x` | Lunghezza massima |
| `size:x` | Lunghezza esatta |
| `between:min,max` | Compreso tra due valori |
| `same:field` | Uguale ad un altro campo |
| `different:field` | Diverso da un altro campo |
| `confirmed` | Corrisponde al campo di conferma |
| `regex:/pattern/` | Pattern regex |
| `in:a,b,c` | Uno dei valori specificati |
| `not_in:a,b,c` | Nessuno dei valori specificati |
| `date` | Data valida |
| `url` | URL valido |
| `ip` | Indirizzo IP valido |
| `file` | File caricato correttamente |
| `mimes:jpg,png,pdf` | Estensione file consentita |
| `uppercase` | Contiene almeno una maiuscola |
| `number` | Contiene almeno un numero |
| `symbol` | Contiene almeno un carattere speciale |
| `array_min:x` | Array con almeno x elementi |
| `array_max:x` | Array con al massimo x elementi |

### Regole Personalizzate (RuleInterface)

```php
use App\Core\Validation\Rules\RuleInterface;

class StartsWithUppercase implements RuleInterface
{
    public function passes(string $field, $value, ?string $param = null): bool
    {
        return preg_match('/^[A-Z]/', $value);
    }

    public function message(string $field, ?string $param = null): string
    {
        return "Il campo {$field} deve iniziare con una lettera maiuscola.";
    }
}
```

---

# Middleware

## Architettura

I middleware seguono il pattern **Chain of Responsibility** e sono configurabili tramite gruppi.

## Gruppi (config/middleware.php)

| Gruppo | Middleware |
|--------|-----------|
| **web** | SecureHeaderMiddleware, CsrfMiddleware, MethodOverrideMiddleware, RateLimitMiddleware |
| **api** | CorsMiddleware, RateLimitMiddleware |
| **guest** | MaintenanceMiddleware |
| **auth** | AuthMiddleware |

## Middleware Disponibili

| Middleware | Funzione |
|-----------|----------|
| **AuthMiddleware** | Verifica autenticazione, timeout sessione, consistenza IP |
| **CsrfMiddleware** | Validazione token CSRF |
| **CorsMiddleware** | Headers Cross-Origin Resource Sharing |
| **RateLimitMiddleware** | Limitazione frequenza richieste |
| **SecureHeaderMiddleware** | Headers di sicurezza (X-Frame-Options, X-Content-Type-Options) |
| **MaintenanceMiddleware** | Pagina di manutenzione |
| **MethodOverrideMiddleware** | Override metodo HTTP (per PUT/PATCH/DELETE da form) |

## Applicazione

```php
// Tramite attributo sul controller
#[ControllerAttr(['auth'])]
class DashboardController extends Controller { }

// Tramite configurazione in config/middleware.php
```

---

# CLI Tool (`php soft`)

## Comandi Disponibili

| Comando | Descrizione |
|---------|-------------|
| `make:model <name>` | Crea Model (opzioni: `--migration`, `--resource`, `--table=name`) |
| `make:controller <name>` | Crea Controller |
| `make:mw <name>` | Crea Middleware |
| `make:migration <name>` | Crea file di migrazione con timestamp |
| `make:seeder <name>` | Crea file seeder |
| `migrate` | Esegue migrazioni pendenti |
| `migrate:rollback` | Rollback ultima batch di migrazioni |
| `migrate:status` | Mostra stato migrazioni |
| `seed` | Esegue i seeder |
| `seed:rollback` | Rollback dei seeder |
| `seed:status` | Mostra stato seeder |
| `serve` | Avvia server di sviluppo (opzioni: `--host`, `--port`, `--open`, `--https`) |
| `test` | Esegue test PHPUnit (opzioni: `--compact`, `--filter`, `--suite`, `--group`, `--coverage`, `--stop`) |
| `rector` | Esegue Rector (opzioni: `--dry`, `--dry-run`, `--clear`) |
| `analyse` | Esegue PHPStan (opzioni: `--baseline`) |
| `storage` | Gestione file storage |
| `clear:cache` | Pulisce la cache |

## Esempi

```bash
# Crea model con migrazione e controller
php soft make:model Post --migration --resource

# Esegue migrazioni
php soft migrate

# Avvia server sulla porta 9000
php soft serve --port 9000

# Esegue test con filtro
php soft test --filter testPlural --compact

# Esegue tutti i test con stop al primo errore
php soft test --stop

# Esegue Rector (aggiunge strict_types, type hints, ecc.)
php soft rector

# Preview modifiche Rector senza applicarle
php soft rector --dry

# Esegue analisi statica PHPStan livello 5
php soft analyse

# Genera baseline PHPStan per errori preesistenti
php soft analyse --baseline
```

---

# Facade

Le Facade forniscono un'interfaccia statica ai servizi sottostanti.

| Facade | Servizio | Metodi principali |
|--------|---------|-------------------|
| **Auth** | `AuthService` | `check()`, `login()`, `logout()`, `user()` |
| **Session** | `SessionStorage` | `get()`, `set()`, `has()`, `setFlash()`, `getFlash()`, `create()` |
| **View** | `Mvc->view` | `make(view, variables, message)` |
| **Storage** | `StorageManager` | `make(diskName)`, `dbPath(path, diskName)` |
| **RouteHelper** | Route registry | `getByName(name, params)` |

---

# Helpers

## Str (String Utilities)

Classe finale con metodi statici puri, 50+ metodi:

```php
use App\Core\Helpers\Str;

Str::camel('hello_world');        // 'helloWorld'
Str::snake('helloWorld');         // 'hello_world'
Str::kebab('helloWorld');         // 'hello-world'
Str::studly('hello_world');       // 'HelloWorld'
Str::slug('Hello World!');        // 'hello-world'
Str::plural('article');           // 'articles'
Str::singular('articles');        // 'article'
Str::contains('hello', 'ell');    // true
Str::limit('Long text...', 10);  // 'Long text...'
Str::random(32);                  // stringa hex crittograficamente sicura
```

## Log

```php
use App\Core\Helpers\Log;

Log::info('Messaggio informativo');
Log::error('Errore critico');
Log::debug('Debug info');  // solo in ambiente non-production
Log::exception($exception);
```

---

# Testing

## Framework

- **PHPUnit** 10.5+
- Configurazione in `phpunit.xml`
- Test in `tests/Unit/`

## Suite di Test

| Suite | Copertura |
|-------|-----------|
| **QueryBuilder** | Costruzione query SQL |
| **Model** | Typed properties, dirty tracking, casting, hydration, serializzazione |
| **Helpers/Str** | 30+ test per conversione case, pluralizzazione, slug, ecc. |
| **Storage** | StorageManager, Filesystem, LocalDrive, DriveFactory |
| **CLI** | MakeModelCommand (generazione stub, opzioni migration/resource) |

## Esecuzione

```bash
php soft test                              # Tutti i test
php soft test tests/Unit/Model/            # Suite specifica
php soft test --filter testDirtyTracking   # Filtro per nome
php soft test --compact --stop             # Compatto, stop al primo errore
php soft test --coverage                   # Con code coverage
```

---

# Design Pattern

| Pattern | Implementazione |
|---------|----------------|
| **Singleton** | SessionStorage, Database, Mvc |
| **Factory** | QueryBuilderFactory, ActiveQueryFactory |
| **Facade** | Auth, Session, View, Storage, RouteHelper |
| **Strategy** | ITimeoutStrategy per gestione timeout sessione |
| **Command** | Tutti i comandi CLI implementano CommandInterface |
| **Registry** | CLI Kernel, RouteRegister |
| **Chain of Responsibility** | Pipeline middleware |
| **Template Method** | AbstractBuilder per varianti QueryBuilder |
| **Adapter** | Controller come adapter HTTP |
| **Repository** | MigrationRepository, metodi query Model |
| **Attribute Pattern** | PHP 8 Attributes per routing e middleware |
| **Reflection** | RouteLoader per auto-discovery delle rotte |

---

# Dipendenze

## Produzione

| Pacchetto | Versione | Scopo |
|-----------|---------|-------|
| `phpmailer/phpmailer` | ^6.9 | Invio email |
| `filp/whoops` | ^2.18 | Gestione errori |
| `sendinblue/api-v3-sdk` | ^8.4 | API email Brevo |
| `symfony/var-dumper` | ^6.4 | Debug output |
| `nesbot/carbon` | ^3.11 | Gestione date |
| `ext-pdo` | * | Estensione PDO |

## Sviluppo

| Pacchetto | Versione | Scopo |
|-----------|---------|-------|
| `laravel/pint` | ^1.20 | Code style fixer |
| `phpunit/phpunit` | ^10.5 | Testing |
| `rector/rector` | ^2.3 | Refactoring automatico e strict_types |
| `phpstan/phpstan` | ^2.1 | Analisi statica livello 5 |

---

# Confronto con altri Framework

| Caratteristica | Symfony | Laravel | Soft MVC |
|---------------|---------|---------|----------|
| **Routing** | YAML/XML + Attributi opzionali | File `routes/web.php` | Solo PHP 8 Attributes, zero-config |
| **ORM** | Doctrine (DataMapper) | Eloquent (ActiveRecord) | ORM proprietario (typed properties, dirty tracking) |
| **Template** | Twig | Blade | Motore custom Blade-like |
| **Sessioni** | SessionInterface + handler | SessionManager | SessionStorage Singleton + Strategy |
| **Autenticazione** | Security Component | Auth + Guards | AuthService + Facade Auth |
| **Middleware** | Event Subscriber | PSR-15 Stack | Chain of Responsibility + Attributi |
| **CLI** | Console Component | Artisan | `php soft` con CommandInterface |
| **Validazione** | Validator Component | FormRequest / Validator | Validator con 25+ regole + Closure + RuleInterface |
| **Filosofia** | "Configuration is power" | "Expressive & elegant" | "Code is the configuration" |

---

> "When code begins to follow human thought,
> programming stops being an instruction — and becomes understanding."
>
> — *Salvatore Iadicola, 2025*
