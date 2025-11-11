# Model – ORM Layer Documentation

The **Model** represents the core abstraction layer between your PHP code and the database.  
It’s built on top of a fully custom **QueryBuilder**, designed for expressive, fluent, and secure database interaction.

Each model encapsulates a database table and provides a natural, object-oriented API for working with data.

---

##  Architecture Overview

The Model–QueryBuilder system is based on three key design patterns:

| Pattern | Purpose | Implementation |
|----------|----------|----------------|
| **Builder Pattern** | Builds complex SQL queries step by step | `QueryBuilder` |
| **Active Record Pattern** | Each model instance represents a single table row and handles its own persistence | `Model` |
| **Template Method Pattern** | Defines the execution flow while letting subclasses override specific steps | `AbstractBuilder` → `QueryBuilder` |

---

## Model Structure

A typical model extends the base `Model` class and defines its table and fillable attributes:

```php
class User extends Model
{
    protected ?string $table = 'users';
    protected array $fillable = ['name', 'email', 'password'];
}

// Cretion
User::create([
    'title' => 'text',
    'text' => 'ipsulorem....',
]);
// Selection
$users = User::select(['id', 'name'])
    ->where('active', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(5)
    ->get();
// First Record 
$user = User::where('email', 'luigi@example.com')->first();

// Update data 
$user = User::find(1);
$user->name = 'Luigi Iadicola';
$user->update(['name' => $user->name]);

// Destroy data 
User::where('id', 3)->delete();
```
# Aggregation Functions

## The QueryBuilder provides built-in SQL aggregate and utility functions:

| Method                   | Description                 | Example                               |
| ------------------------ | --------------------------- | ------------------------------------- |
| `count($column, $alias)` | Counts rows                 | `User::count('id', 'total_users')`    |
| `sum($column, $alias)`   | Returns the sum of a column | `Order::sum('amount', 'total_sales')` |
| `avg($column, $alias)`   | Returns the average value   | `User::avg('age', 'avg_age')`         |
| `max($column, $alias)`   | Returns the maximum         | `Order::max('amount', 'max_order')`   |
| `min($column, $alias)`   | Returns the minimum         | `User::min('age', 'min_age')`         |
| `countDistinct($column)` | Counts distinct values      | `User::countDistinct('email')`        |


```php
// INNER JOIN
User::select(['users.id', 'users.name', 'roles.name AS role'])
    ->join('roles', 'users.role_id', '=', 'roles.id')
    ->where('roles.active', 1)
    ->get();

// LEFT JOIN RIGHT
Post::leftJoin('comments', 'posts.id', '=', 'comments.post_id')
    ->select(['posts.title', 'comments.text'])
    ->get();
// Additional Clauses
    User::whereBetween('age', 18, 30)
    ->whereNotNull('email')
    ->groupBy('city')
    ->having('COUNT(id)', '>', 5)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(20)
    ->get();
```

# QueryBuilder

| Pattern | Description |
|----------|-------------|
| **Builder** | The `QueryBuilder` constructs SQL queries using a fluent interface. |
| **Active Record** | Each Model manages its own table and CRUD operations. |
| **Template Method** | `AbstractBuilder` defines the shared structure for all Builders. |
| **Proxy / Delegation** | The `Model` delegates static method calls to the `QueryBuilder`. |
| **Singleton (PDO)** | The `Database` class maintains a single active PDO connection. |
| **Strategy** | (Planned) Future extension for supporting multiple SQL dialects. |
| **Facade** | The `Auth` facade provides simplified access to services like `AuthService` and `SessionStorage`. |

---

## ORM System (Model & QueryBuilder)

The Soft ORM system is **inspired by last Framework tecnologies **, but **built entirely from scratch**.  
It follows a clear, fluent flow: every Model represents a table, and the `QueryBuilder` dynamically generates and executes SQL statements.

```text
[User::find(1)] -> Exstend Class Model
      │
      ▼
 ┌────────────────┐
 │   Model.php    │ → Creates the instance and forwards static calls to the QueryBuilder
 └────────────────┘
      │
      ▼
 ┌──────────────────────┐
 │   QueryBuilder.php   │ → Builds the SQL query fluently
 │   (SELECT * FROM ...)│
 └──────────────────────┘
      │
      ▼
 ┌────────────────┐
 │   Database.php │ → Singleton PDO that executes the query
 └────────────────┘
      │
      ▼
[ResultSet / Model Hydration]
```
| Feature                         | Symfony *(Doctrine ORM)*                                | CodeIgniter *(Query Builder)*           | Laravel *(Eloquent ORM)*                         | Soft MVC *(Custom ORM + QueryBuilder)*                                             |
| ------------------------------- | ------------------------------------------------------- | --------------------------------------- | ------------------------------------------------ | ---------------------------------------------------------------------------------- |
| **ORM Paradigm**                | Full Data Mapper with Entity Manager                    | None (only fluent Query Builder)        | Active Record + Fluent Builder                   | Active Record + Custom Fluent Query Builder                                        |
| **Model Definition**            | *Entity* classes + annotations or YAML/XML mapping      | No entity classes, uses arrays/stdClass | `Model` classes with static methods              | `Model` classes with dynamic properties + `Getter`, `Relation` traits              |
| **Query Construction**          | `QueryBuilder` based on DQL (Doctrine Query Language)   | `$this->db->select()->from()->where()`  | `User::where('name', 'Luigi')->first()`          | `User::where('name', 'Luigi')->orderBy('id')->get()` *(Eloquent-style)*            |
| **Query Execution**             | DQL → SQL translation via `EntityManager`               | Direct execution on DB driver           | Lazy loading through PDO                         | Lazy execution via PDO + secure parameter binding                                  |
| **Relations**                   | Managed via annotations (`@OneToMany`, etc.)            | Manual                                  | Defined in models (`hasMany`, `belongsTo`, etc.) | Supported via `Relation` trait (`hasOne`, `hasMany`, `belongsTo`, `belongsToMany`) |
| **SQL Injection Safety**        | Automatic parameterized queries                         | Manual or auto escaping                 | Automatic parameter binding                      | Secure `bindValue` parameter binding in `QueryBuilder`                             |
| **Schema Validation**           | Managed by Doctrine Migrations                          | Manual                                  | Integrated migrations                            | Planned: automatic schema validation via `CheckSchema` and reflection              |
| **Query Caching**               | Yes, via `DoctrineCache`                                | No                                      | Optional (`remember()`)                          | Planned cache layer via `QueryCache`                                               |
| **Error Handling**              | Structured exceptions (`ORMException`, `DBALException`) | Driver error codes                      | Laravel exceptions (`QueryException`)            | Custom exceptions (`ModelStructureException`, `QueryBuilderException`, etc.)       |
| **Main Design Patterns**        | Data Mapper, Unit of Work, Repository                   | Builder Pattern                         | Active Record, Builder, Proxy                    | Active Record, Builder, Template Method, Proxy, Facade                             |
| **Database Layer Architecture** | Total separation between entities and DB                | Procedural, no mapping                  | Integrated within the Model                      | Layered architecture separating `Model ↔ QueryBuilder ↔ PDO`                       |
| **Primary Goal**                | Abstract and portable domain representation             | Simple and direct data access           | Expressive, intuitive syntax                     | Combine **clarity**, **manual control**, and **modern fluidity**                   |


# Eloquent-like syntax, clean structure, direct PDO control, and a fully handcrafted ORM — written from the ground up with precision and intent.

---

