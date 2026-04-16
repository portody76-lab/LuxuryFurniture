<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StockController extends Controller
{
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

        $products = $query->orderBy('name')->paginate(10);
        $categories = Category::all();
        $totalStock = Product::where('is_deleted', false)->sum('stock');

        return view('contents.stockmanage', compact('products', 'categories', 'totalStock'));
    }

    protected function getStockRoute()
    {
        return route('contents.stockmanage');
    }

    public function addStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|min:3|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $product->addStock(
                quantity: $request->quantity,
                userId: $userId,
                description: $request->description,
                condition: 'good'
            );

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Stok {$product->name} bertambah {$request->quantity} unit! (Stok sekarang: {$product->stock})"
                ]);
            }

            return redirect()->to($this->getStockRoute())
                ->with('success', "Stok {$product->name} bertambah {$request->quantity} unit! (Stok sekarang: {$product->stock})");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambah stok: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'Gagal menambah stok: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function removeStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|min:3|max:500',
            'condition' => 'required|in:good,damaged',
            'damage_reason' => 'required_if:condition,damaged|nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->product_id);
        $userId = Auth::id();

        if ($product->stock < $request->quantity) {
            $errorMsg = "Stok tidak mencukupi! Stok tersedia: {$product->stock} unit";

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 400);
            }

            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $product->removeStock(
                quantity: $request->quantity,
                userId: $userId,
                description: $request->description,
                condition: $request->condition,
                damageReason: $request->damage_reason
            );

            DB::commit();

            $conditionText = $request->condition == 'damaged' ? ' (Rusak)' : '';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Stok {$product->name} berkurang {$request->quantity} unit{$conditionText}! (Stok sekarang: {$product->stock})"
                ]);
            }

            return redirect()->to($this->getStockRoute())
                ->with('success', "Stok {$product->name} berkurang {$request->quantity} unit{$conditionText}! (Stok sekarang: {$product->stock})");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengurangi stok: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()
                ->with('error', 'Gagal mengurangi stok: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function history($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $transactions = StockTransaction::where('product_id', $productId)
                ->orderBy('transaction_date', 'desc')
                ->get();

            $result = [];
            foreach ($transactions as $transaction) {
                $userName = 'System';
                if ($transaction->created_by) {
                    $user = \App\Models\User::find($transaction->created_by);
                    $userName = $user ? ($user->username ?? 'Unknown') : 'Unknown';
                }

                $result[] = [
                    'id' => $transaction->id,
                    'nomor' => $transaction->id,
                    'tanggal' => $transaction->transaction_date 
                        ? $transaction->transaction_date->translatedFormat('d F Y H:i') . ' WIB'
                        : '-',
                    'kode' => $product->product_code,
                    'nama' => $product->name,
                    'user' => $userName,
                    'jenis' => $transaction->type === 'in' ? 'Masuk' : 'Keluar',
                    'jumlah' => $transaction->quantity,
                    'kondisi' => $transaction->condition === 'good' ? 'Aman' : 'Rusak',
                    'deskripsi' => $transaction->description ?? '-',
                    'alasan_rusak' => $transaction->damage_reason ?? '-',
                ];
            }

            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->product_code,
                    'current_stock' => $product->stock,
                ],
                'transactions' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function detail($productId)
    {
        $product = Product::with('category')->findOrFail($productId);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'code' => $product->product_code,
                'name' => $product->name,
                'category' => $product->category->name ?? '-',
                'stock' => $product->stock,
                'min_stock_threshold' => $product->min_stock_threshold ?? 25,
                'image_url' => $product->image_url,
                'description' => $product->description ?? '-',
            ]
        ]);
    }
}