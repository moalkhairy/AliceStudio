<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 80);
            $table->string('last_name', 80)->nullable();
            $table->string('email')->unique();
            $table->string('password'); // hashed
            $table->string('phone', 40)->nullable();

            // Social auth support
            $table->string('provider')->nullable();   // e.g. google, facebook
            $table->string('provider_id')->nullable();
            $table->string('avatar')->nullable();

            // Optional remember/login helpers
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            $table->timestamps();

            $table->index(['provider', 'provider_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
