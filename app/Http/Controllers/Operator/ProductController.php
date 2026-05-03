<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ProductCodeHelper;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk aktif
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_deleted', false);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        $totalProduct = Product::where('is_deleted', false)->count();

        return view('contents.productmanage', compact('products', 'categories', 'totalProduct'));
    }

    /**
     * Menyimpan produk baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        $productCode = ProductCodeHelper::generate($request->category_id);

        $product = new Product();
        $product->product_code = $productCode;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->is_deleted = false;
        $product->save();

        return redirect()->route('contents.productmanage')
            ->with('success', 'Produk berhasil ditambahkan! (Kode: ' . $productCode . ')');
    }

    /**
     * Mengupdate produk
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_code' => 'required|string',
        ]);

        $oldCategoryId = $product->category_id;
        $newCategoryId = $request->category_id;

        $product->name = $request->name;
        $product->category_id = $newCategoryId;

        if ($oldCategoryId != $newCategoryId) {
            $product->product_code = ProductCodeHelper::generate($newCategoryId);
        } else {
            $product->product_code = $request->product_code;
        }

        $product->save();

        $message = 'Produk berhasil diupdate!';
        if ($oldCategoryId != $newCategoryId) {
            $message .= ' Kode produk diperbarui menjadi: ' . $product->product_code;
        }

        return redirect()->route('contents.productmanage')
            ->with('success', $message);
    }

    /**
     * Menghapus produk (soft delete jika punya transaksi, hard delete jika tidak)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $hasTransactions = DB::table('stock_transactions')
            ->where('product_id', $id)
            ->exists();

        if ($hasTransactions) {
            $product->is_deleted = true;
            $product->save();

            $message = 'Produk berhasil disembunyikan (soft delete).';
        } else {
            $product->delete();

            $message = 'Produk berhasil dihapus permanen.';
        }

        return redirect()->route('contents.productmanage')
            ->with('success', $message);
    }

    /**
     * Menampilkan produk yang sudah soft delete (Trash) - HANYA SUPER ADMIN
     */
    public function trash(Request $request)
    {
        // Hanya Super Admin yang bisa akses
        if (auth()->user()->role->role_name !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        $query = Product::with('category')->where('is_deleted', true);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        $totalTrash = Product::where('is_deleted', true)->count();

        return view('contents.super_admin.productmanage-trash', compact('products', 'categories', 'totalTrash'));
    }

    /**
     * Mengembalikan produk dari trash - HANYA SUPER ADMIN
     */
    public function restore($id)
    {
        // Hanya Super Admin yang bisa akses
        if (auth()->user()->role->role_name !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        $product = Product::where('is_deleted', true)->findOrFail($id);
        $product->is_deleted = false;
        $product->save();

        return redirect()->route('contents.productmanage.trash')
            ->with('success', 'Produk "' . $product->name . '" berhasil direstore!');
    }

    /**
     * Menghapus permanen produk dari trash - HANYA SUPER ADMIN
     */
    public function forceDelete($id)
    {
        // Hanya Super Admin yang bisa akses
        if (auth()->user()->role->role_name !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }

        $product = Product::where('is_deleted', true)->findOrFail($id);
        
        
        $product->forceDelete();
        
        return redirect()->route('contents.productmanage.trash')
            ->with('success', 'Produk berhasil dihapus permanen!');
    }
}