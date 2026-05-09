<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lecture_sessions', function (Blueprint $table) {
            $table->boolean('is_ultrasonic')->default(false)->after('status');
            $table->string('ultrasonic_token')->nullable()->after('is_ultrasonic');
        });
    }

    public function down(): void
    {
        Schema::table('lecture_sessions', function (Blueprint $table) {
            $table->dropColumn(['is_ultrasonic', 'ultrasonic_token']);
        });
    }
};
