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
</head>

<body>

    <script id="GIDO_BOOKING_DATES" type="application/json">
        @json($immersivesData ?? [])
    </script>

    <div class="container_nav">
        <div class="logo_nav">
            <a href="#" class="nav-link" data-page="welcome"><img class="logo" src="/img/logo.png" alt="Логотип"></a>
        </div>

        <nav class="navigation desktop-nav">
            <ul class="ul_nav">
                @if (isset($isAuthenticated) && $isAuthenticated && isset($user) && $user->is_admin)
                    <li><a class="nav_ani" href="{{ route('admin.index') }}">Админ панель</a></li>
                @endif
                <li><a class="nav_ani nav-link" href="{{ route('welcome') }}" data-page="welcome">Главная</a></li>
                <li><a class="nav_ani nav-link" href="{{ route('catalogExcursions') }}"
                        data-page="catalogExcursions">Экскурсии</a></li>
                <li><a class="nav_ani nav-link" href="{{ route('blog') }}" data-page="blog">Блог</a></li>
                <li><a class="nav_ani nav-link" href="{{ route('aboutUs') }}" data-page="aboutUs">О нас</a></li>
            </ul>
        </nav>

        @if (isset($isAuthenticated) && $isAuthenticated)
            <div class="auth-controls desktop-auth">
                <a class="reg nav-link" href="{{ route('personalAccount') }}" data-page="personalAccount">Личный
                    кабинет</a>
                <a href="{{ route('logout') }}" class="logout-button" title="Выйти">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                </a>
            </div>
        @else
            <a class="reg nav-link desktop-auth" href="{{ route('login') }}" data-page="login">Войти в аккаунт</a>
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
    <div class="page page-politics">
        <div class="container">
            <div class="block1_pol">
                <h1 class="pol_title">Политика обработки персональных данных и конфиденциальность</h1>
                <h3 class="pol_title2">Ваша конфиденциальность важна для нас. В этом документе описано, какие данные мы
                    собираем, как их используем и защищаем. Используя наш сайт, вы соглашаетесь с условиями этой
                    политики. </h3>
                <div class="politics-content">
                    <h2>1. Общие положения</h2>
                    <p>Настоящая политика обработки персональных данных составлена в соответствии с требованиями
                        Федерального закона от 27.07.2006 №152-ФЗ «О персональных данных» и определяет порядок обработки
                        персональных данных и меры по обеспечению безопасности персональных данных, предпринимаемые ГидО
                        (далее – Оператор).</p>

                    <h2>2. Основные понятия</h2>
                    <p>Автоматизированная обработка персональных данных – обработка персональных данных с помощью
                        средств вычислительной техники;</p>
                    <p>Блокирование персональных данных – временное прекращение обработки персональных данных (за
                        исключением случаев, если обработка необходима для уточнения персональных данных);</p>

                    <h2>3. Права субъекта персональных данных</h2>
                    <p>Субъект персональных данных имеет право:</p>
                    <ul>
                        <li>Получать информацию, касающуюся обработки его персональных данных</li>
                        <li>Требовать уточнения своих персональных данных, их блокирования или уничтожения</li>
                        <li>Отозвать свое согласие на обработку персональных данных</li>
                    </ul>


                    <h2>4. Какие данные мы собираем</h2>
                    <p>4.1. Личные данные (предоставляются вами добровольно): </p>
                    <ul>
                        <li> Имя, email, номер телефона (при регистрации или оформлении заказа). </li>
                        <li> Данные платежных систем (если совершаете покупку, но мы не храним данные карт). </li>
                        <li>Аватар, никнейм (если есть профиль). </li>
                    </ul>
                    <p>Блокирование персональных данных – временное прекращение обработки персональных данных (за
                        исключением случаев, если обработка необходима для уточнения персональных данных);</p>
                    <p>4.2. Технические данные (собираются автоматически): </p>
                    <ul>
                        <li> Тип устройства, браузер. </li>
                        <li> Cookies и аналогичные технологии (для работы сайта и аналитики). </li>
                        <li> История посещений и предпочтения (для улучшения сервиса). </li>
                    </ul>
                    <p>4.3. Дополнительные данные (если участвуете в активностях): </p>
                    <ul>
                        <li>Отзывы, комментарии, оценки маршрутов. </li>
                    </ul>

                    <h2>5. Как мы используем ваши данные</h2>

                    <p>5.1. Для работы сервиса: </p>
                    <ul>
                        <li> Регистрация и авторизация. </li>
                        <li> Обработка платежей и доступ к платному контенту. </li>
                        <li> Техническая поддержка и уведомления. </li>
                    </ul>

                    <p>5.2. Для улучшения сервиса: </p>
                    <ul>
                        <li>Анализ поведения пользователей (анонимно). </li>
                        <li>Персонализация рекомендаций (например, подбор маршрутов). </li>
                    </ul>

                    <p> 5.3. Хранение: </p>
                    <ul>
                        <li> Данные хранятся до тех пор, пока это необходимо для работы сервиса. </li>
                        <li> Вы можете удалить аккаунт или запросить удаление данных </li>
                    </ul>


                    <h2> 6. Изменения в политике </h2>
                    <p>6.1. Мы можем обновлять политику. Актуальная версия всегда будет на этой странице. </p>
                    <p>6.2. При значительных изменениях уведомим пользователей. </p>

                    <h2>7. Заключительные положения</h2>
                    <p>Пользователь может получить любые разъяснения по интересующим вопросам, касающимся обработки его
                        персональных данных, обратившись к Оператору с помощью электронной почты gidO@mail.ru.</p>
                </div>
            </div>
        </div>
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
                            <a href="#"><img src="/img/whatsApp.svg" alt="" class="network_img"></a>
                            <a href="#"><img src="/img/vk.png" alt="" class="network_img"></a>
                            <a href="#"><img src="/img/tg.png" alt="" class="network_img"></a>
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
                            <a href="#" class="contacts foot_dec">Условия сайта</a>
                            <a href="{{ route('politics') }}" class="politics foot_dec">Политика обработки
                                персональных данных</a>
                            <div class="footer_button">
                                <a href="{{ route('personalAccount') }}" class="button_foot nav-link"
                                    data-page="personalAccount">Оставить
                                    комментарий</a>
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
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('blog') }}"
                                    data-page="blog">Блог</a>
                                <a class="nav_ani_foot foot_dec nav-link" href="{{ route('aboutUs') }}"
                                    data-page="aboutUs">О
                                    нас</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


</body>

</html>
<style>
    /* politics */
    .page-politics {

        padding: 200px 0;
        background-color: #fff;
        color: #333;
        padding-bottom: 150px !important;
    }

    .block1_pol {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .pol_title {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #3752E9;
        text-align: center;
    }

    .politics-content {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .politics-content h2 {
        font-size: 24px;
        margin: 25px 0 15px;
        color: #333;
    }

    .politics-content p,
    .politics-content li {
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .politics-content ul {
        padding-left: 20px;
    }

    @media (max-width: 768px) {
        .pol_title {
            font-size: 28px;
        }

        .politics-content {
            padding: 20px;
        }

        .politics-content h2 {
            font-size: 20px;
        }
    }
</style>