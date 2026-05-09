<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lecture_session_id');
            $table->unsignedBigInteger('student_id');
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->timestamp('marked_at')->nullable();
            $table->decimal('student_gps_lat', 10, 7)->nullable();
            $table->decimal('student_gps_long', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('lecture_session_id')->references('id')->on('lecture_sessions')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');

            $table->unique(['lecture_session_id', 'student_id']);
            $table->index('lecture_session_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
