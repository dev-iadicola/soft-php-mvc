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
