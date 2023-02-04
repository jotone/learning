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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('img_url')->nullable();
            $table->text('about')->nullable();

            $table->unsignedInteger('status');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('last_activity')->nullable();

            $table->boolean('compromised')->default(0);
            $table->unsignedInteger('compromise_threshold')->default(2);

            $table->unsignedSmallInteger('role_id')->index();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
