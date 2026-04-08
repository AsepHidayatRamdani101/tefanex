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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice')->after('question_text');
            $table->json('options')->nullable()->after('type'); // For multiple choice options
            $table->string('correct_answer')->nullable()->after('options'); // For multiple choice correct answer
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'options', 'correct_answer']);
        });
    }
};
