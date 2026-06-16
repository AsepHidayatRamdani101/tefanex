<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->boolean('certificate_enabled')->default(true)->after('school_logo');
            $table->string('certificate_title')->nullable()->after('certificate_enabled');
            $table->text('certificate_subtitle')->nullable()->after('certificate_title');
            $table->text('certificate_footer')->nullable()->after('certificate_subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['certificate_enabled', 'certificate_title', 'certificate_subtitle', 'certificate_footer']);
        });
    }
};
