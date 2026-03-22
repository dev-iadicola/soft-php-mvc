<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\DataLayer\Runtime\ORM;
use App\Core\Exception\NotFoundException;
use App\Core\Exception\ValidationException;
use App\Model\Article;
use App\Model\Token;
use App\Model\User;
use App\Services\ArticleService;
use App\Services\LinkFooterService;
use App\Services\LogService;
use App\Services\MaintenanceService;
use App\Services\PartnerService;
use App\Services\PasswordService;
use App\Services\ProjectTechnologyService;
use App\Services\TerminalService;
use App\Services\TechnologyService;
use App\Services\TokenService;
use PHPUnit\Framework\TestCase;

class ServiceTest extends TestCase
{
    private PDO $pdo;
    private string $envFile;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        ORM::init($this->pdo, 'sqlite');

        $this->createSchema();
        $this->envFile = sys_get_temp_dir() . '/soft-php-mvc-env-' . uniqid('', true) . '.env';
        file_put_contents($this->envFile, "APP_ENV=testing\n");
        putenv('MAINTENANCE');
    }

    protected function tearDown(): void
    {
        putenv('MAINTENANCE');

        if (is_file($this->envFile)) {
            unlink($this->envFile);
        }
    }

    public function testArticleServiceCrudLifecycle(): void
    {
        $created = ArticleService::create([
            'title' => 'Initial title',
            'overview' => 'Initial overview',
        ]);

        $this->assertInstanceOf(Article::class, $created);
        $this->assertNotNull($created->id);

        $found = ArticleService::findOrFail((int) $created->id);
        $this->assertSame('Initial title', $found->title);

        $all = ArticleService::getAll();
        $this->assertCount(1, $all);

        $updated = ArticleService::update((int) $created->id, ['title' => 'Updated title']);
        $this->assertTrue($updated);
        $this->assertSame('Updated title', ArticleService::findOrFail((int) $created->id)->title);

        ArticleService::delete((int) $created->id);

        $this->expectException(NotFoundException::class);
        ArticleService::findOrFail((int) $created->id);
    }

    public function testTechnologyServiceCrudLifecycle(): void
    {
        $created = TechnologyService::create([
            'name' => 'Laravel',
        ]);

        $this->assertSame('Laravel', $created->name);
        $this->assertNotNull($created->id);

        $all = TechnologyService::getAll();
        $this->assertCount(1, $all);
        $this->assertSame('Laravel', $all[0]->name);

        $updated = TechnologyService::update((int) $created->id, ['name' => 'Symfony']);
        $this->assertTrue($updated);
        $this->assertSame('Symfony', TechnologyService::findOrFail((int) $created->id)->name);

        TechnologyService::delete((int) $created->id);

        $this->expectException(NotFoundException::class);
        TechnologyService::findOrFail((int) $created->id);
    }

    public function testPartnerServiceCrudLifecycle(): void
    {
        $created = PartnerService::create([
            'name' => 'Acme',
            'website' => 'https://acme.test',
        ]);

        $this->assertSame('Acme', $created->name);
        $this->assertSame('https://acme.test', $created->website);
        $this->assertNotNull($created->id);

        $all = PartnerService::getAll();
        $this->assertCount(1, $all);

        $updated = PartnerService::update((int) $created->id, ['name' => 'Acme Updated']);
        $this->assertTrue($updated);
        $this->assertSame('Acme Updated', PartnerService::findOrFail((int) $created->id)->name);

        PartnerService::delete((int) $created->id);

        $this->expectException(NotFoundException::class);
        PartnerService::findOrFail((int) $created->id);
    }

    public function testLinkFooterServiceCrudLifecycle(): void
    {
        $created = LinkFooterService::create([
            'title' => 'Portfolio',
            'link' => '/portfolio',
        ]);

        $this->assertSame('Portfolio', $created->title);
        $this->assertSame('/portfolio', $created->link);

        $updated = LinkFooterService::update((int) $created->id, ['title' => 'Progetti']);
        $this->assertTrue($updated);
        $this->assertSame('Progetti', LinkFooterService::findOrFail((int) $created->id)->title);

        LinkFooterService::delete((int) $created->id);

        $this->expectException(NotFoundException::class);
        LinkFooterService::findOrFail((int) $created->id);
    }

    public function testProjectTechnologyServiceSyncsManyToManyRelations(): void
    {
        $this->pdo->exec("INSERT INTO projects (id, title) VALUES (1, 'Soft MVC')");
        $this->pdo->exec("INSERT INTO technology (id, name) VALUES (1, 'PHP'), (2, 'Laravel'), (3, 'React')");

        ProjectTechnologyService::syncForProject(1, [1, '2', 2, 3]);

        $stack = ProjectTechnologyService::getByProject(1);

        $this->assertCount(3, $stack);
        $names = array_map(static fn ($technology) => $technology->name, $stack);
        sort($names);
        $this->assertSame(['Laravel', 'PHP', 'React'], $names);
    }

    public function testPasswordServiceChangeByEmailHashesPassword(): void
    {
        User::query()->create([
            'email' => 'alice@example.com',
            'password' => password_hash('old-password', PASSWORD_BCRYPT),
        ]);

        $changed = PasswordService::changeByEmail('alice@example.com', 'new-password');
        $user = User::query()->where('email', 'alice@example.com')->first();

        $this->assertTrue($changed);
        $this->assertInstanceOf(User::class, $user);
        $this->assertTrue(password_verify('new-password', (string) $user->password));
    }

    public function testPasswordServiceChangeByEmailReturnsFalseForMissingUser(): void
    {
        $this->assertFalse(PasswordService::changeByEmail('missing@example.com', 'irrelevant'));
    }

    public function testPasswordServiceChangeByUserUpdatesPassword(): void
    {
        $user = User::query()->create([
            'email' => 'bob@example.com',
            'password' => password_hash('current-password', PASSWORD_BCRYPT),
        ]);

        PasswordService::changeByUser($user, 'current-password', 'next-password', 'next-password');

        $reloaded = User::query()->where('email', 'bob@example.com')->first();

        $this->assertInstanceOf(User::class, $reloaded);
        $this->assertTrue(password_verify('next-password', (string) $reloaded->password));
    }

    public function testPasswordServiceChangeByUserRejectsInvalidPayload(): void
    {
        $user = User::query()->create([
            'email' => 'charlie@example.com',
            'password' => password_hash('current-password', PASSWORD_BCRYPT),
        ]);

        $this->expectException(ValidationException::class);
        PasswordService::changeByUser($user, 'wrong-password', 'short', 'mismatch');
    }

    public function testTokenServiceGenerateAndValidateStates(): void
    {
        $generated = TokenService::generate('token@example.com');

        $this->assertInstanceOf(Token::class, $generated);
        $this->assertSame('token@example.com', $generated->email);
        $this->assertSame(200, strlen((string) $generated->token));

        Token::query()->create([
            'email' => 'future@example.com',
            'token' => 'future-token',
            'used' => 0,
            'expiry_date' => '2999-01-01 00:00:00',
        ]);
        Token::query()->create([
            'email' => 'used@example.com',
            'token' => 'used-token',
            'used' => 1,
            'expiry_date' => '2999-01-01 00:00:00',
        ]);
        Token::query()->create([
            'email' => 'expired@example.com',
            'token' => 'expired-token',
            'used' => 0,
            'expiry_date' => '2000-01-01 00:00:00',
        ]);

        $this->assertTrue(TokenService::isValid('future-token'));
        $this->assertFalse(TokenService::isExpired('future-token'));
        $this->assertFalse(TokenService::isValid('used-token'));
        $this->assertFalse(TokenService::isValid('expired-token'));
        $this->assertTrue(TokenService::isExpired('expired-token'));
        $this->assertTrue(TokenService::isExpired('missing-token'));
    }

    public function testLogServiceCreatesEntriesAndAggregatesStats(): void
    {
        LogService::create(7, '127.0.0.1', 'Firefox');
        LogService::create(7, '127.0.0.1', 'Firefox');
        LogService::create(7, '10.0.0.1', 'Chrome');

        $stats = LogService::getLoginStats();

        $this->assertCount(2, $stats);

        $firefoxStats = array_values(array_filter(
            $stats,
            static fn (array $stat): bool => $stat['indirizzo'] === '127.0.0.1' && $stat['device'] === 'Firefox'
        ));

        $this->assertCount(1, $firefoxStats);
        $this->assertSame(2, (int) $firefoxStats[0]['login_count']);
    }

    public function testMaintenanceServiceTogglesEnvFlag(): void
    {
        MaintenanceService::enable($this->envFile);
        Config::env($this->envFile);
        $this->assertTrue(MaintenanceService::isEnabled());

        MaintenanceService::disable($this->envFile);
        Config::env($this->envFile);
        $this->assertFalse(MaintenanceService::isEnabled());
    }

    public function testTerminalServiceRejectsInvalidOrEmptyCommands(): void
    {
        $empty = TerminalService::execute('   ');
        $forbidden = TerminalService::execute('php soft destroy-everything');

        $this->assertSame(['output' => '', 'error' => 'Nessun comando inserito.'], $empty);
        $this->assertSame('', $forbidden['output']);
        $this->assertStringContainsString('Comando non consentito', $forbidden['error']);
        $this->assertContains('migrate', TerminalService::getAllowedCommands());
        $this->assertTrue(TerminalService::isAllowed('seed'));
        $this->assertFalse(TerminalService::isAllowed('destroy-everything'));
    }

    private function createSchema(): void
    {
        $this->pdo->exec('CREATE TABLE articles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT,
            slug TEXT,
            subtitle TEXT,
            overview TEXT,
            img TEXT,
            link TEXT,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            log_id INTEGER,
            password TEXT,
            email TEXT,
            token TEXT,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE tokens (
            email TEXT,
            token TEXT PRIMARY KEY,
            used INTEGER DEFAULT 0,
            created_at TEXT,
            expiry_date TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            last_log TEXT,
            indirizzo TEXT,
            device TEXT,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE technology (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE projects (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            technology_id INTEGER,
            partner_id INTEGER,
            title TEXT,
            slug TEXT,
            overview TEXT,
            description TEXT,
            link TEXT,
            img TEXT,
            website TEXT,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE project_technologies (
            project_id INTEGER NOT NULL,
            technology_id INTEGER NOT NULL,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE partners (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            website TEXT,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT,
            updated_at TEXT
        )');

        $this->pdo->exec('CREATE TABLE links_footer (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            link TEXT NOT NULL,
            sort_order INTEGER NOT NULL DEFAULT 0,
            is_active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT,
            updated_at TEXT
        )');
    }
}
