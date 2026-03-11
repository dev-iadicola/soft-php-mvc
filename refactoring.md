# Refactoring Report - Ultimi 3 Commit

## Regola di lavoro

A ogni nuova sessione deve essere eseguita una code review iniziale dei commit e delle modifiche recenti.
Tutti i problemi trovati, le opportunita di refactor, le priorita, le decisioni e i follow-up devono essere scritti qui in `refactoring.md`.

Analisi dei commit:
1. `3abb62f` - chore(git): ignore local planning and workspace files
2. `99556fd` - feat(cli): improve model generation and test command
3. `28e0512` - refactor(orm): use typed model properties as schema

---

## 1. CRITICO - Vulnerabilita e Bug

### 1.1 Command Injection in `TestCommand.php`
**File:** `App/Core/CLI/Commands/TestCommand.php` - `buildCommand()`
Il parametro `$args['path']` e gli elementi di `$args['extra']` vengono passati direttamente a `passthru()` senza `escapeshellarg()`, mentre `filter`, `suite` e `group` sono correttamente escaped.

```php
// PROBLEMA (attuale)
$cmd .= ' ' . $args['path'];

// FIX
$cmd .= ' ' . escapeshellarg($args['path']);
```

### 1.2 Bug SQL in `AbstractBuilder.php` - `whereIn()` / `whereNotIn()`
**File:** `App/Core/DataLayer/Query/AbstractBuilder.php` (~linee 275-300)
La clausola viene costruita come `$column {$this->getPrefix()} IN(...)` che produce SQL invalido tipo `column_name WHERE IN(...)` invece di `WHERE column_name IN(...)`.

### 1.3 Esecuzione continua dopo errore in `TestCommand.php`
**File:** `App/Core/CLI/Commands/TestCommand.php` (linee 21-27)
Quando PHPUnit o `phpunit.xml` non vengono trovati, `Out::error()` viene chiamato ma l'esecuzione **continua** (manca `return`).

### 1.4 Esecuzione continua dopo errore in `Kernel.php`
**File:** `App/Core/CLI/Kernel.php` - `validateCommand()`
Stessa problematica: `Out::error()` alle linee 93 e 98 non blocca l'esecuzione.

---

## 2. IMPORTANTE - Codice Duplicato

### 2.1 Liste duplicate in `Str.php`
**File:** `App/Core/Helpers/Str.php`
Gli array `$uncountable` e `$irregulars` sono definiti identicamente sia in `plural()` (~linea 392) che in `singular()` (~linea 460). Dovrebbero essere `private const` a livello di classe.

```php
// FIX: estrarre come costanti di classe
private const UNCOUNTABLE = ['sheep', 'fish', 'deer', ...];
private const IRREGULARS = ['man' => 'men', 'woman' => 'women', ...];
```

### 2.2 Boilerplate massivo in `ActiveQuery.php`
**File:** `App/Core/DataLayer/Query/ActiveQuery.php`
~20 metodi (`where`, `whereNot`, `orWhere`, `whereNull`, `whereIn`, `orderBy`, `limit`, ecc.) sono deleghe di una riga a `$this->builder`. Un `__call()` magic method eliminerebbe ~150 righe.

```php
public function __call(string $method, array $args): static
{
    if (method_exists($this->builder, $method)) {
        $this->builder->$method(...$args);
        return $this;
    }
    throw new \BadMethodCallException("Method {$method} not found");
}
```

### 2.3 `whereIn()` / `whereNotIn()` quasi identici in `AbstractBuilder.php`
**File:** `App/Core/DataLayer/Query/AbstractBuilder.php`
Stessa logica duplicata, differiscono solo per `IN` vs `NOT IN`. Estrarre metodo privato comune.

### 2.4 `whereBetween()` / `whereNotBetween()` duplicati
Stesso discorso del punto precedente.

### 2.5 `normalizeClassName()` in `MakeModelCommand.php` reimplementa `Str::studly()`
**File:** `App/Core/CLI/Commands/MakeModelCommand.php` (linea 105)
`Str` e gia importato ma `studly()` non viene usato.

