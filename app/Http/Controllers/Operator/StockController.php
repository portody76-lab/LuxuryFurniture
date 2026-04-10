<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Halaman utama stock management
     */
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

        $products = $query->orderBy('name')->paginate(10);
        $categories = Category::all();
        
        // Hitung total stok
        $totalStock = Product::where('is_deleted', false)->sum('stock');

        return view('contents.operator.stockmanage', compact('products', 'categories', 'totalStock'));
    }

    /**
     * Tambah stok (in)
     */
    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldStock = $product->stock ?? 0;
        $newStock = $oldStock + $request->quantity;

        DB::beginTransaction();
        try {
            // Update stok produk
            $product->stock = $newStock;
            $product->save();

            // Catat transaksi
            DB::table('stock_transactions')->insert([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'in',
                'stock' => $newStock,
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('contents.operator.stock')
                ->with('success', "Stok {$product->name} bertambah {$request->quantity} unit! (Stok sekarang: {$newStock})");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal menambah stok: ' . $e->getMessage());
        }
    }

    /**
     * Kurang stok (out)
     */
    public function removeStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldStock = $product->stock ?? 0;

        if ($oldStock < $request->quantity) {
            return redirect()->back()
                ->with('error', "Stok tidak mencukupi! Stok tersedia: {$oldStock} unit");
        }

        $newStock = $oldStock - $request->quantity;

        DB::beginTransaction();
        try {
            // Update stok produk
            $product->stock = $newStock;
            $product->save();

            // Catat transaksi
            DB::table('stock_transactions')->insert([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'out',
                'stock' => $newStock,
                'transaction_date' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('contents.operator.stock')
                ->with('success', "Stok {$product->name} berkurang {$request->quantity} unit! (Stok sekarang: {$newStock})");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal mengurangi stok: ' . $e->getMessage());
        }
    }

    // /**
    //  * Riwayat transaksi stok per produk
    //  */
    // public function history($productId)
    // {
    //     $product = Product::with('category')->findOrFail($productId);
        
    //     $transactions = DB::table('stock_transactions')
    //         ->where('product_id', $productId)
    //         ->orderBy('transaction_date', 'desc')
    //         ->paginate(20);

    //     return view('contents.operator.stock-history', compact('product', 'transactions'));
    // }
}