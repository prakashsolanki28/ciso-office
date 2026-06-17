<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->text('question_text_hi')->nullable();
            $table->enum('question_type', ['single', 'multiple', 'true_false', 'text']);
            $table->decimal('marks', 8, 2)->nullable(); // per-question override
            $table->text('notes')->nullable();
            $table->text('correct_text_answer')->nullable(); // for text type
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
