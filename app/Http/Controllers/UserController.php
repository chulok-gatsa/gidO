<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ExcursionPurchase;
use App\Models\Comment;
use App\Models\Excursion;
use App\Models\Immersive;
use App\Models\ImmersiveDate;
use App\Models\ImmersiveTime;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', [
            'only' => [
                'getProfile', 'updateProfile', 'purchaseExcursion',
                'addComment', 'bookImmersive', 'cancelBooking'
            ]
        ]);
    }

    // тут получаем профиль //
    public function getProfile()
    {
        $user = Auth::user();

        // тут получаем покупки экскурсий //
        $excursionPurchases = ExcursionPurchase::with('excursion')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // тут получаем комменты //
        $comments = Comment::with('excursion')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // тут получаем брони //
        $bookings = $user->bookings()
            ->with(['immersive.excursion', 'date', 'time'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'user' => $user,
            'excursionPurchases' => $excursionPurchases,
            'comments' => $comments,
            'bookings' => $bookings
        ]);
    }

    // тут обновляем профиль //
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:users,login,'.$user->id,
        ], [
            'name.required' => 'Поле "Имя" обязательно для заполнения',
            'login.required' => 'Поле "Логин" обязательно для заполнения',
            'login.unique' => 'Пользователь с таким логином уже существует',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->name = $request->name;
        $user->login = $request->login;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Профиль успешно обновлен',
            'user' => $user
        ]);
    }

    // тут получаем каталог экскурсий //
    public function getExcursions(Request $request)
    {
        $type = $request->input('type');
        $query = Excursion::query();

        if ($type) {
            $query->where('type', $type);
        }

        $excursions = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'excursions' => $excursions
        ]);
    }

    // тут получаем детали экскурсии //
    public function getExcursionDetails($id)
    {
        $excursion = Excursion::with(['places', 'approvedComments.user'])->findOrFail($id);

        // тут проверяем купил ли //
        $purchased = false;
        if (Auth::check()) {
            $purchased = ExcursionPurchase::where('user_id', Auth::id())
                ->where('excursion_id', $id)
                ->where('is_paid', true)
                ->exists();
        }

        // тут формируем список //
        $sightsList = $excursion->sights;
        if (empty($sightsList) && $excursion->places->count() > 0) {
            // тут если список не задан //
            $sightsList = $excursion->places->map(function($place, $index) {
                return [
                    'id' => $index + 1,
                    'name' => $place->name
                ];
            })->toArray();
        }

        $data = [
            'success' => true,
            'excursion' => $excursion,
            'purchased' => $purchased,
            'sightsList' => $sightsList
        ];

        // тут если иммерсив //
        if ($excursion->type === 'immersive') {
            $immersive = $excursion->immersive;
            $availableDates = $immersive->availableDates()->take(3)->get();

            $data['immersive'] = $immersive;
            $data['availableDates'] = $availableDates;
            
            // Добавляем URL для аудио файлов, если они есть
            if ($immersive->audio_demo) {
                $data['immersive']['audio_demo_url'] = asset('storage/' . $immersive->audio_demo);
            }
            if ($immersive->audio_preview) {
                $data['immersive']['audio_preview_url'] = asset('storage/' . $immersive->audio_preview);
            }
        }

        return response()->json($data);
    }

    // тут покупаем экскурсию //
    public function purchaseExcursion(Request $request, $id)
    {
        $excursion = Excursion::findOrFail($id);

        // тут создаем запись //
        $purchase = ExcursionPurchase::create([
            'user_id' => Auth::id(),
            'excursion_id' => $id,
            'price' => $excursion->price,
            'is_paid' => true, // тут типа оплата успешна //
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Экскурсия успешно приобретена',
            'purchase' => $purchase
        ]);
    }

    // тут добавляем коммент //
    public function addComment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Проверяем, оставлял ли пользователь комментарий ранее
        $hasComment = Comment::where('user_id', Auth::id())
            ->where('excursion_id', $id)
            ->exists();
            
        if ($hasComment) {
            return response()->json([
                'success' => false,
                'message' => 'Вы уже оставили комментарий для этой экскурсии'
            ], 422);
        }
        
        $excursion = Excursion::findOrFail($id);
        
        $canComment = false;
        
        if ($excursion->type === 'immersive' && $excursion->immersive) {
            $canComment = Booking::where('user_id', Auth::id())
                ->where('immersive_id', $excursion->immersive->id)
                ->where('is_paid', true)
                ->exists();
        } else {
            $canComment = ExcursionPurchase::where('user_id', Auth::id())
                ->where('excursion_id', $id)
                ->where('is_paid', true)
                ->exists();
        }
        
        if (!$canComment) {
            $message = ($excursion->type === 'immersive') 
                ? 'Вы не можете оставить комментарий, так как не бронировали эту иммерсивную экскурсию' 
                : 'Вы не можете оставить комментарий, так как не приобрели эту экскурсию';
                
            return response()->json([
                'success' => false,
                'message' => $message
            ], 403);
        }

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'excursion_id' => $id,
            'content' => $request->content,
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ваш комментарий отправлен на модерацию',
            'comment' => $comment
        ]);
    }

    // тут получаем даты иммерсива //
    public function getImmersiveDates($immersiveId)
    {
        $immersive = Immersive::find($immersiveId);

        if (!$immersive) {
            return response()->json([
                'success' => false,
                'message' => 'Иммерсив не найден',
                'dates' => []
            ], 404);
        }

        $dates = $immersive->availableDates()->get();

        return response()->json([
            'success' => true,
            'dates' => $dates
        ]);
    }

    // тут получаем время для даты //
    public function getImmersiveTimes($dateId)
    {
        \Log::info('Запрос времен для даты ID: ' . $dateId);

        $date = ImmersiveDate::find($dateId);

        if (!$date) {
            \Log::warning('Дата не найдена: ' . $dateId);
            return response()->json([
                'success' => false,
                'message' => 'Дата не найдена'
            ], 404);
        }

        $times = $date->allTimes()
            ->where('immersive_date_id', $dateId)
            ->where('is_available', true)
            ->orderBy('event_time')
            ->get();

        \Log::info('Найдено доступных времен: ' . $times->count() . ' для даты ID: ' . $dateId);

        return response()->json([
            'success' => true,
            'times' => $times
        ]);
    }

    // тут бронируем иммерсив //
    public function bookImmersive(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'immersive_id' => 'required|exists:immersives,id',
            'date_id' => 'required|exists:immersive_dates,id',
            'time_id' => 'required|exists:immersive_times,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'starting_point' => 'required|string',
            'people_count' => 'required|integer|min:1',
        ], [
            'immersive_id.required' => 'Идентификатор иммерсива обязателен',
            'date_id.required' => 'Дата обязательна для выбора',
            'time_id.required' => 'Время обязательно для выбора',
            'name.required' => 'Имя обязательно для заполнения',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'starting_point.required' => 'Начальная точка обязательна для заполнения',
            'people_count.required' => 'Количество человек обязательно для заполнения',
            'people_count.min' => 'Количество человек должно быть не менее 1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Используем транзакцию для атомарности операций
            return \DB::transaction(function () use ($request) {
                
                // Проверяем, нет ли уже бронирования для этого пользователя на это время
                $existingBooking = Booking::where('user_id', Auth::id())
                    ->where('immersive_time_id', $request->time_id)
                    ->where('is_paid', true)
                    ->first();

                if ($existingBooking) {
                    return response()->json([
                        'success' => false,
                        'message' => 'У вас уже есть бронирование на это время.',
                        'errors' => ['time_id' => ['Вы уже забронировали это время']]
                    ], 422);
                }

                // Получаем время с блокировкой для обновления
                $time = \App\Models\ImmersiveTime::lockForUpdate()->find($request->time_id);
                
                if (!$time) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранное время не найдено.',
                        'errors' => ['time_id' => ['Время не найдено']]
                    ], 422);
                }

                // Еще раз проверяем доступность времени (может измениться между запросами)
                if (!$time->is_available) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранное время уже забронировано. Пожалуйста, выберите другое время.',
                        'errors' => ['time_id' => ['Это время уже недоступно для бронирования']]
                    ], 422);
                }

                // Проверяем, что время относится к правильной дате
                if ($time->immersive_date_id != $request->date_id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Выбранное время не соответствует выбранной дате.',
                        'errors' => ['time_id' => ['Несоответствие времени и даты']]
                    ], 422);
                }

                $immersive = Immersive::findOrFail($request->immersive_id);
                $totalPrice = $immersive->price_per_person * $request->people_count;

                // Создаем бронирование
                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'immersive_id' => $request->immersive_id,
                    'immersive_date_id' => $request->date_id,
                    'immersive_time_id' => $request->time_id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'starting_point' => $request->starting_point,
                    'people_count' => $request->people_count,
                    'total_price' => $totalPrice,
                    'is_paid' => true, // тут типа оплата успешна //
                ]);

                // Обновляем статус времени как недоступное
                $time->is_available = false;
                $time->save();

                \Log::info('Успешное бронирование', [
                    'user_id' => Auth::id(),
                    'booking_id' => $booking->id,
                    'immersive_id' => $request->immersive_id,
                    'time_id' => $request->time_id,
                    'date_id' => $request->date_id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Бронирование успешно создано',
                    'booking' => $booking
                ]);
            });
            
        } catch (\Exception $e) {
            \Log::error('Ошибка при бронировании', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'immersive_id' => $request->immersive_id,
                'time_id' => $request->time_id,
                'date_id' => $request->date_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при создании бронирования. Пожалуйста, попробуйте снова.',
                'errors' => ['general' => ['Внутренняя ошибка сервера']]
            ], 500);
        }
    }

    public function cancelBooking($id)
    {
        $booking = Booking::findOrFail($id);

        if (Auth::id() !== $booking->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'У вас нет прав для отмены этого бронирования'
            ], 403);
        }

        $timeId = $booking->immersive_time_id;

        $booking->delete();

        $time = \App\Models\ImmersiveTime::find($timeId);
        if ($time) {
            $time->is_available = true;
            $time->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Бронирование успешно отменено'
        ]);
    }

    // тут получаем новости //
    public function getNews(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $news = \App\Models\News::orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'news' => $news
        ]);
    }

    // тут получаем детали новости //
    public function getNewsDetails($id)
    {
        $news = \App\Models\News::findOrFail($id);

        // тут если хтмл в описании //
        if (!isset($news->content)) {
            $news->content = $news->description;
        }

        return response()->json([
            'success' => true,
            'news' => $news
        ]);
    }

    // тут получаем комменты для главной //
    public function getFeaturedComments()
    {
        $comments = Comment::with('user')
            ->where('is_approved', true)
            ->inRandomOrder()
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    // тут получаем тип экскурсии //
    public function getExcursionType($id)
    {
        $excursion = Excursion::findOrFail($id);

        return response()->json([
            'success' => true,
            'type' => $excursion->type
        ]);
    }

    // тут проверяем бронь юзера //
    public function checkUserBooking($excursionId)
    {
        $excursion = Excursion::findOrFail($excursionId);

        // тут проверяем что иммерсив //
        if ($excursion->type !== 'immersive' || !$excursion->immersive) {
            return response()->json([
                'success' => false,
                'message' => 'Экскурсия не является иммерсивом',
                'hasBooking' => false
            ]);
        }

        // тут проверяем бронирования юзера //
        $hasBooking = Booking::where('user_id', Auth::id())
            ->where('immersive_id', $excursion->immersive->id)
            ->where('is_paid', true)
            ->exists();

        return response()->json([
            'success' => true,
            'hasBooking' => $hasBooking
        ]);
    }

    // тут проверяем коммент юзера //
    public function hasComment($excursionId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
                'hasComment' => false
            ]);
        }

        $hasComment = Comment::where('user_id', Auth::id())
            ->where('excursion_id', $excursionId)
            ->exists();

        return response()->json([
            'success' => true,
            'hasComment' => $hasComment
        ]);
    }

    // тут проверяем покупку экскурсии //
    public function checkExcursionPurchase($excursionId)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Пользователь не авторизован',
                'hasPurchase' => false
            ]);
        }
        
        $excursion = Excursion::findOrFail($excursionId);
        
        if ($excursion->type === 'immersive') {
            return response()->json([
                'success' => false,
                'message' => 'Для иммерсивных экскурсий используйте проверку бронирования',
                'hasPurchase' => false
            ]);
        }
        
        $hasPurchase = ExcursionPurchase::where('user_id', Auth::id())
            ->where('excursion_id', $excursionId)
            ->where('is_paid', true)
            ->exists();
        
        return response()->json([
            'success' => true,
            'hasPurchase' => $hasPurchase
        ]);
    }
}
