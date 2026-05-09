<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();

            $table->unsignedBigInteger('student_id');
            $table->string('document_type');       // e.g. 10th_marksheet, aadhaar, tc
            $table->string('file_name');            // original filename
            $table->string('file_path');            // storage path on local disk
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();

            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Foreign keys
            // student_id: RESTRICT — document must not vanish if student reference is broken
            $table->foreign('student_id')
                  ->references('id')->on('students')
                  ->restrictOnDelete();

            // uploaded_by / verified_by: nullOnDelete — user can be removed without losing document record
            $table->foreign('uploaded_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            $table->foreign('verified_by')
                  ->references('id')->on('users')
                  ->nullOnDelete();

            // One document per type per student
            $table->unique(['student_id', 'document_type'], 'uq_student_document_type');

            // Indexes
            $table->index('student_id');
            $table->index('document_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
