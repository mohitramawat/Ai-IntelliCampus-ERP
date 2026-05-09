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
        Schema::create('courses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('department_id');
            $table->string('name', 150);
            $table->string('code', 20);
            $table->unsignedTinyInteger('duration_years');
            $table->enum('unit_type', ['semester', 'year']);
            $table->unsignedTinyInteger('total_units');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');

            // Constraints
            $table->unique(['department_id', 'code']);

            // Indexes
            $table->index('department_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
