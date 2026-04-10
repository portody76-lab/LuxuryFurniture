<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * LIST + SEARCH CATEGORY
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // search by id or name
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $categories = $query
            ->orderBy('id', 'asc')
            ->paginate(10);

        return view('contents.admin.categories', compact('categories'));
    }

    /**
     * STORE CATEGORY (ADD)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Category berhasil ditambahkan');
    }

    /**
     * UPDATE CATEGORY
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Category berhasil diupdate');
    }

/**
     * DELETE CATEGORY
     * Hanya bisa dihapus jika TIDAK ADA produk (termasuk yang soft delete)
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // CEK APAKAH KATEGORI MEMILIKI PRODUK (TERMASUK YANG SOFT DELETE)
        $hasProducts = Product::where('category_id', $id)->exists();
        // exists() akan true jika ada 1 saja produk, termasuk yang is_deleted = true

        if ($hasProducts) {
            return redirect()
                ->back()
                ->with('error', 'Category tidak bisa dihapus karena masih memiliki produk! Hapus atau pindahkan produk terlebih dahulu.');
        }

        // Hapus kategori (HARD DELETE karena tidak pakai soft delete)
        $category->delete();

        return redirect()
            ->back()
            ->with('success', 'Category berhasil dihapus');
    }

}