<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coin_packages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('coins');
            $table->decimal('price', 10, 2);
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->decimal('final_price', 10, 2); // store computed for audit
            $table->string('currency', 8)->default('AED');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coin_packages');
    }
};
