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
        Schema::create('course_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->index();
            $table->unsignedInteger('related_id')->index();
            $table->unsignedTinyInteger('relation_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_relations');
    }
};
