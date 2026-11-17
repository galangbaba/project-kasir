<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kasir;
use App\Models\Menu;

class KasirController extends Controller
{
   public function dashboard()
    {
        $jumlahKasir = kasir::count();
        $jumlahMenu = Menu::count();

        // Diisi setelah tabel transaksi dibuat
        $jumlahTransaksi = 0;
        $pendapatan = 0;

        $menus = Menu::latest()->take(5)->get();

        return view('dashboard', compact(
            'jumlahKasir',
            'jumlahMenu',
            'jumlahTransaksi',
            'pendapatan',
            'menus'
        ));
    }

    public function kasir() {

        $Kasirs = Kasir::all();

        return view('kasir.kasir', compact('Kasirs'));
    }

    public function menu() {
        $menus = Menu::all();
        return view('menu.menu', compact('menus'));
    }

    public function transaksi() {
        return view('transaksi.transaksi');
    }
}
