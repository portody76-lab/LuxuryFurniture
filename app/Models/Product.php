<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'category_id',
        'name',
        // 'image', ← HAPUS
        'stock',
        'is_deleted',
        'created_by',
        'updated_by',
        'min_stock_threshold', // threshold stok minimal (default 25)
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'min_stock_threshold' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // HAPUS method getImageUrlAttribute
    // public function getImageUrlAttribute(): string
    // {
    //     if ($this->image) {
    //         return asset('storage/' . $this->image);
    //     }
    //     return asset('images/placeholder.png');
    // }

    // Calculate current stock (gunakan kolom stock langsung, bukan dari transaksi)
    public function getCurrentStockAttribute(): int
    {
        return $this->stock;
    }

    // Method untuk menambah stok
    public function addStock($quantity, $userId, $description = null, $condition = 'good')
    {
        $oldStock = $this->stock;
        $this->stock += $quantity;
        $this->save();

        return $this->stockTransactions()->create([
            'type' => 'in',
            'quantity' => $quantity,
            'stock' => $this->stock, // stok setelah transaksi
            'transaction_date' => now(),
            'created_by' => $userId,
            'updated_by' => $userId,
            'condition' => $condition,
            'description' => $description,
            'damage_reason' => null,
        ]);
    }

    // Method untuk mengurangi stok
    public function removeStock($quantity, $userId, $description = null, $condition = 'good', $damageReason = null)
    {
        if ($this->stock < $quantity) {
            throw new \Exception('Stok tidak mencukupi');
        }

        $oldStock = $this->stock;
        $this->stock -= $quantity;
        $this->save();

        return $this->stockTransactions()->create([
            'type' => 'out',
            'quantity' => $quantity,
            'stock' => $this->stock, // stok setelah transaksi
            'transaction_date' => now(),
            'created_by' => $userId,
            'updated_by' => $userId,
            'condition' => $condition,
            'description' => $description,
            'damage_reason' => $damageReason,
        ]);
    }

    // Cek apakah stok hampir habis (<= threshold)
    public function isLowStock()
    {
        $threshold = $this->min_stock_threshold ?? 25;
        return $this->stock <= $threshold;
    }

    // Hitung frekuensi transaksi (untuk ranking)
    public function getTransactionFrequencyAttribute()
    {
        return $this->stockTransactions()->count();
    }
}