<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ГидО - Экскурсии по Астрахани' }}</title>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-touch-fullscreen" content="yes">
    <link rel="stylesheet" href="/css/user/base.css?v={{ time() }}">
    <link rel="stylesheet" href="/css/user/components.css?v={{ time() }}">
    <link rel="stylesheet" href="/css/user/pages.css?v={{ time() }}">
    <link rel="stylesheet" href="/css/gido-alert.css?v={{ time() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Unbounded:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .payment-details {
            margin-top: 20px;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .payment-title {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 18px;
            color: #F39040;
            font-weight: 500;
        }

        .card-extra-details {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .card-expiry,
        .card-cvv {
            flex: 1;
        }

        #card-expiry,
        #card-cvv {
            width: 100%;
        }

        .btn_reason:disabled {
            background-color: #d68035;
            cursor: wait;
        }

        #card-number {
            font-family: monospace;
            letter-spacing: 1px;
        }

        .invalid-feedback {
            color: #ff6b6b;
            font-size: 12px;
            margin-top: 5px;
        }

        .audio_content {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 50px;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .audio_preview {
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            border-radius: 15px;
        }

        .audio_preview .preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .audio_preview .preview:hover {
            transform: scale(1.05);
        }

        .audio-player {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 15px;
            min-width: 0;
        }

        .audio-player audio {
            display: none;
        }

        .audio-player-controls {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .play-btn {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 50%;
            background: #3752E9;
            color: white;
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            flex-shrink: 0;
        }

        .play-btn:hover {
            background: #2941D1;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(55, 82, 233, 0.3);
        }

        .progress-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        #immersive-progress-bar {
            width: 100%;
            height: 6px;
            border-radius: 3px;
            background: rgba(55, 82, 233, 0.2);
            outline: none;
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
        }

        #immersive-progress-bar::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3752E9;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(55, 82, 233, 0.3);
            transition: all 0.2s ease;
        }

        #immersive-progress-bar::-webkit-slider-thumb:hover {
            transform: scale(1.2);
            box-shadow: 0 3px 8px rgba(55, 82, 233, 0.4);
        }

        #immersive-progress-bar::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #3752E9;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 6px rgba(55, 82, 233, 0.3);
        }

        .time-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #current-time,
        #immersive-current-time,
        #duration-time,
        #immersive-duration-time {
            font-family: "Unbounded", sans-serif;
            font-size: 12px;
            font-weight: 500;
            color: #3752E9;
        }

        #duration-time,
        #immersive-duration-time {
            opacity: 0.7;
        }

        @media (max-width: 768px) {
            .audio_content {
                flex-direction: column;
                text-align: center;
                gap: 20px;
                padding: 20px;
            }

            .audio_preview .preview {
                width: 120px;
                height: 120px;
            }

            .audio-player-controls {
                flex-direction: column;
                gap: 15px;
                width: 100%;
                align-items: center;
            }

            .progress-container {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .time-display {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                padding: 0 10px;
            }

            #current-time,
            #immersive-current-time,
            #duration-time,
            #immersive-duration-time {
                font-size: 14px !important;
                min-width: 45px;
                text-align: center;
                color: #3752E9;
                font-weight: 500;
            }

            #immersive-progress-bar,
            #progress-bar {
                width: 100%;
                height: 8px;
                border-radius: 4px;
                background: rgba(55, 82, 233, 0.2);
                outline: none;
                cursor: pointer;
                -webkit-appearance: none;
                appearance: none;
                border: none;
            }

            #immersive-progress-bar::-webkit-slider-thumb,
            #progress-bar::-webkit-slider-thumb {
                -webkit-appearance: none;
                appearance: none;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: #3752E9;
                cursor: pointer;
                box-shadow: 0 2px 6px rgba(55, 82, 233, 0.3);
                transition: all 0.2s ease;
                border: none;
            }

            #immersive-progress-bar::-webkit-slider-thumb:hover,
            #progress-bar::-webkit-slider-thumb:hover {
                transform: scale(1.2);
                box-shadow: 0 3px 8px rgba(55, 82, 233, 0.4);
            }

            #immersive-progress-bar::-moz-range-thumb,
            #progress-bar::-moz-range-thumb {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                background: #3752E9;
                cursor: pointer;
                border: none;
                box-shadow: 0 2px 6px rgba(55, 82, 233, 0.3);
            }
        }

        @media (max-width: 480px) {
            .audio_preview .preview {
                width: 100px;
                height: 100px;
            }

            .audio_content {
                gap: 15px;
                padding: 15px;
            }

            .audio-player-controls {
                gap: 12px;
            }

            .progress-container {
                gap: 6px;
            }

            #current-time,
            #immersive-current-time,
            #duration-time,
            #immersive-duration-time {
                font-size: 12px !important;
                min-width: 40px;
            }

            .time-display {
                padding: 0 5px;
            }
        }

        .sight-anchor-link {
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: inline-block;
            padding: 2px 0;
        }

        .sight-anchor-link:hover {
            color: #3752E9;
            text-decoration: none;
            transform: translateX(5px);
        }

        .sight-anchor-link:focus {
            outline: 2px solid #3752E9;
            outline-offset: 2px;
            text-decoration: none;
        }

        #excursion-sights-list {
            line-height: 1.8;
        }
    </style>
</head>

