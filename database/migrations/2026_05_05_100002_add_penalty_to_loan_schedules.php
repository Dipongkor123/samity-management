<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loan_schedules', function (Blueprint $table) {
            $table->decimal('penalty_amount', 15, 2)->default(0)->after('closing_balance');
            $table->date('paid_date')->nullable()->after('penalty_amount');
        });
    }

    public function down(): void
    {
        Schema::table('loan_schedules', function (Blueprint $table) {
            $table->dropColumn(['penalty_amount', 'paid_date']);
        });
    }
};
