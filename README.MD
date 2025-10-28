# Portfolio PHP MVC

> Framework MVC artigianale sviluppato da **Luigi Iadicola**, ispirato a Laravel, con un ORM proprietario, un motore di template interno e un router personalizzato.  
> Scritto interamente in PHP per fini didattici e dimostrativi, con un'architettura estendibile, sicura e conforme ai principi SOLID.

---

## Introduzione

**Portfolio PHP MVC** è un mini-framework PHP progettato per offrire un ambiente di sviluppo leggero ma completo, integrando:

- un ORM (Object Relational Mapper) simile a *Eloquent*;  
- un motore di template PHP personalizzato;  
- un sistema di routing flessibile e modulare;  
- una gestione delle eccezioni estensibile tramite classi dedicate;  
- un’architettura chiara in stile **MVC** (Model-View-Controller).

Il framework è stato costruito da zero per comprendere in modo pratico il funzionamento interno dei grandi framework come Laravel o Symfony.

---


## Design Pattern utilizzati 

| Pattern | Descrizione |
|----------|-------------|
| **Builder** | Il `QueryBuilder` costruisce query SQL fluenti. |
| **Active Record** | Ogni Model gestisce la propria tabella e le operazioni CRUD. |
| **Template Method** | `AbstractBuilder` definisce la struttura comune dei Builder. |
| **Proxy / Delegation** | Il `Model` inoltra i metodi statici al `QueryBuilder`. |
| **Singleton (PDO)** | La classe `Database` gestisce una sola connessione attiva. |
| **Strategy (in prospettiva)** | Possibile estensione futura per supportare dialetti SQL diversi. |

---

# ⚙️ Requisiti

- **PHP** ≥ 8.1  
- **PDO** abilitato  
- **Composer** per autoloading  
- Server locale (Laragon, XAMPP, o PHP built-in server)
##  Installazione

### Clona il progetto
```bash
git clone https://github.com/dev-iadicola/portfolio-php-mvc.git
 - Questo repository non è una base del framwork, ma sarà aggiunto.
### Start Software
 1. composer install
 2. scrivi il comando `php soft serve`
 3. Inserisci le variabili nel file ENV


