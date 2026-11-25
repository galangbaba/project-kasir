
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuApiController extends Controller
{
    // MENAMPILKAN SEMUA MENU
    public function index()
    {
        $menus = Menu::all();

        return response()->json([
            'success' => true,
            'message' => 'Data menu berhasil ditampilkan',
            'data' => $menus
        ], 200);
    }

    // MENAMPILKAN DETAIL MENU
    public function show($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail menu berhasil ditampilkan',
            'data' => $menu
        ], 200);
    }

    // CREATE - MENAMBAHKAN MENU
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ]);

        $menu = Menu::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan',
            'data' => $menu
        ], 201);
    }

    // UPDATE - MENGUBAH MENU
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nama_menu' => 'sometimes|required|string|max:255',
            'kategori' => 'sometimes|nullable|string|max:255',
            'harga' => 'sometimes|required|numeric|min:0',
            'stok' => 'sometimes|required|integer|min:0',
        ]);

        $menu->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui',
            'data' => $menu
        ], 200);
    }

    // DELETE - MENGHAPUS MENU
    public function destroy($id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu tidak ditemukan'
            ], 404);
        }

        $menu->delete();

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus'
        ], 200);
    }
}
