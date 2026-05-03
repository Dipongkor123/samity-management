<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('address');
            $table->date('date_of_birth')->nullable()->after('photo');
            $table->string('blood_group', 5)->nullable()->after('date_of_birth');
            $table->string('occupation')->nullable()->after('blood_group');
            $table->string('father_name')->nullable()->after('occupation');
            $table->string('mother_name')->nullable()->after('father_name');
            $table->string('spouse_name')->nullable()->after('mother_name');
            $table->string('emergency_contact')->nullable()->after('spouse_name');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'photo', 'date_of_birth', 'blood_group', 'occupation',
                'father_name', 'mother_name', 'spouse_name',
                'emergency_contact', 'emergency_phone',
            ]);
        });
    }
};
