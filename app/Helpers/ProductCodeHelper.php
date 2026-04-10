<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\Category;

class ProductCodeHelper
{
    /**
     * Generate kode produk otomatis berdasarkan kategori
     *
     * @param int $categoryId
     * @return string
     */
    public static function generate($categoryId)
    {
        // Ambil data kategori
        $category = Category::find($categoryId);
        
        // Jika kategori tidak ditemukan
        if (!$category) {
            return 'PRD-001';
        }
        
        // Mapping dengan lowercase (case insensitive)
        $codeMapping = [
            'kursi' => 'KRS',
            'meja' => 'MJA',
            'lemari' => 'LMR',
            'sofa' => 'SFA',
            'rak' => 'RAK',
            'tempat tidur' => 'TTD',
            'buffet' => 'BFT',
            'lampu' => 'LMP',
            'dekorasi' => 'DKR',
            'aksesoris' => 'AKS',
            'pintu' => 'PTU',
        ];
        
        // Trim dan lowercase untuk mapping
        $categoryName = strtolower(trim($category->name));
        
        // Cari mapping, jika tidak ada ambil 3 huruf pertama
        $categoryCode = $codeMapping[$categoryName] ?? strtoupper(substr($category->name, 0, 3));
        
        // Cari produk terakhir dengan kode yang sama
        $lastProduct = Product::where('product_code', 'LIKE', $categoryCode . '-%')
                              ->orderBy('id', 'desc')
                              ->first();
        
        if ($lastProduct) {
            // Ambil nomor urut terakhir (3 digit terakhir)
            $lastNumber = (int) substr($lastProduct->product_code, -3);
            $newNumber = $lastNumber + 1;
            return $categoryCode . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
        }
        
        // Jika belum ada produk dengan kategori ini
        return $categoryCode . '-001';
    }
}