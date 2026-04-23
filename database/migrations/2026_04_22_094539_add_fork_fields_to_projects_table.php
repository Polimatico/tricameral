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
        Schema::table('projects', function (Blueprint $table) {
            $table->string('fork_permission')->default('everyone')->after('visibility');
            $table->foreignId('forked_from_id')->nullable()->constrained('projects')->nullOnDelete()->after('fork_permission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['forked_from_id']);
            $table->dropColumn(['fork_permission', 'forked_from_id']);
        });
    }
};
