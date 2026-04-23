<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('issue_tag', function (Blueprint $table) {
            $table->foreignId('issue_id')->constrained()->cascadeOnDelete();
            $table->foreignId('issue_tag_id')->constrained('issue_tags')->cascadeOnDelete();
            $table->primary(['issue_id', 'issue_tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_tag');
    }
};
