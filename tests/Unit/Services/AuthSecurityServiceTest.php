<?php

declare(strict_types=1);

use App\Core\DataLayer\Runtime\ORM;
use App\Services\AuthSessionService;
use App\Services\RateLimitService;
use App\Services\TotpService;
use PHPUnit\Framework\TestCase;

class AuthSecurityServiceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        ORM::init($this->pdo, 'sqlite');

        $this->pdo->exec('CREATE TABLE rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip TEXT NOT NULL,
            route TEXT NOT NULL,
            attempts INTEGER NOT NULL DEFAULT 0,
            last_attempt_at TEXT NOT NULL
        )');

        $this->pdo->exec('CREATE TABLE sessions (
            id TEXT PRIMARY KEY,
            user_id INTEGER NOT NULL,
            ip TEXT NOT NULL,
            user_agent TEXT NULL,
            last_activity TEXT NOT NULL,
            created_at TEXT NULL
        )');
    }

    public function testTotpServiceGeneratesVerifiableCode(): void
    {
        $secret = TotpService::generateSecret(32);
        $code = TotpService::currentCode($secret, 1711891800);

        $this->assertMatchesRegularExpression('/^[A-Z2-7]{32}$/', $secret);
        $this->assertTrue(TotpService::verify($secret, $code, 0, 1711891800));
        $this->assertFalse(TotpService::verify($secret, '12345', 0, 1711891800));
    }

    public function testTotpProvisioningUriContainsIssuerLabelAndSecret(): void
    {
        $uri = TotpService::provisioningUri('admin@example.com', 'ABCDEF1234567890', 'Soft MVC');

        $this->assertStringContainsString('otpauth://totp/', $uri);
        $this->assertStringContainsString('Soft%20MVC%3Aadmin%40example.com', $uri);
        $this->assertStringContainsString('secret=ABCDEF1234567890', $uri);
        $this->assertStringContainsString('issuer=Soft%20MVC', $uri);
    }

    public function testRateLimitServiceBlocksAttemptsOverConfiguredThreshold(): void
    {
        $first = RateLimitService::hit('127.0.0.1', 'POST /login', 2, 900);
        $second = RateLimitService::hit('127.0.0.1', 'POST /login', 2, 900);
        $third = RateLimitService::hit('127.0.0.1', 'POST /login', 2, 900);

        $this->assertTrue($first['allowed']);
        $this->assertTrue($second['allowed']);
        $this->assertFalse($third['allowed']);
        $this->assertSame(3, $third['attempts']);
        $this->assertGreaterThanOrEqual(0, $third['retry_after']);
    }

    public function testRateLimitServiceResetsExpiredWindow(): void
    {
        RateLimitService::hit('127.0.0.1', 'POST /contatti', 1, 60);

        $expired = date('Y-m-d H:i:s', time() - 120);
        $this->pdo->exec("UPDATE rate_limits SET last_attempt_at = '{$expired}', attempts = 9");

        $result = RateLimitService::hit('127.0.0.1', 'POST /contatti', 1, 60);

        $this->assertTrue($result['allowed']);
        $this->assertSame(1, $result['attempts']);
    }

    public function testAuthSessionServiceTracksAndTerminatesSessions(): void
    {
        $session = AuthSessionService::create('sess-1', 10, '127.0.0.1', 'Firefox');

        $this->assertSame('sess-1', $session->id);
        $this->assertSame(10, $session->user_id);

        $all = AuthSessionService::getForUser(10);
        $this->assertCount(1, $all);

        AuthSessionService::touch('sess-1');
        $updated = AuthSessionService::find('sess-1');
        $this->assertNotNull($updated);
        $this->assertSame('127.0.0.1', $updated->ip);

        AuthSessionService::terminate('sess-1');
        $this->assertNull(AuthSessionService::find('sess-1'));
    }
}
