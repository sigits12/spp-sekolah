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
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->text('pesan_terkirim')->nullable()->after('total_bayar');
            $table->enum('status_pesan', ['terkirim', 'gagal'])->default('terkirim')->after('pesan_terkirim');
            $table->text('keterangan_gagal')->nullable()->after('status_pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropColumn(['pesan_terkirim', 'status_pesan', 'keterangan_gagal']);
        });
    }
};
