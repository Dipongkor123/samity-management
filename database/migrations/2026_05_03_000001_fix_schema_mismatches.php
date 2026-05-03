<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Fixes schema mismatches between original migrations and application code.
 * Safe to run on both fresh installs (tables won't have old enum values)
 * and existing installs (alters columns to match app expectations).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── samities ──────────────────────────────────────────────────────
        if (Schema::hasTable('samities')) {
            // cycle_type: was enum('weekly','monthly'), now string (supports yearly too)
            DB::statement("ALTER TABLE samities MODIFY COLUMN cycle_type VARCHAR(20) NOT NULL DEFAULT 'monthly'");

            // meeting_day: was integer, now string (day names like 'Saturday')
            DB::statement("ALTER TABLE samities MODIFY COLUMN meeting_day VARCHAR(20) NULL");

            // start_date: make nullable if not already
            DB::statement("ALTER TABLE samities MODIFY COLUMN start_date DATE NULL");

            // Add unique index on name if not present
            if (!$this->indexExists('samities', 'samities_name_unique')) {
                Schema::table('samities', function (Blueprint $table) {
                    $table->unique('name');
                });
            }
        }

        // ── deposits ──────────────────────────────────────────────────────
        if (Schema::hasTable('deposits')) {
            // status: was enum('paid','unpaid','late'), now string for paid/pending
            DB::statement("ALTER TABLE deposits MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'paid'");
        }

        // ── loans ─────────────────────────────────────────────────────────
        if (Schema::hasTable('loans')) {
            // status: was enum('pending','approved','rejected','running','closed')
            // now string for active/completed/overdue
            DB::statement("ALTER TABLE loans MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active'");
        }

        // ── fines ─────────────────────────────────────────────────────────
        if (Schema::hasTable('fines')) {
            // reason: was enum('late_payment','absent','other'), now free-text string
            DB::statement("ALTER TABLE fines MODIFY COLUMN reason VARCHAR(255) NOT NULL");

            // status: was enum('paid','unpaid'), now string for pending/paid/waived
            DB::statement("ALTER TABLE fines MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        // Restore original enums (for rollback)
        if (Schema::hasTable('samities')) {
            DB::statement("ALTER TABLE samities MODIFY COLUMN cycle_type ENUM('weekly','monthly') NOT NULL DEFAULT 'monthly'");
            DB::statement("ALTER TABLE samities MODIFY COLUMN meeting_day INT NULL");
            DB::statement("ALTER TABLE samities MODIFY COLUMN start_date DATE NOT NULL");
        }
        if (Schema::hasTable('deposits')) {
            DB::statement("ALTER TABLE deposits MODIFY COLUMN status ENUM('paid','unpaid','late') NOT NULL DEFAULT 'unpaid'");
        }
        if (Schema::hasTable('loans')) {
            DB::statement("ALTER TABLE loans MODIFY COLUMN status ENUM('pending','approved','rejected','running','closed') NOT NULL DEFAULT 'pending'");
        }
        if (Schema::hasTable('fines')) {
            DB::statement("ALTER TABLE fines MODIFY COLUMN reason ENUM('late_payment','absent','other') NOT NULL DEFAULT 'other'");
            DB::statement("ALTER TABLE fines MODIFY COLUMN status ENUM('paid','unpaid') NOT NULL DEFAULT 'unpaid'");
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$index]);
        return count($indexes) > 0;
    }
};
