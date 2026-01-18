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
        Schema::table('order_payments', function (Blueprint $table) {
            // Tambahkan kolom untuk menyimpan path (alamat file) bukti pembayaran
            // Kolom ini boleh NULL karena COD tidak perlu upload bukti.
            $table->string('proof_image_path')->nullable()->after('validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_payments', function (Blueprint $table) {
            // Saat rollback, hapus kolom yang baru dibuat
            $table->dropColumn('proof_image_path');
        });
    }
};