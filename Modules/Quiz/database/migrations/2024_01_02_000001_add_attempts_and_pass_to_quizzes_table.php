<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // 0 = unlimited attempts; >= 1 = maximum attempts per employee.
            $table->unsignedInteger('attempts_allowed')->default(0)->after('can_view_result');
            $table->decimal('pass_percentage', 5, 2)->default(60)->after('attempts_allowed');
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['attempts_allowed', 'pass_percentage']);
        });
    }
};
