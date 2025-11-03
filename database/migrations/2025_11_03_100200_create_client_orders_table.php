<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coin_package_id')->constrained('coin_packages')->cascadeOnDelete();
            $table->string('currency', 8)->default('AED');
            $table->decimal('price', 10, 2);
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->decimal('final_price', 10, 2);
            $table->string('status', 20)->default('paid'); // mocked success for now
            $table->string('payment_ref')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_orders');
    }
};
