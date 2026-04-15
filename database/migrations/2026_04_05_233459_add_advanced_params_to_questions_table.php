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
        Schema::table('questions', function (Blueprint $table) {

            $table->boolean('shuffle_options')->default(false);
            $table->boolean('limit_time_enabled')->default(false);
            $table->integer('time_limit')->default(0);
            $table->boolean('show_dropdown_matching')->default(false);
            $table->boolean('use_audio')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {

        });
    }
};
