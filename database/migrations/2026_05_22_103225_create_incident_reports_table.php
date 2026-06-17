<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('employee_id')->nullable();
            $table->string('email');
            $table->string('department')->nullable();
            $table->date('incident_date');
            $table->time('incident_time')->nullable();
            $table->string('incident_type');
            $table->string('assets_affected')->nullable();
            $table->enum('severity', ['critical', 'high', 'medium', 'low'])->default('medium');
            $table->text('description');
            $table->text('actions_taken')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['new', 'in_review', 'investigating', 'resolved', 'closed'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
