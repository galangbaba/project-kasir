<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\ReportService;
use App\Support\SimpleXlsx;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(private ReportService $report) {}

    public function index(Request $request)
    {
        [$from, $to] = $this->periode($request);

        return view('tenant.laporan', [
            'from' => $from, 'to' => $to,
            'ringkas' => $this->report->ringkasan($from, $to),
            'harian' => $this->report->harian($from, $to),
            'terbaru' => $this->report->terbaru($from, $to, 10),
            'perProduk' => $this->report->topProduk($from, $to, 20),
            'perKasir' => $this->report->perKasir($from, $to),
        ]);
    }

    public function export(Request $request)
    {
        [$from, $to] = $this->periode($request);
        $r = $this->report->ringkasan($from, $to);

        $trx = [['No. Transaksi', 'Tanggal', 'Waktu', 'Kasir', 'Metode', 'Status', 'Total (Rp)']];
        foreach ($this->report->terbaru($from, $to, null) as $t) {
            $trx[] = [$t->no_transaksi, $t->created_at->format('Y-m-d'), $t->created_at->format('H:i'), $t->kasir?->nama ?? '-',
                ucfirst($t->metode_bayar), ucfirst($t->status), (int) $t->total_bayar];
        }

        $sheets = [
            ['name' => 'Ringkasan', 'header' => false, 'money' => [1], 'rows' => [
                ['Laporan Penjualan ' . Auth::user()->tenant->nama_tenants, ''],
                ['Periode', $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y')],
                ['Total Pendapatan', $r['omzet']],
                ['Jumlah Transaksi', $r['jumlah']],
                ['Rata-rata / Transaksi', $r['rata']],
                [''],
                ['Tanggal', 'Pendapatan', 'Transaksi'],
                ...$this->report->harian($from, $to)->map(fn ($h) => [$h['tanggal']->format('Y-m-d'), $h['omzet'], $h['jumlah']])->all(),
            ]],
            ['name' => 'Transaksi', 'money' => [6], 'rows' => $trx],
            ['name' => 'Per Produk', 'money' => [2], 'rows' => array_merge([['Produk', 'Qty', 'Omzet (Rp)']],
                $this->report->topProduk($from, $to, 1000)->map(fn ($p) => [$p->nama, (int) $p->qty, (int) $p->omzet])->all())],
            ['name' => 'Per Kasir', 'money' => [2], 'rows' => array_merge([['Kasir', 'Transaksi', 'Omzet (Rp)']],
                $this->report->perKasir($from, $to)->map(fn ($p) => [$p->nama, (int) $p->jml, (int) $p->omzet])->all())],
        ];

        $path = tempnam(sys_get_temp_dir(), 'lap');
        SimpleXlsx::write($path, $sheets);
        AuditLog::catat('laporan.ekspor', 'laporan', null, ['dari' => $from->toDateString(), 'sampai' => $to->toDateString()]);

        return response()->download($path, 'laporan-penjualan-' . $from->format('Ymd') . '-' . $to->format('Ymd') . '.xlsx',
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])->deleteFileAfterSend(true);
    }

    /** @return array{0:Carbon,1:Carbon} */
    private function periode(Request $request): array
    {
        $v = $request->validate(['dari' => ['nullable', 'date_format:Y-m-d'], 'sampai' => ['nullable', 'date_format:Y-m-d']]);
        $to = isset($v['sampai']) ? Carbon::createFromFormat('Y-m-d', $v['sampai'])->startOfDay() : now()->startOfDay();
        $from = isset($v['dari']) ? Carbon::createFromFormat('Y-m-d', $v['dari'])->startOfDay() : $to->copy()->subDays(6);
        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }
        if ($from->diffInDays($to) > 92) {
            $from = $to->copy()->subDays(92);
        }

        return [$from, $to];
    }
}
