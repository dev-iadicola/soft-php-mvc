<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Core\DataLayer\Model;
use App\Core\DataLayer\Query\ModelHydrator;
use App\Core\DataLayer\Query\MySqlBuilder;
use App\Model\Article;
use App\Model\LogTrace;

// ---------------------------------------------------------------------------
// Test-only stub models
// ---------------------------------------------------------------------------

class TestModelWithColumnMap extends Model
{
    protected ?string $migrationTable = null;

    protected function columnMap(): array
    {
        return ['migrationTable' => 'migration_table'];
    }
}

class TestModelWithCasts extends Model
{
    protected ?bool $active = null;
    protected ?int $count = null;
    protected ?array $meta = null;

    protected function casts(): array
    {
        return [
            'active' => 'bool',
            'count' => 'int',
            'meta' => 'json',
        ];
    }
}

class TestModelNoTable extends Model
{
    protected int|string|null $id = null;
    protected ?string $name = null;
}

class TestModelEndingInY extends Model
{
    protected int|string|null $id = null;
}

class TestModelEndingInS extends Model
{
    protected string $table = '';
    protected int|string|null $id = null;
}

class Bus extends Model
{
    protected int|string|null $id = null;
}

class Fox extends Model
{
    protected int|string|null $id = null;
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

class ModelTest extends TestCase
{
    // ======================================================================
    // Model — getPersistableColumns
    // ======================================================================

    public function testGetPersistableColumnsReturnsOnlyDeclaredProperties(): void
    {
        $article = new Article();
        $columns = $article->getPersistableColumns();

        $expected = ['id', 'title', 'subtitle', 'overview', 'img', 'link', 'is_active', 'created_at', 'updated_at'];
        $this->assertSame($expected, $columns);

        // Internal framework properties must never leak into the column list.
        $this->assertNotContains('primaryKey', $columns);
        $this->assertNotContains('table', $columns);
        $this->assertNotContains('timestamps', $columns);
        $this->assertNotContains('attributes', $columns);
    }

    public function testColumnsIsCanonicalAliasForPersistableColumns(): void
    {
        $article = new Article();

        $this->assertSame($article->getPersistableColumns(), $article->columns());
    }

    // ======================================================================
    // Model — setAttribute / getAttribute
    // ======================================================================

    public function testSetAttributeWritesToTypedProperty(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'Hello World');

        // getAttribute should read it back from the typed property.
        $this->assertSame('Hello World', $article->getAttribute('title'));
    }

    public function testGetAttributeReadsFromTypedProperty(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'Read Me');
        $article->setAttribute('subtitle', 'A subtitle');

