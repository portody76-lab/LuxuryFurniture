<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $categories = $query->orderBy('id', 'asc')->paginate(10);

        return view('contents.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.string' => 'Nama kategori harus berupa teks',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'name.unique' => 'Nama kategori sudah ada',
        ]);

        Category::create([
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('contents.categories')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ], [
            'name.required' => 'Nama kategori wajib diisi',
            'name.string' => 'Nama kategori harus berupa teks',
            'name.max' => 'Nama kategori maksimal 255 karakter',
            'name.unique' => 'Nama kategori sudah ada',
        ]);

        $category = Category::findOrFail($id);

        $category->update([
            'name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('contents.categories')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        $hasProducts = Product::where('category_id', $id)->exists();

        if ($hasProducts) {
            return redirect()->route('contents.categories')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki produk!');
        }

        $category->delete();

        return redirect()->route('contents.categories')
            ->with('success', 'Kategori berhasil dihapus');
    }
}