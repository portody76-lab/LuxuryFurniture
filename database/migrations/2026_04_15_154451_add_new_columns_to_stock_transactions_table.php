<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            // Tambah kolom condition (good/damaged)
            if (!Schema::hasColumn('stock_transactions', 'condition')) {
                $table->enum('condition', ['good', 'damaged'])->default('good')->after('quantity');
            }
            
            // Tambah kolom description
            if (!Schema::hasColumn('stock_transactions', 'description')) {
                $table->text('description')->nullable()->after('condition');
            }
            
            // Tambah kolom damage_reason
            if (!Schema::hasColumn('stock_transactions', 'damage_reason')) {
                $table->string('damage_reason')->nullable()->after('description');
            }
            
            // Tambah kolom user_id (tanpa foreign key dulu)
            if (!Schema::hasColumn('stock_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('product_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stock_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('stock_transactions', 'user_id')) {
                $table->dropColumn('user_id');
            }
            
            if (Schema::hasColumn('stock_transactions', 'condition')) {
                $table->dropColumn('condition');
            }
            
            if (Schema::hasColumn('stock_transactions', 'description')) {
                $table->dropColumn('description');
            }
            
            if (Schema::hasColumn('stock_transactions', 'damage_reason')) {
                $table->dropColumn('damage_reason');
            }
        });
    }
};