<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Excursion;
use App\Models\Comment;
use App\Models\Immersive;

class PageController extends Controller
{

    public function index($path = null)
    {
        $title = 'ГидО - Экскурсии по Астрахани'; // тут название //
        
        $initialPath = $path ?: (request()->is('politics') ? 'politics' : 'welcome');
        
        $isAuthenticated = Auth::check(); // тут проверка авторизации //
        $user = $isAuthenticated ? Auth::user() : null; // тут пользователь //
        
        $userData = null; // тут данные пользователя //
        if ($isAuthenticated) {
            // тут передаем данные //
            $userData = [
                'excursionPurchases' => $user->excursionPurchases()->with('excursion')->orderBy('created_at', 'desc')->get(),
                'bookings' => $user->bookings()->with(['immersive.excursion', 'date', 'time'])->orderBy('created_at', 'desc')->get(),
                'comments' => $user->comments()->with('excursion')->orderBy('created_at', 'desc')->get()
            ];
        }
        
        $recentExcursions = Excursion::orderBy('created_at', 'desc')->take(2)->get(); // тут последние экскурсии //
        
        $recentComments = Comment::with('user') // тут последние комменты //
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Загружаем все данные иммерсивов с датами и временем для SSR
        $immersivesData = $this->prepareImmersivesData();

        // тут выводим данные на страницу //
        return view('main', [
            'title' => $title,
            'initialPath' => $initialPath,
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
            'userData' => $userData,
            'recentExcursions' => $recentExcursions,
            'recentComments' => $recentComments,
            'immersivesData' => $immersivesData
        ]);
    }

    public function indexPolitics()
    {
        $title = 'ГидО - Политика обработки персональных данных';
        $isAuthenticated = Auth::check();
        $user = $isAuthenticated ? Auth::user() : null;
        $userData = null; // тут данные пользователя //
        if ($isAuthenticated) {
            // тут передаем данные //
            $userData = [
                'excursionPurchases' => $user->excursionPurchases()->with('excursion')->orderBy('created_at', 'desc')->get(),
                'bookings' => $user->bookings()->with(['immersive.excursion', 'date', 'time'])->orderBy('created_at', 'desc')->get(),
                'comments' => $user->comments()->with('excursion')->orderBy('created_at', 'desc')->get()
            ];
        }

        return view('politics', [
            'title' => $title,
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
            'userData' => $userData,
        ]);
    }

    public function indexCond()
    {
        $title = 'ГидО - Политика обработки персональных данных';
        $isAuthenticated = Auth::check();
        $user = $isAuthenticated ? Auth::user() : null;
        $userData = null; // тут данные пользователя //
        if ($isAuthenticated) {
            // тут передаем данные //
            $userData = [
                'excursionPurchases' => $user->excursionPurchases()->with('excursion')->orderBy('created_at', 'desc')->get(),
                'bookings' => $user->bookings()->with(['immersive.excursion', 'date', 'time'])->orderBy('created_at', 'desc')->get(),
                'comments' => $user->comments()->with('excursion')->orderBy('created_at', 'desc')->get()
            ];
        }

        return view('cond', [
            'title' => $title,
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
            'userData' => $userData,
        ]);
    }

