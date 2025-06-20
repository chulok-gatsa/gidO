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
                <h1 class="pol_title">Условия сайта</h1>

                <h3 class="pol_title2">Перед использованием сервиса ознакомьтесь с нашими правилами. Они помогут вам
                    комфортно и безопасно взаимодействовать с платформой, а также избежать возможных ограничений. </h3>

                <div class="politics-content">
                    <h2>1. Общие положения</h2>
                    <p>1.1. Наш сайт предоставляет доступ к аудиогидам, текстовым материалам, картам маршрутов и другим
                        иммерсивным материалам для самостоятельного изучения. </p>
                    <p>1.2. Используя сайт, вы соглашаетесь с настоящими правилами и обязуетесь их соблюдать. </p>
                    <p>1.3. Администрация оставляет за собой право изменять правила без предварительного уведомления.
                    </p>


                    <h2>2. Условия использования </h2>
                    <p>2.1. Регистрация и аккаунт</p>
                    <ul>
                        <li>Для доступа к некоторым функциям сайта может потребоваться регистрация.</li>
                        <li>Вы обязаны предоставлять достоверные данные при создании аккаунта. </li>
                        <li>Запрещено передавать свой аккаунт третьим лицам.</li>
                    </ul>
                    <p>2.2. Доступ к контенту </p>
                    <ul>
                        <li>Аудиогиды и маршруты предоставляются для личного некоммерческого использования.</li>
                        <li>Запрещено копировать, распространять или продавать контент без согласия правообладателей.
                        </li>
                        <li>Некоторые материалы могут быть платными. Оплачивая их, вы получаете доступ в соответствии с
                            условиями подписки. </li>
                    </ul>
                    <p>2.3. Поведение пользователей</p>
                    <ul>
                        <li>Запрещено размещать спам, рекламу или вредоносные ссылки.</li>
                        <li>Запрещено оскорблять других пользователей или администрацию.</li>
                        <li>Запрещено использовать сайт для мошенничества или распространения незаконного контента.
                        </li>
                        <li>Комментарии и отзывы должны быть корректными и соответствовать тематике.</li>
                    </ul>

                    <h2>3. Безопасность и конфиденциальность</h2>
                    <p>3.1. Мы собираем только необходимые данные для работы сервиса.
                    </p>
                    <p>
                        3.2. Мы не передаем ваши данные третьим лицам без вашего согласия, за исключением случаев,
                        предусмотренных законом.
                    </p>
                    <p>3.3. Используйте сложные пароли и не сообщайте их посторонним. </p>


                    <h2>4. Ответственность и ограничения</h2>
                    <p>4.1. Администрация не несет ответственности за: </p>
                    <ul>
                        <li> Неточности в описании маршрутов или изменения в реальных локациях. </li>
                        <li> Неправильное использование контента пользователями. </li>
                        <li> Технические сбои, вызванные действиями третьих лиц. </li>
                    </ul>

                    <p>4.2. При использовании аудиогидов соблюдайте правила дорожного движения и личную безопасность.
                    </p>

                    <h2>Спасибо, что выбрали наш сервис! Приятных прогулок! </h2>

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