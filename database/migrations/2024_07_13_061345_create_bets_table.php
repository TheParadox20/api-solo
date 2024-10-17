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
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('game_id')->constrained('games');
            $table->string('market')->default('3 way');
            $table->string('choice');
            $table->double('amount');
            $table->double('reward')->nullable();
            $table->boolean('status')->nullable(); // pending, disputed, success
            $table->boolean('result')->nullable(); // true if win false otherwies
            $table->boolean('onchain')->nullable(); // true if recorded on chain
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
