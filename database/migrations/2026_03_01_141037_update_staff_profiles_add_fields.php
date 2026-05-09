<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->string('employee_code')->unique()->after('user_id');
            $table->string('phone_number', 20)->nullable()->after('staff_type');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('gender', 10)->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('date_of_birth');

            // Indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropUnique(['employee_code']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'employee_code',
                'phone_number',
                'address',
                'gender',
                'date_of_birth',
                'status',
            ]);
        });
    }
};
