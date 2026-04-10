<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'type',
        'transaction_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
