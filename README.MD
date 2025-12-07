# Portfolio PHP MVC

> Framework MVC artigianale sviluppato da **Salvatore Iadicola**, con un ORM proprietario, un motore di template interno e un router personalizzato.  
> Scritto interamente in PHP per fini didattici e dimostrativi, con un'architettura estendibile, sicura e conforme ai principi SOLID.

---
<br><br><br>
## Introduzione

**Portfolio PHP MVC** è un mini-framework PHP progettato per offrire un ambiente di sviluppo leggero ma completo, integrando:

 
- un motore di template PHP personalizzato;  
- un sistema di routing flessibile e modulare;  
- una gestione delle eccezioni estensibile tramite classi dedicate;  
- un’architettura chiara in stile **MVC** (Model-View-Controller).
- Facade per semplificare le operazioni 


Il framework è stato costruito da zero.

---

# Architettura software e principi progettuali

Il framework **Portfolio PHP MVC** non adotta una singola architettura, ma un insieme di principi e strutture moderne che si integrano armoniosamente per ottenere un sistema scalabile, manutenibile e didatticamente chiaro.

---

## ⚙️ Architettura - i suoi principi

### **MVC (Model–View–Controller)**
È la base fondante del framework.  
Ogni livello ha un ruolo chiaro e indipendente:

- **Model** → gestisce la logica di business e interagisce con il database tramite l’ORM proprietario.  
- **View** → motore di template PHP personalizzato, per il rendering dinamico delle pagine.  
- **Controller** → riceve la richiesta HTTP, invoca i Model, e restituisce la View o una `Response`.

Questo approccio mantiene una separazione netta tra **presentazione**, **logica applicativa** e **dati**.

---

### **Layered Architecture**
L’architettura a strati estende l’MVC introducendo una **divisione logica in livelli** con responsabilità isolate:

| Layer | Responsabilità |
|--------|----------------|
| **Core Layer** | Gestione del ciclo HTTP (`Router`, `Request`, `Response`, `Controller`, `View`). |
| **Domain Layer** | Logica applicativa, ORM, QueryBuilder, servizi. |
| **Infrastructure Layer** | Connessione al database, logging, sessioni, autenticazione. |
| **Presentation Layer** | Template e rendering finale della risposta. |

 Ogni layer è indipendente, testabile e facilmente sostituibile.

---

### **Hexagonal Architecture (Ports & Adapters)**
Il framework è progettato secondo un approccio **esagonale**:
- I **Controller** agiscono come **adapter** tra l’esterno (HTTP) e il dominio interno.  
- I **Model** e i **Service** costituiscono il **core domain**, indipendente dall’infrastruttura.  
- Le classi come `Database`, `SessionStorage`, `Logger`, `MailService` rappresentano **port** che implementano interfacce e permettono di cambiare facilmente le dipendenze esterne.

Questo consente di cambiare motore di database, sistema di cache o logger senza toccare il core del framework.

---

### **SOA interna (Service-Oriented Architecture)**
Alcuni componenti seguono un approccio **a servizi indipendenti**:
- `AuthService`
- `SessionStorage`
- `Logger`
- `MailService`

Ciascun servizio è autonomo e comunicante tramite interfacce o Facade, rispettando il principio **“low coupling / high cohesion”**.

 In futuro, questi moduli potrebbero evolvere in microservizi o task separati.

---

### **Clean Architecture e principi SOLID**
Il framework è stato costruito applicando i **principi SOLID**, garantendo estendibilità e ordine progettuale:

| Principio | Applicazione nel framework |
|------------|----------------------------|
| **S – Single Responsibility** | Ogni classe ha un solo scopo (es. `QueryBuilder`, `Router`, `SessionStorage`). |
| **O – Open/Closed** | Le classi possono essere estese senza modificarne il codice originale. |
| **L – Liskov Substitution** | I servizi e gli adapter rispettano le interfacce comuni. |
| **I – Interface Segregation** | Le interfacce sono piccole e specifiche (`ITimeoutStrategy`, `MiddlewareInterface`). |
| **D – Dependency Inversion** | Il core dipende da astrazioni, non da implementazioni concrete. |

 Questi principi rendono il codice **robusto, estendibile e riusabile**.

---

##  Sintesi generale

