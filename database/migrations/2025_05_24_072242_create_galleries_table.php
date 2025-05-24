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
        // First ensure occasions table exists
        if (!Schema::hasTable('occasions')) {
            Schema::create('occasions', function (Blueprint $table) {
                $table->id();
                $table->string('name_en');
                $table->string('name_hi');
                $table->text('description_en')->nullable();
                $table->text('description_hi')->nullable();
                $table->string('slug')->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_hi');
            $table->text('description_en')->nullable();
            $table->text('description_hi')->nullable();
            $table->unsignedBigInteger('occasion_id');
            $table->dateTime('event_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->integer('display_order')->default(0);
            $table->timestamps();

            $table->foreign('occasion_id')
                ->references('id')
                ->on('occasions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
