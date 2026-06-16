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
            if (! Schema::hasColumn('school_settings', 'certificate_number')) {
                $table->string('certificate_number')->nullable()->after('certificate_footer');
            }

            if (! Schema::hasColumn('school_settings', 'certificate_place')) {
                $table->string('certificate_place')->nullable()->after('certificate_number');
            }

            if (! Schema::hasColumn('school_settings', 'certificate_template')) {
                $table->string('certificate_template')->nullable()->after('certificate_place');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('school_settings', function (Blueprint $table) {
            $columns = [];

            if (Schema::hasColumn('school_settings', 'certificate_number')) {
                $columns[] = 'certificate_number';
            }

            if (Schema::hasColumn('school_settings', 'certificate_place')) {
                $columns[] = 'certificate_place';
            }

            if (Schema::hasColumn('school_settings', 'certificate_template')) {
                $columns[] = 'certificate_template';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