| Architettura | Ruolo nel framework | Stato |
|---------------|---------------------|--------|
| **MVC** | Struttura principale (Model, View, Controller) | ✅ Implementata |
| **Layered Architecture** | Separazione tra Core, Domain, Infrastructure, Presentation | ✅ Implementata |
| **Hexagonal Architecture** | Indipendenza tramite Ports & Adapters | ⚙️ Parzialmente implementata |
| **SOA interna** | Servizi indipendenti (Auth, Session, Mail, Log) | ⚙️ Parziale |
| **Clean Architecture (SOLID)** | Linee guida architetturali e di progettazione | ✅ Forte influenza |

---

###  In sintesi
Il framework segue un approccio ibrido che unisce la chiarezza dell’**MVC**, la modularità della **Layered Architecture**,  
e la flessibilità della **Hexagonal Architecture**, guidato dai principi **SOLID** e da un forte focus sulla **manutenibilità del codice**.

> “Ogni componente conosce il proprio ruolo — e solo il proprio ruolo.”  
> — *Salvatore Iadicola, 2025*


____

<br><br><br><br>

# Design Pattern utilizzati 

# Sistema di Sessioni SessionStorage / AuthService / Session / Auth
Il sistema di sessioni in Soft MVC è progettato per offrire sicurezza, isolamento logico e compatibilità con il pattern architetturale del framework.
È interamente costruito sopra PHP nativo, ma introduce concetti avanzati come timeout automatico, flash session, e validazione sicura tramite interfacce dedicate

| Pattern             | Ruolo nel sistema                                                                                                                                                  |
| ------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **Singleton**       | La classe `SessionStorage` garantisce un’unica istanza globale di sessione, centralizzando gestione e sicurezza.                                                   |
| **Strategy**        | Implementato tramite `ITimeoutStrategy`, consente di sostituire la logica di timeout (es. inattività, tempo assoluto, ecc.) senza modificare la classe principale. |
| **Facade**          | Facade come Session e Auth. L’interfaccia `AuthService` utilizza `SessionStorage` come *facciata* per la gestione di autenticazione, messaggi flash e logout.                                  |
| **Template Method** | La sessione segue un flusso standard di avvio, verifica e distruzione, definito da metodi protetti e specializzabili.    

## Descrizione funzionale 
| Funzionalità                   | Descrizione                                                                                                                |
| ------------------------------ | -------------------------------------------------------------------------------------------------------------------------- |
| **Inizializzazione sicura**    | Attiva automaticamente `session_start()` con opzioni di sicurezza (cookie HTTP-only, `strict_mode`, `secure` su HTTPS).    |
| **Timeout automatico**         | Distrugge la sessione dopo un periodo di inattività (in futuro configurabile).                             |
| **Flash session**              | Memorizza messaggi temporanei (`success`, `error`, `warning`) che vengono eliminati automaticamente dopo la prima lettura da mostrare all'UI come falsh message. |
| **Strategia personalizzabile** | È possibile applicare strategie diverse di scadenza sessione tramite l’interfaccia `ITimeoutStrategy`.                     |
| **Distruzione controllata**    | `destroy()` elimina completamente i dati utente, logga l’operazione e resetta il contesto.                                 |

