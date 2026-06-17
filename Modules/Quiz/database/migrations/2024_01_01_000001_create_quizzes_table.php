<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('banner')->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->decimal('marks_per_question', 8, 2)->default(1);
            $table->enum('duration_type', ['per_question', 'full_paper'])->nullable();
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->unsignedInteger('duration_per_question')->nullable(); // seconds
            $table->boolean('can_review_paper')->default(false);
            $table->boolean('can_view_result')->default(true);
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->enum('language', ['en', 'hi'])->default('en');
            $table->string('title_hi')->nullable();
            $table->text('description_hi')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
