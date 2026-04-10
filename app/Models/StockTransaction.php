<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $table = 'stock_transactions';
    
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'stock',
        'transaction_date',
        'created_by',
        'updated_by'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}