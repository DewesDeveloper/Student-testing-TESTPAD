<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tests', function (Blueprint $table) {
            $table->boolean('show_numbers')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_error_reports')->default(true);
            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);
            $table->boolean('require_all_answers')->default(false);
            $table->boolean('show_progress_bar')->default(true);
            $table->boolean('show_time')->default(false);
            $table->boolean('limit_time')->default(false);
            $table->boolean('prevent_copy')->default(false);
            $table->boolean('prevent_back')->default(false);
            $table->boolean('confirm_next')->default(false);
            $table->boolean('confirm_finish')->default(true);
            $table->boolean('show_correct_instantly')->default(false);
            $table->boolean('show_dropdown')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tests', function (Blueprint $table) {
            //
        });
    }
};
