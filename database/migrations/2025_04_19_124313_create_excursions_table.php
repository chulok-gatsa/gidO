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
        Schema::create('excursions', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Заголовок
            $table->text('description'); // Полное описание
            $table->text('short_description')->nullable(); // Краткое описание
            $table->decimal('route_length', 5, 2); // Длина маршрута в км
            $table->string('duration'); // Время прохождения (текстом)
            $table->enum('type', ['audio', 'immersive'])->default('audio'); // Тип экскурсии
            $table->string('main_image')->nullable(); // Главное изображение
            $table->string('audio_preview')->nullable(); // Изображение для превью аудио
            $table->string('audio_demo')->nullable(); // Ознакомительный фрагмент
            $table->string('audio_full')->nullable(); // Полное аудио
            $table->string('starting_point'); // Начальная точка маршрута
            $table->text('map_embed'); // Ссылка на карту (iframe)
            $table->json('sights_list')->nullable(); // Список достопримечательностей в формате JSON
            $table->decimal('price', 8, 2)->default(0.00); // Цена для аудиогида
            $table->timestamps(); // Метки времени
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excursions');
    }
};