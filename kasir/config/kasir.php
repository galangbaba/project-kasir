<?php

return [
    // Batas keamanan login (SRS UC-01: terkunci 15 menit setelah 5 kali gagal)
    'max_login_attempts' => 5,
    'lock_minutes'       => 15,

    // Masa hidup sesi (token opaque tersimpan di basis data) — Bab 6: TTL + cek tiap permintaan
    'session_ttl_hours'  => 8,
    'session_cookie'     => 'kasir_session',

    // Aturan bisnis
    'void_minutes'       => 15,  // batas waktu kasir membatalkan transaksi (FR-KAS-04)
    'low_stock'          => 5,   // stok <= angka ini dianggap "menipis"
    'trx_start'          => 1000,
];
