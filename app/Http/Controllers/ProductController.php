<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\ProductCodeHelper; // ← TAMBAHKAN INI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the products (Dashboard Operator)
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stockTransactions'])
            ->where('is_deleted', false);

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        // 🎯 Filter category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 📦 Pagination
        $products = $query->orderBy('created_at', 'desc')
                          ->paginate(10)
                          ->withQueryString();

        // Pastikan data yang dikirim tidak null
        $categories = Category::all() ?? collect();
        $totalProduct = Product::where('is_deleted', false)->count();

        return view('operator.dashboard', compact('products', 'categories', 'totalProduct'));
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        // VALIDASI - Hapus 'product_code' dari validasi
        $request->validate([
            // 'product_code' dihapus karena akan di-generate otomatis
            'name'         => 'required|string|max:255',
            'category_id'  => 'required|exists:categories,id',
            'image'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        // 📸 Upload image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 🔥 GENERATE KODE PRODUK OTOMATIS menggunakan helper
        $productCode = ProductCodeHelper::generate($request->category_id);

        $product = Product::create([
            'product_code' => $productCode, // ← Pakai hasil generate
            'name'         => $request->name,
            'category_id'  => $request->category_id,
            'image'        => $imagePath,
            'is_deleted'   => false,
            'created_by'   => Auth::id(),
            'updated_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('operator.dashboard')
            ->with('success', 'Produk berhasil ditambahkan! Kode produk: ' . $product->product_code);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'product_code' => [
                'required',
                'string',
                Rule::unique('products', 'product_code')->ignore($product->id),
            ],
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = $product->image;

        // 📸 Update image
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'product_code' => $request->product_code,
            'name'         => $request->name,
            'category_id'  => $request->category_id,
            'image'        => $imagePath,
            'updated_by'   => Auth::id(),
        ]);

        return redirect()
            ->route('operator.dashboard')
            ->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Soft delete the specified product
     */
    public function destroy(Product $product)
    {
        $product->update([
            'is_deleted' => true,
            'updated_by' => Auth::id(),
        ]);

        return redirect()
            ->route('operator.dashboard')
            ->with('success', 'Produk berhasil dihapus!');
    }
}