<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('staff_type', 50); // accounts, writer, admin_assistant, etc.
            $table->date('joining_date')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->restrictOnDelete();

            $table->foreign('department_id')
                  ->references('id')->on('departments')
                  ->restrictOnDelete();

            // Constraints
            $table->unique('user_id');

            // Indexes
            $table->index('staff_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};
