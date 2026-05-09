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
        Schema::create('installment_fines', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->id();
            $table->unsignedBigInteger('student_unit_installment_id');
            $table->unsignedBigInteger('fine_rule_id');
            $table->decimal('fine_amount', 10, 2);
            $table->date('applied_on');
            $table->boolean('is_paid')->default(false);
            $table->timestamps();

            $table->foreign('student_unit_installment_id', 'if_sui_id_foreign')
                  ->references('id')
                  ->on('student_unit_installments')
                  ->onDelete('cascade');
            $table->foreign('fine_rule_id')
                  ->references('id')
                  ->on('fine_rules')
                  ->onDelete('cascade');

            $table->unique(['student_unit_installment_id', 'fine_rule_id'], 'if_sui_id_rule_id_unique');

            $table->index('student_unit_installment_id');
            $table->index('is_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_fines');
    }
};
