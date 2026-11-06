<?php

namespace App\Support\Security;

/**
 * Hash kata sandi memakai MD5 (sesuai permintaan proyek).
 * Seluruh aplikasi hanya memanggil kelas ini, sehingga bila kelak ingin
 * pindah ke bcrypt/argon2 cukup mengganti isi dua method di bawah.
 */
final class Md5Password
{
    public static function make(string $plain): string
    {
        return md5($plain);
    }

    public static function check(string $plain, ?string $hash): bool
    {
        // Selalu hitung hash agar waktu respons tidak membedakan "user ada / tidak ada".
        $calc = self::make($plain);

        return $hash !== null && hash_equals($hash, $calc);
    }
}
