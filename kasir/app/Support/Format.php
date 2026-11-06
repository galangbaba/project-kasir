<?php

namespace App\Support;

final class Format
{
    public static function rupiah(int|float|null $n): string
    {
        return 'Rp' . number_format((float) ($n ?? 0), 0, ',', '.');
    }

    /** Rp4,2 jt / Rp640 rb / Rp0 */
    public static function rupiahSingkat(int|float|null $n): string
    {
        $n = (float) ($n ?? 0);
        if ($n >= 1_000_000) {
            return 'Rp' . rtrim(rtrim(number_format($n / 1_000_000, 1, ',', '.'), '0'), ',') . ' jt';
        }
        if ($n >= 1_000) {
            return 'Rp' . number_format($n / 1_000, 0, ',', '.') . ' rb';
        }

        return 'Rp' . number_format($n, 0, ',', '.');
    }

    public static function jt(int|float $n): string
    {
        return rtrim(rtrim(number_format($n / 1_000_000, 1, '.', ''), '0'), '.') . 'jt';
    }

    public static function tanggal(\DateTimeInterface|string $d): string
    {
        $d = \Illuminate\Support\Carbon::parse($d)->locale('id');

        return $d->translatedFormat('j F Y');
    }

    /** "16 - 22 September 2026" atau "28 September - 4 Oktober 2026" */
    public static function periode(\DateTimeInterface $from, \DateTimeInterface $to): string
    {
        $f = \Illuminate\Support\Carbon::instance($from)->locale('id');
        $t = \Illuminate\Support\Carbon::instance($to)->locale('id');

        if ($f->isSameDay($t)) {
            return $t->translatedFormat('j F Y');
        }
        if ($f->isSameMonth($t)) {
            return $f->format('j') . ' - ' . $t->translatedFormat('j F Y');
        }
        if ($f->isSameYear($t)) {
            return $f->translatedFormat('j F') . ' - ' . $t->translatedFormat('j F Y');
        }

        return $f->translatedFormat('j F Y') . ' - ' . $t->translatedFormat('j F Y');
    }
}
