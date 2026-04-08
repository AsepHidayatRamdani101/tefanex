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
        Schema::table('test_results', function (Blueprint $table) {
            $table->integer('manual_score')->nullable()->after('score');
            $table->integer('task_score')->nullable()->after('manual_score');
            $table->text('attitude_note')->nullable()->after('task_score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('test_results', function (Blueprint $table) {
            $table->dropColumn(['manual_score', 'task_score', 'attitude_note']);
        });
    }
};
