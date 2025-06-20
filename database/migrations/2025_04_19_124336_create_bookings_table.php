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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ссылка на пользователя
            $table->foreignId('immersive_id')->constrained()->onDelete('cascade'); // Ссылка на иммерсив
            $table->foreignId('immersive_date_id')->constrained()->onDelete('cascade'); // Ссылка на дату события
            $table->foreignId('immersive_time_id')->constrained()->onDelete('cascade'); // Ссылка на время события
            $table->string('name'); // Имя
            $table->string('email'); // Email
            $table->string('starting_point'); // Начальная точка маршрута
            $table->integer('people_count')->default(1); // Количество людей
            $table->decimal('total_price', 10, 2); // Общая стоимость
            $table->boolean('is_paid')->default(false); // Оплачено
            $table->timestamps(); // Метки времени
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
}; 