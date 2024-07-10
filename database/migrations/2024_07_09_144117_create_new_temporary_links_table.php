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
        Schema::create('new_temporary_links', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->unsignedBigInteger('user_id');
            $table->string('ip_address');
            $table->string('user_agent');
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('new_temporary_links');
    }
};

