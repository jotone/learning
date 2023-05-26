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
        Schema::create('course_testimonials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->unique();

            $table->boolean('enable')->default(0);
            $table->unsignedTinyInteger('min_progress')->default(0);
            $table->boolean('show_on_skip')->default(0);
            $table->text('testimonial_text')->nullable();

            $table->boolean('description_enable')->default(0);
            $table->boolean('description_required')->default(0);

            $table->boolean('video_enable')->default(0);
            $table->boolean('video_required')->default(0);

            $table->boolean('lessons_description_enable')->default(0);
            $table->boolean('lessons_description_required')->default(0);
            $table->text('lessons_description_text')->nullable();
            $table->boolean('lessons_video_enable')->default(0);
            $table->boolean('lessons_video_required')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_testimonials');
    }
};
