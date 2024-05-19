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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary_base_id');
            $table->dropColumn('salary_night_id');
            $table->dropColumn('salary_overtime_id');
            $table->dropColumn('holiday_salary_base_id');
            $table->dropColumn('holiday_salary_night_id');
            $table->dropColumn('holiday_salary_overtime_id');

            $table->json('salary_base')->nullable();
            $table->json('salary_night')->nullable();
            $table->json('salary_overtime')->nullable();
            $table->json('holiday_salary_base')->nullable();
            $table->json('holiday_salary_night')->nullable();
            $table->json('holiday_salary_overtime')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('salary_base');
            $table->dropColumn('salary_night');
            $table->dropColumn('salary_overtime');
            $table->dropColumn('holiday_salary_base');
            $table->dropColumn('holiday_salary_night');
            $table->dropColumn('holiday_salary_overtime');

            $table->unsignedBigInteger('salary_base_id')->nullable();
            $table->unsignedBigInteger('salary_night_id')->nullable();
            $table->unsignedBigInteger('salary_overtime_id')->nullable();
            $table->unsignedBigInteger('holiday_salary_base_id')->nullable();
            $table->unsignedBigInteger('holiday_salary_night_id')->nullable();
            $table->unsignedBigInteger('holiday_salary_overtime_id')->nullable();
        });
    }
};
