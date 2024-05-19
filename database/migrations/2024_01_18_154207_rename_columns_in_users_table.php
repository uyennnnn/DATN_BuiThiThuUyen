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
            $table->renameColumn('salary_base', 'salary_base_id');
            $table->renameColumn('salary_night', 'salary_night_id');
            $table->renameColumn('salary_overtime', 'salary_overtime_id');
            $table->renameColumn('holiday_salary_base', 'holiday_salary_base_id');
            $table->renameColumn('holiday_salary_night', 'holiday_salary_night_id');
            $table->renameColumn('holiday_salary_overtime', 'holiday_salary_overtime_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('salary_base_id', 'salary_base');
            $table->renameColumn('salary_night_id', 'salary_night');
            $table->renameColumn('salary_overtime_id', 'salary_overtime');
            $table->renameColumn('holiday_salary_base_id', 'holiday_salary_base');
            $table->renameColumn('holiday_salary_night_id', 'holiday_salary_night');
            $table->renameColumn('holiday_salary_overtime_id', 'holiday_salary_overtime');
        });
    }
};
