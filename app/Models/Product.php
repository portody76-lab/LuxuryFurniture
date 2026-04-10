<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'category_id',
        'name',
        'image',
        'is_deleted',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
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

    // Accessor for image URL
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholder.png');
    }

    // Calculate current stock
    public function getStockAttribute(): int
    {
        $stockIn  = $this->stockTransactions->where('type', 'in')->sum('quantity');
        $stockOut = $this->stockTransactions->where('type', 'out')->sum('quantity');
        return $stockIn - $stockOut;
    }
}
