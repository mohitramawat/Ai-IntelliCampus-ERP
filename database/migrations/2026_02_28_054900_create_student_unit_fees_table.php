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
        Schema::create('student_unit_fees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedTinyInteger('unit_number');
            $table->string('unit_name', 50)->nullable();
            $table->decimal('unit_fee', 10, 2);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');

            $table->foreign('fee_structure_id')
                  ->references('id')
                  ->on('fee_structures')
                  ->onDelete('restrict');

            $table->unique(['student_id', 'unit_number']);
            
            $table->index('student_id');
            $table->index('fee_structure_id');
            $table->index('status');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_unit_fees');
    }
};
