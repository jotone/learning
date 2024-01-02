<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('img_url')->nullable();
            $table->text('about')->nullable();

            $table->unsignedTinyInteger('status')->default(2);
            $table->dateTime('activated_at')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->unsignedInteger('time_online')->default(0);

            $table->boolean('compromised')->default(0);
            $table->unsignedInteger('compromise_threshold')->default(2);

            $table->unsignedSmallInteger('role_id')->index();

            $table->string('timezone')->nullable();
            $table->string('country')->nullable();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('ext_addr')->nullable();
            $table->string('zip', 31)->nullable();
            $table->string('phone', 31)->nullable();
            $table->unsignedSmallInteger('shirt_size')->nullable();

            $table->string('signature')->nullable();
            $table->string('signature_ip', 15)->nullable();
            $table->dateTime('signature_date')->nullable();

            $table->rememberToken();
            $table->dateTime('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
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
