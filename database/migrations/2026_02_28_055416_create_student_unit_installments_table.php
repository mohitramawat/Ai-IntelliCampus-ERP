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
        Schema::create('student_unit_installments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('student_unit_fee_id');
            $table->unsignedTinyInteger('installment_number');
            $table->decimal('installment_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('student_unit_fee_id')
                  ->references('id')
                  ->on('student_unit_fees')
                  ->onDelete('cascade');

            $table->unique(['student_unit_fee_id', 'installment_number'], 'sui_fee_id_number_unique');

            $table->index('student_unit_fee_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_unit_installments');
    }
};
