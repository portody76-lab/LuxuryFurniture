<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // Hapus use SoftDeletes jika ada
    
    protected $fillable = [
        'name',
        'created_by',
        'updated_by'
    ];

    /**
     * Relasi ke Product
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Relasi ke User (created_by)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke User (updated_by)
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}