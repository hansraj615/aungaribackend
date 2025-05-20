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
        // database/migrations/xxxx_xx_xx_xxxxxx_create_home_pages.php
        Schema::create('homes', function (Blueprint $table) {
            $table->id();

            // Core Sections (Hero, About, etc.)
            $table->json('hero_section')->nullable();
            $table->json('about_section')->nullable();

            // Dynamic Sections Container
            $table->json('dynamic_sections')->nullable();

            // Settings
            $table->boolean('show_read_more')->default(true);
            $table->integer('read_more_char_limit')->default(150);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homes');
    }
};
