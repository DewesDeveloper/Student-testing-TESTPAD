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
            $table->boolean('enable_grading')->default(true);
            $table->string('grading_type')->default('percent');
            $table->integer('grade_5_threshold')->default(85);
            $table->integer('grade_4_threshold')->default(65);
            $table->integer('grade_3_threshold')->default(55);
            $table->string('grade_label')->default('Ваша оценка:');
            $table->boolean('show_result_to_user')->default(true);
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
