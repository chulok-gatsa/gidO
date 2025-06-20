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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Имя
            $table->string('login')->unique(); // Логин
            $table->string('email')->unique(); // Email
            $table->timestamp('email_verified_at')->nullable(); // Подтверждение email
            $table->string('password'); // Пароль
            $table->boolean('is_admin')->default(false); // Админ
            $table->rememberToken(); // Токен для запоминания
            $table->timestamps(); // Метки времени
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};