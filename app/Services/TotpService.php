<?php

declare(strict_types=1);

namespace App\Services;

class TotpService
{
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret(int $length = 32): string
    {
        $secret = '';

        while (strlen($secret) < $length) {
            $secret .= self::BASE32_ALPHABET[random_int(0, strlen(self::BASE32_ALPHABET) - 1)];
        }

        return substr($secret, 0, $length);
    }

    public static function provisioningUri(string $label, string $secret, string $issuer): string
    {
        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s',
            rawurlencode($issuer . ':' . $label),
            rawurlencode($secret),
            rawurlencode($issuer)
        );
    }

    public static function verify(string $secret, string $code, int $window = 1, ?int $timestamp = null): bool
    {
        $normalizedCode = preg_replace('/\s+/', '', $code) ?? '';

        if (!preg_match('/^\d{6}$/', $normalizedCode)) {
            return false;
        }

        $timestamp ??= time();
        $counter = (int) floor($timestamp / 30);

        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals(self::codeForCounter($secret, $counter + $offset), $normalizedCode)) {
                return true;
            }
        }

        return false;
    }

    public static function currentCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp ??= time();
        return self::codeForCounter($secret, (int) floor($timestamp / 30));
    }

    private static function codeForCounter(string $secret, int $counter): string
    {
        $secretKey = self::decodeBase32($secret);
        $binaryCounter = pack('N*', 0) . pack('N*', $counter);
        $hash = hash_hmac('sha1', $binaryCounter, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $chunk = substr($hash, $offset, 4);
        $value = unpack('N', $chunk)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    private static function decodeBase32(string $secret): string
    {
        $secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret) ?? '');
        $buffer = 0;
        $bitsLeft = 0;
        $decoded = '';

        foreach (str_split($secret) as $char) {
            $value = strpos(self::BASE32_ALPHABET, $char);

            if ($value === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            while ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $decoded .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $decoded;
    }
}
