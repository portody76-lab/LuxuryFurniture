<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;

class OperatorController extends Controller
{
    public function dashboard()
    {
        $products = Product::with('category')
            ->where('is_deleted', false)
            ->latest()
            ->paginate(10);

        $categories = Category::all();
        $totalProduct = Product::where('is_deleted', false)->count();

        return view('operator.dashboard', compact('products', 'categories', 'totalProduct'));
    }
}