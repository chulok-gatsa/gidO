<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Excursion;
use App\Models\Immersive;
use App\Models\ImmersiveDate;
use App\Models\ImmersiveTime;
use App\Models\News;
use App\Models\Place;
use App\Models\Comment;
use App\Models\Booking;
use App\Models\User;
use App\Models\ExcursionPurchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    // тут проверка прав администратора //
    private function checkAdminAccess()
    {
            if (!Auth::user() || !Auth::user()->isAdmin()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Доступ запрещен'
            ], 403));
            }
    }
    
    // тут получаем статистику //
    public function getDashboardStats()
    {
        $this->checkAdminAccess();
        
        $excursionsCount = Excursion::count(); // тут количество экскурсий //
        $immersivesCount = Immersive::count(); // тут количество иммерсивов //
        $placesCount = Place::count(); // тут количество мест //
        $newsCount = News::count(); // тут количество новостей //
        $pendingCommentsCount = Comment::where('is_approved', false)->whereNull('rejection_reason')->count(); // тут количество не одобренных комментов //
        $bookingsCount = Booking::count(); // тут количество броней //
        $usersCount = User::count(); // тут количество пользователей //
        
        $latestBookings = Booking::with(['user', 'immersive.excursion']) // тут последние брони //
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        return response()->json([
            'success' => true,
            'stats' => [
                'excursionsCount' => $excursionsCount,
                'immersivesCount' => $immersivesCount,
                'placesCount' => $placesCount,
                'newsCount' => $newsCount,
                'pendingCommentsCount' => $pendingCommentsCount,
                'bookingsCount' => $bookingsCount,
                'usersCount' => $usersCount,
            ],
            'latestBookings' => $latestBookings
        ]);
    }
    
    // тут получаем список пользователей //
    public function getUsers()
    {
        $this->checkAdminAccess();
        
        $users = User::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'users' => $users
        ]);
    }
    
    // тут получаем информацию о пользователе //
    public function getUserDetails($id)
    {
        $this->checkAdminAccess();
        
        $user = User::with(['bookings.immersive.excursion', 'excursionPurchases.excursion', 'comments.excursion'])
            ->findOrFail($id);
            
        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
    
    // тут назначение пользователя администратором //
    public function makeAdmin($id)
    {
        $this->checkAdminAccess();
        
        $user = User::findOrFail($id);
        $user->is_admin = true;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователю присвоена роль администратора',
            'user' => $user
        ]);
    }
    
    // тут снятие прав администратора //
    public function removeAdmin($id)
    {
        $this->checkAdminAccess();
        
        $user = User::findOrFail($id);
        
        // тут проверяем, чтобы не лишить прав последнего админа //
        $adminsCount = User::where('is_admin', true)->count();
        
        if ($adminsCount <= 1 && $user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно снять права с последнего администратора'
            ], 422);
        }
        
        $user->is_admin = false;
        $user->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Пользователь больше не является администратором',
            'user' => $user
        ]);
    }
    
    // тут получаем список экскурсий //
    public function getExcursions()
    {
        $this->checkAdminAccess();
        
        $excursions = Excursion::with(['places'])->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'excursions' => $excursions
        ]);
    }
    
    // тут создание новой экскурсии //
    public function createExcursion(Request $request)
    {
        // тут валидация //
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'route_length' => 'required|numeric',
            'duration' => 'required|string',
            'starting_point' => 'required|string',
            'map_embed' => 'required|string',
            'price' => 'required|numeric',
            'main_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'audio_preview' => 'required|image|mimes:jpeg,png,jpg,gif|max:5048',
            'audio_demo' => 'required|file|mimes:mp3,wav|max:10240',
            'audio_full' => 'required|file|mimes:mp3,wav|max:20480',
            'places' => 'array',
            'sights_list' => 'nullable',
        ], [
            'title.required' => 'Заголовок обязателен',
            'description.required' => 'Описание обязательно',
            'short_description.required' => 'Краткое описание обязательно',
            'route_length.required' => 'Длина маршрута обязательна',
            'duration.required' => 'Время прохождения обязательно',
            'starting_point.required' => 'Начальная точка обязательна',
            'map_embed.required' => 'Ссылка на карту обязательна',
            'price.required' => 'Цена обязательна',
            'main_image.required' => 'Главное изображение обязательно',
            'audio_preview.required' => 'Превью аудио обязательно',
            'audio_demo.required' => 'Демо аудио обязательно',
            'audio_full.required' => 'Полное аудио обязательно',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // тут путь для сохранения файлов //
        $mainImagePath = $request->file('main_image')->store('excursions/images', 'public');
        $audioPreviewPath = $request->file('audio_preview')->store('excursions/previews', 'public');
        $audioDemoPath = $request->file('audio_demo')->store('excursions/demos', 'public');
        $audioFullPath = $request->file('audio_full')->store('excursions/audios', 'public');
        
        // тут обработка sights_list из JSON-строки в массив //
        $sightsList = null;
        if ($request->has('sights_list')) {
            $sightsList = json_decode($request->sights_list, true);
            
            // тут проверяем, что sights_list действительно массив //
            if ($sightsList !== null && !is_array($sightsList)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['sights_list' => ['Некорректный формат списка достопримечательностей']]
                ], 422);
            }
        }
        
        // тут создание экскурсии //
        $excursion = Excursion::create([
            'title' => $request->title, // тут название //
            'description' => $request->description, // тут описание //
            'short_description' => $request->short_description, // тут краткое описание //
            'route_length' => $request->route_length, // тут длина маршрута //
            'duration' => $request->duration, // тут время прохождения //
            'type' => 'audio', // тут для аудиогида тип audio //
            'main_image' => $mainImagePath, // тут главное изображение //
            'audio_preview' => $audioPreviewPath, // тут превью аудио //
            'audio_demo' => $audioDemoPath,
            'audio_full' => $audioFullPath,
            'starting_point' => $request->starting_point,
            'map_embed' => $request->map_embed,
            'price' => $request->price,
            'sights_list' => $sightsList,
        ]);
        
        // тут привязка мест к экскурсии (если есть) //
        if ($request->has('places') && is_array($request->places)) {
            $placesWithOrder = [];
            foreach ($request->places as $index => $placeId) {
                $placesWithOrder[$placeId] = ['order' => $index + 1];
            }
            $excursion->places()->attach($placesWithOrder);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Экскурсия успешно создана',
            'excursion' => $excursion
        ]);
    }
    
    // тут обновление экскурсии //
    public function updateExcursion(Request $request, $id)
    {
        $this->checkAdminAccess();
        
        $excursion = Excursion::findOrFail($id);
        
        // тут валидация //
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'route_length' => 'required|numeric',
            'duration' => 'required|string',
            'starting_point' => 'required|string',
            'map_embed' => 'required|string',
            'price' => 'required|numeric',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'audio_preview' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5048',
            'audio_demo' => 'nullable|file|mimes:mp3,wav|max:10240',
            'audio_full' => 'nullable|file|mimes:mp3,wav|max:20480',
            'places' => 'array',
            'sights_list' => 'nullable',
        ], [
            'title.required' => 'Заголовок обязателен',
            'description.required' => 'Описание обязательно',
            'short_description.required' => 'Краткое описание обязательно',
            'route_length.required' => 'Длина маршрута обязательна',
            'duration.required' => 'Время прохождения обязательно',
            'starting_point.required' => 'Начальная точка обязательна',
            'map_embed.required' => 'Ссылка на карту обязательна',
            'price.required' => 'Цена обязательна',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // тут обновление данных экскурсии //
        $excursion->title = $request->title; // тут название //
        $excursion->description = $request->description; // тут описание //
        $excursion->short_description = $request->short_description; // тут краткое описание //
        $excursion->route_length = $request->route_length; // тут длина маршрута //
        $excursion->duration = $request->duration; // тут время прохождения //
        $excursion->starting_point = $request->starting_point; // тут начальная точка //
        $excursion->map_embed = $request->map_embed; // тут ссылка на карту //
        $excursion->price = $request->price; // тут цена //
        
        // тут обновление sights_list //
        if ($request->has('sights_list')) {
            $sightsList = json_decode($request->sights_list, true);
            
            // тут проверяем, что sights_list действительно массив //
            if ($sightsList !== null && !is_array($sightsList)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['sights_list' => ['Некорректный формат списка достопримечательностей']]
                ], 422);
            }
            
            $excursion->sights_list = $sightsList;
        }
        
        // тут обновление файлов, если загружены новые //
        if ($request->hasFile('main_image')) {
            $mainImagePath = $request->file('main_image')->store('excursions/images', 'public');
            $excursion->main_image = $mainImagePath;
        }
        // тут обновление превью аудио //
        if ($request->hasFile('audio_preview')) {
            $audioPreviewPath = $request->file('audio_preview')->store('excursions/previews', 'public');
            $excursion->audio_preview = $audioPreviewPath;
        }
        // тут обновление демо аудио //
        if ($request->hasFile('audio_demo')) {
            $audioDemoPath = $request->file('audio_demo')->store('excursions/demos', 'public');
            $excursion->audio_demo = $audioDemoPath;
        }
        // тут обновление полной версии аудио //
        if ($request->hasFile('audio_full')) {
            $audioFullPath = $request->file('audio_full')->store('excursions/audios', 'public');
            $excursion->audio_full = $audioFullPath;
        }
        // тут сохранение экскурсии //
        $excursion->save();
        
        // тут обновление связанных мест //
        if ($request->has('places') && is_array($request->places)) {
            $excursion->places()->detach(); // тут удаляем старые связи //
            
            $placesWithOrder = [];
            foreach ($request->places as $index => $placeId) {
                $placesWithOrder[$placeId] = ['order' => $index + 1];
            }
            $excursion->places()->attach($placesWithOrder);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Экскурсия успешно обновлена',
            'excursion' => $excursion
        ]);
    }
    
    // тут удаление экскурсии //
    public function deleteExcursion($id)
    {
        $this->checkAdminAccess();
        
        $excursion = Excursion::findOrFail($id);
        
        // тут удаляем файлы //
        if ($excursion->main_image) {
            Storage::disk('public')->delete($excursion->main_image);
        }
        
        // тут удаляем превью аудио //
        if ($excursion->audio_preview) {
            Storage::disk('public')->delete($excursion->audio_preview);
        }
        
        // тут удаляем демо аудио //
        if ($excursion->audio_demo) {
            Storage::disk('public')->delete($excursion->audio_demo);
        }
        
        // тут удаляем полное аудио //
        if ($excursion->audio_full) {
            Storage::disk('public')->delete($excursion->audio_full);
        }
        
        // тут удаляем экскурсию //
        $excursion->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Экскурсия успешно удалена'
        ]);
    }
    
    // тут создание иммерсива //
    public function createImmersive(Request $request)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'excursion_id' => 'required|exists:excursions,id',
            'main_description' => 'required|string',
            'additional_description' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'dates' => 'required|array|min:1',
            'dates.*' => 'required|date',
            'times' => 'required|array|min:1',
            'times.*' => 'required|date_format:H:i',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $excursion = Excursion::findOrFail($request->excursion_id);
        
        // тут убеждаемся, что тип экскурсии - иммерсив //
        $excursion->type = 'immersive';
        $excursion->save();
        
        // тут создаем иммерсив //
        $immersive = Immersive::create([
            'excursion_id' => $request->excursion_id,
            'main_description' => $request->main_description,
            'additional_description' => $request->additional_description,
            'price_per_person' => $request->price_per_person,
        ]);
        
        // тут создаем даты //
        foreach ($request->dates as $dateString) {
            $date = ImmersiveDate::create([
                'immersive_id' => $immersive->id,
                'event_date' => $dateString,
                'is_available' => true,
            ]);
            
            // тут для каждой даты создаем временные слоты //
            foreach ($request->times as $timeString) {
                ImmersiveTime::create([
                    'immersive_date_id' => $date->id,
                    'event_time' => $timeString,
                    'is_available' => true,
                ]);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Иммерсив успешно создан',
            'immersive' => $immersive->fresh(['excursion', 'dates.allTimes'])
        ]);
    }
    
    // тут обновление иммерсива //
    public function updateImmersive(Request $request, $id)
    {
        $this->checkAdminAccess();
        
        $immersive = Immersive::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'main_description' => 'required|string',
            'additional_description' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'new_dates' => 'nullable|array',
            'new_dates.*' => 'required|date',
            'new_times' => 'nullable|array',
            'new_times.*' => 'required|date_format:H:i',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // тут обновляем иммерсив //
        $immersive->main_description = $request->main_description; // тут основное описание //
        $immersive->additional_description = $request->additional_description; // тут дополнительное описание //
        $immersive->price_per_person = $request->price_per_person; // тут цена за человека //
        $immersive->save();
        
        // тут добавляем новые даты //
        if ($request->has('new_dates') && is_array($request->new_dates)) {
            foreach ($request->new_dates as $dateString) {
                $date = ImmersiveDate::create([
                    'immersive_id' => $immersive->id,
                    'event_date' => $dateString,
                    'is_available' => true,
                ]);
                
                // тут для каждой новой даты создаем временные слоты //
                if ($request->has('new_times') && is_array($request->new_times)) {
                    foreach ($request->new_times as $timeString) {
                        ImmersiveTime::create([
                            'immersive_date_id' => $date->id,
                            'event_time' => $timeString,
                            'is_available' => true,
                        ]);
                    }
                }
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Иммерсив успешно обновлен',
            'immersive' => $immersive->fresh(['excursion', 'dates.allTimes'])
        ]);
    }
    
    // тут удаление иммерсива //
    public function deleteImmersive($id)
    {
        $this->checkAdminAccess();
        
        $immersive = Immersive::findOrFail($id);
        
        // тут проверяем наличие активных бронирований //
        $activeBookings = $immersive->bookings()->where('is_paid', true)->exists();
        
        if ($activeBookings) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно удалить иммерсив с активными бронированиями'
            ], 422);
        }
        
        // тут удаляем иммерсив //
        $immersive->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Иммерсив успешно удален'
        ]);
    }
    
    // тут получение списка мест //
    public function getPlaces()
    {
        $this->checkAdminAccess();
        
        $places = Place::orderBy('name')->get();
        
        return response()->json([
            'success' => true,
            'places' => $places
        ]);
    }
    
    // тут создание нового места //
    public function createPlace(Request $request)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'image' => 'nullable|image',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Название места обязательно',
            'description.required' => 'Описание места обязательно',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('places', 'public'); // тут сохраняем изображение //
        }
        
        $place = Place::create([
            'name' => $request->name, // тут название //
            'description' => $request->description, // тут описание //
            'short_description' => $request->short_description, // тут краткое описание //
            'image' => $imagePath, // тут изображение //
            'address' => $request->address, // тут адрес //
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Место успешно создано',
            'place' => $place
        ]);
    }
    
    // тут обновление места //
    public function updatePlace(Request $request, $id)
    {
        $this->checkAdminAccess();
        
        $place = Place::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'image' => 'nullable|image',
            'address' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Название места обязательно',
            'description.required' => 'Описание места обязательно',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        if ($request->hasFile('image')) {
            // тут удаляем старое изображение, если оно есть //
            if ($place->image) {
                Storage::disk('public')->delete($place->image);
            }
            
            $imagePath = $request->file('image')->store('places', 'public'); // тут сохраняем изображение //
            $place->image = $imagePath;
        }
        
        $place->name = $request->name; // тут название //
        $place->description = $request->description; // тут описание //
        $place->short_description = $request->short_description; // тут краткое описание //
        $place->address = $request->address; // тут адрес //
        $place->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Место успешно обновлено',
            'place' => $place
        ]);
    }
    
    // тут удаление места //
    public function deletePlace($id)
    {
        $this->checkAdminAccess();
        
        $place = Place::findOrFail($id);
        
        // тут проверяем наличие экскурсий, связанных с этим местом //
        if ($place->excursions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно удалить место, которое используется в экскурсиях'
            ], 422);
        }
        
        // тут удаляем изображение //
        if ($place->image) {
            Storage::disk('public')->delete($place->image);
        }
        
        $place->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Место успешно удалено'
        ]);
    }
    
    // тут получение списка новостей //
    public function getNews()
    {
        $this->checkAdminAccess();
        
        $news = News::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'news' => $news
        ]);
    }
    
    // тут получение деталей новости //
    public function getNewsDetails($id)
    {
        $this->checkAdminAccess();
        
        $news = News::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'news' => $news
        ]);
    }
    
    // тут создание новой новости //
    public function createNews(Request $request)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'nullable|url',
            'main_image' => 'required|image',
        ], [
            'title.required' => 'Заголовок обязателен',
            'description.required' => 'Описание обязательно',
            'url.url' => 'URL должен быть корректным',
            'main_image.required' => 'Главное изображение обязательно',
            'main_image.image' => 'Файл должен быть изображением',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $imagePath = $request->file('main_image')->store('news', 'public');
        
        $news = News::create([
            'title' => $request->title,
            'description' => $request->description,
            'url' => $request->url,
            'main_image' => $imagePath,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Новость успешно создана',
            'news' => $news
        ]);
    }
    
    // тут обновление новости //
    public function updateNews(Request $request, $id)
    {
        $this->checkAdminAccess();
        
        $news = News::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'nullable|url',
            'main_image' => 'nullable|image',
        ], [
            'title.required' => 'Заголовок обязателен',
            'description.required' => 'Описание обязательно',
            'url.url' => 'URL должен быть корректным',
            'main_image.image' => 'Файл должен быть изображением',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        if ($request->hasFile('main_image')) {
            // тут удаляем старое изображение //
            if ($news->main_image) {
                Storage::disk('public')->delete($news->main_image);
            }
            
            $imagePath = $request->file('main_image')->store('news', 'public');
            $news->main_image = $imagePath;
        }
        
        $news->title = $request->title;
        $news->description = $request->description;
        $news->url = $request->url;
        $news->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Новость успешно обновлена',
            'news' => $news
        ]);
    }
    
    // тут удаление новости //
    public function deleteNews($id)
    {
        $this->checkAdminAccess();
        
        $news = News::findOrFail($id);
        
        // тут удаляем изображение //
        if ($news->main_image) {
            Storage::disk('public')->delete($news->main_image);
        }
        
        $news->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Новость успешно удалена'
        ]);
    }
    
    // тут получение списка комментариев, требующих модерации //
    public function getPendingComments()
    {
        $this->checkAdminAccess();
        
        $pendingComments = Comment::with(['user', 'excursion'])
            ->where('is_approved', false)
            ->whereNull('rejection_reason')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'author_name' => $comment->user->name ?? 'Аноним',
                    'excursion_title' => $comment->excursion->title ?? 'Неизвестная экскурсия',
                    'created_at' => $comment->created_at,
                    'updated_at' => $comment->updated_at,
                ];
            });
            
        return response()->json([
            'success' => true,
            'comments' => $pendingComments
        ]);
    }
    
    // тут одобрение комментария //
    public function approveComment($id)
    {
        $this->checkAdminAccess();
        
        $comment = Comment::findOrFail($id);
        $comment->is_approved = true; // тут одобряем комментарий //
        $comment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Комментарий успешно одобрен',
        ]);
    }
    
    // тут отклонение комментария //
    public function rejectComment(Request $request, $id)
    {
        $this->checkAdminAccess();
        
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:255',
        ], [
            'reason.required' => 'Причина отклонения обязательна',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $comment = Comment::findOrFail($id);
        $comment->is_approved = false;
        $comment->rejection_reason = $request->reason;
        $comment->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Комментарий отклонен',
        ]);
    }
    
    // тут удаление комментария //
    public function deleteComment($id)
    {
        $this->checkAdminAccess();
        
        $comment = Comment::findOrFail($id);
        $comment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Комментарий успешно удален',
        ]);
    }
    
    // тут получение списка заявок (бронирований) //
    public function getApplications()
    {
        $this->checkAdminAccess();
        
        $applications = Booking::with(['user', 'immersive.excursion', 'date', 'time'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'user_name' => $booking->user->name ?? 'Неизвестный пользователь',
                    'name' => $booking->name,
                    'email' => $booking->email,
                    'phone' => $booking->phone ?? null,
                    'people_count' => $booking->people_count,
                    'total_price' => $booking->total_price,
                    'starting_point' => $booking->starting_point,
                    'status' => $booking->is_paid ? 'approved' : 'pending',
                    'excursion_title' => $booking->immersive->excursion->title ?? 'Неизвестная экскурсия',
                    'event_date' => $booking->date->event_date ?? null,
                    'event_time' => $booking->time->event_time ?? null,
                    'created_at' => $booking->created_at,
                    'updated_at' => $booking->updated_at,
                ];
            });
            
        return response()->json([
            'success' => true,
            'applications' => $applications
        ]);
    }

    // тут получение списка бронирований //
    public function getBookings()
    {
        $this->checkAdminAccess();
        
        $bookings = Booking::with(['user', 'immersive.excursion', 'date', 'time'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'bookings' => $bookings
        ]);
    }

    // тут одобрение заявки //
    public function approveApplication($id)
    {
        $this->checkAdminAccess();
        
        $booking = Booking::findOrFail($id);
        $booking->is_paid = true;
        $booking->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Заявка успешно одобрена'
        ]);
    }

    // тут отклонение заявки //
    public function rejectApplication($id)
    {
        $this->checkAdminAccess();
        
        $booking = Booking::findOrFail($id);
        $booking->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Заявка отклонена и удалена'
        ]);
    }
    
    // тут получение деталей бронирования //
    public function getBookingDetails($id)
    {
        $this->checkAdminAccess();
        
        $booking = Booking::with(['user', 'immersive.excursion', 'date', 'time'])->findOrFail($id);
        
        // тут получаем историю бронирований пользователя //
        $userBookings = Booking::with(['immersive.excursion'])
            ->where('user_id', $booking->user_id)
            ->where('id', '!=', $id) // тут исключаем текущее бронирование //
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'success' => true,
            'booking' => $booking,
            'userBookings' => $userBookings
        ]);
    }
    
    // тут пометка бронирования как оплаченного //
    public function markBookingAsPaid($id)
    {
        $this->checkAdminAccess();
        
        $booking = Booking::findOrFail($id);
        $booking->is_paid = true;
        $booking->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Бронирование помечено как оплаченное',
            'booking' => $booking
        ]);
    }
    
    // тут отмена бронирования //
    public function cancelBooking($id)
    {
        $this->checkAdminAccess();
        
        $booking = Booking::findOrFail($id);
        $booking->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Бронирование успешно отменено'
        ]);
    }
    
    // тут получение деталей места //
    public function getPlaceDetails($id)
    {
        $this->checkAdminAccess();
        
        $place = Place::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'place' => $place
        ]);
    }
    // тут получение деталей экскурсии //
    public function getExcursionDetails($id)
    {
        $this->checkAdminAccess();

        $excursion = Excursion::with(['places', 'immersive', 'immersive.dates', 'immersive.dates.times'])->findOrFail($id);

        $linkedPlaceIds = $excursion->places->pluck('id')->toArray();

        return response()->json([
            'success' => true,
            'excursion' => $excursion,
            'linkedPlaceIds' => $linkedPlaceIds
        ]);
    }

    // тут получение списка иммерсивов //
    public function getImmersives()
    {
        $this->checkAdminAccess();
        
        $immersives = Immersive::with(['excursion', 'dates'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $immersives->load(['dates.allTimes']);
        
        return response()->json([
            'success' => true,
            'immersives' => $immersives
        ]);
    }

    // тут создание иммерсива //
    public function createDirectImmersive(Request $request)
    {
        $this->checkAdminAccess();
        
        // Обновленная валидация
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'route_length' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'starting_point' => 'nullable|string',
            'map_embed' => 'nullable|string',
            'main_description' => 'required|string',
            'additional_description' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'main_image' => 'nullable|image',
            'audio_demo' => 'nullable|mimes:mp3,wav,ogg|max:10240', // 10MB max
            'audio_preview' => 'nullable|image',
            'date_times' => 'required|json', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $dateTimesData = json_decode($request->date_times, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($dateTimesData)) {
             return response()->json(['success' => false, 'errors' => ['date_times' => ['Некорректный формат данных дат и времени.']]], 422);
        }

        $dateTimeValidator = Validator::make($dateTimesData, [
            '*.date' => 'required|date_format:Y-m-d', 
            '*.times' => 'required|array|min:1',
            '*.times.*' => 'required|date_format:H:i', 
        ], [
            '*.date.required' => 'Дата обязательна для каждого блока.',
            '*.date.date_format' => 'Некорректный формат даты (ожидается ГГГГ-ММ-ДД).',
            '*.times.required' => 'Необходимо выбрать хотя бы одно время для каждой даты.',
            '*.times.min' => 'Необходимо выбрать хотя бы одно время для каждой даты.',
            '*.times.*.required' => 'Время обязательно для каждого слота.',
            '*.times.*.date_format' => 'Некорректный формат времени (ожидается ЧЧ:ММ).',
        ]);

        if ($dateTimeValidator->fails()) {
             return response()->json(['success' => false, 'errors' => $dateTimeValidator->errors()], 422);
        }

        $imagePath = null;
        if ($request->hasFile('main_image')) {
            $imagePath = $request->file('main_image')->store('excursions', 'public');
        }

        // Обработка аудио файлов
        $audioDemoPath = null;
        if ($request->hasFile('audio_demo')) {
            $audioDemoPath = $request->file('audio_demo')->store('audio/demo', 'public');
        }

        $audioPreviewPath = null;
        if ($request->hasFile('audio_preview')) {
            $audioPreviewPath = $request->file('audio_preview')->store('audio/preview', 'public');
        }

        $excursion = Excursion::create([
            'title' => $request->title, 
            'description' => $request->description, 
            'short_description' => $request->short_description, 
            'route_length' => $request->route_length, 
            'duration' => $request->duration, 
            'type' => 'immersive', 
            'main_image' => $imagePath, 
            'starting_point' => $request->starting_point, 
            'map_embed' => $request->map_embed, 
            'price' => 0, 
        ]);

        $immersive = Immersive::create([
            'excursion_id' => $excursion->id, 
            'main_description' => $request->main_description, 
            'additional_description' => $request->additional_description, 
            'price_per_person' => $request->price_per_person,
            'audio_demo' => $audioDemoPath,
            'audio_preview' => $audioPreviewPath,
        ]);

        foreach ($dateTimesData as $dateTime) {
            $date = ImmersiveDate::create([
                'immersive_id' => $immersive->id,
                'event_date' => $dateTime['date'],
                'is_available' => true,
            ]);

            foreach ($dateTime['times'] as $timeString) {
                ImmersiveTime::create([
                    'immersive_date_id' => $date->id,
                    'event_time' => $timeString,
                    'is_available' => true,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Иммерсив успешно создан',
            'immersive' => $immersive->fresh(['excursion', 'dates.allTimes']),
            'excursion' => $excursion
        ]);
    }

    // тут получение деталей иммерсива //
    public function getImmersiveDetails($id)
    {
        $this->checkAdminAccess();
        
        $immersive = Immersive::with(['excursion', 'dates'])->findOrFail($id);
        
        $immersive->load(['dates.allTimes']);
        
        return response()->json([
            'success' => true,
            'immersive' => $immersive
        ]);
    }

    // тут обновление иммерсива //
    public function updateDirectImmersive(Request $request, $id)
    {
        $this->checkAdminAccess();

        // Обновленная валидация
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string',
            'route_length' => 'required|numeric|min:0',
            'duration' => 'required|string',
            'starting_point' => 'nullable|string',
            'map_embed' => 'nullable|string',
            'main_description' => 'required|string',
            'additional_description' => 'required|string',
            'price_per_person' => 'required|numeric|min:0',
            'main_image' => 'nullable|image',
            'audio_demo' => 'nullable|mimes:mp3,wav,ogg|max:10240', // 10MB max
            'audio_preview' => 'nullable|image',
            'new_date_times' => 'nullable|json', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $immersive = Immersive::findOrFail($id);
        $excursion = $immersive->excursion;

        if (!$excursion) {
            return response()->json([
                'success' => false,
                'message' => 'Не найдена связанная экскурсия'
            ], 404);
        }

        $newDateTimesData = null;
        if ($request->has('new_date_times')) {
             $newDateTimesData = json_decode($request->new_date_times, true);
             if (json_last_error() !== JSON_ERROR_NONE || !is_array($newDateTimesData)) {
                 return response()->json(['success' => false, 'errors' => ['new_date_times' => ['Некорректный формат данных новых дат и времени.']]], 422);
             }

             $newDateTimeValidator = Validator::make($newDateTimesData, [
                 '*.date' => 'required|date_format:Y-m-d',
                 '*.times' => 'required|array|min:1',
                 '*.times.*' => 'required|date_format:H:i',
             ], [
                '*.date.required' => 'Дата обязательна для каждого нового блока.',
                '*.date.date_format' => 'Некорректный формат новой даты (ожидается ГГГГ-ММ-ДД).',
                '*.times.required' => 'Необходимо выбрать хотя бы одно время для каждой новой даты.',
                '*.times.min' => 'Необходимо выбрать хотя бы одно время для каждой новой даты.',
                '*.times.*.required' => 'Время обязательно для каждого нового слота.',
                '*.times.*.date_format' => 'Некорректный формат нового времени (ожидается ЧЧ:ММ).',
             ]);

             if ($newDateTimeValidator->fails()) {
                 return response()->json(['success' => false, 'errors' => $newDateTimeValidator->errors()], 422);
             }
        }

        if ($request->hasFile('main_image')) {
            if ($excursion->main_image && Storage::disk('public')->exists($excursion->main_image)) {
                Storage::disk('public')->delete($excursion->main_image);
            }
            $imagePath = $request->file('main_image')->store('excursions', 'public');
            $excursion->main_image = $imagePath;
        }

        // Обработка аудио файлов
        if ($request->hasFile('audio_demo')) {
            if ($immersive->audio_demo && Storage::disk('public')->exists($immersive->audio_demo)) {
                Storage::disk('public')->delete($immersive->audio_demo);
            }
            $immersive->audio_demo = $request->file('audio_demo')->store('audio/demo', 'public');
        }

        if ($request->hasFile('audio_preview')) {
            if ($immersive->audio_preview && Storage::disk('public')->exists($immersive->audio_preview)) {
                Storage::disk('public')->delete($immersive->audio_preview);
            }
            $immersive->audio_preview = $request->file('audio_preview')->store('audio/preview', 'public');
        }

        $excursion->title = $request->title; 
        $excursion->description = $request->description; 
        $excursion->short_description = $request->short_description; 
        $excursion->route_length = $request->route_length; 
        $excursion->duration = $request->duration; 
        $excursion->starting_point = $request->starting_point; 
        $excursion->map_embed = $request->map_embed; 
        $excursion->save();

        $immersive->main_description = $request->main_description; 
        $immersive->additional_description = $request->additional_description; 
        $immersive->price_per_person = $request->price_per_person; 
        $immersive->save();

        if ($newDateTimesData) { 
            foreach ($newDateTimesData as $dateTime) {
                $existingDate = ImmersiveDate::firstOrCreate(
                    [
                        'immersive_id' => $immersive->id,
                        'event_date' => $dateTime['date']
                    ],
                    [
                        'is_available' => true 
                    ]
                );

                 foreach ($dateTime['times'] as $timeString) {
                     ImmersiveTime::firstOrCreate(
                        [
                            'immersive_date_id' => $existingDate->id,
                            'event_time' => $timeString
                        ],
                        [
                             'is_available' => true 
                        ]
                     );
                 }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Иммерсив успешно обновлен',
            'immersive' => $immersive->fresh(['excursion', 'dates.allTimes'])
        ]);
    }

    // тут удаление даты иммерсива //
    public function deleteImmersiveDate($id)
    {
        $this->checkAdminAccess();
        
        $date = ImmersiveDate::findOrFail($id);
        
        $date->times()->delete();
        $date->allTimes()->delete();
        
        $date->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Дата и все связанные времена успешно удалены'
        ]);
    }
    
    // тут удаление времени иммерсива //
    public function deleteImmersiveTime($id)
    {
        $this->checkAdminAccess();
        
        $time = ImmersiveTime::findOrFail($id);
        
        $hasBookings = $time->bookings()->where('is_paid', true)->exists();
        
        if ($hasBookings) {
            return response()->json([
                'success' => false,
                'message' => 'Невозможно удалить время с активными бронированиями'
            ], 422);
        }
        
        $time->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Время успешно удалено'
        ]);
    }
}
