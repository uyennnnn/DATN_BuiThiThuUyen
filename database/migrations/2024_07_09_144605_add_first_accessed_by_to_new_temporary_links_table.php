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
        Schema::table('new_temporary_links', function (Blueprint $table) {
            $table->unsignedBigInteger('first_accessed_by')->nullable()->after('used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('new_temporary_links', function (Blueprint $table) {
            $table->dropColumn('first_accessed_by');
        });
    }
};
