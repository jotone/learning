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
        Schema::create('main_menu', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('url');
            $table->string('img')->nullable();
            $table->string('img_color')->nullable();
            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->unsignedSmallInteger('position');
            $table->boolean('trial_upgrade')->default(0);
            $table->json('courses')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_menu');
    }
};
