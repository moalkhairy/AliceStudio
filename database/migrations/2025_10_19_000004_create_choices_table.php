<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();

            $table->string('label');
            $table->string('slug'); // unique per section
            $table->string('token')->nullable();          // prompt fragment / tag
            $table->string('negative_token')->nullable(); // optional
            $table->decimal('weight', 5, 2)->nullable();
            $table->string('icon_url')->nullable();

            // Optional: per-choice gender hint; section-level gender is authoritative
            $table->enum('gender_scope', ['male', 'female', 'both'])->default('both');

            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['section_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};