    /**
     * Подготавливает данные всех иммерсивов с датами и временем для SSR
     */
    private function prepareImmersivesData()
    {
        $immersives = Immersive::with([
            'excursion.places',
            'availableDates.availableTimes'
        ])->get();

        $immersivesData = [];

        foreach ($immersives as $immersive) {
            $dates = [];
            
            foreach ($immersive->availableDates as $date) {
                $times = [];
                
                foreach ($date->availableTimes as $time) {
                    $times[] = [
                        'id' => $time->id,
                        'event_time' => $time->event_time,
                        'formatted_time' => date('H:i', strtotime($time->event_time)),
                        'is_available' => $time->is_available
                    ];
                }
                
                $dates[] = [
                    'id' => $date->id,
                    'event_date' => $date->event_date->format('Y-m-d'),
                    'formatted_date' => $date->event_date->format('d.m.Y'),
                    'month_name' => $this->getMonthName($date->event_date->format('n')),
                    'day' => $date->event_date->format('j'),
                    'display_date' => $date->event_date->format('j') . ' ' . $this->getMonthName($date->event_date->format('n')),
                    'is_available' => $date->is_available,
                    'times' => $times
                ];
            }
            
            // Подготовка данных о местах
            $places = [];
            foreach ($immersive->excursion->places as $place) {
                $places[] = [
                    'id' => $place->id,
                    'name' => $place->name,
                    'description' => $place->description,
                    'image_path' => $place->image_path,
                    'order' => $place->pivot->order ?? 0
                ];
            }
            
            $immersivesData[$immersive->id] = [
                'id' => $immersive->id,
                'excursion' => [
                    'id' => $immersive->excursion->id,
                    'title' => $immersive->excursion->title,
                    'subtitle' => $immersive->excursion->short_description,
                    'description' => $immersive->excursion->description,
                    'image_path' => $immersive->excursion->image_path,
                    'distance' => $immersive->excursion->route_length,
                    'duration' => $immersive->excursion->duration,
                    'starting_point' => $immersive->excursion->starting_point,
                    'map_embed' => $immersive->excursion->map_embed,
                    'about_content' => $immersive->excursion->description,
                    'places' => $places
                ],
                'main_description' => $immersive->main_description,
                'additional_description' => $immersive->additional_description,
                'price_per_person' => $immersive->price_per_person,
                'audio_demo' => $immersive->audio_demo,
                'audio_preview' => $immersive->audio_preview,
                'dates' => $dates
            ];
        }

        return $immersivesData;
    }

    // тут выводим 404 //
    public function notFound()
    {
        return view('errors.404', [
            'title' => 'Страница не найдена'
        ]);
    }

    // тут загружаем страницу booking с данными иммерсива
    public function immersiveBooking($immersiveId)
    {
        $title = 'Бронирование иммерсива - ГидО';
        
        $isAuthenticated = Auth::check();
        $user = $isAuthenticated ? Auth::user() : null;
        
        $userData = null;
        if ($isAuthenticated) {
            $userData = [
                'excursionPurchases' => $user->excursionPurchases()->with('excursion')->orderBy('created_at', 'desc')->get(),
                'bookings' => $user->bookings()->with(['immersive.excursion', 'date', 'time'])->orderBy('created_at', 'desc')->get(),
                'comments' => $user->comments()->with('excursion')->orderBy('created_at', 'desc')->get()
            ];
        }
        
        $recentExcursions = Excursion::orderBy('created_at', 'desc')->take(2)->get();
        
        $recentComments = Comment::with('user')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // загружаем данные иммерсива с датами и временем
        $immersive = Immersive::with([
            'excursion',
            'availableDates.availableTimes'
        ])->find($immersiveId);

        if (!$immersive) {
            return redirect('/catalogExcursions')->with('error', 'Иммерсив не найден');
        }

        // подготавливаем данные для JSON
        $bookingData = [
            'immersive' => [
                'id' => $immersive->id,
                'excursion' => $immersive->excursion,
                'main_description' => $immersive->main_description,
                'additional_description' => $immersive->additional_description,
                'price_per_person' => $immersive->price_per_person,
                'audio_demo' => $immersive->audio_demo,
                'audio_preview' => $immersive->audio_preview
            ],
            'dates' => $immersive->availableDates->map(function($date) {
                return [
                    'id' => $date->id,
                    'event_date' => $date->event_date->format('Y-m-d'),
                    'formatted_date' => $date->event_date->format('d.m.Y'),
                    'month_name' => $this->getMonthName($date->event_date->format('n')),
                    'day' => $date->event_date->format('j'),
                    'is_available' => $date->is_available,
                    'times' => $date->availableTimes->map(function($time) {
                        return [
                            'id' => $time->id,
                            'event_time' => $time->event_time,
                            'is_available' => $time->is_available
                        ];
                    })
                ];
            })
        ];

        return view('main', [
            'title' => $title,
            'initialPath' => 'booking',
            'isAuthenticated' => $isAuthenticated,
            'user' => $user,
            'userData' => $userData,
            'recentExcursions' => $recentExcursions,
            'recentComments' => $recentComments,
            'bookingData' => $bookingData
        ]);
    }

    // вспомогательный метод для получения названия месяца
    private function getMonthName($monthNumber)
    {
        $months = [
            1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
            5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
            9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
        ];
        
        return $months[$monthNumber] ?? '';
    }
}