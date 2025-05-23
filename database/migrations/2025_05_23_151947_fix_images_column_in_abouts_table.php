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
        Schema::table('abouts', function (Blueprint $table) {
            // First check if the column exists, if it does, drop it
            if (Schema::hasColumn('abouts', 'images')) {
                $table->dropColumn('images');
            }

            // Add the column as JSON
            $table->json('images')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            if (Schema::hasColumn('abouts', 'images')) {
                $table->dropColumn('images');
            }
        });
    }
};
