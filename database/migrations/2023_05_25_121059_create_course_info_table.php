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
        Schema::create('course_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id')->unique();

            $table->boolean('enable_terms_conditions')->unsigned()->default(0);
            $table->boolean('enable_signature')->unsigned()->default(0);
            $table->longText('terms_conditions_text')->nullable();

            $table->boolean('enable_certification')->unsigned()->default(0);
            $table->string('certificate_img_url')->nullable();
            $table->json('certificate_coordinates')->nullable();

            $table->boolean('enable_free_trial')->unsigned()->default(0);
            $table->string('free_trial_upgrade_url')->nullable();
            $table->string('free_trial_upgrade_title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_infos');
    }
};
