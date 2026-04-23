<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opinion_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opinion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('value'); // 1 = upvote, -1 = downvote
            $table->timestamps();

            $table->unique(['opinion_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opinion_votes');
    }
};
