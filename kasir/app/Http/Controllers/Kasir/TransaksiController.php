<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaksi;
use App\Services\TransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransaksiController extends Controller
{
    /** FR-KAS-05: kasir hanya MELIHAT produk aktif; tidak ada endpoint ubah produk/stok untuk peran ini. */
    public function create()
    {
        return view('kasir.transaksi', [
            'products' => Product::query()->where('is_active', true)->orderBy('nama_product')->get(),
        ]);
    }

    public function store(Request $request, TransaksiService $service)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['integer', 'min:0', 'max:9999'],
            'metode_bayar' => ['required', Rule::in(['tunai', 'transfer'])],
        ]);

        $items = [];
        foreach ($data['items'] as $id => $qty) {
            if (is_numeric($id)) {
                $items[(int) $id] = (int) $qty;
            }
        }

        $trx = $service->buat(Auth::user(), $items, $data['metode_bayar']);

        return redirect()->route('kasir.struk', $trx);
    }

    public function struk(Transaksi $transaksi)
    {
        abort_unless($transaksi->user_id === Auth::id(), 404); // kasir hanya melihat transaksinya sendiri

        return view('kasir.struk', ['trx' => $transaksi->load('details', 'kasir', 'tenant')]);
    }

    public function batal(Transaksi $transaksi, TransaksiService $service)
    {
        $service->batalkan(Auth::user(), $transaksi->id);

        return redirect()->route('kasir.struk', $transaksi)->with('ok', 'Transaksi dibatalkan dan stok dikembalikan.');
    }

    public function riwayat()
    {
        $q = Transaksi::query()->where('user_id', Auth::id())
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()]);

        $selesai = (clone $q)->where('status', 'selesai');

        return view('kasir.riwayat', [
            'daftar' => $q->orderByDesc('created_at')->orderByDesc('id')->get(),
            'jumlah' => (clone $selesai)->count(),
            'total' => (int) (clone $selesai)->sum('total_bayar'),
        ]);
    }
}
