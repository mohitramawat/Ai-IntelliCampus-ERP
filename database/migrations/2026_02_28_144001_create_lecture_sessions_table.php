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
        Schema::create('lecture_sessions', function (Blueprint $table) {
            $table->id();
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('teacher_subject_assignment_id');
            $table->date('lecture_date');
            $table->unsignedTinyInteger('period_number');
            $table->timestamp('start_time');
            $table->timestamp('end_time')->nullable();
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->decimal('teacher_gps_lat', 10, 7);
            $table->decimal('teacher_gps_long', 10, 7);
            $table->integer('gps_radius_meters')->default(15);
            $table->integer('attendance_window_minutes')->default(10);
            $table->timestamps();

            $table->foreign('teacher_subject_assignment_id', 'ls_tsa_id_foreign')
                  ->references('id')->on('teacher_subject_assignments')
                  ->onDelete('cascade');

            $table->index('teacher_subject_assignment_id');
            $table->index('lecture_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_sessions');
    }
};
