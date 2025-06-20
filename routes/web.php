<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [PageController::class, 'index'])->name('welcome');
Route::get('/catalogExcursions', [PageController::class, 'index'])->name('catalogExcursions');
Route::get('/blog', [PageController::class, 'index'])->name('blog');
Route::get('/politics', [PageController::class, 'indexPolitics'])->name('politics');
Route::get('/cond', [PageController::class, 'indexCond'])->name('cond');
Route::get('/blog/{id}', [PageController::class, 'index'])->name('news.show');
Route::get('/aboutUs', [PageController::class, 'index'])->name('aboutUs');
Route::get('/login', [PageController::class, 'index'])->name('login');
Route::get('/registration', [PageController::class, 'index'])->name('registration');

Route::get('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

// роуты для авторизации
Route::middleware(['auth'])->group(function () {
    Route::get('/personalAccount', [PageController::class, 'index'])->name('personalAccount');
    Route::get('/editProfile', [PageController::class, 'index'])->name('editProfile');
    Route::get('/excursion/{id}', [PageController::class, 'index'])->name('excursion');
    Route::get('/booking/{id}', [PageController::class, 'index'])->name('booking');
});

// роуты для админ-панели
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Главная страница
    Route::get('/', function () {
        return view('admin.post.index');
    })->name('admin.index');
    
    // Новости
    Route::get('/news', function () {
        return view('admin.post.index');
    })->name('admin.news');
    
    Route::get('/news/create', function () {
        return view('admin.post.create_news');
    })->name('admin.create_news');
    
    Route::get('/news/edit/{id}', function ($id) {
        return view('admin.post.update_news', ['newsId' => $id]);
    })->name('admin.edit_news');
    
    // Места
    Route::get('/places', function () {
        return view('admin.post.places_admin');
    })->name('admin.places_admin');
    
    Route::get('/places/create', function () {
        return view('admin.post.create_place');
    })->name('admin.create_place');
    
    Route::get('/places/edit/{id}', function ($id) {
        return view('admin.post.update_place', ['placeId' => $id]);
    })->name('admin.edit_place');
    
    // Экскурсии
    Route::get('/excursions', function () {
        return view('admin.post.excursions_admin');
    })->name('admin.excursions_admin');
    
    Route::get('/excursions/create', function () {
        return view('admin.post.create_excursion');
    })->name('admin.create_excursion');
    
    Route::get('/excursions/edit/{id}', function ($id) {
        return view('admin.post.update_excursion', ['excursionId' => $id]);
    })->name('admin.update_excursion');
    
    // Иммерсивы
    Route::get('/immersives', function () {
        return view('admin.post.immersives');
    })->name('admin.immersives');
    
    Route::get('/immersives/create', function () {
        return view('admin.post.create_immersive');
    })->name('admin.create_immersive');
    
    Route::get('/immersives/edit/{immersiveId}/{excursionId}', function ($immersiveId, $excursionId) {
        return view('admin.post.update_immersive', [
            'immersiveId' => $immersiveId,
            'excursionId' => $excursionId
        ]);
    })->name('admin.update_immersive');
    
    // Заявки и комментарии
    Route::get('/applications', function () {
        return view('admin.post.applications');
    })->name('admin.applications');
    
    Route::get('/comments', function () {
        return view('admin.post.comments');
    })->name('admin.comments');
    
    // Пользователи
    Route::get('/users/{id}', function ($id) {
        return view('admin.post.profile', ['userId' => $id]);
    })->name('admin.user_profile');
});

// роуты для пользовательской части
Route::prefix('api/user')->group(function () {
    // Экскурсии
    Route::get('/excursions', [UserController::class, 'getExcursions']);
    Route::get('/excursion/{id}', [UserController::class, 'getExcursionDetails']);
    Route::post('/excursion/{id}/purchase', [UserController::class, 'purchaseExcursion']);
    Route::post('/excursion/{id}/comment', [UserController::class, 'addComment']);
    Route::get('/excursion/{id}/has-comment', [UserController::class, 'hasComment']);
});