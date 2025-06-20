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
        Schema::create('immersives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('excursion_id')->constrained()->onDelete('cascade'); // Ссылка на экскурсию
            $table->text('main_description'); // Основное описание
            $table->text('additional_description'); // Дополнительное описание
            $table->decimal('price_per_person', 8, 2); // Стоимость за человека
            $table->timestamps(); // Метки времени
        });
        
        Schema::create('immersive_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('immersive_id')->constrained()->onDelete('cascade'); // Ссылка на иммерсив
            $table->date('event_date'); // Дата события
            $table->boolean('is_available')->default(true); // Доступность
            $table->timestamps(); // Метки времени
            
            $table->unique(['immersive_id', 'event_date']); // Уникальность даты для иммерсива
        });
        
        Schema::create('immersive_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('immersive_date_id')->constrained()->onDelete('cascade'); // Ссылка на дату события
            $table->time('event_time'); // Время события
            $table->boolean('is_available')->default(true); // Доступность
            $table->timestamps(); // Метки времени
            
            $table->unique(['immersive_date_id', 'event_time']); // Уникальность времени для даты
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immersive_times');
        Schema::dropIfExists('immersive_dates');
        Schema::dropIfExists('immersives');
    }
};