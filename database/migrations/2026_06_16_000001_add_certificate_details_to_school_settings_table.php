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
        Schema::table('school_settings', function (Blueprint $table) {
            $table->string('certificate_number')->nullable()->comment('Nomor sertifikat (misal: No. 001/2026)');
            $table->string('certificate_location')->nullable()->comment('Tempat pengeluaran sertifikat');
            $table->string('certificate_template')->nullable()->comment('Template design JPG untuk background sertifikat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['certificate_number', 'certificate_location', 'certificate_template']);
        });
    }
};
