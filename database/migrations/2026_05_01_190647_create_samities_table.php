<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('samities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('cycle_type', ['weekly', 'monthly'])->default('monthly');
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->date('start_date');
            $table->integer('meeting_day')->nullable()->comment('1-7 for weekly, 1-31 for monthly');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samities');
    }
};
