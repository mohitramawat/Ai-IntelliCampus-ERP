<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand teachers table with full academic profile fields.
     *
     * HOD ARCHITECTURE NOTE (Step 2):
     * There is NO separate hod table. A HOD is simply a Teacher record
     * whose associated User has the Spatie role 'hod' assigned.
     * Structural assumption: a Teacher with role 'hod' MUST have a
     * non-null department_id (already enforced at FK level).
     * Role assignment and validation happens at the controller/service layer.
     */
    public function up(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('employee_code')->unique()->after('user_id');
            $table->string('phone_number', 20)->nullable()->after('qualification');
            $table->text('address')->nullable()->after('phone_number');
            $table->string('gender', 10)->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->string('profile_photo')->nullable()->after('date_of_birth'); // file path only
            $table->enum('status', ['active', 'inactive'])->default('active')->after('profile_photo');

            // Indexes
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropUnique(['employee_code']);
            $table->dropIndex(['status']);
            $table->dropColumn([
                'employee_code',
                'phone_number',
                'address',
                'gender',
                'date_of_birth',
                'profile_photo',
                'status',
            ]);
        });
    }
};