```text
[HTTP Request]
      │
      ▼
 ┌────────────────────┐
 │  SessionStorage    │ → Controlla se la sessione è attiva
 └────────────────────┘
      │
      ▼
 ┌────────────────────┐
 │ verifyTimeout()    │ → Verifica scadenza o inattività
 └────────────────────┘
      │
      ▼
 ┌────────────────────┐
 │ Flash Session      │ → Recupera e rimuove messaggi temporanei
 └────────────────────┘
      │
      ▼
 ┌────────────────────┐
 │ Sessione Attiva    │ → Accesso a dati utente, Auth, CSRF, ecc.
 └────────────────────┘
      │
      ▼
 ┌────────────────────┐
 │ destroy()          │ → Cancella tutto in modo sicuro e loggato
 └────────────────────┘
      │
      ▼
[Session End]
```
---
| Caratteristica         | Symfony                                                 | CodeIgniter                    | Laravel                                        | Soft MVC                                                       |
| ---------------------- | ------------------------------------------------------- | ------------------------------ | ---------------------------------------------- | -------------------------------------------------------------- |
| **Gestione sessione**  | Basata su `SessionInterface`, supporta file, DB o Redis | PHP nativo (`session_start()`) | `SessionManager` integrato con driver multipli | `SessionStorage` custom, Singleton + Facade                    |
| **Configurabilità**    | Alta, tramite `framework.yaml`                          | Limitata                       | Media/Alta con `config/session.php`            | Zero-config, auto-avvio intelligente                           |
| **Sicurezza cookie**   | Gestita da `SessionHandler`                             | Manuale                        | Automatica (Secure + HTTPOnly)                 | Automatica e forzata via `ini_set()`                           |
| **Flash messages**     | Manuale (`addFlash()`)                                  | Manuale (`set_flashdata`)      | Integrato (`session()->flash()`)               | Integrato e autodistruttivo (`setFlashSession`)                |
| **Timeout e scadenza** | Tramite listener evento                                 | Manuale                        | Richiede middleware custom                     | Integrato via `verifyInactivityTimeout()` e `ITimeoutStrategy` |
| **Architettura**       | Service-oriented, dipende dal container                 | Procedurale                    | Basata su Service Container                    | Autonoma, centralizzata e indipendente                         |
| **Pattern applicati**  | Strategy, Factory                                       | Singleton                      | Facade, Repository                             | Singleton, Strategy, Facade, Template Method                   |
| **Filosofia**          | Configurabile e modulare                                | Essenziale e diretta           | Espressiva e fluida                            | Sicura, autonoma, e leggibile                                  |
 
 ```php
    use App\Core\Services\SessionStorage;

    $session = SessionStorage::getInstance();

    // Imposta un valore
    $session->setOrCreate('user', ['id' => 1, 'name' => 'Luigi']);

    // Recupera un valore
    $user = $session->get('user');

    // Imposta un messaggio flash
    $session->setFlashSession('success', 'Login effettuato con successo!');

    // Recupera e rimuove il messaggio flash
    $message = $session->getFlashSession('success');

    // Distrugge la sessione in modo sicuro
    $session->destroy();

    // * IN FUTURO
    Session::setOrCreate('user', ['id' => 1, 'name' => 'Luigi'])
    Auth::login(User::find(1));
    Auth::logout(); // elimina le tracce dalla sessione.

```
>“Una sessione non è un contenitore di dati, ma un ponte tra due richieste.
>Il suo scopo non è conservare, ma dare continuità all’esperienza umana del codice.”
____
<br><br><br><br>
____
## Sistema di autenticazione AuthService
Il sistema di autenticazione di Soft MVC è costruito come servizio indipendente che si appoggia su SessionStorage e segue i principi SOA interna e Dependency Inversion, rendendolo facilmente sostituibile o estendibile.
| Pattern                  | Ruolo nel sistema                                                                                                      |
| ------------------------ | ---------------------------------------------------------------------------------------------------------------------- |
| **Facade**               | L’interfaccia `Auth` funge da facciata per interagire con `AuthService`, semplificando le chiamate nei controller.     |
| **Singleton**            | Il servizio utilizza `SessionStorage` singleton per mantenere lo stato utente.                                         |
| **Strategy (Timeout)**   | L’autenticazione si integra con `ITimeoutStrategy` per gestire la scadenza automatica della sessione.                  |
| **Template Method**      | Definisce il flusso standard `login → verify → logout`, mantenendo estendibilità per vari tipi di autenticazione.      |
| **Dependency Inversion** | `AuthService` non dipende da implementazioni concrete, ma da interfacce (`SessionInterface`, `UserProviderInterface`). |

| Funzionalità                    | Descrizione                                                                                     |
| ------------------------------- | ----------------------------------------------------------------------------------------------- |
| **Login**                       | Verifica le credenziali, genera una sessione sicura e registra timestamp di accesso.            |
| **Autenticazione persistente**  | Mantiene lo stato dell’utente tra le richieste tramite `SessionStorage`.                        |
| **Logout sicuro**               | Elimina tutti i dati di sessione, invalida il token e logga l’evento.                           |
| **Verifica autenticazione**     | Controlla se un utente è autenticato (`Auth::check()`).                                         |
| **Recupero utente**             | Ritorna i dati utente autenticato (`Auth::user()`).                                             |
| **Flash di benvenuto / errore** | Usa `SessionStorage` per messaggi temporanei come “Benvenuto Luigi” o “Credenziali non valide”. |

