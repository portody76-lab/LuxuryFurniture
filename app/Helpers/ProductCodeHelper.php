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
        
        // Mapping nama kategori ke kode (3 huruf)
        $codeMapping = [
            'Kursi' => 'KRS',
            'Meja' => 'MJA',
            'Lemari' => 'LMR',
            'Sofa' => 'SFA',
            'Rak' => 'RAK',
            'Tempat Tidur' => 'TTD',
            'Buffet' => 'BFT',
            'Lampu' => 'LMP',
            'Dekorasi' => 'DKR',
            'Aksesoris' => 'AKS',
        ];
        
        // Ambil kode dari mapping, jika tidak ada ambil 3 huruf pertama
        $categoryCode = $codeMapping[$category->name] ?? strtoupper(substr($category->name, 0, 3));
        
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