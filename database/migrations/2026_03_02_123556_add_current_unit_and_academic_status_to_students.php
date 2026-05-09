<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Academic progress tracking
            $table->unsignedTinyInteger('current_unit')->default(1)->after('is_active');
            $table->enum('academic_status', ['active', 'completed', 'dropped'])->default('active')->after('current_unit');

            $table->index('current_unit');
            $table->index('academic_status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['current_unit']);
            $table->dropIndex(['academic_status']);
            $table->dropColumn(['current_unit', 'academic_status']);
        });
    }
};
