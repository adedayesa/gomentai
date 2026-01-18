<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_option', function (Blueprint $table) {
            // Foreign Key ke products:
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); 
            // Foreign Key ke options:
            $table->foreignId('option_id')->constrained()->onDelete('cascade');
            
            // Primary Key gabungan (agar produk dan opsi tidak terulang):
            $table->primary(['product_id', 'option_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_option');
    }
};