---

## 3. IMPORTANTE - Inconsistenze

### 3.1 `declare(strict_types=1)` mancante
File che non lo dichiarano (mentre altri si):
- `App/Core/DataLayer/Query/AbstractBuilder.php`
- `App/Core/DataLayer/Factory/ActiveQueryFactory.php`
- `App/Core/Traits/Attributes.php`
- `App/Core/CLI/Kernel.php`

### 3.2 Typo negli identificatori
| File | Typo | Correzione |
|------|------|------------|
| `Kernel.php` | `$istance` | `$instance` |
| `AbstractBuilder.php` | `$inCluase` | `$inClause` |
| `ActiveQueryFactory.php` | `getDrive()` | `getDriver()` |
| `ActiveQuery.php` | `form()` | `from()` |
| `ActiveQuery.php` | `tosql()` (in `findAll`) | `toSql()` |

### 3.3 Naming inconsistente tra comandi
- `MakeModelCommand` usa `$command` come parametro
- `MakeControllerCommand` usa `$params` come parametro
- Entrambi implementano `CommandInterface::exe()` - meglio `execute()`

### 3.4 Pattern inconsistente per "not found"
`ActiveQuery`: `find()` ritorna `?Model` (null), `findOrFalse()` ritorna `bool|Model` (false). Scegliere un pattern unico.

### 3.5 Template inline vs stub file
- `MakeModelCommand` usa un file stub (`model.stub`)
- `MakeControllerCommand` usa un heredoc inline
Uniformare l'approccio.

---

## 4. MIGLIORAMENTI - Design e Responsabilita

### 4.1 `Model.php` - God Object
**File:** `App/Core/DataLayer/Model.php` (~322 linee)
Gestisce: attributi, dirty tracking, casting, serializzazione, schema introspection, column mapping, e query initiation. Estrarre:
- **CastManager** per la logica di casting
- **DirtyTracker** per il dirty tracking
- **SchemaResolver** per la reflection-based schema resolution

### 4.2 `AbstractBuilder.php` - Classe troppo grande (~488 linee)
Gestisce SELECT, WHERE, JOIN, INSERT, UPDATE, DELETE, ORDER BY, GROUP BY, HAVING, LIMIT, OFFSET, binding, timestamp. Considerare decomposizione in clause builders separati.

### 4.3 `ActiveQuery::save()` - Responsabilita fuori posto
La logica insert-or-update appartiene a un Repository/UnitOfWork, non al query object.

### 4.4 `Attributes::__set()` - Pattern pericoloso
**File:** `App/Core/Traits/Attributes.php` (linea 41)
`if (method_exists($this, $key))` usa il nome della proprieta come nome metodo. Se un model ha una proprieta `save` o `delete`, `$model->save = 'value'` chiamerebbe `$this->save('value')`.

---

## 5. MINORI - Error Handling e Magic Values

### 5.1 Operazioni filesystem non verificate
`file_put_contents()` e `mkdir()` in `MakeModelCommand` e `MakeControllerCommand` non controllano il valore di ritorno.

### 5.2 Magic strings in `Model.php`
Formati data `'Y-m-d'` e `'Y-m-d H:i:s'` hardcoded in `castAttribute()`. Estrarre come costanti.

### 5.3 Stack trace perso in `ActiveQuery.php`
In `create()` e `update()`, il catch fa `throw new ModelException($e . ' for Model ' ...)` convertendo l'eccezione in stringa. Usare `$e->getMessage()` e passare `$e` come `$previous`.

### 5.4 `$systemColumns` hardcoded in `AbstractBuilder.php`
`['id', 'created_at', 'updated_at']` dovrebbe essere configurabile o ereditato dal Model.

### 5.5 Commenti misti italiano/inglese
File coinvolti: `AbstractBuilder`, `Kernel`, `Attributes`, `ActiveQueryFactory`. Uniformare in inglese.

---

## Riepilogo Priorita

