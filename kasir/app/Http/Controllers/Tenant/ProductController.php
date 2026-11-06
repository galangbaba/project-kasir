<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Services\StokService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function __construct(private StokService $stok) {}

    public function index()
    {
        return view('tenant.produk.index', [
            'products' => Product::query()->with('category')->orderByDesc('is_active')->orderBy('nama_product')->paginate(15),
        ]);
    }

    public function create()
    {
        return view('tenant.produk.form', ['product' => new Product(['is_active' => true]), 'kategori' => $this->kategori()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules() + ['stok' => ['required', 'integer', 'min:0', 'max:1000000']]);

        $product = DB::transaction(function () use ($data) {
            $product = new Product(['nama_product' => $data['nama_product'], 'harga' => $data['harga']]);
            $product->category_id = $this->kategoriId($data['kategori'] ?? null);
            $product->stok = 0;
            $product->is_active = true;
            $product->save();
            if ($data['stok'] > 0) {
                $this->stok->ubah($product, 'awal', $data['stok'], 'Stok awal');
            }

            return $product;
        });
        AuditLog::catat('produk.tambah', 'products', $product->id, ['nama' => $product->nama_product, 'harga' => $product->harga]);

        return redirect()->route('tenant.produk')->with('ok', 'Produk ditambahkan.');
    }

    public function edit(Product $product)
    {
        $this->milikSaya($product);
        $logs = \App\Models\StockLog::query()->where('product_id', $product->id)->latest('id')->limit(8)->get();

        return view('tenant.produk.form', ['product' => $product->load('category'), 'kategori' => $this->kategori(), 'logs' => $logs]);
    }

    public function update(Request $request, Product $product)
    {
        $this->milikSaya($product);
        $data = $request->validate($this->rules());
        $lama = $product->only(['nama_product', 'harga']);

        $product->fill(['nama_product' => $data['nama_product'], 'harga' => $data['harga']]);
        $product->category_id = $this->kategoriId($data['kategori'] ?? null);
        $product->save();
        AuditLog::catat('produk.ubah', 'products', $product->id, ['sebelum' => $lama, 'sesudah' => $product->only(['nama_product', 'harga'])]);

        return redirect()->route('tenant.produk')->with('ok', 'Produk diperbarui.');
    }

    /** Penyesuaian stok: restock (tambah) atau koreksi (set nilai baru) — selalu tercatat di stock_logs (FR-ADM-03). */
    public function stok(Request $request, Product $product)
    {
        $this->milikSaya($product);
        $data = $request->validate([
            'jenis' => ['required', Rule::in(['restock', 'koreksi'])],
            'jumlah' => ['required', 'integer', 'min:0', 'max:1000000'],
            'keterangan' => [Rule::requiredIf($request->input('jenis') === 'koreksi'), 'nullable', 'string', 'max:200'],
        ]);
        if ($data['jenis'] === 'restock' && $data['jumlah'] < 1) {
            return back()->withErrors(['jumlah' => 'Jumlah restock minimal 1.'])->withInput();
        }

        DB::transaction(function () use ($product, $data) {
            $locked = Product::query()->whereKey($product->id)->lockForUpdate()->firstOrFail();
            $delta = $data['jenis'] === 'restock' ? $data['jumlah'] : $data['jumlah'] - $locked->stok;
            if ($delta !== 0) {
                $this->stok->ubah($locked, $data['jenis'], $delta, $data['keterangan'] ?? null);
            }
            AuditLog::catat('stok.' . $data['jenis'], 'products', $locked->id, ['delta' => $delta, 'stok' => $locked->stok]);
        });

        return redirect()->route('tenant.produk.edit', $product)->with('ok', 'Stok diperbarui.');
    }

    public function toggle(Product $product)
    {
        $this->milikSaya($product);
        $product->is_active = ! $product->is_active;
        $product->save();
        AuditLog::catat('produk.status', 'products', $product->id, ['aktif' => $product->is_active]);

        return back()->with('ok', $product->is_active ? 'Produk diaktifkan.' : 'Produk dinonaktifkan (tidak tampil di layar kasir).');
    }

    /** Pertahanan berlapis di atas global scope: produk harus milik tenant pengguna. */
    private function milikSaya(Product $product): void
    {
        abort_unless($product->tenant_id === Auth::user()->tenant_id, 404);
    }

    private function rules(): array
    {
        return [
            'nama_product' => ['required', 'string', 'max:100'],
            'harga' => ['required', 'integer', 'min:1', 'max:100000000'],
            'kategori' => ['nullable', 'string', 'max:60'],
        ];
    }

    private function kategori()
    {
        return Category::query()->orderBy('nama_kategori')->pluck('nama_kategori');
    }

    private function kategoriId(?string $nama): ?int
    {
        $nama = trim((string) $nama);

        return $nama === '' ? null : Category::query()->firstOrCreate(['nama_kategori' => $nama])->id;
    }
}
