<nav class="mt-2">
    <ul  class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">


        <li style=" color:#fff;" class="nav-header">Админ панель</li>
        <li class="nav-item">
            <a href="{{route('admin.index')}}" class="nav-link">
                <i class="nav-icon bi bi-newspaper" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                  Новости
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.create_news')}}" class="nav-link">
                <i class="nav-icon bi bi-file-earmark-plus" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Добавить новости
                </p>
            </a>
        </li>   
         <li class="nav-item">
            <a href="{{route('admin.excursions_admin')}}" class="nav-link">
                <i class="nav-icon bi bi-person-walking" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Экскурсии
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.create_excursion')}}" class="nav-link">
                <i class="nav-icon bi bi-folder-plus" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Добавить экскурсию
                </p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.places_admin')}}" class="nav-link">
                <i class="nav-icon bi bi-bookmark" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                   Места
                </p>

            </a>
        </li>

        <li class="nav-item">
            <a href="{{route('admin.create_place')}}" class="nav-link">
                <i class="nav-icon bi bi-bookmark-plus" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Добавить место
                </p>

            </a>
        </li>

        <li class="nav-item">
            <a href="{{route('admin.applications')}}" class="nav-link">
                <i class="nav-icon bi bi-journal-bookmark" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Заявки
                </p>

            </a>
        </li>


        <li class="nav-item">
            <a href="{{route('admin.immersives')}}" class="nav-link">
                <i class="nav-icon bi bi-calendar-week" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Иммерсивы
                </p>

            </a>
        </li>

        <li class="nav-item">
            <a href="{{route('admin.create_immersive')}}" class="nav-link">
                <i class="nav-icon bi bi-calendar2-plus" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Добавить иммерсив
                </p>

            </a>
        </li>
        <li class="nav-item">
            <a href="{{route('admin.comments')}}" class="nav-link">
                <i class="nav-icon bi bi-chat-left-dots" style="font-size: 18px; color:#fff;"></i>
                <p style="font-size: 12px; color:#fff;">
                    Отзывы
                </p>

            </a>
        </li>
