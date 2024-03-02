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
        Schema::create('page_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('field')->nullable();
            $table->unsignedSmallInteger('section_id')->index();
            $table->boolean('enable')->default(0);
            $table->unsignedTinyInteger('position');
            $table->unsignedSmallInteger('table_position')->nullable();
        });

        Schema::table('page_columns', function (Blueprint $table) {
            $table->foreign('section_id')->references('id')->on('page_column_sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_columns');
    }
};
