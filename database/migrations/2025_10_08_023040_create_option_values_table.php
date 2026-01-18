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
        Schema::create('option_values', function (Blueprint $table) {
            $table->id();
            // Foreign Key ke options:
            $table->foreignId('option_id')->constrained()->onDelete('cascade'); 
            $table->string('name', 50); 
            $table->integer('price_modifier')->default(0); // Perubahan harga (bisa positif/negatif)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_values');
    }
};