| Priorita | # Issue | Azione |
|----------|---------|--------|
| CRITICO | 4 | Bug e vulnerabilita da fixare subito |
| IMPORTANTE | 8 | Duplicazioni e inconsistenze da risolvere |
| MIGLIORAMENTO | 4 | Refactoring architetturale |
| MINORE | 5 | Cleanup e best practices |

---

## 6. NUOVO - Centralizzazione Directory e Path

### 6.1 Uso sparso di `__DIR__`
**File coinvolti:** `Str.php`, `StubGenerator.php`, `WhoopsProvider.php`, `Log.php`

I path assoluti erano costruiti in modo distribuito tramite `__DIR__`, `dirname(__DIR__, N)` e concatenazioni manuali. Questo rende il codice piu fragile ai refactor di struttura e disperde la conoscenza dell'albero del progetto in classi non infrastrutturali.

**Direzione consigliata**
- Introdurre una piccola gerarchia di classi directory/path centralizzate come strato infrastrutturale unico.
- Far dipendere helper, provider e generatori da queste classi invece che da percorsi relativi hardcoded.
- Estendere gradualmente il pattern anche agli altri accessi filesystem applicativi.

---

## 7. COMPLETATO - Allineamento Model alle Migration (2026-03-11)

Analisi e correzione di tutti i 13 model in `app/Model/` rispetto alle migration in `database/migration/`.

### Correzioni applicate

| Model | Problema | Fix |
|-------|----------|-----|
| **User** | Proprieta `indirizzo` e `last_log` non presenti in migration (sono colonne di `logs`, non di `users`) | Rimosse |
| **User** | Mancava `updated_at` (aggiunto da migration timestamps) | Aggiunta |
| **Contatti** | Mancava `id` | Aggiunta |
| **Contatti** | Aveva `timestamps = false` ma la migration timestamps aggiunge le colonne | Rimosso `timestamps = false` |
| **Contatti** | Mancava `updated_at` | Aggiunta |
| **LogTrace** | Mancava `id` | Aggiunta |
| **LogTrace** | Aveva `timestamps = false` ma la migration timestamps aggiunge le colonne | Rimosso `timestamps = false` |
| **LogTrace** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Project** | Mancava `id` | Aggiunta |
| **Project** | Aveva `timestamps = false` ma la migration timestamps aggiunge le colonne | Rimosso `timestamps = false` |
| **Project** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Technology** | Table name `technologies` non corrispondeva a migration `technology` | Corretto in `technology` |
| **Technology** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Article** | Mancava `updated_at` | Aggiunta |
| **Certificate** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Curriculum** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Law** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Partner** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Profile** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Skill** | Mancava `created_at` e `updated_at` | Aggiunte |
| **Token** | Mancava `updated_at` | Aggiunta |

### Verifiche completate
- Tutti i 13 model hanno `declare(strict_types=1)`
- Tutti i tipi PHP sono coerenti con i tipi SQL delle migration
- Tutte le colonne nullable hanno tipi nullable
- Nessuna proprieta extra non presente nelle migration
- Nessuna colonna mancante nei model

---

## 8. NOTE - CLI Commands Implementation (2026-03-11)

### Comandi creati/aggiornati
- **MakeServiceCommand** (`make:service`) - Nuovo comando per generare service in `app/Services/`
- **ModelInspectCommand** (`model:inspect`) - Nuovo comando per ispezionare proprieta tipizzate dei model
- **MakeControllerCommand** - Migliorato con validazione nome, gestione errori try/catch, protezione da flag come primo argomento
- **MakeModelCommand** - Aggiunto supporto per flag combinabili `-m` `-c` `-r` `-s` (e combinazioni come `-mcrs`), aggiunto `--service` e `--controller` long flags

### Osservazioni durante lo sviluppo

#### 8.1 ValidateClassName e hardcoded a Middleware
**File:** `app/Core/CLI/Commands/Validation/ValidateClassName.php`
Il metodo `Validate()` ha il path `app/Middleware/` hardcoded alla riga 33 per il controllo file duplicato. Il parametro `$classEndName` suggerisce che dovrebbe essere generico, ma il check file existence e specifico per middleware. Questo validator non puo essere riutilizzato per controller, service, ecc.

