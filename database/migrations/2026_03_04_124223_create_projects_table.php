<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
        $table->string('judul');
        $table->text('deskripsi')->nullable();
        $table->string('client')->nullable();
        $table->enum('status', [
            'design_brief',
            'timeline',
            'design',
            'produksi',
            'qc',
            'mass_production',
            'invoice',
            'selesai'
        ])->default('design_brief');
        $table->foreignId('guru_id')->constrained('users');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
