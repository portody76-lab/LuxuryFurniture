<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StockTransaction;
use App\Models\Category;

class Product extends Model
{
    public function transactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
