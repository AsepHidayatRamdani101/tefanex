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
        Schema::table('design_briefs', function (Blueprint $table) {
            $table->json('reference_files')->nullable()->after('reference_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('design_briefs', function (Blueprint $table) {
            $table->dropColumn('reference_files');
        });
    }
};