```text
[Login Form]
     │
     ▼
 ┌────────────────┐
 │ AuthService    │ → verifica credenziali
 └────────────────┘
     │
     ▼
 ┌────────────────┐
 │ SessionStorage │ → salva ID utente, ruoli e token
 └────────────────┘
     │
     ▼
 ┌────────────────┐
 │ Middleware Auth │ → controlla accesso a rotte protette
 └────────────────┘
     │
     ▼
 ┌────────────────┐
 │ Controller     │ → utente autenticato accede alla risorsa
 └────────────────┘
     │
     ▼
[HTTP Response]
```
| Caratteristica                      | Symfony                                                   | CodeIgniter                | Laravel                                | Soft MVC                                                 |
| ----------------------------------- | --------------------------------------------------------- | -------------------------- | -------------------------------------- | -------------------------------------------------------- |
| **Autenticazione**                  | Basata su `Security Component` con provider configurabili | Manuale via sessione       | Sistema completo con `Auth` e `Guards` | Servizio autonomo `AuthService` con Facade `Auth`        |
| **Gestione sessione**               | Integrata con session handler e firewall                  | Manuale (`$this->session`) | Integrata nel container                | Basata su `SessionStorage` (Singleton sicuro)            |
| **Middleware di protezione**        | Firewall, Voters, RoleHierarchy                           | Filtri nel controller      | Middleware `auth`                      | `AttributeMiddleware('auth')` eseguito prima della route |
| **Recupero utente**                 | Tramite `UserInterface`                                   | Array utente in sessione   | `Auth::user()`                         | `Auth::user()` semplice e diretto                        |
| **Login persistente (Remember Me)** | Integrato                                                 | Manuale                    | Integrato                              | Pianificato tramite cookie/token hash                    |
| **Ruoli e permessi**                | Avanzato (RoleHierarchy, voters)                          | Manuale                    | `Gate`, `Policy`                       | Da estendere via `PolicyInterface`                       |
| **Architettura**                    | Event-driven, containerizzata                             | Procedurale                | Layered, containerizzata               | Service Oriented + Facade                                |
| **Filosofia**                       | “Security is configuration”                               | “Manuale e veloce”         | “Expressive & fluent”                  | “Sicurezza integrata, senza complessità”                 |




### Differenze con altri Frameworks



| Caratteristica                      | Symfony                                                   | CodeIgniter                | Laravel                                | Soft MVC                                                 |
| ----------------------------------- | --------------------------------------------------------- | -------------------------- | -------------------------------------- | -------------------------------------------------------- |
| **Autenticazione**                  | Basata su `Security Component` con provider configurabili | Manuale via sessione       | Sistema completo con `Auth` e `Guards` | Servizio autonomo `AuthService` con Facade `Auth`        |
| **Gestione sessione**               | Integrata con session handler e firewall                  | Manuale (`$this->session`) | Integrata nel container                | Basata su `SessionStorage` (Singleton sicuro)            |
| **Middleware di protezione**        | Firewall, Voters, RoleHierarchy                           | Filtri nel controller      | Middleware `auth`                      | `AttributeMiddleware('auth')` eseguito prima della route |
| **Recupero utente**                 | Tramite `UserInterface`                                   | Array utente in sessione   | `Auth::user()`                         | `Auth::user()` semplice e diretto                        |
| **Login persistente (Remember Me)** | Integrato                                                 | Manuale                    | Integrato                              | Pianificato tramite cookie/token hash                    |
| **Ruoli e permessi**                | Avanzato (RoleHierarchy, voters)                          | Manuale                    | `Gate`, `Policy`                       | Da estendere via `PolicyInterface`                       |
| **Architettura**                    | Event-driven, containerizzata                             | Procedurale                | Layered, containerizzata               | Service Oriented + Facade                                |
| **Filosofia**                       | “Security is configuration”                               | “Manuale e veloce”         | “Expressive & fluent”                  | “Sicurezza integrata, senza complessità”                 |




_________
<br><br>
_________