        $this->assertSame('Read Me', $article->getAttribute('title'));
        $this->assertSame('A subtitle', $article->getAttribute('subtitle'));
    }

    public function testSetAttributeFallsBackToAttributesArray(): void
    {
        $article = new Article();
        $article->setAttribute('unknown_column', 'dynamic_value');

        $this->assertSame('dynamic_value', $article->getAttribute('unknown_column'));
    }

    public function testGetAttributeReturnsNullForMissing(): void
    {
        $article = new Article();

        $this->assertNull($article->getAttribute('nonexistent_key'));
    }

    // ======================================================================
    // Model — toArray / jsonSerialize
    // ======================================================================

    public function testToArrayMergesPropertiesAndAttributes(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'T');
        $article->setAttribute('extra', 'dynamic');

        $array = $article->toArray();

        // Typed property
        $this->assertSame('T', $array['title']);
        // Dynamic attribute
        $this->assertSame('dynamic', $array['extra']);
        // Null typed properties should still appear
        $this->assertArrayHasKey('id', $array);
        $this->assertNull($array['id']);
    }

    public function testJsonSerializeReturnsToArray(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'JSON');

        $this->assertSame($article->toArray(), $article->jsonSerialize());
    }

    // ======================================================================
    // Model — columnMap
    // ======================================================================

    public function testColumnMapOverride(): void
    {
        $model = new TestModelWithColumnMap();
        $model->setAttribute('migration_table', 'users');

        // The PHP property is "migrationTable" but the DB column name is "migration_table".
        $this->assertSame('users', $model->getAttribute('migration_table'));

        // getPersistableColumns should return the DB column name.
        $columns = $model->getPersistableColumns();
        $this->assertContains('migration_table', $columns);
        $this->assertNotContains('migrationTable', $columns);

        // toArray should use the mapped column name as key.
        $array = $model->toArray();
        $this->assertArrayHasKey('migration_table', $array);
        $this->assertSame('users', $array['migration_table']);
    }

    public function testSetAttributeAppliesConfiguredCasts(): void
    {
        $model = new TestModelWithCasts();

        $model->setAttribute('active', 1);
        $model->setAttribute('count', '42');
        $model->setAttribute('meta', '{"role":"admin"}');

        $this->assertTrue($model->getAttribute('active'));
        $this->assertSame(42, $model->getAttribute('count'));
        $this->assertSame(['role' => 'admin'], $model->getAttribute('meta'));
    }

    // ======================================================================
    // Model — getTable
    // ======================================================================

    public function testGetTableAutoResolvesFromClassName(): void
    {
        $model = new TestModelNoTable();
        // "TestModelNoTable" → "testmodelnottable" → pluralized: "testmodelnottables"
        $this->assertNotEmpty($model->getTable());
        // It should end with 's' since it gets auto-pluralized.
        $this->assertStringEndsWith('s', $model->getTable());
    }

    public function testGetTableUsesExplicitTable(): void
    {
        $article = new Article();
        $this->assertSame('articles', $article->getTable());
    }

    public function testGetKeyIdReturnsPrimaryKey(): void
    {
        $article = new Article();
        $this->assertSame('id', $article->getKeyId());
    }

    // ======================================================================
    // Attributes trait — magic __get / __set
    // ======================================================================

    public function testMagicGetReturnsTypedProperty(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'Magic');

        $this->assertSame('Magic', $article->title);
    }

    public function testSetAttributeWritesTypedProperty(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'Written via setAttribute');

        $this->assertSame('Written via setAttribute', $article->getAttribute('title'));
    }

    public function testMagicGetFallsBackToAttributes(): void
    {
        $article = new Article();
        $article->setAttribute('dynamic_key', 'dynamic_val');

        $this->assertSame('dynamic_val', $article->dynamic_key);
    }

    public function testSetAttributeFallsBackToAttributes(): void
    {
        $article = new Article();
        $article->setAttribute('dynamic_key', 'set_via_magic');

        $this->assertSame('set_via_magic', $article->getAttribute('dynamic_key'));
    }

    public function testDirtyTrackingMarksAndClearsChanges(): void
    {
        $article = new Article();

        $this->assertFalse($article->isDirty());

        $article->setAttribute('title', 'Draft');

        $this->assertTrue($article->isDirty());
        $this->assertTrue($article->isDirty('title'));
        $this->assertSame(['title' => 'Draft'], $article->getDirtyAttributes());

        $article->syncOriginal();

        $this->assertFalse($article->isDirty());
        $this->assertSame([], $article->getDirtyAttributes());
    }

    public function testExistingModelsExposeOnlyDirtyAttributesForUpdate(): void
    {
        $article = new Article();
        $article->setAttribute('id', 10);
        $article->setAttribute('title', 'Before');
        $article->syncOriginal();

        $article->setAttribute('title', 'After');

        $this->assertTrue($article->exists());
        $this->assertSame(['title' => 'After'], $article->getAttributesForUpdate());
    }

    public function testNewModelsExposeOnlyDirtyAttributesForInsert(): void
    {
        $article = new Article();
        $article->setAttribute('title', 'Only changed field');

        $this->assertFalse($article->exists());
        $this->assertSame(['title' => 'Only changed field'], $article->getAttributesForInsert());
    }

    // ======================================================================
    // ModelHydrator — one / many
    // ======================================================================

    private function createHydrator(string $modelClass): ModelHydrator
    {
        $builder = new MySqlBuilder();
        $builder->from('test');
        $hydrator = new ModelHydrator($builder);
        $hydrator->setModelClass($modelClass);
        return $hydrator;
    }

    public function testHydrateOneReturnsModelWithProperties(): void
    {
        $hydrator = $this->createHydrator(Article::class);

        $model = $hydrator->one([
            'id'    => 1,
            'title' => 'Hydrated Title',
            'link'  => 'https://example.com',
        ]);

        $this->assertInstanceOf(Article::class, $model);
        $this->assertSame(1, $model->getAttribute('id'));
        $this->assertSame('Hydrated Title', $model->getAttribute('title'));
        $this->assertSame('https://example.com', $model->getAttribute('link'));
        $this->assertFalse($model->isDirty());
    }

    public function testHydrateOneReturnNullForEmptyRow(): void
    {
        $hydrator = $this->createHydrator(Article::class);

        $this->assertNull($hydrator->one([]));
        $this->assertNull($hydrator->one(false));
    }

    public function testHydrateManyReturnsArrayOfModels(): void
    {
        $hydrator = $this->createHydrator(Article::class);

        $rows = [
            ['id' => 1, 'title' => 'First'],
            ['id' => 2, 'title' => 'Second'],
            ['id' => 3, 'title' => 'Third'],
        ];

        $models = $hydrator->many($rows);

        $this->assertCount(3, $models);
        foreach ($models as $model) {
            $this->assertInstanceOf(Article::class, $model);
        }
        $this->assertSame('First', $models[0]->getAttribute('title'));
        $this->assertSame('Second', $models[1]->getAttribute('title'));
        $this->assertSame('Third', $models[2]->getAttribute('title'));
    }

    public function testHydrateManyReturnsEmptyArrayForNoRows(): void
    {
        $hydrator = $this->createHydrator(Article::class);

        $this->assertSame([], $hydrator->many([]));
    }

    public function testHydratedModelsAreIndependentInstances(): void
    {
        $hydrator = $this->createHydrator(Article::class);

        $rows = [
            ['id' => 1, 'title' => 'A'],
            ['id' => 2, 'title' => 'B'],
        ];

        $models = $hydrator->many($rows);

        $this->assertNotSame($models[0], $models[1]);

        // Mutating one must not affect the other.
        $models[0]->setAttribute('title', 'Changed');
        $this->assertSame('Changed', $models[0]->getAttribute('title'));
        $this->assertSame('B', $models[1]->getAttribute('title'));
    }

    // ======================================================================
    // Table name auto-resolution — pluralization
    // ======================================================================

    public function testPluralizeSimple(): void
    {
        // "TestModelNoTable" → snake → "test_model_no_table" → plural → "test_model_no_tables"
        $model = new TestModelNoTable();
        $table = $model->getTable();
        $this->assertSame('test_model_no_tables', $table);
    }

    public function testPluralizeEndingInY(): void
    {
        // TestModelEndingInY → "testmodelendininy" → consonant + y → "testmodelendininies"
        $model = new TestModelEndingInY();
        $table = $model->getTable();
        $this->assertStringEndsWith('ies', $table);
    }

    public function testPluralizeEndingInS(): void
    {
        // Bus → "bus" → ends in 's' → "buses"
        $bus = new Bus();
        $this->assertStringEndsWith('es', $bus->getTable());

        // Fox → "fox" → ends in 'x' → "foxes"
        $fox = new Fox();
        $this->assertStringEndsWith('es', $fox->getTable());
    }

    // ======================================================================
    // Integration-style tests (no DB)
    // ======================================================================

    public function testModelInstanceCreation(): void
    {
        $article = new Article();

        $this->assertNull($article->getAttribute('id'));
        $this->assertNull($article->getAttribute('title'));
        $this->assertNull($article->getAttribute('subtitle'));
        $this->assertNull($article->getAttribute('overview'));
        $this->assertNull($article->getAttribute('img'));
        $this->assertNull($article->getAttribute('link'));
        $this->assertNull($article->getAttribute('created_at'));
    }

    public function testModelTimestampsDefault(): void
    {
        // Article has timestamps = true (default).
        $article = new Article();
        $articleArray = $article->toArray();
        // We cannot read protected $timestamps directly, but we can verify
        // the default via reflection.
        $ref = new ReflectionProperty(Article::class, 'timestamps');
        $this->assertTrue($ref->getValue($article));

        // LogTrace uses default timestamps = true (aligned to migration).
        $logTrace = new LogTrace();
        $ref = new ReflectionProperty(LogTrace::class, 'timestamps');
        $this->assertTrue($ref->getValue($logTrace));
    }
}
