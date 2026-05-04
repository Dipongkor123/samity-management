<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_plan_id')->constrained('savings_plans')->cascadeOnDelete();
            $table->foreignId('samity_id')->constrained('samities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('deposit_date');
            $table->string('receipt_number')->nullable();
            $table->string('status')->default('paid'); // paid, pending
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_deposits');
    }
};
