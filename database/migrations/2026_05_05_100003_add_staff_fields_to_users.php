<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Extend role to include field_officer and staff
            // role column already exists; we just add staff-specific columns
            $table->string('designation')->nullable()->after('role');
            $table->string('assigned_area')->nullable()->after('designation');
            $table->date('joining_date')->nullable()->after('assigned_area');
            $table->boolean('is_staff')->default(false)->after('joining_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['designation', 'assigned_area', 'joining_date', 'is_staff']);
        });
    }
};
