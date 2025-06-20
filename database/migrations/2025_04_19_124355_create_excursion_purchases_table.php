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
        Schema::create('excursion_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ссылка на пользователя
            $table->foreignId('excursion_id')->constrained()->onDelete('cascade'); // Ссылка на экскурсию
            $table->decimal('price', 8, 2); // Цена
            $table->boolean('is_paid')->default(false); // Оплачено
            $table->timestamps(); // Метки времени
            
            $table->unique(['user_id', 'excursion_id']); // Уникальность для пользователя и экскурсии
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excursion_purchases');
    }
};
