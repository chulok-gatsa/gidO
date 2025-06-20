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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ссылка на пользователя
            $table->foreignId('excursion_id')->constrained()->onDelete('cascade'); // Ссылка на экскурсию
            $table->text('content'); // Содержимое комментария
            $table->boolean('is_approved')->default(false); // Подтверждение комментария
            $table->text('rejection_reason')->nullable(); // Причина отклонения
            $table->timestamps(); // Метки времени
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}; 