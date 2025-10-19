<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->cascadeOnDelete();

            $table->string('code');      // e.g., prompt, style, camera
            $table->string('title');     // e.g., Describe Your Image
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable(); // emoji or icon key
            $table->text('tip_text')->nullable();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['studio_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('steps');
    }
};
