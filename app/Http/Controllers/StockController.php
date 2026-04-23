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
            $product->stock += $request->quantity;
            $product->save();

            StockTransaction::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'in',
                'condition' => 'good',
                'description' => $request->description,
                'transaction_date' => now(),
                'created_by' => $userId,
            ]);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Stok {$product->name} bertambah {$request->quantity} unit! (Stok sekarang: {$product->stock})"
                ]);
            }

            return redirect()->route('stock')
                ->with('success', "Stok {$product->name} bertambah {$request->quantity} unit!");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambah stok: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Gagal menambah stok: ' . $e->getMessage());
        }
    }

    public function removeStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'description' => 'required|string|min:3|max:500',
            'condition' => 'required|in:good,damaged',
        ]);

        $product = Product::findOrFail($request->product_id);
        $userId = Auth::id();

        if ($product->stock < $request->quantity) {
            $errorMsg = "Stok tidak mencukupi! Stok tersedia: {$product->stock} unit";

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 400);
            }

            return redirect()->back()->with('error', $errorMsg);
        }

        DB::beginTransaction();
        try {
            $product->stock -= $request->quantity;
            $product->save();

            StockTransaction::create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'type' => 'out',
                'condition' => $request->condition,
                'description' => $request->description,
                'transaction_date' => now(),
                'created_by' => $userId,
            ]);

            DB::commit();

            $conditionText = $request->condition == 'damaged' ? ' (Rusak)' : '';

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => "Stok {$product->name} berkurang {$request->quantity} unit{$conditionText}!"
                ]);
            }

            return redirect()->route('stock')
                ->with('success', "Stok {$product->name} berkurang {$request->quantity} unit{$conditionText}!");
        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengurangi stok: ' . $e->getMessage()
                ], 400);
            }

            return redirect()->back()->with('error', 'Gagal mengurangi stok: ' . $e->getMessage());
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

                $tanggal = '-';
                if ($transaction->transaction_date) {
                    $date = \Carbon\Carbon::parse($transaction->transaction_date);
                    $tanggal = $date->translatedFormat('d F Y');
                }

                $result[] = [
                    'id' => $transaction->id,
                    'tanggal' => $tanggal,
                    'user' => $userName,
                    'jenis' => $transaction->type === 'in' ? 'Masuk' : 'Keluar',
                    'jumlah' => $transaction->quantity,
                    'kondisi' => $transaction->condition === 'good' ? 'Aman' : 'Rusak',
                    'deskripsi' => $transaction->description ?? '-',
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
                'description' => $product->description ?? '-',
            ]
        ]);
    }
}