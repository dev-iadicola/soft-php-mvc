# Portfolio PHP MVC

> Framework MVC artigianale sviluppato da **Salvatore Iadicola**, con un ORM proprietario, un motore di template interno e un router personalizzato.  
> Scritto interamente in PHP per fini didattici e dimostrativi, con un'architettura estendibile, sicura e conforme ai principi SOLID.

---

## Introduzione

**Portfolio PHP MVC** è un mini-framework PHP progettato per offrire un ambiente di sviluppo leggero ma completo, integrando:

- un ORM (Object Relational Mapper) simile a *Eloquent*;  
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




# Design Pattern utilizzati 

# QueryBuilder
| Pattern | Descrizione |
|----------|-------------|
| **Builder** | Il `QueryBuilder` costruisce query SQL fluenti. |
| **Active Record** | Ogni Model gestisce la propria tabella e le operazioni CRUD. |
| **Template Method** | `AbstractBuilder` definisce la struttura comune dei Builder. |
| **Proxy / Delegation** | Il `Model` inoltra i metodi statici al `QueryBuilder`. |
| **Singleton (PDO)** | La classe `Database` gestisce una sola connessione attiva. |
| **Strategy ** | e in prospessitva: Possibile estensione futura per supportare dialetti SQL diversi. |
| **Facade**    | Possibilità di eseguire con il Facade Auth i servizi come AuthService e SessionStorage |

## Sistema ORM (Model & QueryBuilder)

Il sistema ORM è ispirato a *Eloquent* di Laravel, ma costruito interamente da zero.  
Segue un flusso chiaro e leggibile, dove ogni Model rappresenta una tabella e il `QueryBuilder` si occupa di generare e inviare le query SQL.

```text
[Model::find(1)]
      │
      ▼
 ┌────────────────┐
 │   Model.php    │ → Crea istanza e inoltra la chiamata statica al QueryBuilder
 └────────────────┘
      │
      ▼
 ┌──────────────────────┐
 │   QueryBuilder.php   │ → Costruisce la query SQL in modo fluente
 │   (SELECT * FROM ...)│
 └──────────────────────┘
      │
      ▼
 ┌────────────────┐
 │   Database.php │ → Singleton PDO che esegue la query
 └────────────────┘
      │
      ▼
[ResultSet / Model Hydration]
```

## Esempio di utilizzo 
```php
    // Recupera tutti i record
    $projects = Project::findAll();

    // Recupera un record per ID
    $project = Project::find(1);

    // Query più complesse in stile fluent
    $projects = Project::where('category', 'web')
                    ->orderBy('created_at', 'DESC')
                    ->get();

```

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

### Sintesi 
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
git clone https://github.com/dev-iadicola/portfolio-php-mvc.git
 - Questo repository non è una base del framwork, ma sarà aggiunto.
### Start Software
 1. composer install
 2. scrivi il comando `php soft serve`
 3. Inserisci le variabili nel file ENV


