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
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('campus_id');
            $table->string('name', 150);
            $table->string('code', 20);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('hod_user_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('hod_user_id')->references('id')->on('users')->onDelete('set null');

            // Unique Constraints
            $table->unique(['campus_id', 'code']);

            // Indexes
            $table->index('campus_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
