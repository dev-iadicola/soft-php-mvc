# Portfolio PHP MVC

> Framework MVC artigianale sviluppato da **Luigi Iadicola**, con un ORM proprietario, un motore di template interno e un router personalizzato.  
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


Il framework è stato costruito da zero per comprendere in modo pratico il funzionamento interno dei grandi framework come Laravel o Symfony.

---


## Design Pattern utilizzati 

### QueryBuilder
| Pattern | Descrizione |
|----------|-------------|
| **Builder** | Il `QueryBuilder` costruisce query SQL fluenti. |
| **Active Record** | Ogni Model gestisce la propria tabella e le operazioni CRUD. |
| **Template Method** | `AbstractBuilder` definisce la struttura comune dei Builder. |
| **Proxy / Delegation** | Il `Model` inoltra i metodi statici al `QueryBuilder`. |
| **Singleton (PDO)** | La classe `Database` gestisce una sola connessione attiva. |
| **Strategy ** | e in prospessitva: Possibile estensione futura per supportare dialetti SQL diversi. |
| **Facade**    | Possibilità di eseguire con il Facade Auth i servizi come AuthService e SessionStorage |

### Route Dinamiche (Novità!)
| Pattern                           | Ruolo nel framework                                                                                                                                 |
| --------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Attribute Pattern (Metadata)**  | Usa gli *Attributes* di PHP 8 per dichiarare le rotte e i middleware direttamente sopra i metodi del controller, eliminando configurazioni manuali. |
| **Reflection (Meta-programming)** | Permette di analizzare dinamicamente i controller, estrarre i metadati e costruire la mappa delle rotte in modo automatico.                         |
| **Registry Pattern**              | Il `RouteRegister` funge da archivio centralizzato delle rotte, indicizzate per metodo HTTP (`GET`, `POST`, ecc.).                                  |
| **Strategy Pattern**              | Implementato nel `RouteMatcher`, che può essere esteso con strategie di matching diverse (regex, path dinamici, nomi, ecc.).                        |
| **Dispatcher Pattern**            | Il `RouteDispatcher` applica il principio di *Separation of Concerns*: prima esegue i middleware, poi chiama l’action del controller.               |
| **Chain of Responsibility**       | I middleware vengono eseguiti in sequenza prima della logica del controller, come una catena di responsabilità.                                     |
| **Factory Pattern (implicit)**    | Ogni controller viene istanziato in modo dinamico dal dispatcher tramite Reflection o factory interna .                   |



# Sistema di Routing

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
#[AttributeRoute('progetti/{id}/{slug}', method: 'GET', middleware: ['auth'])]
public function show(Request $request, int $id, string $slug)
{
    $project = Project::find($id);
    return view('project', compact('project'));
}
```

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


