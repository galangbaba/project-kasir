<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductApiController extends Controller
{
    // ==========================================
    // MENAMPILKAN SEMUA PRODUK
    // ==========================================
    public function index()
    {
        $user = Auth::user();

        if (!$user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak memiliki tenant!'
            ], 403);
        }

        $products = Product::where('tenant_id', $user->tenant_id)
            ->with('category')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data produk berhasil ditampilkan!',
            'data' => $products
        ], 200);
    }

    // ==========================================
    // MENAMPILKAN DETAIL PRODUK
    // ==========================================
    public function show($id)
    {
        $product = $this->findProduct($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan!'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail produk berhasil ditampilkan!',
            'data' => $product->load('category')
        ], 200);
    }

    // ==========================================
    // MENAMBAHKAN PRODUK
    // ==========================================
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->tenant_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak memiliki tenant!'
            ], 403);
        }

        $data = $request->validate([
            'nama_product' => 'required|string|max:100',
            'harga' => 'required|integer|min:0',
            'stok' => 'required|integer|min:0',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        // Memastikan kategori milik tenant sendiri
        if (!empty($data['category_id'])) {
            $categoryExists = Category::where(
                'id',
                $data['category_id']
            )
            ->where('tenant_id', $user->tenant_id)
            ->exists();

            if (!$categoryExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan!'
                ], 422);
            }
        }

        // Membuat produk menggunakan forceFill
        $product = new Product();

        $product->forceFill([
            'tenant_id' => $user->tenant_id,
            'category_id' => $data['category_id'] ?? null,
            'nama_product' => $data['nama_product'],
            'harga' => $data['harga'],
            'stok' => $data['stok'],
            'is_active' => true,
        ]);

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan!',
            'data' => $product
        ], 201);
    }

    // ==========================================
    // UPDATE PRODUK
    // ==========================================
    public function update(Request $request, $id)
    {
        $product = $this->findProduct($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan!'
            ], 404);
        }

        $user = Auth::user();

        $data = $request->validate([
            'nama_product' => 'sometimes|required|string|max:100',
            'harga' => 'sometimes|required|integer|min:0',
            'stok' => 'sometimes|required|integer|min:0',
            'category_id' => 'sometimes|nullable|exists:categories,id',
        ]);

        // Memastikan kategori milik tenant sendiri
        if (
            array_key_exists('category_id', $data) &&
            $data['category_id'] !== null
        ) {
            $categoryExists = Category::where(
                'id',
                $data['category_id']
            )
            ->where('tenant_id', $user->tenant_id)
            ->exists();

            if (!$categoryExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak ditemukan!'
                ], 422);
            }
        }

        // Update data produk menggunakan forceFill
        $product->forceFill([
            'nama_product' => $data['nama_product'] ?? $product->nama_product,
            'harga' => $data['harga'] ?? $product->harga,
            'stok' => $data['stok'] ?? $product->stok,
            'category_id' => array_key_exists('category_id', $data)
                ? $data['category_id']
                : $product->category_id,
        ]);

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui!',
            'data' => $product
        ], 200);
    }

    // ==========================================
    // DELETE PRODUK
    // ==========================================
    public function destroy($id)
    {
        $product = $this->findProduct($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan!'
            ], 404);
        }

        // Menonaktifkan produk agar riwayat transaksi tetap aman
        $product->forceFill([
            'is_active' => false
        ]);

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus!'
        ], 200);
    }

    // ==========================================
    // MENCARI PRODUK BERDASARKAN TENANT
    // ==========================================
    private function findProduct($id)
    {
        $user = Auth::user();

        if (!$user || !$user->tenant_id) {
            return null;
        }

        return Product::where('tenant_id', $user->tenant_id)
            ->where('id', $id)
            ->first();
    }
}
