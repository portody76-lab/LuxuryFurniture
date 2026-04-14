<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\ProductCodeHelper;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_deleted', false);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name or product code
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

        return view('contents.operator.productmanage', compact('products', 'categories', 'totalProduct'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $productCode = ProductCodeHelper::generate($request->category_id);

        $product = new Product();
        $product->product_code = $productCode;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->is_deleted = false;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        return redirect()->route('contents.operator.productmanage')
            ->with('success', 'Produk berhasil ditambahkan! (Kode: ' . $productCode . ')');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_code' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $product->image = $path;
        }

        $product->save();

        $message = 'Produk berhasil diupdate!';
        if ($oldCategoryId != $newCategoryId) {
            $message .= ' Kode produk diperbarui menjadi: ' . $product->product_code;
        }

        return redirect()->route('contents.operator.productmanage')
            ->with('success', $message);
    }

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
            if ($product->image && file_exists(storage_path('app/public/' . $product->image))) {
                unlink(storage_path('app/public/' . $product->image));
            }

            $product->delete();

            $message = 'Produk berhasil dihapus permanen.';
        }

        return redirect()->route('contents.operator.productmanage')
            ->with('success', $message);
    }

    /**
     * Menampilkan produk yang sudah soft delete (trash)
     */
    public function trash(Request $request)
    {
        $query = Product::with('category')->where('is_deleted', true);

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search by name or product code
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

        // Tentukan view berdasarkan role user
        $userRole = auth()->user()->role->role_name;
        
        if ($userRole === 'super_admin') {
            return view('contents.super_admin.productmanage-trash', compact('products', 'categories', 'totalTrash'));
        } else {
            return view('contents.operator.productmanage-trash', compact('products', 'categories', 'totalTrash'));
        }
    }

    /**
     * Restore produk yang sudah soft delete
     */
    public function restore($id)
    {
        $product = Product::where('is_deleted', true)->findOrFail($id);
        $product->is_deleted = false;
        $product->save();

        // Redirect berdasarkan role user
        $userRole = auth()->user()->role->role_name;
        if ($userRole === 'super_admin') {
            return redirect()->route('contents.super_admin.products.trash')
                ->with('success', 'Produk "' . $product->name . '" berhasil direstore!');
        } else {
            return redirect()->route('contents.operator.productmanage.trash')
                ->with('success', 'Produk "' . $product->name . '" berhasil direstore!');
        }
    }
}