<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_plan_id')->constrained('savings_plans')->cascadeOnDelete();
            $table->foreignId('samity_id')->constrained('samities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('withdrawal_date');
            $table->string('status')->default('approved'); // approved, pending, rejected
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_withdrawals');
    }
};
