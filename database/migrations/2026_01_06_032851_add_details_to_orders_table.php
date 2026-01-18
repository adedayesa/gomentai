<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Cek payment_method
            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->default('COD');
            }
            
            // Cek notes
            if (!Schema::hasColumn('orders', 'notes')) {
                $table->text('notes')->nullable();
            }

            // Cek status (Ini yang menyebabkan error tadi)
            if (!Schema::hasColumn('orders', 'status')) {
                $table->string('status')->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'notes', 'status']);
        });
    }
};