**Fix suggerito:** Accettare un terzo parametro `?string $basePath = null` oppure rimuovere il file existence check (delegandolo al chiamante).

#### 8.2 Percorsi inconsistenti tra comandi
- `MakeModelCommand` usa `getcwd() . '/App/Model/'` (App maiuscolo)
- `MakeServiceCommand` usa `getcwd() . '/app/Services/'` (app minuscolo)
- `MakeMiddlewareCommand` usa path relativo `app/Middleware/`
- `MakeControllerCommand` usa `getcwd() . '/App/Controllers/'` (App maiuscolo)

Il filesystem Linux e case-sensitive; questa inconsistenza puo causare file creati in directory sbagliate. Verificare quale sia la convenzione corretta del progetto.

#### 8.3 Out::warn() dentro match expression
In `MakeModelCommand::parseOptions()`, il default branch del match chiama `Out::warn()` che potrebbe avere side-effects inattesi nel contesto di un'espressione match. Funziona perche PHP permette statement expressions nel match, ma e un pattern insolito.

---

## 9. Routing Refactoring (2026-03-11)

### Pulizia effettuata
- **Router.php**: rimossi 3 metodi deprecati (`getRoute()`, `Exresolve()`, `dispatch(array)`) e un blocco commentato duplicato. Erano codice morto che aumentava la complessita senza essere usato.
- **Router.php**: rimosso import inutilizzato `DashBoardController`.

### Bug risolti in RouteLoader
1. `extractRoutes()` lanciava eccezione per ogni metodo pubblico senza `#[RouteAttr]`. Impediva controller con metodi helper pubblici. Ora i metodi senza attributi di routing sono ignorati.
2. `getAllControllers()`: la variabile `$controllers` non era inizializzata prima del loop. Aggiunto `$controllers = []`.
3. Mancava validazione sull'esistenza della directory controller.
4. Mancava validazione sull'esistenza effettiva della classe dopo il `require_once`.

### Typo corretti in RouteLoader
- `getReflrectedController` -> `getReflectedControllers`
- `$conntrollerPath` -> `$controllerPath`
- `GetAttributesOfController` -> `buildControllerStack`
- `$refleciton` -> `$reflection`

### Error reporting migliorato
- Messaggi di errore chiari per attributi malformati (#[RouteAttr], #[ControllerAttr], #[Prefix], #[Middleware], #[NamePrefix])
- Ogni messaggio indica: la classe, il file, e la sintassi corretta dell'attributo

### Nuove feature aggiunte
- Attributi Spatie-style: `#[Get]`, `#[Post]`, `#[Put]`, `#[Patch]`, `#[Delete]` (tutti estendono `RouteAttribute`)
- Attributi di classe: `#[Prefix]`, `#[Middleware]`, `#[NamePrefix]`
- `RouteHelper::url()` per URL generation da nomi rotta (reverse routing)
- `RouteCache` per serializzazione/deserializzazione rotte in produzione
- Comandi CLI: `route:list`, `route:cache`, `route:clear`
- Test unitari: `RouteCollectionTest`, `RouteMatcherTest`, `RouteLoaderTest`

### Problemi individuati ma NON corretti (fuori scope)
1. **RouteDispatcher**: le proprieta `$controller`, `$path`, `$method`, `$action`, `$name`, `$dispatche` sono dichiarate ma mai assegnate nel metodo `dispatch()`. `$this->controller` viene usato nel messaggio di errore di `executeMiddleware()` ma non viene valorizzato, causando potenziale errore se un middleware non e nel config.
2. **RouteRegister**: non sembra essere usato nel flusso attuale (Router non chiama mai `register()`). Valutare se eliminarlo o integrarlo.
3. **RouteDispatcher**: il blocco commentato in cima (vecchia implementazione di `dispatch()`) andrebbe rimosso.
