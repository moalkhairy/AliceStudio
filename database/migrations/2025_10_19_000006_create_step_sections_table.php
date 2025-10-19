<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('step_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('studio_id')->constrained('studios')->cascadeOnDelete();
            $table->foreignId('step_id')->constrained('steps')->cascadeOnDelete();
            $table->foreignId('section_id')->constrained('sections')->cascadeOnDelete();

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // strict: the same section can appear in at most one step (per studio)
            $table->unique(['studio_id', 'section_id']);
            // avoid duplicates inside a step
            $table->unique(['step_id', 'section_id']);
            // helpful index for ordering
            $table->index(['step_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('step_sections');
    }
};