# Route Dinamiche (Novità!)
| Pattern                           | Ruolo nel framework                                                                                                                                 |
| --------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Attribute Pattern (Metadata)**  | Usa gli *Attributes* di PHP 8 per dichiarare le rotte e i middleware direttamente sopra i metodi del controller, eliminando configurazioni manuali. |
| **Reflection (Meta-programming)** | Permette di analizzare dinamicamente i controller, estrarre i metadati e costruire la mappa delle rotte in modo automatico.                         |
| **Registry Pattern**              | Il `RouteRegister` funge da archivio centralizzato delle rotte, indicizzate per metodo HTTP (`GET`, `POST`, ecc.).                                  |
| **Strategy Pattern**              | Implementato nel `RouteMatcher`, che può essere esteso con strategie di matching diverse (regex, path dinamici, nomi, ecc.).                        |
| **Dispatcher Pattern**            | Il `RouteDispatcher` applica il principio di *Separation of Concerns*: prima esegue i middleware, poi chiama l’action del controller.               |
| **Chain of Responsibility**       | I middleware vengono eseguiti in sequenza prima della logica del controller, come una catena di responsabilità.                                     |
| **Factory Pattern (implicit)**    | Ogni controller viene istanziato in modo dinamico dal dispatcher tramite Reflection o factory interna .                   |

## Dettaglio dei componenti 
| Componente | Ruolo | Pattern applicato |
|-------------|-------|------------------|
| **RouteLoader** | Scansiona ricorsivamente la directory dei controller tramite *Reflection*, legge gli attributi `#[AttributeRoute]` e `#[AttributeMiddleware]`, e costruisce la lista piatta delle rotte. | *Attribute Pattern* + *Reflection* |
| **RouteRegister** | Archivia tutte le rotte organizzandole per metodo HTTP (`GET`, `POST`, ecc.) e le espone in memoria per un accesso veloce. | *Registry Pattern* |
| **RouteMatcher** | Analizza l’URI richiesta, converte i percorsi come `/progetti/{id}` in regex (`~^/progetti/([^/]+)$~`) e associa i parametri catturati ai nomi definiti. | *Strategy Pattern* |
| **RouteDispatcher** | Gestisce il ciclo finale della richiesta: esegue i middleware in ordine, istanzia il controller, inietta la `Request` e invoca l’action corrispondente. | *Dispatcher Pattern* + *Chain of Responsibility* |

---

## Sistema di Routing

Il sistema di routing segue una catena ben definita di componenti che gestiscono il ciclo di vita di una richiesta HTTP, dall’input dell’utente alla risposta del controller.

```text
[HTTP Request]
      │
      ▼
 ┌───────────────┐
 │ RouteLoader   │ → Scansiona i controller e legge gli attributi
 └───────────────┘
      │
      ▼
 ┌───────────────┐
 │ RouteRegister │ → Registra tutte le rotte per metodo HTTP
 └───────────────┘
      │
      ▼
 ┌───────────────┐
 │ RouteMatcher  │ → Converte i path in regex e cattura i parametri dinamici
 └───────────────┘
      │
      ▼
 ┌────────────────┐
 │ RouteDispatcher│ → Esegue middleware e chiama il controller/action
 └────────────────┘
      │
      ▼
[HTTP Response]
```

---

## Esempio di utilizzo 
```php
#[AttributeMiddleware('auth')]
class HomeController extends Controller{
    
    #[AttributeRoute('progetti')]
    public function index() {
     $projects = Project::findAll();
        
        $this->render(view: 'progetti',  variables: compact('projects' ));
    }

    #[AttributeRoute('progetti/{id}')]
    public function show(Request $request, int $id){
         $project = Project::find($id);  
         $projects = Project::findAll();
         view('progetto', compact('project', "projects"));
    }
     #[AttributeRoute('contatti', 'POST')]
    public function sendForm(Request $request)
    {
        // Codice della gestione contatti...


        return view('contatti');
    }
}
```
---
>“When code begins to follow human thought,
>programming stops being an instruction — and becomes understanding.”

>
---

