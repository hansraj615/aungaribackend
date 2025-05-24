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
        // Create table for storing unique visitor IPs
        Schema::create('unique_visitors', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address')->unique();
            $table->timestamp('first_visit_at');
            $table->timestamp('last_visit_at');
            $table->timestamps();
        });

        // Update visitor_counts table
        Schema::table('visitor_counts', function (Blueprint $table) {
            // Rename count to total_visits for clarity
            $table->renameColumn('total', 'total_visits');
            // Add column for unique visitors count
            $table->unsignedBigInteger('unique_visitors')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unique_visitors');

        Schema::table('visitor_counts', function (Blueprint $table) {
            $table->renameColumn('total_visits', 'total');
            $table->dropColumn('unique_visitors');
        });
    }
};
