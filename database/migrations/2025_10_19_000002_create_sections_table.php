<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('studio_id')->constrained('studios')->cascadeOnDelete();

            $table->foreignId('group_id')->nullable()->constrained('groups')->nullOnDelete();
            // null => standalone section; set => belongs to a group (e.g., Model Style)

            $table->string('code'); // unique within studio
            $table->string('name');

            $table->text('help_text')->nullable();
            $table->enum('input_type', ['chips', 'text', 'textarea', 'upload'])->default('chips');
            $table->enum('selection_mode', ['single', 'multiple'])->default('multiple');
            $table->unsignedInteger('min_select')->nullable();
            $table->unsignedInteger('max_select')->nullable();

            // Gender classification at the Section level (main classification)
            $table->enum('gender_scope', ['male', 'female', 'both'])->default('both');

            $table->boolean('is_required')->default(false);
            $table->boolean('is_general')->default(false); // for global controls (lighting, aspect ratio, etc.)

            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['studio_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
