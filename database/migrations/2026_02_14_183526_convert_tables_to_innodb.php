<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'campuses',
            'departments',
            'courses',
            'batches',
            'students',
            'fee_structures',
        ];

        foreach ($tables as $table) {
            DB::statement("ALTER TABLE $table ENGINE=InnoDB");
        }
    }

    public function down(): void
    {
        // No reversion to MyISAM
    }
};
