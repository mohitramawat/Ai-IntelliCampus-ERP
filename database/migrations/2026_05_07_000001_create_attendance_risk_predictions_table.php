<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AI Attendance Risk Predictions Table
     * Stores the AI-generated risk predictions for each student,
     * including Hugging Face API analysis results.
     */
    public function up(): void
    {
        Schema::create('attendance_risk_predictions', function (Blueprint $table) {
            $table->id();

            // Student reference
            $table->foreignId('student_id')
                ->constrained('students')
                ->onDelete('cascade');

            // Attendance Metrics (snapshot at time of prediction)
            $table->decimal('attendance_percentage', 5, 2)->default(0.00);
            $table->unsignedInteger('total_present')->default(0);
            $table->unsignedInteger('total_absent')->default(0);
            $table->unsignedInteger('total_lectures')->default(0);
            $table->unsignedInteger('consecutive_absences')->default(0);

            // AI Prediction Output
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->text('ai_remark')->nullable();
            $table->text('suggested_action')->nullable();

            // AI Model metadata
            $table->string('ai_model_used')->nullable()->default('meta-llama/Llama-3.2-3B-Instruct');
            $table->json('raw_ai_response')->nullable(); // store raw JSON for debugging

            // Analysis context
            $table->date('prediction_date')->nullable();

            $table->timestamps();

            // Index for fast lookups
            $table->index(['student_id', 'created_at']);
            $table->index('risk_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_risk_predictions');
    }
};