<body>
    <script id="GIDO_BOOKING_DATES" type="application/json">
        @json($immersivesData ?? [])
    </script>

    <div class="container_nav">
        <div class="logo_nav">
            <a href="#" class="nav-link" data-page="welcome"><img class="logo" src="/img/logo.png"
                    alt="Логотип"></a>
        </div>

        <nav class="navigation desktop-nav">
            <ul class="ul_nav">
                @if (isset($isAuthenticated) && $isAuthenticated && isset($user) && $user->is_admin)
                    <li><a class="nav_ani" href="{{ route('admin.index') }}">Админ панель</a></li>
                @endif
                <li><a class="nav_ani nav-link" href="#" data-page="welcome">Главная</a></li>
                <li><a class="nav_ani nav-link" href="#" data-page="catalogExcursions">Экскурсии</a></li>
                <li><a class="nav_ani nav-link" href="#" data-page="blog">Блог</a></li>
                <li><a class="nav_ani nav-link" href="#" data-page="aboutUs">О нас</a></li>
            </ul>
        </nav>

        @if (isset($isAuthenticated) && $isAuthenticated)
            <div class="auth-controls desktop-auth">
                <a class="reg nav-link" href="#" data-page="personalAccount">Личный кабинет</a>
                <a href="{{ route('logout') }}" class="logout-button" title="Выйти">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        @else
            <a class="reg nav-link desktop-auth" href="#" data-page="login">Войти в аккаунт</a>
        @endif

        <div class="mobile-burger" id="mobile-burger">
            <div class="mobile-burger-inner">
                <div class="burger-line"></div>
                <div class="burger-line"></div>
                <div class="burger-line"></div>
            </div>
        </div>
    </div>

    <div class="mobile-nav-overlay" id="mobile-nav-overlay">
        <div class="mobile-nav-container">
            <div class="mobile-nav-header">
                <div class="mobile-nav-logo">
                    <img src="/img/logo.png" alt="ГидО">
                </div>
            </div>

            <nav class="mobile-nav-menu">
                <ul class="mobile-nav-list">
                    @if (isset($isAuthenticated) && $isAuthenticated && isset($user) && $user->is_admin)
                        <li class="mobile-nav-item">
                            <a class="mobile-nav-link" href="{{ route('admin.index') }}">
                                <i class="fas fa-cog"></i>
                                <span>Админ панель</span>
                            </a>
                        </li>
                    @endif
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link nav-link" href="#" data-page="welcome">
                            <i class="fas fa-home"></i>
                            <span>Главная</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link nav-link" href="#" data-page="catalogExcursions">
                            <i class="fas fa-map-marked-alt"></i>
                            <span>Экскурсии</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link nav-link" href="#" data-page="blog">
                            <i class="fas fa-newspaper"></i>
                            <span>Блог</span>
                        </a>
                    </li>
                    <li class="mobile-nav-item">
                        <a class="mobile-nav-link nav-link" href="#" data-page="aboutUs">
                            <i class="fas fa-info-circle"></i>
                            <span>О нас</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="mobile-nav-auth">
                @if (isset($isAuthenticated) && $isAuthenticated)
                    <div class="mobile-auth-user">
                        <a class="mobile-nav-link nav-link" href="#" data-page="personalAccount">
                            <i class="fas fa-user-circle"></i>
                            <span>Личный кабинет</span>
                        </a>
                    </div>
                    <div class="mobile-auth-logout">
                        <a href="{{ route('logout') }}" class="mobile-logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Выйти</span>
                        </a>
                    </div>
                @else
                    <div class="mobile-auth-login">
                        <a class="mobile-login-btn nav-link" href="#" data-page="login">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Войти в аккаунт</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const initialPage = '{{ $initialPath }}';
            showPage(initialPage);

            const mobileNavOverlay = document.getElementById('mobile-nav-overlay');
            const mobileBurger = document.getElementById('mobile-burger');
            const mobileNavLinks = document.querySelectorAll(
                '.mobile-nav-link.nav-link, .mobile-login-btn.nav-link');
            const body = document.body;
            let isAnimating = false;
            let lastClickTime = 0;

            function debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
            }

            function isDoubleClick() {
                const now = Date.now();
                const isDouble = now - lastClickTime < 300;
                lastClickTime = now;
                return isDouble;
            }

            function openMobileNav() {
                if (isAnimating) return;
                isAnimating = true;

                console.log('Opening mobile nav');

                mobileNavOverlay.classList.add('active');
                mobileBurger.classList.add('active');
                body.classList.add('mobile-nav-open');

                const scrollY = window.scrollY;
                body.style.position = 'fixed';
                body.style.top = `-${scrollY}px`;
                body.style.width = '100%';

                const navItems = document.querySelectorAll('.mobile-nav-item');
                navItems.forEach((item, index) => {
                    setTimeout(() => {
                        item.classList.add('animate-in');
                    }, index * 60);
                });

                setTimeout(() => {
                    isAnimating = false;
                }, 400);
            }

            function closeMobileNav() {
                if (isAnimating) return;
                isAnimating = true;

                console.log('Closing mobile nav');

                mobileNavOverlay.classList.remove('active');
                mobileBurger.classList.remove('active');
                body.classList.remove('mobile-nav-open');

                const scrollY = body.style.top;
                body.style.position = '';
                body.style.top = '';
                body.style.width = '';
                window.scrollTo(0, parseInt(scrollY || '0') * -1);

                const navItems = document.querySelectorAll('.mobile-nav-item');
                navItems.forEach(item => {
                    item.classList.remove('animate-in');
                });

                setTimeout(() => {
                    isAnimating = false;
                }, 300);
            }

            function toggleMobileNav() {
                if (isDoubleClick()) {
                    console.log('Double click detected, ignoring');
                    return;
                }

                if (mobileNavOverlay.classList.contains('active')) {
                    closeMobileNav();
                } else {
                    openMobileNav();
                }
            }

            const debouncedToggle = debounce(toggleMobileNav, 50);

            if (mobileBurger) {
                mobileBurger.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    debouncedToggle();
                });

                mobileBurger.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                }, {
                    passive: false
                });

                mobileBurger.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    debouncedToggle();
                }, {
                    passive: false
                });
            }

            if (mobileNavOverlay) {
                mobileNavOverlay.addEventListener('click', function(e) {
                    if (e.target === mobileNavOverlay || !e.target.closest('.mobile-nav-container')) {
                        closeMobileNav();
                    }
                });
            }

            mobileNavLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    setTimeout(() => {
                        closeMobileNav();
                    }, 100);
                });
            });

            const mobileLogoutBtn = document.querySelector('.mobile-logout-btn');
            if (mobileLogoutBtn) {
                mobileLogoutBtn.addEventListener('click', function() {
                    closeMobileNav();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileNavOverlay.classList.contains('active')) {
                    closeMobileNav();
                }
            });

            document.addEventListener('click', function(e) {
                if (mobileNavOverlay && mobileNavOverlay.classList.contains('active')) {
                    if (!e.target.closest('#mobile-burger') &&
                        !e.target.closest('.mobile-nav-container')) {
                        closeMobileNav();
                    }
                }
            });

            if (mobileNavOverlay) {
                mobileNavOverlay.addEventListener('touchmove', function(e) {
                    if (e.target === mobileNavOverlay) {
                        e.preventDefault();
                    }
                }, {
                    passive: false
                });
            }

            window.addEventListener('orientationchange', function() {
                if (mobileNavOverlay && mobileNavOverlay.classList.contains('active')) {
                    setTimeout(() => {
                        mobileNavOverlay.style.height = window.innerHeight + 'px';
                    }, 100);
                }
            });

            window.addEventListener('resize', debounce(function() {
                if (window.innerWidth > 768) {
                    closeMobileNav();
                }
            }, 250));
            setTimeout(() => {
                if (!mobileBurger || !mobileNavOverlay) {
                    return;
                }
            }, 1000);
        });
    </script>

    





    <div id="app-container">
        <div class="page page-welcome">
            <div class="block1_back">
                <img src="/img/block1_backgr.png" class="block1_backgr" alt="background_block1">
                <img src="/img/welcome_mobile/backgr_block1.png" alt="" class="backgr_block1_mobile">
            </div>

            <div class="container">
                <div class="block1">
                    <div class="block1_text">
                        <h1 class="block1_title1">
                            гуляй по астрахани <span class="dec_block1_text">вместе с нами</span>
                        </h1>
                        <p class="block1_subtitle">
                            Присоединяйтесь к нам, чтобы открыть для себя все секреты и богатства Астрахани! Ваше
                            приключение начнется здесь!
                        </p>
                    </div>
                    <div class="block1_img">
                        <img src="/img/block1_img1.png" id="scrollImage" alt="" class="block1_img1">
                    </div>
                </div>
            </div>

            <div class="block1_decoration">
                <div class="text_around_block1">
                    <img src="/img/text_around_img_block1.png" id="myImage" alt=""
                        class="text_around_block1_img">
                </div>
                <div class="arrow_decoration">
                    <img src="/img/arrow.png" alt="" class="arrow">
                </div>
                <div class="block_text_decoration">
                    <div id="text" class="text_decoration">Астраханский Кремль</div>
                </div>
            </div>

            <div class="block2">
                <div class="container">
                    <div class="block2_all">
                        <div class="block2_title1">
                            <h1 class="block2_main_title">
                                АСТРАХАНЬ В ТВОЁМ РИТМЕ
                            </h1>
                        </div>
                        <div class="block2_content_images">
                            <div class="block2_content">
                                <img src="/img/block2_img1.png" alt="" class="block2_img">
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link" data-page="catalogExcursions"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content excursion-card">
                                <img src="{{ $recentExcursions[0]->image_path ?? '/img/block2_img2.png' }}"
                                    alt="{{ $recentExcursions[0]->title ?? 'Экскурсия' }}" class="block2_img">
                                <div class="excursion-title-overlay">
                                    <h3 class="excursion-title">
                                        {{ $recentExcursions[0]->title ?? 'Популярная экскурсия' }}</h3>
                                </div>
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link excursion-link" data-page="excursion"
                                        data-excursion-id="{{ $recentExcursions[0]->id ?? '' }}"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content">
                                <img src="/img/block2_img3.png" alt="" class="block2_img">
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link" data-page="catalogExcursions"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content excursion-card">
                                <img src="{{ $recentExcursions[1]->image_path ?? '/img/block2_img4.png' }}"
                                    alt="{{ $recentExcursions[1]->title ?? 'Экскурсия' }}" class="block2_img">
                                <div class="excursion-title-overlay">
                                    <h3 class="excursion-title">
                                        {{ $recentExcursions[1]->title ?? 'Популярная экскурсия' }}</h3>
                                </div>
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link excursion-link" data-page="excursion"
                                        data-excursion-id="{{ $recentExcursions[1]->id ?? '' }}"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block2_backgr">
                    <img src="/img/block2_backgr.png" alt="" class="block2_backgr_img">
                </div>
            </div>

            <div class="block3">
                <div class="container">
                    <div class="block3_button">
                        <a href="#" class="button3 nav-link" data-page="catalogExcursions">Перейти к экскурсиям
                            <img src="/img/arrow_block3.png" alt="" class="arrow_button">
                        </a>
                    </div>
                    <div class="block3_content">
                        <div class="block3_text_content">
                            <h1 class="block3_title">Первый раз в городе?</h1>
                        </div>
                    </div>
                </div>
                <div class="block3_img_content">
                    <img src="/img/block3_backgr.png" alt="" class="block3_background">
                    <img src="/img/welcome_mobile/block3_mobile_bgr.png" alt="" class="block3_mobile">
                </div>
            </div>

            <div class="block4">
                <div class="container">
                    <div class="block4_content">
                        <div class="block4_text_content">
                            <h1 class="block4_title">отзывы</h1>
                        </div>
                        <div class="comments welcome-comments" id="welcome-comments">
                            @if (isset($recentComments) && $recentComments->count() > 0)
                                @foreach ($recentComments as $comment)
                                    <div class="comment_block_right welcome-comment">
                                        <div class="my_name_comment">
                                            <img src="/img/personalAccount/avatar.png" alt=""
                                                class="avatar_comment">
                                            <h4 class="title_name">{{ $comment->user->name }}</h4>
                                            <div class="comment-status">
                                                @if ($comment->is_approved)
                                                    <div class="approved" title="Одобрен"></div>
                                                @elseif($comment->is_rejected)
                                                    <div class="not_approved" title="Отклонен"></div>
                                                @else
                                                    <div class="pending" title="На модерации"></div>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="title_name2">
                                            {{ $comment->excursion ? strtolower($comment->excursion->title) : 'экскурсия' }}
                                        </p>
                                        <p class="descript_comment">{{ $comment->content }}</p>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-state">
                                    <div class="empty-state-icon"
                                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\'/%3E%3C/svg%3E');">
                                    </div>
                                    <h3 class="empty-state-title">Пока нет отзывов</h3>
                                    <p class="empty-state-description">Станьте первым, кто поделится впечатлениями о
                                        наших экскурсиях!</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="block5">
                <div class="container">
                    <div class="block5_content">
                        <div class="block5_text_content">
                            <h1 class="block5_title">ПОЛНОЕ ПОГРУЖЕНИЕ</h1>
                        </div>
                        <div class="block2_content_images block5_content_images">
                            <div class="block2_content block5_content">
                                <img src="/img/block5_img1.png" alt="" class="block2_img">
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link" data-page="catalogExcursions"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content excursion-card">
                                <img src="{{ $recentExcursions[0]->image_path ?? '/img/block2_img2.png' }}"
                                    alt="{{ $recentExcursions[0]->title ?? 'Экскурсия' }}" class="block2_img">
                                <div class="excursion-title-overlay">
                                    <h3 class="excursion-title">
                                        {{ $recentExcursions[0]->title ?? 'Популярная экскурсия' }}</h3>
                                </div>
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link excursion-link" data-page="excursion"
                                        data-excursion-id="{{ $recentExcursions[0]->id ?? '' }}"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content">
                                <img src="/img/block5_img3.png" alt="" class="block2_img">
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link" data-page="catalogExcursions"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                            <div class="block2_content excursion-card">
                                <img src="{{ $recentExcursions[1]->image_path ?? '/img/block5_img4.png' }}"
                                    alt="{{ $recentExcursions[1]->title ?? 'Экскурсия' }}" class="block2_img">
                                <div class="excursion-title-overlay">
                                    <h3 class="excursion-title">
                                        {{ $recentExcursions[1]->title ?? 'Популярная экскурсия' }}</h3>
                                </div>
                                <div class="block2_link_arrow">
                                    <a href="#" class="nav-link excursion-link" data-page="excursion"
                                        data-excursion-id="{{ $recentExcursions[1]->id ?? '' }}"><img
                                            src="/img/arrow_link.png" alt="Стрелка" class="arrow_link"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="block2_backgr">
                    <img src="/img/block2_backgr.png" alt="" class="block2_backgr_img">
                </div>
            </div>

            <div class="block6">
                <div class="block6_backgr">
                    <img src="/img/block6_backgr.png" alt="" class="block6_backgr_img">
                </div>
                <div class="container">
                    <div class="block6_content">
                        <div class="block6_text_content">
                            <h1 class="block6_title">ЧАСТО ЗАДАВАЕМЫЕ ВОПРОСЫ</h1>
                        </div>
                    </div>
                    <div class="items_block6">
                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Что такое иммерсивный формат?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>Иммерсивный формат — это взаимодействие зрителя и самого шоу, эффект полного погружения в
                                происходящее. Здесь продумано всё: костюмы, декорации, реквизит, сюжет.</p>
                        </div>

                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Что входит в стоимость билета?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>На время мероприятия предусмотрена аренда беспроводных наушников, а также сопровождение
                                администратора для комфортного участия. В случае дождя участникам будут предоставлены
                                дождевики для прогулок, что позволит оставаться сухими и наслаждаться временем на свежем
                                воздухе. Кроме того, это отличная возможность для новых интересных знакомств.</p>
                        </div>

                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Обязательно ли бронировать билет заранее?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>Забронировать билет заранее очень важно, чтобы гарантировать себе место на мероприятии и
                                избежать возможности его распродажи. Кроме того, это поможет вам лучше спланировать своё
                                время и гарантирует, что вы получите все преимущества, предлагаемые в рамках
                                мероприятия.</p>
                        </div>
                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Что насчет физической нагрузки? Придется бегать?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>Не волнуйтесь, вам не придется бегать. При прослушивании аудио экскурсии вы можете
                                самостоятельно выбирать темп своей прогулки. Это позволяет наслаждаться экскурсионным
                                материалом в удобном для вас ритме, не испытывая избыточной физической нагрузки.</p>
                        </div>

                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Как проходит бесконтактная экскурсия?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>Бесконтактная экскурсия проходит в формате аудиоэкскурсии, где участники предварительно
                                получают доступ к аудиоматериалам, которые проводят их по заданному маршруту. Они могут
                                слушать информацию о достопримечательностях в любое время, делая остановки по желанию.
                                Это обеспечивает комфортное и безопасное изучение новых мест без необходимости в личном
                                гидe.</p>
                        </div>
                        <div class="faq-item" onclick="toggleFAQ(this)">
                            <span>Какие места будут посещены во время экскурсии?</span>
                            <span class="toggle-icon">+</span>
                        </div>
                        <div class="faq-content">
                            <p>Каждая иммерсивная прогулка имеет свои индивидуальные места для посещения. Вы можете
                                узнать о конкретных локациях и деталях экскурсии на странице, посвященной данной
                                прогулке. Рекомендуем ознакомиться с этой информацией перед бронированием, чтобы
                                убедиться, что экскурсия соответствует вашим ожиданиям.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-catalogExcursions" style="display: none;">
            <div class="block1_back">
                <img src="/img/catalogExcursions/block1_backgr.png" class="block1_backgr" alt="background_block1">
            </div>
            <div class="block1_back2">
                <img src="/img/catalogExcursions/block1_backgr2.png" class="block2_backgr" alt="background_block1">
            </div>
            <div class="block1_blur">
                <img src="/img/catalogExcursions/blur.png" class="block1_blur_img" alt="background_block1">
            </div>
            <div class="block1_mobile">
                <img src="/img/catalogExcursions_mobile/backgr_block1_mobile.png" alt=""
                    class="backgr_mobile">
            </div>

            <div class="block1">
                <div class="block1_img_dec1">
                    <img src="/img/catalogExcursions/block1_dec1.png" alt="Первая картинка" class="image image1"
                        id="image1">
                    <img src="/img/catalogExcursions/block1_dec2.png" alt="Вторая картинка" class="image image2"
                        id="image2">
                    <img src="/img/catalogExcursions/block1_dec3.png" alt="Третья картинка" class="image image3"
                        id="image3">
                </div>

                <div class="container">
                    <div class="block1_content">
                    </div>
                </div>
            </div>
            <div class="arrow_block1_2">
                <img id="arrow" src="/img/catalogExcursions/arrow.png" alt="Стрелка" class="arrow" />
            </div>

            <div class="block2" id="block2">
                <div class="container">
                    <div class="block2_content">
                        <div class="main_title_block2">
                            <h1 class="title_block2">Экскурсии</h1>
                        </div>

                        <div class="sort">
                            <div class="custom-select" id="customSelect">
                                Выберите фильтры
                                <img src="/img/catalogExcursions/arrow_block2.png" alt="arrow" class="arrow-icon">
                            </div>
                            <div class="option-list" id="optionList">
                                <div class="option-item" data-value="all">Все экскурсии</div>
                                <div class="option-item" data-value="audio">Аудиоэкскурсии</div>
                                <div class="option-item" data-value="immersive">Иммерсив</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block3">
                <div class="container">
                    <div class="block3_content">
                        <div class="excursion" id="excursions-container">
                            <!-- dynamic (AJAX) -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-blog" style="display: none;">
            <div class="container">
                <div class="block1">
                    <div class="block1_text">
                        <h1 class="block1_title1">
                            Читай наш <span class="dec_block1_text">блог</span>
                        </h1>
                        <p class="block1_subtitle">
                            Следи за нашим блогом и погружайся в мир интересных идей и вдохновения! Читай, делись и будь
                            на волне свежих мыслей!
                        </p>
                    </div>
                    <div class="block1_img">
                        <img src="/img/blog/block1_img1.png" id="scrollImage" alt="" class="block1_img1">
                    </div>
                </div>
            </div>

            <div class="block1_decoration">
                <div class="text_around_block1">
                    <img src="/img/text_around_img_block1.png" id="myImage" alt=""
                        class="text_around_block1_img">
                </div>
                <div class="arrow_decoration">
                    <img src="/img/arrow.png" alt="" class="arrow">
                </div>
                <div class="block_text_decoration">
                    <div id="text" class="text_decoration">Астраханский Кремль</div>
                </div>
            </div>

            <div class="block2">
                <div class="container">
                    <div class="block2_content">
                        <h1 class="block2_main_title">
                            следи за волной новых мыслей
                        </h1>
                        <div class="news" id="news-container">
                            <!-- dynamic (AJAX) -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="block3">
                <img src="/img/blog/backgr_block3.png" alt="" class="backgr_block3">
                <div class="container">
                    <div class="block3_content">
                        <div class="left">
                            <h1 class="main_title">Не пропусти ни одной интересной статьи!</h1>
                            <p class="subtitle">Подписывайтесь на наш блог и получайте свежие новости об
                                аудиоэкскурсиях и иммерсивных прогулках</p>
                            <img src="/img/blog/arrow_block3.png" alt="" class="arrow_block3_news">
                        </div>
                        <div class="right">
                            <a href="#"><img src="/img/blog/group_tg.png" alt="" class="group_tg"></a>
                            <a href="#"><img src="/img/blog/group_vk.png" alt="" class="group_vk"></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-aboutUs" style="display: none;">
            <div class="block1_back">
                <img src="/img/aboutUs/block1_backgr.png" class="block1_backgr" alt="background_block1">
                <img src="/img/aboutUs_mobile/backgr_mobile.png" alt="" class="mobile_backgr_block1">
            </div>

            <div class="block1">
                <div class="backgr2">
                    <img src="/img/aboutUs/backgr_monu.png" alt="" class="monu">
                </div>
                <div class="container">
                    <div class="block1_content">
                        <h1 class="main_title">Кто мы такие?</h1>
                    </div>
                    <div class="content_dec_block3">
                        <div class="image-container_block1">
                            <img src="/img/aboutUs/messages.png" class="mess_dec" alt="Изображение 1">
                            <img src="/img/aboutUs_mobile/mess_dec_mobile.png" alt=""
                                class="mess_dec_mobile">
                            <div class="text text_dec1" id="text1"></div>
                            <div class="text text_dec2" id="text2"></div>
                            <div class="text text_dec3" id="text3"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block2">
                <div class="container">
                    <div class="block2_content">
                        <div class="block2_text_content">
                            <h1 class="main_title_block2">Почему именно мы?</h1>
                            <p class="subtitle_block2">Мы стремимся представить Астрахань не как статичную
                                достопримечательность, а как динамичное пространство, полное жизни. Каждое историческое
                                место будет оживать благодаря увлекательным рассказам и интерактивным элементам.</p>
                            <div class="image-container_block2">
                                <img src="/img/aboutUs/dec1_block2.png" alt="Картинка 1" class="fade dec1_block2">
                                <img src="/img/aboutUs/dec2_block2.png" alt="Картинка 2" class="fade dec2_block2">
                                <img src="/img/aboutUs/dec3_block2.png" alt="Картинка 3" class="fade dec3_block2">
                                <img src="/img/aboutUs/dec4_block2.png" alt="Картинка 4" class="fade dec4_block2">
                                <img src="/img/aboutUs/dec5_block2.png" alt="Картинка 5" class="fade dec5_block2">
                                <img src="/img/aboutUs/dec6_block2.png" alt="Картинка 6" class="fade dec6_block2">
                            </div>
                            <a href="#" class="block2_button nav-link" data-page="catalogExcursions">Пойдём
                                гулять?
                                <img src="/img/aboutUs/arrow_but_block2.png" alt="" class="arrow_but_block2">
                            </a>
                        </div>
                        <div class="block2_img_content">
                            <img src="/img/aboutUs/img_block2.png" alt="" class="img_block2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="block3">
                <div class="backgr_block3">
                    <img src="/img/aboutUs/block3_back.png" alt="" class="block3_back">
                    <img src="/img/aboutUs_mobile/back_block3_mobile.png" alt="" class="block3_back_mobile">
                </div>
                <div class="container">
                    <div class="block3_content">
                        <div class="text_content_block3">
                            <h1 class="main_title_block3">Как выбрать маршрут?</h1>
                            <p class="subtitle_block3">Мы стремимся представить Астрахань не как статичную
                                достопримечательность, а как динамичное пространство, полное жизни.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block4">
                <div class="backgr_block4">
                    <img src="/img/aboutUs/backgr_block4.png" alt="" class="block4_back">
                </div>

                <div class="container">
                    <div class="block4_content">
                        <div class="block4_text_content">
                            <h1 class="main_title_block4">Определите свои интересы</h1>
                            <p class="subtitle_block4">Прежде чем выбирать экскурсию, подумайте о том, что именно вас
                                интересует. История, культура, природа или архитектура? Это поможет вам сузить круг
                                выбора.</p>
                        </div>
                        <div class="block4_text_content">
                            <h1 class="main_title_block4">Читайте отзывы</h1>
                            <p class="subtitle_block4">Ознакомьтесь с отзывами других участников экскурсий. Это даст
                                вам представление о качестве и содержании экскурсии, а также о том, насколько она вам
                                подойдет.</p>
                        </div>
                        <div class="block4_text_content">
                            <h1 class="main_title_block4">Забронируйте заранее</h1>
                            <p class="subtitle_block4">После выбора экскурсии не откладывайте бронирование. Убедитесь,
                                что у вас есть место и вы получите лучшие условия.</p>
                        </div>
                    </div>
                </div>
                <div class="image-container_block4" id="imageBlock">
                    <img src="/img/aboutUs/dec_block4.png" alt="Изображение" class="animated-image"
                        id="imageElement">
                </div>
            </div>

            <div class="block5">
                <div class="container">
                    <div class="backgr_block5">
                        <img src="/img/aboutUs/backgr_block5.png" alt="" class="backgr_img_block5">
                    </div>
                    <div class="block5_content">
                        <div class="text_content_block5">
                            <h1 class="main_block5">В чём смысл иммерсивных прогулок?</h1>
                            <div class="second_content">
                                <div class="left_cont">
                                    <img src="/img/aboutUs/block5_img.png" alt="" class="block5_img">
                                </div>
                                <div class="right_cont">
                                    <div class="block5_text_content">
                                        <h1 class="main_title_block5">Погружение в атмосферу</h1>
                                        <p class="subtitle_block5">Благодаря аудиосопровождению вы будете как никогда
                                            глубже чувствовать историю и культуру Астрахани</p>
                                    </div>
                                    <div class="block5_text_content">
                                        <h1 class="main_title_block5">Индивидуальный подход</h1>
                                        <p class="subtitle_block5">Вы сами управляете своим временем и темпом прогулки
                                        </p>
                                    </div>
                                    <div class="block5_text_content">
                                        <h1 class="main_title_block5">Увлекательные рассказы</h1>
                                        <p class="subtitle_block5">Наши аудиогиды наполнены увлекательными историями,
                                            анекдотами и фактами о городе, которые сделают вашу прогулку незабываемой
                                        </p>
                                    </div>
                                    <div class="but_block5">
                                        <a href="#" class="nav-link" data-page="catalogExcursions">
                                            <p class="but5">Скорее бронируй</p>
                                            <img src="/img/aboutUs/arrow_but_block2.png" alt=""
                                                class="but_block5_arrow">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-login" style="display: none;">
            <div class="block1">
                <div class="background_block1">
                    <img src="/img/registration/back_block1.png" alt="" class="back_block1">
                    <img src="/img/registration_mobile/backgr_registr_mobile.png" alt=""
                        class="backgr_mobile">
                </div>
                <div class="container">
                    <div class="block1_content">
                        <p class="main_title_block1">Войти в аккаунт</p>
                        <div class="fields">
                            <input type="text" placeholder="Логин или Email" class="entry_field" id="login-login"
                                name="login" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                            </div>

                            <input type="password" placeholder="Пароль" class="entry_field" id="login-password"
                                name="password" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                            </div>
                        </div>
                        <div id="login-error-message" style="display: none; color: red; margin-bottom: 15px;"></div>

                        <div class="agreement-container">
                            <label class="agreement-label">
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="login-agreement">
                                    <span class="checkmark"></span>
                                </div>
                                <span class="agreement-text">Я соглашаюсь с <a href="{{ route('cond') }}">условиями сайта</a> и <a
                                        href="{{ route('politics') }}">политикой обработки персональных данных</a></span>
                            </label>
                            <div id="login-agreement-error" class="agreement-error">Необходимо согласие с условиями
                                сайта</div>
                        </div>

                        <button type="button" class="button_block1" id="login-button">Войти</button>
                        <p class="enter">Ещё нет аккаунта? <a href="#" class="nav-link"
                                data-page="registration"><span class="orange">Зарегистрироваться</span></a></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-registration" style="display: none;">
            <div class="block1">
                <div class="background_block1">
                    <img src="/img/registration/back_block1.png" alt="" class="back_block1">
                    <img src="/img/registration_mobile/backgr_registr_mobile.png" alt=""
                        class="backgr_mobile">
                </div>
                <div class="container">
                    <div class="block1_content">
                        <p class="main_title_block1">Регистрация</p>
                        <div class="fields">
                            <input type="text" placeholder="Имя" class="entry_field" id="register-name"
                                name="name" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                            </div>

                            <input type="text" placeholder="Логин" class="entry_field" id="register-login"
                                name="login" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                            </div>

                            <input type="email" placeholder="Email" class="entry_field" id="register-email"
                                name="email" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                            </div>

                            <input type="password" placeholder="Пароль" class="entry_field" id="register-password"
                                name="password" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                                Пароль должен содержать не менее 6 символов</div>

                            <input type="password" placeholder="Повтор пароля" class="entry_field"
                                id="register-password_confirmation" name="password_confirmation" required>
                            <div class="invalid-feedback"
                                style="display: none; color: red; font-size: 0.8em; margin-top: -10px; margin-bottom: 10px;">
                                Пароль должен содержать не менее 6 символов</div>
                        </div>
                        <div class="agreement-container">
                            <label class="agreement-label">
                                <div class="custom-checkbox">
                                    <input type="checkbox" id="register-agreement">
                                    <span class="checkmark"></span>
                                </div>
                                <span class="agreement-text">Я соглашаюсь с <a href="{{ route('cond') }}">условиями сайта</a> и <a
                                        href="{{ route('politics') }}">политикой обработки персональных данных</a></span>
                            </label>
                            <div id="register-agreement-error" class="agreement-error">Необходимо согласие с условиями
                                сайта</div>
                        </div>

                        <button type="button" class="button_block1" id="register-button">Зарегистрироваться</button>
                        <p class="enter">Уже есть аккаунт? <a href="#" class="nav-link"
                                data-page="login"><span class="orange">Войти</span></a></p>
                    </div>
                </div>
            </div>
        </div>
        @auth
            <div class="page page-personalAccount" style="display: none;">
                <div class="block1">
                    <div class="container">
                        <div class="block1_content">
                            <div class="profile-left-section">
                                <div class="profile-header">
                                    <div class="avatar-container">
                                        <img src="/img/personalAccount/avatar.png" alt="" class="avatar_profile">
                                    </div>
                                    <div class="profile-info">
                                        <h2 class="my_name_profile" id="profile-name">{{ $user->name ?? 'Загрузка...' }}
                                        </h2>
                                        <p class="my_friends" id="profile-comments-count">мои комментарии:
                                            {{ $userData['comments']->count() ?? '...' }}</p>
                                        <a href="#" class="edit_profile_btn nav-link"
                                            data-page="editProfile">Изменить профиль</a>
                                    </div>
                                </div>

                                <div class="excursions-section">
                                    <h3 class="main_title_block2">Мои экскурсии</h3>
                                    <div class="block2_content_left" id="user-excursions">
                                        @if (isset($userData) && ($userData['excursionPurchases']->count() > 0 || $userData['bookings']->count() > 0))
                                            @foreach ($userData['excursionPurchases'] as $purchase)
                                                <div class="block2_img_content">
                                                    <img src="{{ $purchase->excursion->image_path ?? '/img/personalAccount/excursuion.png' }}"
                                                        alt="" class="my_excurs">
                                                    <div class="excursion-info">
                                                        <h4 class="name_excursion">{{ $purchase->excursion->title }}</h4>
                                                        <div class="excursion-meta">
                                                            <span
                                                                class="excursion-type">{{ $purchase->excursion->type === 'audio' ? 'Аудио' : 'Иммерсив' }}</span>
                                                            <a href="#" class="arrow_block3 excursion-link"
                                                                data-excursion-id="{{ $purchase->excursion->id }}"
                                                                data-excursion-type="{{ $purchase->excursion->type }}"></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            @foreach ($userData['bookings'] as $booking)
                                                @if ($booking->immersive && $booking->immersive->excursion)
                                                    @php
                                                        $excursion = $booking->immersive->excursion;
                                                        $dateString = $booking->date
                                                            ? date('d.m.Y', strtotime($booking->date->event_date))
                                                            : '';
                                                        $timeString = $booking->time ? $booking->time->event_time : '';
                                                        $dateTimeInfo =
                                                            $dateString && $timeString
                                                                ? " ($dateString, $timeString)"
                                                                : '';
                                                    @endphp
                                                    <div class="block2_img_content">
                                                        <img src="{{ $excursion->image_path ?? '/img/personalAccount/excursuion.png' }}"
                                                            alt="" class="my_excurs">
                                                        <div class="excursion-info">
                                                            <h4 class="name_excursion">
                                                                {{ $excursion->title }}{{ $dateTimeInfo }}</h4>
                                                            <div class="excursion-meta">
                                                                <span class="excursion-type">Иммерсив</span>
                                                                <a href="#" class="arrow_block3 excursion-link"
                                                                    data-excursion-id="{{ $excursion->id }}"
                                                                    data-excursion-type="{{ $excursion->type }}"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="empty-state">
                                                <div class="empty-state-icon"
                                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z\'/%3E%3C/svg%3E');">
                                                </div>
                                                <h3 class="empty-state-title">Нет приобретенных экскурсий</h3>
                                                <p class="empty-state-description">Исследуйте наш каталог и выберите
                                                    экскурсию, которая вас заинтересует!</p>
                                                <a href="#" class="empty-state-action nav-link"
                                                    data-page="catalogExcursions">Перейти в каталог</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="profile-right-section">
                                <h3 class="main_title_block_right">Мои комментарии</h3>
                                <div id="user-comments">
                                    @if (isset($userData) && $userData['comments']->count() > 0)
                                        @foreach ($userData['comments'] as $comment)
                                            <div class="comment_block_right">
                                                <div class="my_name_comment">
                                                    <img src="/img/personalAccount/avatar.png" alt=""
                                                        class="avatar_comment">
                                                    <h4 class="title_name">{{ $user->name }}</h4>
                                                    <div class="comment-status">
                                                        @if ($comment->is_approved)
                                                            <div class="approved" title="Одобрен"></div>
                                                        @elseif($comment->is_rejected)
                                                            <div class="not_approved"
                                                                onclick="openRejectionReasonModal('{{ $comment->rejection_reason ?? 'Причина не указана' }}')"
                                                                title="Отклонен - нажмите для подробностей"></div>
                                                        @else
                                                            <div class="pending" title="На модерации"></div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <p class="title_name2">
                                                    {{ $comment->excursion ? strtolower($comment->excursion->title) : 'экскурсия' }}
                                                </p>
                                                <p class="descript_comment">{{ $comment->content }}</p>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="empty-state">
                                            <div class="empty-state-icon"
                                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\'/%3E%3C/svg%3E');">
                                            </div>
                                            <h3 class="empty-state-title">Вы еще не оставили отзывов</h3>
                                            <p class="empty-state-description">После прохождения экскурсии вы сможете
                                                поделиться своими впечатлениями!</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page page-editProfile" style="display: none;">
                <div class="block1">
                    <div class="block1_content">
                        <div class="block1_content_left">
                            <a href="#" class="nav-link back_edit_profile" data-page="personalAccount"></a>
                            <h1 class="main_title">Изменение профиля</h1>

                            <div class="inp_fields" id="profile-update-form">
                                <div class="form-group">
                                    <label for="name">Имя</label>
                                    <input type="text" class="edit_field" id="name" placeholder="Загрузка...">
                                    <div class="invalid-feedback" id="name-error" style="display: none; color: red;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="login">Логин</label>
                                    <input type="text" class="edit_field" id="login" placeholder="Загрузка...">
                                    <div class="invalid-feedback" id="login-error" style="display: none; color: red;">
                                    </div>
                                </div>

                                <button type="button" class="btn_save_edit" id="update-profile-button">Сохранить
                                    изменения</button>
                            </div>

                            <div class="inp_fields" id="password-update-form">
                                <div class="form-group">
                                    <label for="current_password">Текущий пароль</label>
                                    <input type="password" class="edit_field" id="current_password"
                                        placeholder="Введите текущий пароль">
                                    <div class="invalid-feedback" id="current_password-error"
                                        style="display: none; color: red;"></div>
                                </div>

                                <div class="form-group">
                                    <label for="password">Новый пароль</label>
                                    <input type="password" class="edit_field" id="password"
                                        placeholder="Введите новый пароль">
                                    <div class="invalid-feedback" id="password-error" style="display: none; color: red;">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="password_confirmation">Повторите новый пароль</label>
                                    <input type="password" class="edit_field" id="password_confirmation"
                                        placeholder="Повторите новый пароль">
                                    <div class="invalid-feedback" id="password_confirmation-error"
                                        style="display: none; color: red;"></div>
                                </div>

                                <button type="button" class="btn_save_edit" id="update-password-button">Изменить
                                    пароль</button>
                            </div>

                            <div class="menu" id="menu" style="display: none;">
                                <div class="content_menu">
                                    <button class="close-btn" onclick="closeMenu()">×</button>
                                    <p class="menu_title" id="menu-message">Изменения сохранены</p>
                                </div>
                            </div>
                            <div class="overlay" id="overlay" onclick="closeMenu()" style="display: none;"></div>
                        </div>

                        <div class="block1_content_right">
                        </div>
                    </div>
                </div>
            </div>
        @endauth
        <div class="page page-excursion" style="display: none;">
            <div class="block1">
                <div class="backgr_block1">
                    <img src="/img/excursion/block1_backgr.png" alt="" class="back1">
                </div>
                <div class="container">
                    <div class="block1_content">
                        <div class="img_content_block1">
                            <img src="/img/excursion/block1_img1.png" alt="" class="block1_image"
                                id="excursion-image">
                        </div>
                        <div class="text_content_block1">
                            <h1 class="main_title_block1" id="excursion-title">Загрузка...</h1>
                            <p class="subtitle_block1" id="excursion-subtitle">Загрузка...</p>
                            <p class="descript_block1" id="excursion-description">Загрузка...</p>
                        </div>
                        <div class="dec_block1">
                            <div class="decors">
                                <p class="km" id="excursion-distance">... км</p>
                                <p class="view" id="excursion-type">...</p>
                                <p class="places" id="excursion-duration">... часа</p>
                            </div>
                        </div>
                        <div class="buttons_block1" id="excursion-buttons">
                            <!-- dynamic (AJAX) -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="block2">
                <div class="backgr_block2">
                    <img src="/img/excursion/block2_backgr.png" alt="" class="back2">
                </div>
                <div class="container">
                    <div class="container" style="justify-content: center; width: 100%;">
                        <h1 class="main_title_block2" id="excursion-about-title"
                            style="text-align: center; width: 100%;">О чём прогулка?</h1>
                    </div>
                    <div class="block2_content">
                        <div class="text_content_block2" id="excursion-about-content">
                            <!-- dynamic (AJAX) -->
                        </div>
                        <div class="audio_content" id="aud" style="margin-left: auto;">
                            <div class="audio_preview">
                                <img src="/img/excursion/block2_preview.png" alt="" class="preview">
                            </div>
                            <div class="audio-player">
                                <audio id="audio" src=""></audio>
                                <div class="audio-player-controls">
                                    <button id="play-button" class="play-btn">▶</button>
                                    <div class="progress-container">
                                        <input type="range" id="progress-bar" value="0" max="100">
                                        <div class="time-display">
                                            <span id="current-time">0:00</span>
                                            <span id="duration-time">0:00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="excursion-comments-form" style="display: none;">
                        <h1 class="title_block5">Оставь свой отзыв</h1>
                        <textarea name="" id="comment-content" class="feedback" placeholder="Поделись впечатлениями"></textarea>
                        <a href="#" class="btn_upload" id="comment-submit">Опубликовать</a>
                    </div>
                </div>
            </div>

            <div class="start_point">
                <div class="container">
                    <h1 class="title_start_point">Начальная точка маршрута</h1>
                    <p class="start" id="excursion-start-point">Загрузка...</p>
                </div>
            </div>

            <div class="block3">
                <div class="container">
                    <div class="block3_content">
                        <div class="map_block3">
                            <iframe class="carts" id="excursion-map"
                                src="https://www.google.com/maps/embed?pb=!1m52!1m12!1m3!1d11015.815525605656!2d48.015154514240265!3d46.350459698769676!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m37!3e2!4m5!1s0x41a9057f3cc56263%3A0xf6ef8074ee3596f0!2z0JHRgNCw0YLRgdC60LjQuSDRgdCw0LTQuNC6LCDRg9C70LjRhtCwINCSLiDQotGA0LXQtNC40LDQutC-0LLRgdC60L7Qs9C-LCDQkNGB0YLRgNCw0YXQsNC90Yw!3m2!1d46.350308299999995!2d48.035155499999995!4m5!1s0x41a90f807a376a7d%3A0x6d56563a480ea4d!2z0J_QsNC80Y_RgtC90LjQuiDQki7QmC4g0JvQtdC90LjQvdGDLCDQv9C7LiDQm9C10L3QuNC90LAsIDEwLCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.3477263!2d48.0301391!4m5!1s0x41a90f820ff9430b%3A0xee0f6df05a84b971!2z0JrQuNGA0L7QstGB0LrQuNC5INGALdC9LCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.345650199999994!2d48.022130399999995!4m5!1s0x41a90f7905f3ffb7%3A0x27cc8a2b471532fc!2z0J_QsNC80Y_RgtC90LjQuiDQn9C10YLRgNGDIEk!3m2!1d46.3471579!2d48.015971699999994!4m5!1s0x41a90f7e57c7de0d%3A0x6f3e089a8da43e49!2z0JrRgNC10LzQu9C10LLRgdC60LDRjyDRg9C7LiwgMtCxLCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.349669399999996!2d48.0218316!4m5!1s0x41a90583bf5bfc41%3A0x2e45f051cbdd41fe!2z0KjQvtC70Lg!3m2!1d46.3552405!2d48.0326964!5e0!3m2!1sru!2sru!4v1738578131745!5m2!1sru!2sru"
                                allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block4" id="places-block" style="display: none;">
                <div class="container">
                    <div class="block4_content" id="excursion-places">
                        <!-- dynamic (AJAX) -->
                    </div>
                </div>
            </div>

            <div class="block5">
                <div class="container">
                    <div class="block5_content">
                        <h1 class="main_title_block5">Отзывы</h1>
                        <div class="comments" id="excursion-comments">
                            <!-- dynamic (AJAX) -->
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-booking" style="display: none;">
            <div class="block1">
                <div class="backgr_block1">
                    <img src="/img/excursion/block1_backgr.png" alt="" class="back1">
                </div>
                <div class="container">
                    <div class="block1_content">
                        <div class="img_content_block1">
                            <img src="/img/excursion/block1_img1.png" alt="" class="block1_image"
                                id="immersive-image">
                        </div>
                        <div class="text_content_block1">
                            <h1 class="main_title_block1" id="immersive-title">Загрузка...</h1>
                            <p class="subtitle_block1" id="immersive-subtitle">Загрузка...</p>
                            <p class="descript_block1" id="immersive-description">Загрузка...</p>
                        </div>
                        <div class="dec_block1">
                            <div class="decors">
                                <p class="km" id="immersive-distance">... км</p>
                                <p class="view">иммерсив</p>
                                <p class="places" id="immersive-duration">... часа</p>
                            </div>
                        </div>
                        <div class="buttons_block1">
                            <p class="but1_block1" id="immersive-price">Стоимость: ... руб/чел.</p>
                            <a href="#" class="but2_block1" id="immersive-listen-preview-button">Послушай
                                отрывок</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block2">
                <div class="container">
                    <h1 class="main_title_block2">О чём прогулка?</h1>
                    <div class="block2_content">
                        <div class="text_content_block2" id="immersive-about-content">
                            <!-- dynamic (AJAX) -->
                        </div>
                        <div class="audio_content" id="immersive-audio-demo"
                            style="margin-left: auto; display: none;">
                            <div class="audio_preview">
                                <img src="/img/excursion/block2_preview.png" alt="" class="preview"
                                    id="immersive-audio-preview-img">
                            </div>
                            <div class="audio-player">
                                <audio id="immersive-audio" src=""></audio>
                                <div class="audio-player-controls">
                                    <button id="immersive-play-button" class="play-btn">▶</button>
                                    <div class="progress-container">
                                        <input type="range" id="immersive-progress-bar" value="0"
                                            max="100">
                                        <div class="time-display">
                                            <span id="immersive-current-time">0:00</span>
                                            <span id="immersive-duration-time">0:00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="start_point">
                <div class="container">
                    <h1 class="title_start_point">Начальная точка маршрута</h1>
                    <p class="start" id="immersive-start-point">Загрузка...</p>
                </div>
            </div>

            <div class="block3">
                <div class="container">
                    <div class="block3_content">
                        <div class="map_block3">
                            <iframe class="carts" id="immersive-map"
                                src="https://www.google.com/maps/embed?pb=!1m52!1m12!1m3!1d11015.815525605656!2d48.015154514240265!3d46.350459698769676!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m37!3e2!4m5!1s0x41a9057f3cc56263%3A0xf6ef8074ee3596f0!2z0JHRgNCw0YLRgdC60LjQuSDRgdCw0LTQuNC6LCDRg9C70LjRhtCwINCSLiDQotGA0LXQtNC40LDQutC-0LLRgdC60L7Qs9C-LCDQkNGB0YLRgNCw0YXQsNC90Yw!3m2!1d46.350308299999995!2d48.035155499999995!4m5!1s0x41a90f807a376a7d%3A0x6d56563a480ea4d!2z0J_QsNC80Y_RgtC90LjQuiDQki7QmC4g0JvQtdC90LjQvdGDLCDQv9C7LiDQm9C10L3QuNC90LAsIDEwLCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.3477263!2d48.0301391!4m5!1s0x41a90f820ff9430b%3A0xee0f6df05a84b971!2z0JrQuNGA0L7QstGB0LrQuNC5INGALdC9LCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.345650199999994!2d48.022130399999995!4m5!1s0x41a90f7905f3ffb7%3A0x27cc8a2b471532fc!2z0J_QsNC80Y_RgtC90LjQuiDQn9C10YLRgNGDIEk!3m2!1d46.3471579!2d48.015971699999994!4m5!1s0x41a90f7e57c7de0d%3A0x6f3e089a8da43e49!2z0JrRgNC10LzQu9C10LLRgdC60LDRjyDRg9C7LiwgMtCxLCDQkNGB0YLRgNCw0YXQsNC90YwsINCQ0YHRgtGA0LDRhdCw0L3RgdC60LDRjyDQvtCx0LsuLCA0MTQwMDA!3m2!1d46.349669399999996!2d48.0218316!4m5!1s0x41a90583bf5bfc41%3A0x2e45f051cbdd41fe!2z0KjQvtC70Lg!3m2!1d46.3552405!2d48.0326964!5e0!3m2!1sru!2sru!4v1738578131745!5m2!1sru!2sru"
                                allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block4">
                <div class="backgr_block4">
                </div>
                <div class="container">
                    <div class="block4_content">
                        <h1 class="main_title_block4">История шепчет...</h1>
                        <p class="descript1_block4" id="immersive-story1">Астрахань — город, где переплелись эпохи, культуры и судьбы. Здесь каждый камень помнит шепот купцов Великого Шелкового пути, шаги повстанцев Степана Разина и тени забытых легенд. Но настоящие тайны скрыты за фасадами старинных домов, в извилистых улочках, в тишине набережных и в глубинах Волги...  </p>
                        <p class="descript2_block4" id="immersive-story2">Здесь каждый камень — страница старой книги, а ветер с Волги доносит обрывки разговоров, которых не могло быть. Кто-то ищет сокровища, кто-то — легенды, но настоящие тайны открываются только тем, кто готов пройти по городу... *не только глазами*.  </p>
                    </div>
                </div>
            </div>

            <div class="block5">
                <div class="container">
                    <div class="block5_content">
                        <h1 class="main_title_block5">бронируй сейчас!</h1>
                        <div class="category_slide">
                            <div id="booking-dates" class="cont1">
                                <!-- dynamic (AJAX) -->
                            </div>

                            <div class="menu" id="booking-menu" style="display: none;">
                                <div class="content_menu">
                                    <button class="close-btn" onclick="closeBookingMenu()">×</button>
                                    <p class="title_form_main">Проверьте данные</p>
                                    <form id="booking-form">
                                        <input type="hidden" id="immersive_id" name="immersive_id"
                                            value="2">
                                        <input type="hidden" id="date_id" name="date_id">
                                        <input type="hidden" id="time_id" name="time_id">
                                        <input type="hidden" id="total_price_input" name="total_price"
                                            value="0">
                                        <input type="hidden" id="price_per_person" data-price="900">

                                        <label class="label_style" for="booking-name">Имя</label>
                                        <input type="text" id="booking-name" name="name"
                                            class="edit_field">
                                        <div class="invalid-feedback" id="booking-name-error"
                                            style="display: none; color: red;">Пожалуйста, введите имя</div>

                                        <label class="label_style" for="booking-email">E-mail</label>
                                        <input type="email" id="booking-email" name="email"
                                            class="edit_field">
                                        <div class="invalid-feedback" id="booking-email-error"
                                            style="display: none; color: red;">Пожалуйста, введите email</div>

                                        <label class="label_style" for="booking-starting-point">Начальная
                                            точка</label>
                                        <input type="text" id="booking-starting-point" name="starting_point"
                                            class="edit_field" value="Ул. 28 Армии, 6" readonly>
                                        <div class="invalid-feedback" id="booking-starting-point-error"
                                            style="display: none; color: red;"></div>

                                        <label class="label_style" for="booking-date-display">Дата</label>
                                        <input type="text" id="booking-date-display" class="edit_field"
                                            value="1 марта" readonly>
                                        <div class="invalid-feedback" id="booking-date-display-error"
                                            style="display: none; color: red;"></div>

                                        <label class="label_style" for="booking-time-display">Время</label>
                                        <input type="text" id="booking-time-display" class="edit_field"
                                            value="12:00" readonly>
                                        <div class="invalid-feedback" id="booking-time-display-error"
                                            style="display: none; color: red;"></div>

                                        <label class="label_style" for="booking-people-count">Укажите количество
                                            людей</label>
                                        <input type="number" id="booking-people-count" name="people_count"
                                            class="edit_field" value="1" min="1"
                                            onchange="calculateTotal()">
                                        <div class="invalid-feedback" id="booking-people-count-error"
                                            style="display: none; color: red;">Укажите количество человек (минимум 1)
                                        </div>

                                        <div class="payment-details">
                                            <h3 class="payment-title">Платежные данные</h3>

                                            <label class="label_style" for="card-number">Номер карты</label>
                                            <input type="text" id="card-number" name="card_number"
                                                class="edit_field" placeholder="1234 5678 9012 3456"
                                                maxlength="19">
                                            <div class="invalid-feedback" id="card-number-error"
                                                style="display: none; color: red;">Пожалуйста, введите номер карты
                                            </div>

                                            <div class="card-extra-details">
                                                <div class="card-expiry">
                                                    <label class="label_style" for="card-expiry">Срок
                                                        действия</label>
                                                    <input type="text" id="card-expiry" name="card_expiry"
                                                        class="edit_field" placeholder="ММ/ГГ" maxlength="5">
                                                    <div class="invalid-feedback" id="card-expiry-error"
                                                        style="display: none; color: red;">Введите срок действия</div>
                                                </div>
                                                <div class="card-cvv">
                                                    <label class="label_style" for="card-cvv">CVV/CVC</label>
                                                    <input type="password" id="card-cvv" name="card_cvv"
                                                        class="edit_field" placeholder="123" maxlength="3">
                                                    <div class="invalid-feedback" id="card-cvv-error"
                                                        style="display: none; color: red;">Введите код</div>
                                                </div>
                                            </div>
                                        </div>

                                        <p class="total_price_style">Итого: <span id="total_price_display">900
                                                руб.</span></p>

                                        <button type="submit" class="btn_reason">Оплатить</button>
                                    </form>
                                </div>
                            </div>
                            <div class="overlay" id="booking-overlay" onclick="closeBookingMenu()"
                                style="display: none;"></div>
                        </div>

                        <div class="content2" id="booking-times"
                            style="display: flex; flex-wrap: wrap; gap: 10px;">
                            <div id="time-1" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-1', '12:00')">12:00</a>
                            </div>
                            <div id="time-2" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-2', '13:00')">13:00</a>
                            </div>
                            <div id="time-3" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-3', '14:00')">14:00</a>
                            </div>
                            <div id="time-4" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-4', '15:00')">15:00</a>
                            </div>
                            <div id="time-5" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-5', '16:00')">16:00</a>
                            </div>
                            <div id="time-6" class="main_title">
                                <a href="#" class="time_booking"
                                    onclick="selectTime('time-6', '11:00')">11:00</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="block6">
                <div class="container">
                    <div class="block6_content">
                        <h1 class="main_title_block5">Отзывы</h1>
                        <div class="comments" id="immersive-comments">
                            <!-- dynamic (AJAX) -->
                        </div>

                        <div id="immersive-comments-form" style="display: none;">
                            <h1 class="title_block5">Оставь свой отзыв</h1>
                            <textarea id="immersive-comment-content" class="feedback" placeholder="Поделись впечатлениями"></textarea>
                            <a href="#" class="btn_upload" onclick="submitComment()">Опубликовать</a>
                        </div>

                        <div id="immersive-comments-need-booking" style="display: none;"
                            class="comment-info-block">
                            <div class="alert alert-info mt-4">
                                <i class="fas fa-info-circle me-2"></i>
                                Чтобы оставить отзыв, вам необходимо сначала забронировать и посетить этот иммерсивный
                                опыт.
                            </div>
                        </div>

                        <div id="immersive-comments-need-auth" style="display: none;" class="comment-info-block">
                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-user-lock me-2"></i>
                                Чтобы оставить отзыв, пожалуйста, <a href="#"
                                    class="nav-link d-inline p-0 alert-link" data-page="login">войдите в
                                    систему</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page page-news" style="display: none;">
            <a href="#" class="back-to-blog nav-link" data-page="blog">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.42-1.41L7.83 13H20v-2z" />
                </svg>
                Назад к блогу
            </a>
            <div class="container">
                <div class="news-content">
                    <h1 id="news-title">Загрузка...</h1>
                    <div class="news-meta">
                        <span id="news-date">Загрузка...</span>
                    </div>
                    <div class="news-image">
                        <img id="news-image-main" src="" alt="">
                    </div>
                    <div class="news-body" id="news-content">
                        <!-- dynamic (AJAX) -->
                    </div>
                    <div class="news-source" id="news-source" style="display: none;">
                        <a href="#" id="news-source-link" target="_blank" rel="noopener noreferrer"
                            class="source-link">
                            Читать в источнике
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="menu" id="purchase-confirmation-modal" style="display: none;">
            <div class="content_menu">
                <button class="close-btn" onclick="closePurchaseModal()">×</button>
                <p class="menu_title">Подтверждение покупки</p>
                <p>Вы собираетесь приобрести экскурсию:</p>
                <p id="purchase-modal-title" class="modal-excursion-title"></p>
                <p>Стоимость: <span id="purchase-modal-price"></span></p>
                <input type="hidden" id="purchase-modal-excursion-id">

                <div class="payment-details">
                    <h3 class="payment-title">Платежные данные</h3>

                    <label class="label_style" for="purchase-card-number">Номер карты</label>
                    <input type="text" id="purchase-card-number" name="purchase_card_number"
                        class="edit_field" placeholder="1234 5678 9012 3456" maxlength="19">
                    <div class="invalid-feedback" id="purchase-card-number-error"
                        style="display: none; color: red;">Пожалуйста, введите номер карты</div>

                    <div class="card-extra-details">
                        <div class="card-expiry">
                            <label class="label_style" for="purchase-card-expiry">Срок действия</label>
                            <input type="text" id="purchase-card-expiry" name="purchase_card_expiry"
                                class="edit_field" placeholder="ММ/ГГ" maxlength="5">
                            <div class="invalid-feedback" id="purchase-card-expiry-error"
                                style="display: none; color: red;">Введите срок действия</div>
                        </div>
                        <div class="card-cvv">
                            <label class="label_style" for="purchase-card-cvv">CVV/CVC</label>
                            <input type="password" id="purchase-card-cvv" name="purchase_card_cvv"
                                class="edit_field" placeholder="123" maxlength="3">
                            <div class="invalid-feedback" id="purchase-card-cvv-error"
                                style="display: none; color: red;">Введите код</div>
                        </div>
                    </div>
                </div>

                <button class="btn_reason" id="confirm-purchase-button">Подтвердить покупку</button>
                <button class="cancel-btn" onclick="closePurchaseModal()">Отмена</button>
            </div>
        </div>
        <div class="overlay" id="purchase-overlay" onclick="closePurchaseModal()" style="display: none;"></div>

        <style>
            #purchase-confirmation-modal {
                display: none;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: #ffffff;
                padding: 25px 30px;
                box-shadow: 0 5px 25px rgba(0, 0, 0, 0.15);
                z-index: 1000;
                border-radius: 15px;
                width: 90%;
                max-width: 480px;
                max-height: 90vh;
                overflow-y: auto;
                border: 1px solid #e0e0e0;
            }

            #purchase-confirmation-modal .content_menu {
                display: flex;
                flex-direction: column;
            }

            #purchase-confirmation-modal .menu_title {
                font-weight: 600;
                font-size: 22px;
                color: #3752e9;
                margin-bottom: 15px;
                text-align: center;
            }

            #purchase-confirmation-modal p {
                font-size: 15px;
                line-height: 1.5;
                color: #333;
                margin-bottom: 8px;
                text-align: center;
            }

            #purchase-confirmation-modal .modal-excursion-title {
                font-weight: 500;
                color: #000;
                font-size: 17px;
                margin-bottom: 15px;
            }

            #purchase-confirmation-modal #purchase-modal-price {
                font-weight: 600;
                color: #ff6a2b;
            }

            #purchase-confirmation-modal .close-btn {
                position: absolute;
                top: 15px;
                right: 15px;
                background: #e0e0e0;
                color: #555;
                border: none;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                text-align: center;
                cursor: pointer;
                transition: background-color 0.3s, color 0.3s;
            }

            #purchase-confirmation-modal .close-btn:hover {
                background-color: #d1d1d1;
                color: #333;
            }

            #purchase-confirmation-modal .payment-details {
                margin-top: 25px;
                margin-bottom: 25px;
                padding: 20px;
                background-color: #f9f9f9;
                border-radius: 10px;
                border: 1px solid #eee;
            }

            #purchase-confirmation-modal .payment-title {
                margin-top: 0;
                margin-bottom: 20px;
                font-size: 18px;
                color: #3752e9;
                font-weight: 500;
                text-align: left;
            }

            #purchase-confirmation-modal .label_style {
                font-size: 14px;
                color: #555;
                margin-bottom: 5px;
                display: block;
                text-align: left;
            }

            #purchase-confirmation-modal .edit_field {
                width: 100%;
                height: auto;
                padding: 10px 15px;
                font-size: 15px;
                border: 1px solid #ccc;
                border-radius: 8px;
                margin-bottom: 10px;
                box-sizing: border-box;
                transition: border-color 0.3s;
            }

            #purchase-confirmation-modal .edit_field:focus {
                border-color: #3752e9;
                outline: none;
            }

            #purchase-confirmation-modal .card-extra-details {
                display: flex;
                gap: 15px;
                margin-top: 10px;
            }

            #purchase-confirmation-modal .card-expiry,
            #purchase-confirmation-modal .card-cvv {
                flex: 1;
            }

            #purchase-confirmation-modal .invalid-feedback {
                color: #e74c3c;
                font-size: 13px;
                margin-top: -5px;
                margin-bottom: 10px;
                display: block;
                text-align: left;
            }

            #purchase-confirmation-modal .btn_reason,
            #purchase-confirmation-modal .cancel-btn {
                font-weight: 500;
                font-size: 16px;
                padding: 12px 25px;
                border-radius: 8px;
                cursor: pointer;
                transition: all 0.3s ease;
                border: none;
                display: block;
                width: 100%;
                margin-top: 10px;
                box-sizing: border-box;
            }

            #purchase-confirmation-modal .btn_reason {
                background-color: #3752e9;
                color: #ffffff;
            }

            #purchase-confirmation-modal .btn_reason:hover {
                background-color: #2941d1;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            #purchase-confirmation-modal .btn_reason:disabled {
                background-color: #a3b1e8;
                cursor: wait;
                transform: none;
                box-shadow: none;
            }

            #purchase-confirmation-modal .cancel-btn {
                background-color: #f0f0f0;
                color: #555;
                border: 1px solid #dcdcdc;
            }

            #purchase-confirmation-modal .cancel-btn:hover {
                background-color: #e6e6e6;
                border-color: #c8c8c8;
                transform: translateY(-2px);
            }

            @media (max-width: 768px) {
                #purchase-confirmation-modal {
                    width: 95%;
                    padding: 20px;
                }

                #purchase-confirmation-modal .menu_title {
                    font-size: 20px;
                }

                #purchase-confirmation-modal p {
                    font-size: 14px;
                }

                #purchase-confirmation-modal .modal-excursion-title {
                    font-size: 16px;
                }

                #purchase-confirmation-modal .payment-title {
                    font-size: 17px;
                }

                #purchase-confirmation-modal .label_style {
                    font-size: 13px;
                }

                #purchase-confirmation-modal .edit_field {
                    font-size: 14px;
                    padding: 9px 12px;
                }

                #purchase-confirmation-modal .invalid-feedback {
                    font-size: 12px;
                }

                #purchase-confirmation-modal .btn_reason,
                #purchase-confirmation-modal .cancel-btn {
                    font-size: 15px;
                    padding: 10px 20px;
                }
            }

            .news-source {
                margin: 15px 0;
                text-align: center;
            }

            .source-link {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                background: linear-gradient(135deg, #3752E9 0%, #FF6A2B 100%);
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-weight: 500;
                font-size: 14px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(55, 82, 233, 0.3);
            }

            .source-link:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(55, 82, 233, 0.4);
                color: white;
                text-decoration: none;
            }

            .source-link i {
                font-size: 12px;
            }

            @media (max-width: 768px) {
                .source-link {
                    font-size: 13px;
                    padding: 8px 16px;
                }
            }





        </style>

    </div>

    <div class="footer">
        <div class="footer_backgr">
            <img src="/img/footer_backgr.png" alt="" class="footer_backgr_img">
            <img src="/img/welcome_mobile/footer_back_mobile.png" alt="" class="footer_back_mobile">
        </div>
        <div class="marquee_container">
            <div class="marquee">погрузись в историю и атмосферу с нашими уникальными экскурсиями</div>
            <div class="container">
                <div class="footer_content">
                    <div class="foot_block_left">
                        <div class="networks">
                            <a href="https://wa.me/qr/RGVDIBB5P4GGM1"><img src="/img/whatsApp.svg" alt=""
                                    class="network_img"></a>
                            <a href="https://vk.com/club231086233?from=groups"><img src="/img/vk.png" alt="" class="network_img"></a>
                            <a href="https://t.me/gidO_ast"><img src="/img/tg.png" alt="" class="network_img"></a>
                        </div>
                        <div class="logo_foot">
                            <img class="logo" src="/img/logo_footer.png" alt="Логотип">
                        </div>
                    </div>
                    <div class="foot_block_right">
                        <div class="questions">
                            <p class="footer_main_title">По всем вопросам</p>
                            <a href="mailto:gidO@mail.ru" class="mail foot_dec">gidO@mail.ru</a>
                            <a href="tel:+79064558505" class="phone foot_dec">+7 (906) 455-85-05</a>
                        </div>
                        <div class="about_us">
                            <p class="footer_main_title">О нас</p>
                            <a href="{{ route('cond') }}" class="contacts foot_dec">Условия сайта</a>
                            <a href="{{ route('politics') }}" class="politics foot_dec">Политика обработки
                                персональных данных</a>
                            <div class="footer_button">
                                <a href="#" class="button_foot nav-link"
                                    data-page="personalAccount">Оставить комментарий</a>
                                <img src="/img/arrow_footer.png" alt="" class="arrow_foot">
                            </div>
                        </div>
                        <div class="navigation">
                            <p class="footer_main_title">Навигация</p>
                            <div class="foot_nav">
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('welcome') }}"
                                    data-page="welcome">Главная</a>
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('catalogExcursions') }}"
                                    data-page="catalogExcursions">Экскурсии</a>
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('blog') }}" data-page="blog">Блог</a>
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('aboutUs') }}" data-page="aboutUs">О
                                    нас</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.min.js"
            integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <script src="/js/gido-alert.js?v={{ time() }}"></script>
        <script src="/js/user.js?v={{ time() }}"></script>
</body>

</html>
