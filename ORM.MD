# ORM Layer — Architecture & Usage Guide

This ORM is a lightweight, fully custom implementation inspired by modern frameworks,  
but designed with **strict separation of concerns**, **type-safety**, and **zero magic**.

Unlike classic Active Record ORMs, this architecture introduces an intermediate layer called **ActiveQuery**,  
which cleanly separates:

- **query building**  
- **query execution**  
- **model hydration**

This results in a predictable, testable, extensible ORM.

---

# 🔷 Architecture Overview

The ORM is composed of five core components:

Model → OrmEngine → ActiveQuery → QueryBuilder → QueryExecutor → PDO
│
ModelHydrator

### **Model**
Represents a table and provides a static API (`User::make()->where()->first()`).

### **OrmEngine**
Bootstraps the ORM stack for a model (QueryBuilder, Executor, Hydrator).  
Does NOT execute queries directly anymore.

### **ActiveQuery**
The core worker:
- holds the QueryBuilder
- executes SQL through QueryExecutor
- hydrates results through ModelHydrator

### **QueryBuilder**
Fluent SQL builder (select, where, joins, limit, …).

### **QueryExecutor**
Runs prepared statements safely with PDO.

### **ModelHydrator**
Transforms raw DB rows into Model instances.

---

# 🔷 Example Model

```php
class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];
}
User::make()
    ->create([
        'name' => 'Luigi',
        'email' => 'luigi@example.com'
    ]);
    // SELECT
    $users = User::make()
    ->select(['id', 'name'])
    ->where('active', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get();
    // UPDATE
    User::make()
    ->where('id', 1)
    ->update(['name' => 'Luigi Updated'])
    ->execute();
    // DELETE
    User::make()
    ->where('id', 3)
    ->delete()
    // ADDITIONAL CLAUSES 
    User::make()
    ->whereBetween('age', 18, 30)
    ->whereNotNull('email')
    ->groupBy('city')
    ->having('COUNT(id)', '>', 5)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(20)
    ->get();
```
___

| Pattern                     | Role                                            |
| --------------------------- | ----------------------------------------------- |
| **ActiveQuery**             | Encapsulates QueryBuilder + Executor + Hydrator |
| **Builder Pattern**         | Fluent SQL construction                         |
| **Data Mapper (partial)**   | Hydration separate from Models                  |
| **Active Record (partial)** | Model defines table + fillable                  |
| **Dependency Injection**    | Engine injects Builder/Executor/Hydrator        |
| **Separation of Concerns**  | Clean architectural layering                    |

```TEXT
User::make()->where('id', 1)->first()
        │
        ▼
Model (boot + table + fillable)
        │
        ▼
OrmEngine::Make()
        │
        ▼
ActiveQuery
│       │
│    QueryBuilder → builds SQL
│    QueryExecutor → runs PDO safely
│    ModelHydrator → returns model objects
│
▼
Result (User instance or re-call ActiveQuery)

```

# Why This Architecture?



- Cleaner: No magic static calls directly executed

- Testable: Each component has a single responsibility

- Predictable: QueryBuilder has no side effects

- Extensible: Easy to add relations, caching, different SQL dialects

- Compared to Doctrine:

- Lighter

- More direct control

- No metadata overhead

- Closer to raw SQL, but safer

# Work in Progress (WIP)

## This ORM is functional but still under development.

### Pending features:
- toInsert() / toUpdate() / toDelete() finalization
- Unified insert/update/delete execution through ActiveQuery
- Relations (hasOne / hasMany / belongsTo)
- Schema validator improvements
- Query caching
- Batch operations


