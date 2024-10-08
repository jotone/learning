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
        Schema::create('admin_menu', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('name');
            $table->string('route');
            $table->string('img')->nullable();
            $table->unsignedSmallInteger('parent_id')->nullable();
            $table->unsignedSmallInteger('position');
            $table->unsignedTinyInteger('section');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_menu');
    }
};
