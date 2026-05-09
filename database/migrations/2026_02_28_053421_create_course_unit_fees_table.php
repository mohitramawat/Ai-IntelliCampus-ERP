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
        Schema::create('course_unit_fees', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('fee_structure_id');
            $table->unsignedTinyInteger('unit_number');
            $table->string('unit_name', 50)->nullable();
            $table->decimal('unit_fee', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('fee_structure_id')
                  ->references('id')
                  ->on('fee_structures')
                  ->onDelete('cascade');
            
            $table->unique(['fee_structure_id', 'unit_number']);
            $table->index('fee_structure_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_unit_fees');
    }
};
