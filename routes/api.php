<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// роуты для авторизации
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
    Route::get('/user', [AuthController::class, 'getCurrentUser']);
    Route::post('/password', [AuthController::class, 'updatePassword'])->middleware('auth');
});

// роуты для экскурсий, новостей и общих данных (публичные)
Route::get('/excursions', [UserController::class, 'getExcursions']);
Route::get('/excursion/{id}', [UserController::class, 'getExcursionDetails']);
Route::get('/excursions/{id}', [UserController::class, 'getExcursionDetails']);
Route::get('/excursion/{id}/type', [UserController::class, 'getExcursionType']);
Route::get('/news', [UserController::class, 'getNews']);
Route::get('/news/{id}', [UserController::class, 'getNewsDetails']);
Route::get('/comments/featured', [UserController::class, 'getFeaturedComments']);
Route::get('/immersive/{id}/dates', [UserController::class, 'getImmersiveDates']);
Route::get('/immersive/date/{id}/times', [UserController::class, 'getImmersiveTimes']);

// роуты для зарегистрированных пользователей
Route::middleware('auth')->prefix('user')->group(function () {
    // Профиль
    Route::get('/profile', [UserController::class, 'getProfile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    
    // Покупка экскурсий и комментарии
    Route::post('/excursion/{id}/purchase', [UserController::class, 'purchaseExcursion']);
    Route::post('/excursion/{id}/comment', [UserController::class, 'addComment']);
    Route::get('/excursion/{id}/has-comment', [UserController::class, 'hasComment']);
    Route::get('/excursion/{id}/check-purchase', [UserController::class, 'checkExcursionPurchase']);
    
    // Иммерсивы и бронирования
    Route::post('/booking', [UserController::class, 'bookImmersive']);
    Route::delete('/booking/{id}', [UserController::class, 'cancelBooking']);
    
    // Проверка бронирований для комментариев
    Route::get('/bookings/check/{excursionId}', [UserController::class, 'checkUserBooking']);
});

// роуты для админки
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Статистика панели управления
    Route::get('/dashboard/stats', [AdminController::class, 'getDashboardStats']);
    
    // Управление пользователями
    Route::get('/users', [AdminController::class, 'getUsers']);
    Route::get('/users/{id}', [AdminController::class, 'getUserDetails']);
    Route::post('/users/{id}/make-admin', [AdminController::class, 'makeAdmin']);
    Route::post('/users/{id}/remove-admin', [AdminController::class, 'removeAdmin']);
    
    // Управление экскурсиями
    Route::get('/excursions', [AdminController::class, 'getExcursions']);
    Route::post('/excursions', [AdminController::class, 'createExcursion']);
    Route::get('/excursions/{id}', [AdminController::class, 'getExcursionDetails']);
    Route::put('/excursions/{id}', [AdminController::class, 'updateExcursion']);
    Route::delete('/excursions/{id}', [AdminController::class, 'deleteExcursion']);
    
    // Управление новостями
    Route::get('/news', [AdminController::class, 'getNews']);
    Route::post('/news', [AdminController::class, 'createNews']);
    Route::get('/news/{id}', [AdminController::class, 'getNewsDetails']);
    Route::put('/news/{id}', [AdminController::class, 'updateNews']);
    Route::delete('/news/{id}', [AdminController::class, 'deleteNews']);
    
    // Управление иммерсивами
    Route::get('/immersives', [AdminController::class, 'getImmersives']);
    Route::post('/immersives', [AdminController::class, 'createImmersive']);
    Route::post('/immersives/create-direct', [AdminController::class, 'createDirectImmersive']);
    Route::get('/immersives/{id}', [AdminController::class, 'getImmersiveDetails']);
    Route::put('/immersives/{id}', [AdminController::class, 'updateImmersive']);
    Route::post('/immersives/{id}/update-direct', [AdminController::class, 'updateDirectImmersive']);
    Route::delete('/immersives/{id}', [AdminController::class, 'deleteImmersive']);
    Route::delete('/immersives/date/{id}', [AdminController::class, 'deleteImmersiveDate']);
    Route::delete('/immersives/time/{id}', [AdminController::class, 'deleteImmersiveTime']);
    
    // Управление местами
    Route::get('/places', [AdminController::class, 'getPlaces']);
    Route::post('/places', [AdminController::class, 'createPlace']);
    Route::get('/places/{id}', [AdminController::class, 'getPlaceDetails']);
    Route::put('/places/{id}', [AdminController::class, 'updatePlace']);
    Route::delete('/places/{id}', [AdminController::class, 'deletePlace']);
    
    // Модерация комментариев
    Route::get('/comments/pending', [AdminController::class, 'getPendingComments']);
    Route::post('/comments/{id}/approve', [AdminController::class, 'approveComment']);
    Route::post('/comments/{id}/reject', [AdminController::class, 'rejectComment']);
    Route::delete('/comments/{id}', [AdminController::class, 'deleteComment']);
    
    // Управление заявками
    Route::get('/applications', [AdminController::class, 'getApplications']);
    Route::post('/applications/{id}/approve', [AdminController::class, 'approveApplication']);
    Route::post('/applications/{id}/reject', [AdminController::class, 'rejectApplication']);
    
    // Управление бронированиями
    Route::get('/bookings', [AdminController::class, 'getBookings']);
    Route::get('/bookings/{id}', [AdminController::class, 'getBookingDetails']);
    Route::post('/bookings/{id}/mark-paid', [AdminController::class, 'markBookingAsPaid']);
    Route::delete('/bookings/{id}', [AdminController::class, 'cancelBooking']);
});

