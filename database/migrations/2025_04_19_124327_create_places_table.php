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
        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название места
            $table->text('description'); // Полное описание
            $table->text('short_description')->nullable(); // Краткое описание
            $table->string('image')->nullable(); // Изображение
            $table->string('address')->nullable(); // Адрес
            $table->timestamps(); // Метки времени
        });
        
        Schema::create('excursion_places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('excursion_id')->constrained()->onDelete('cascade'); // Ссылка на экскурсию
            $table->foreignId('place_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // Порядок места в экскурсии
            $table->timestamps();
            
            $table->unique(['excursion_id', 'place_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excursion_places');
        Schema::dropIfExists('places');
    }
};