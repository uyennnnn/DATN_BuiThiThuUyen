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
            $table->string('full_name')->nullable()->after('name');
            $table->string('position')->nullable();

            $table->unsignedTinyInteger('salary_type')->default(1);
            $table->unsignedBigInteger('salary_fixed')->nullable();

            $table->unsignedBigInteger('salary_base')->nullable();
            $table->unsignedBigInteger('salary_night')->nullable();
            $table->unsignedBigInteger('salary_overtime')->nullable();

            $table->unsignedTinyInteger('set_holiday_salary')->default(1);
            $table->unsignedTinyInteger('set_saturday_salary')->default(0);
            $table->unsignedTinyInteger('set_sunday_salary')->default(0);
            $table->unsignedTinyInteger('set_celebrate_salary')->default(0);

            $table->unsignedBigInteger('holiday_salary_base')->nullable();
            $table->unsignedBigInteger('holiday_salary_night')->nullable();
            $table->unsignedBigInteger('holiday_salary_overtime')->nullable();

            $table->unsignedBigInteger('one_way_travel_expense')->nullable();
            $table->string('nearest_train_station')->nullable();
            $table->unsignedTinyInteger('role')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('full_name');
            $table->dropColumn('position');
            $table->dropColumn('salary_type');
            $table->dropColumn('salary');
            $table->dropColumn('one_way_travel_expense');
            $table->dropColumn('nearest_train_station');
            $table->dropColumn('role');
        });
    }
};
