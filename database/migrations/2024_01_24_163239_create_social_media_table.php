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
        Schema::create('social_media', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('type');
            $table->string('caption')->nullable();
            $table->string('link')->nullable();
            $table->string('img')->nullable();
            $table->tinyInteger('position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_media');
    }
};
