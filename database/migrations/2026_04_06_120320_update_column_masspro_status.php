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
        Schema::table('mass_productions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'proses', 'selesai'])->default('pending')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mass_productions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'in_progress', 'done'])->default('pending')->change();
        });
    }
};
