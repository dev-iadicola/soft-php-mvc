<?php

declare(strict_types=1);

use App\Core\DataLayer\Model;
use App\Core\DataLayer\Query\ActiveQuery;
use App\Core\Repository\BaseRepository;
use PHPUnit\Framework\TestCase;

/**
 * Tests for BaseRepository CRUD delegation.
 *
 * We cannot use a real DB, so we mock the Model::query() static method
 * by creating a concrete repository subclass that overrides the query() call.
 */
class RepositoryTest extends TestCase
{
    private ActiveQuery $mockQuery;

    protected function setUp(): void
    {
        $this->mockQuery = $this->createMock(ActiveQuery::class);
    }

    // ========================================================================
    // find()
    // ========================================================================

    public function testFindDelegatesToActiveQueryFind(): void
    {
        $model = $this->createMock(Model::class);

        $this->mockQuery
            ->expects($this->once())
            ->method('find')
            ->with(42)
            ->willReturn($model);

        $repo = $this->createTestRepository($this->mockQuery);

        $result = $repo->find(42);
        $this->assertSame($model, $result);
    }

    public function testFindReturnsNullWhenNotFound(): void
    {
        $this->mockQuery
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertNull($repo->find(999));
    }

    // ========================================================================
    // all()
    // ========================================================================

    public function testAllDelegatesToActiveQueryAll(): void
    {
        $models = [
            $this->createMock(Model::class),
            $this->createMock(Model::class),
        ];

        $this->mockQuery
            ->expects($this->once())
            ->method('all')
            ->willReturn($models);

        $repo = $this->createTestRepository($this->mockQuery);

        $result = $repo->all();
        $this->assertCount(2, $result);
        $this->assertSame($models, $result);
    }

    public function testAllReturnsEmptyArrayWhenNoRecords(): void
    {
        $this->mockQuery
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertSame([], $repo->all());
    }

    // ========================================================================
    // create()
    // ========================================================================

    public function testCreateDelegatesToActiveQueryCreate(): void
    {
        $model = $this->createMock(Model::class);
        $data = ['name' => 'Test', 'email' => 'test@example.com'];

        $this->mockQuery
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->willReturn($model);

        $repo = $this->createTestRepository($this->mockQuery);

        $result = $repo->create($data);
        $this->assertSame($model, $result);
    }

    // ========================================================================
    // update()
    // ========================================================================

    public function testUpdateReturnsTrueOnSuccess(): void
    {
        $model = $this->createMock(Model::class);
        $model->method('getKeyId')->willReturn('id');

        // find() call
        $this->mockQuery
            ->method('find')
            ->with(1)
            ->willReturn($model);

        // where()->update() chain
        $this->mockQuery
            ->method('where')
            ->with('id', 1)
            ->willReturnSelf();

        $this->mockQuery
            ->method('update')
            ->with(['name' => 'Updated'])
            ->willReturn(true);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertTrue($repo->update(1, ['name' => 'Updated']));
    }

    public function testUpdateReturnsFalseWhenModelNotFound(): void
    {
        $this->mockQuery
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertFalse($repo->update(999, ['name' => 'Nope']));
    }

    // ========================================================================
    // delete()
    // ========================================================================

    public function testDeleteReturnsTrueOnSuccess(): void
    {
        $model = $this->createMock(Model::class);
        $model->method('getKeyId')->willReturn('id');

        $this->mockQuery
            ->method('find')
            ->with(1)
            ->willReturn($model);

        $this->mockQuery
            ->method('where')
            ->with('id', 1)
            ->willReturnSelf();

        $this->mockQuery
            ->method('delete')
            ->willReturn(true);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertTrue($repo->delete(1));
    }

    public function testDeleteReturnsFalseWhenModelNotFound(): void
    {
        $this->mockQuery
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $repo = $this->createTestRepository($this->mockQuery);

        $this->assertFalse($repo->delete(999));
    }

    // ========================================================================
    // Helper: creates a concrete repository subclass with injected mock query
    // ========================================================================

    private function createTestRepository(ActiveQuery $mockQuery): BaseRepository
    {
        return new class ($mockQuery) extends BaseRepository {
            private ActiveQuery $mockQuery;

            public function __construct(ActiveQuery $mockQuery)
            {
                parent::__construct(Model::class);
                $this->mockQuery = $mockQuery;
            }

            protected function query(): ActiveQuery
            {
                return $this->mockQuery;
            }
        };
    }
}
