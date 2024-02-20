<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url')->unique()->index();
            $table->text('description')->nullable();
            $table->string('img_url')->nullable();
            $table->string('lang', 15)->default('en');

            $table->string('sale_page_url')->nullable();
            $table->string('expire_url')->nullable();
            $table->unsignedTinyInteger('status')->default(2);

            $table->unsignedTinyInteger('tracking_type')->default(0);
            $table->unsignedTinyInteger('tracking_status')->default(0);

            $table->unsignedTinyInteger('optional_duration')->nullable();
            $table->string('optional_expire_page')->nullable();

            $table->unsignedInteger('category_id')->nullable();
            $table->unsignedInteger('instructor_id')->nullable();

            $table->boolean('invitation_email')->unsigned()->default(1);
            $table->unsignedSmallInteger('position');
            $table->dateTime('published_at')->nullable();

            $table->boolean('terms_conditions_enable')->unsigned()->default(0);
            $table->longText('terms_conditions_text')->nullable();
            $table->boolean('signature_enable')->unsigned()->default(0);

            $table->boolean('certificate_enable')->unsigned()->default(0);
            $table->string('certificate_img_url')->nullable();
            $table->json('certificate_coordinates')->nullable();

            $table->boolean('free_trial_enable')->unsigned()->default(0);
            $table->string('free_trial_upgrade_url')->nullable();
            $table->string('free_trial_upgrade_title')->nullable();

            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
