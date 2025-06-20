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
        Schema::table('immersives', function (Blueprint $table) {
            $table->string('audio_demo')->nullable()->after('price_per_person');
            $table->string('audio_preview')->nullable()->after('audio_demo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('immersives', function (Blueprint $table) {
            $table->dropColumn(['audio_demo', 'audio_preview']);
        });
    }
};
