<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kasir;
use App\Models\Menu;

class KasirController extends Controller
{
   public function dashboard()
    {
        return view('dashboard', [
            'jumlahKasir' => Kasir::count(),
            'jumlahMenu' => Menu::count(),
            'jumlahTransaksi' => 0,
        ]);
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
