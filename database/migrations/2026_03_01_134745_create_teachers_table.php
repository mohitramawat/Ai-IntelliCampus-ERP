<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('department_id');
            $table->string('qualification', 200)->nullable();
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->date('joining_date')->nullable();
            $table->timestamps();

            // Foreign keys — both RESTRICT: teacher profile must not silently vanish
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->restrictOnDelete();

            $table->foreign('department_id')
                  ->references('id')->on('departments')
                  ->restrictOnDelete();

            // Constraints
            $table->unique('user_id');

            // Indexes
            $table->index('department_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
