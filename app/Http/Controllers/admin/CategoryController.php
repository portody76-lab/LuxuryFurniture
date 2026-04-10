<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * LIST + SEARCH CATEGORY
     */
    public function index(Request $request)
    {
        $query = Category::query();

        // search by id atau name
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

        return view('admin.categories', compact('categories'));
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
     * DELETE CATEGORY (AUTO CHECK TRANSACTION)
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // cek apakah kategori punya transaksi lewat products
        $hasTransaction = $category->products()
            ->whereHas('transactions')
            ->exists();

        if ($hasTransaction) {
            // SOFT DELETE (deleted_at)
            $category->delete();
        } else {
            // HARD DELETE
            $category->forceDelete();
        }

        return redirect()
            ->back()
            ->with('success', 'Category berhasil dihapus');
    }
}
