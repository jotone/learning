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
        Schema::create('page_column_sections', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->string('page');
            $table->string('icon')->nullable();
            $table->unsignedSmallInteger('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_column_sections');
    }
};
