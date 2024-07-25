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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('sport_id');
            $table->integer('category_id');
            $table->integer('country_id');
            $table->double('amount');
            $table->integer('stakers');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->json('match');
            $table->double('popularity');
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
