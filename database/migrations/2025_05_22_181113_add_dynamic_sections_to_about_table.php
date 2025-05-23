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
            // Drop old columns
            $table->dropColumn(['title', 'body', 'images']);

            // Add new columns
            $table->string('title_en');
            $table->string('title_hi');
            $table->string('featured_image')->nullable();
            $table->json('dynamic_sections')->nullable();
            $table->text('body_en')->nullable();
            $table->text('body_hi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abouts', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['title_en', 'title_hi', 'featured_image', 'dynamic_sections', 'body_en', 'body_hi']);

            // Restore old columns
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('images')->nullable();
        });
    }
};
