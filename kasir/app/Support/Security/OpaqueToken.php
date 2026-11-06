<?php

namespace App\Support\Security;

use InvalidArgumentException;

/**
 * Token buram (opaque) — Bab 6.3.3.
 * Dibangkitkan dari sumber acak kriptografis; server hanya menyimpan HASH-nya (SHA-256 mentah, 32 byte).
 */
final class OpaqueToken
{
    /** @return array{plain: string, hash: string} */
    public static function issue(int $bytes = 32): array
    {
        if ($bytes < 16) {
            throw new InvalidArgumentException('Token minimal 128 bit (16 byte).');
        }

        $plain = rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');

        return ['plain' => $plain, 'hash' => self::hash($plain)];
    }

    public static function hash(string $plain): string
    {
        return hash('sha256', $plain, true);
    }
}
