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
        Schema::create('order_delivery', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade')->unique();
            $table->foreignId('courier_id')->nullable()->constrained('users')->onDelete('set null'); // FK ke user (kurir internal)
            $table->text('address_text');
            $table->string('lat', 20)->nullable();
            $table->string('lon', 20)->nullable();
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->unsignedSmallInteger('eta_minutes')->nullable();
            $table->unsignedInteger('delivery_fee');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delivery');
    }
};