## Differenze con altri Frameworks 
Il sistema di routing e l’architettura di Soft si collocano a metà tra l’approccio minimalista di Laravel e la struttura modulare di Symfony, combinando chiarezza, riflessione dinamica e assenza di configurazione esterna.
Di seguito una panoramica comparativa dei principali framework PHP moderni:
| Caratteristica                | Symfony                                                                             | CodeIgniter                                                     | Laravel                                                         | Soft (Portfolio MVC)                                                                                                          |
| ----------------------------- | ----------------------------------------------------------------------------------- | --------------------------------------------------------------- | ------------------------------------------------------------------------------------------ | ----------------------------------------------------------------------------------------------------------------------------- |
| **Dichiarazione delle rotte** | In file YAML, XML o tramite attributi (`#[Route('/path', name: '...')]`)            | In file PHP con funzioni `$route['path'] = 'Controller/method'` | In file `routes/web.php` con closure o controller (`Route::get('/path', [C::class, 'm'])`) | Solo con **AttributeRoute**, nessun file esterno. Le rotte vivono direttamente nel controller.                                |
| **Caricamento delle rotte**   | Basato su `RouteCollection` e `RouteLoader` configurati nel `Kernel`                | Statico, caricato all’avvio dell’app                            | Cache automatica (`php artisan route:cache`)                                               | Basato su `RouteLoader` personalizzato che usa *Reflection* per leggere dinamicamente i controller.                           |
| **Matching dell’URI**         | Usa il `UrlMatcher` con *compiled regex cache*                                      | Basato su regex basilari, senza parametri dinamici complessi    | Regex dinamiche + caching ottimizzato                                                      | Usa un `RouteMatcher` che trasforma `/path/{id}` → regex runtime con supporto parametri dinamici e caching pianificato.       |
| **Middleware**                | Usa `EventSubscriber` e `Kernel Events` (`kernel.request`, `kernel.response`, ecc.) | Middleware registrati manualmente nel `Config/Filters.php`      | Stack PSR-15 gestito centralmente                                                          | Sistema **Chain of Responsibility** basato su attributi `#[AttributeMiddleware]`, leggibile e indipendente dal kernel.        |
| **Dependency Injection**      | Integrata nel Service Container Symfony                                             | Assente o manuale tramite costruttori                           | Completa (IoC Container)                                                                   | Possibile evoluzione futura del `RouteDispatcher` (ora usa `mvc()` come service locator, pronto per DI).                      |
| **Configurazione**            | Centralizzata in `/config/routes/*.yaml` + Attributes opzionali                     | Tutto in un file globale `app/Config/Routes.php`                | Configurazione multipla con cache e Facade                                                 | Completamente **zero-config**, ogni controller si auto-descrive tramite attributi chiari e leggibili.                         |
| **Cache del routing**         | Compila le rotte in PHP (directory `var/cache/`)                                    | Nessuna cache, parsing runtime                                  | Cache precompilata con `route:cache`                                                       | Attualmente runtime, ma predisposto per **route cache automatica** con file serializzato o precompilato.                      |
| **Architettura software**     | Hexagonal / Event-driven                                                            | MVC puro e minimale                                             | Layered / Service Container                                                                | Layered MVC + Reflection-driven con *Registry, Strategy e Dispatcher Pattern*.                                                |
| **Filosofia**                 | “Configuration is power” → forte astrazione e containerizzazione                    | “Semplicità e velocità” → focus sull’immediatezza               | “Expressive, elegant syntax” → convenzioni e sintassi fluenti                              | “Code is the configuration” → leggibilità, immediatezza e flusso logico umano. Il codice segue il pensiero, non il contrario.                                                     |
### Sintesi 
>Symfony privilegia la configurazione e la modularità per grandi sistemi enterprise.
>CodeIgniter si concentra sulla leggerezza e sulla velocità di esecuzione.
>Laravel offre una sintassi elegante, ma richiede diversi layer e caching manuale.
>Soft (Portfolio MVC) elimina la configurazione esterna e riflette direttamente il pensiero umano nel codice.
>In Soft, ogni controller è auto-descrittivo:
>la rotta, i middleware e la logica convivono nello stesso contesto, rendendo il flusso leggibile e naturale.

>"Dove gli altri framework “configurano” le rotte, Soft le comprende."

---
<br><br><br>
___

# Validator 

