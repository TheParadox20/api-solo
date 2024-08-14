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
        // Schema::disableForeignKeyConstraints();
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('sport_id')->constrained('sports');
            $table->foreignId('category_id')->constrained('categories');
            $table->double('amount');
            $table->integer('stakers');
            $table->dateTime('start_time');
            $table->json('options');
            $table->json('outcomes');
            $table->double('popularity');
            $table->string('gameID')->unique();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
