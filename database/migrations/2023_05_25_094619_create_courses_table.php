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
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url')->index();
            $table->text('description')->nullable();
            $table->string('img_url')->nullable();
            $table->string('link')->nullable();
            $table->string('expire_link')->nullable();
            $table->string('support_link')->nullable();
            $table->string('fb_link')->nullable();
            $table->unsignedSmallInteger('status')->default(0);

            $table->unsignedTinyInteger('tracking_type')->default(0);
            $table->unsignedTinyInteger('tracking_status')->default(0);

            $table->unsignedTinyInteger('optional_duration')->nullable();
            $table->string('optional_expire_page')->nullable();

            $table->boolean('invitation_email')->unsigned()->default(1);
            $table->unsignedSmallInteger('position');
            $table->timestamps();
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
