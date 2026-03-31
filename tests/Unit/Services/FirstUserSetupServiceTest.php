<?php

declare(strict_types=1);

use App\Core\DataLayer\Runtime\ORM;
use App\Core\Exception\ValidationException;
use App\Services\FirstUserSetupService;
use PHPUnit\Framework\TestCase;

class FirstUserSetupServiceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        ORM::init($this->pdo, 'sqlite');

        $this->pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            log_id INTEGER NULL,
            password TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            token TEXT NULL,
            two_factor_secret TEXT NULL,
            two_factor_enabled INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NULL,
            updated_at TEXT NULL
        )');
    }

    public function testRegistrationIsOpenWhenNoUsersExist(): void
    {
        $this->assertTrue(FirstUserSetupService::requiresRegistration());
    }

    public function testCreateInitialUserCreatesTheFirstAdmin(): void
    {
        $user = FirstUserSetupService::createInitialUser('admin@example.com', 'hashed-password');

        $this->assertSame('admin@example.com', $user->email);
        $this->assertFalse(FirstUserSetupService::requiresRegistration());
    }

    public function testCannotCreateASecondUser(): void
    {
        FirstUserSetupService::createInitialUser('admin@example.com', 'hashed-password');

        $this->expectException(ValidationException::class);

        FirstUserSetupService::createInitialUser('other@example.com', 'other-password');
    }
}
