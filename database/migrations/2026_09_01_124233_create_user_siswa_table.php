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
        Schema::create('user_siswa', function (Blueprint $table) {
        $table->id()->primary();
        $table->uuid('user_id');
        $table->unsignedInteger('siswa_id');
        $table->string('hubungan')->nullable();

        $table->timestamps();

        $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->cascadeOnDelete();

        $table->foreign('siswa_id')
            ->references('id')
            ->on('siswa')
            ->cascadeOnDelete();

        $table->unique(['user_id', 'siswa_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_siswa');
    }
};
