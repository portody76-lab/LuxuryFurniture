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
        'updated_by',
        'condition',        // baru: good/damaged
        'description',      // baru: deskripsi wajib
        'damage_reason',    // baru: alasan jika rusak
    ];

    // Relasi ke User (siapa yang melakukan transaksi)
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Accessor untuk menampilkan kondisi dalam Bahasa Indonesia
    public function getConditionLabelAttribute()
    {
        return $this->condition === 'good' ? 'Aman' : 'Rusak';
    }

    // Accessor untuk menampilkan type dalam Bahasa Indonesia
    public function getTypeLabelAttribute()
    {
        return $this->type === 'in' ? 'Masuk' : 'Keluar';
    }

    // Scope filter
    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeGood($query)
    {
        return $query->where('condition', 'good');
    }

    public function scopeDamaged($query)
    {
        return $query->where('condition', 'damaged');
    }
}