## utilizzo 
```php
use App\Core\Validation\Validator;

$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8|max:20',
]);

// avanzato con messaggi personalizzati
$validator = Validator::make($request->all(), [
    'email' => 'required|email',
    'password' => 'required|min:8|max:20',
], [
    'email.required' => 'Inserisci il tuo indirizzo email.',
    'email.email' => 'L\'indirizzo email non è valido.',
    'password.required' => 'La password è obbligatoria.',
    'password.min' => 'La password deve avere almeno 8 caratteri.',
]);

// Utilizzo Closure 
$validator = Validator::make($_POST, [
    'username' => [
        fn($value) => strlen($value) > 3 ?: 'Il nome utente deve essere lungo almeno 4 caratteri.'
    ]
]);

// Utilizzo di Classi di Regole Personalizzate con esempio
namespace App\Core\Validation\Rules;

interface RuleInterface {
    public function passes(string $field, $value, ?string $param = null): bool;
    public function message(string $field, ?string $param = null): string;
}
use App\Core\Validation\Rules\RuleInterface;

class StartsWithUppercase implements RuleInterface {
    public function passes(string $field, $value, ?string $param = null): bool {
        return preg_match('/^[A-Z]/', $value);
    }

    public function message(string $field, ?string $param = null): string {
        return "Il campo {$field} deve iniziare con una lettera maiuscola.";
    }
}


$validator = Validator::make($request->all(), [
    'name' => [new StartsWithUppercase()],
]);


//verifica e stampa errori
if ($validator->fails()) {
    print_r($validator->errors());
} else {
    echo "Validazione superata!";
}


```
## lista di regole
| Regola              | Descrizione                                         |
| ------------------- | --------------------------------------------------- |
| `required`          | Il campo è obbligatorio                             |
| `email`             | Deve essere un indirizzo email valido               |
| `min:x`             | Lunghezza minima                                    |
| `max:x`             | Lunghezza massima                                   |
| `numeric`           | Deve essere un valore numerico                      |
| `integer`           | Deve essere un numero intero                        |
| `decimal`           | Deve essere un numero decimale                      |
| `alpha`             | Solo lettere                                        |
| `alpha_num`         | Solo lettere e numeri                               |
| `boolean`           | Accetta true/false, 1/0, yes/no                     |
| `same:altro`        | Deve essere uguale ad un altro campo                |
| `different:altro`   | Deve essere diverso da un altro campo               |
| `regex:/pattern/`   | Deve rispettare un pattern regex                    |
| `in:a,b,c`          | Deve essere uno dei valori specificati              |
| `not_in:a,b,c`      | Non deve essere uno dei valori specificati          |
| `size:x`            | Deve avere lunghezza esatta                         |
| `between:min,max`   | Deve essere compreso tra due valori                 |
| `date`              | Deve essere una data valida                         |
| `url`               | Deve essere un URL valido                           |
| `ip`                | Deve essere un indirizzo IP valido                  |
| `file`              | Deve essere un file caricato correttamente          |
| `mimes:jpg,png,pdf` | Il file deve avere una delle estensioni specificate |
| `uppercase`         | Deve contenere almeno una lettera maiuscola         |
| `number`            | Deve contenere almeno un numero                     |
| `symbol`            | Deve contenere almeno un carattere speciale         |
| `array_min:x`       | L’array deve contenere almeno x elementi            |
| `array_max:x`       | L’array può contenere al massimo x elementi         |

___
<br><br><br>

# Conclusioni 
Il sistema di routing e l’intera architettura del framework sono costruiti per rispettare i principi:

SOLID

Dependency Inversion

Open/Closed Principle

Separation of Concerns

Convention over Configuration

Questo approccio rende il framework estendibile, scalabile e adatto come base per progetti più complessi o come laboratorio didattico di architettura software in PHP.

# Requisiti

- **PHP** ≥ 8.1-8.4  
- **PDO** abilitato  
- **Composer** per autoloading e sistemi smtp, API BREVO 
- **Database MySQL** al momento il querybuilder supporta solo MYSQL e MariaDB
- Server locale (Docker, Laragon, XAMPP, o PHP built-in server)
##  Installazione

### Clona il progetto
```bash
git clone https://github.com/dev-iadicola/soft-php-mvc.git
 - Questo repository non è una base del framwork, ma sarà aggiunto.
### Start Software
 1. composer install
 2. scrivi il comando `php soft serve`
 3. Inserisci le variabili nel file ENV


