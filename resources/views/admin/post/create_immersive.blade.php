@extends('admin.admin')
@section('content')


<div id="admin-create-immersive-form" class="admin-form-container">
    <h2 class="admin-form-title"><i class="bi bi-people-fill"></i>Добавить Иммерсив</h2>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-info-circle"></i>Основная информация</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="immersive-title">
                <i class="bi bi-type"></i>Заголовок
            </label>
            <input type="text" class="form-input" id="immersive-title" placeholder="Введите заголовок иммерсива">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-description">
                <i class="bi bi-text-paragraph"></i>Описание
            </label>
            <textarea class="form-textarea" id="immersive-description" placeholder="Введите полное описание иммерсива" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-short-description">
                <i class="bi bi-quote"></i>Цитата
            </label>
            <textarea class="form-textarea" id="immersive-short-description" placeholder="Введите краткую цитату для иммерсива" rows="2"></textarea>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-gear"></i>Параметры иммерсива</h3>
        
        <div class="form-row">
            <div class="form-control-group">
                <label class="form-label" for="immersive-route-length">
                    <i class="bi bi-signpost"></i>Длина маршрута (км)
                </label>
                <input type="number" step="0.1" placeholder="Например: 2.5" id="immersive-route-length" class="form-input number-input">
            </div>

            <div class="form-control-group">
                <label class="form-label" for="immersive-duration">
                    <i class="bi bi-clock"></i>Время прохождения
                </label>
                <input type="text" placeholder="Например: 1 час 30 минут" id="immersive-duration" class="form-input">
            </div>
        </div>

        <div class="form-row">
            <div class="form-control-group">
                <label class="form-label">
                    <i class="bi bi-tag"></i>Вид экскурсии
                </label>
                <input type="text" value="Иммерсив" id="immersive-type-display" class="form-input readonly-input" readonly>
                <input type="hidden" id="immersive-type" value="immersive">
            </div>

            <div class="form-control-group">
                <label class="form-label" for="immersive-price">
                    <i class="bi bi-currency-ruble"></i>Стоимость за человека (руб.)
                </label>
                <input type="number" step="1" min="0" placeholder="Например: 1000" id="immersive-price" class="form-input price-input">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-images"></i>Медиафайлы</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="immersive-main-image-input">
                <i class="bi bi-image"></i>Главное фото
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Главное фото" id="immersive-main-image-preview">
                </div>
                <label class="file-upload-btn" for="immersive-main-image-input">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="immersive-main-image-input" style="display: none;" accept="image/*">
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-audio-demo-input">
                <i class="bi bi-music-note"></i>Демо аудиозапись
            </label>
            <div class="file-upload-container">
                <label class="file-upload-btn" for="immersive-audio-demo-input">
                    <i class="bi bi-upload"></i>Выбрать аудио файл
                </label>
                <input type="file" id="immersive-audio-demo-input" style="display: none;" accept="audio/*">
                <div class="form-note">
                    <i class="bi bi-info-circle"></i>Поддерживаемые форматы: MP3, WAV, OGG. Максимальный размер: 10MB
                </div>
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-audio-preview-input">
                <i class="bi bi-image"></i>Превью для аудио
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Превью аудио" id="immersive-audio-preview-image">
                </div>
                <label class="file-upload-btn" for="immersive-audio-preview-input">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="immersive-audio-preview-input" style="display: none;" accept="image/*">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-map"></i>Маршрут и география</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="immersive-starting-point">
                <i class="bi bi-geo-alt"></i>Начальная точка маршрута
            </label>
            <input type="text" placeholder="Например: Лебединое озеро" id="immersive-starting-point" class="form-input">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-map-embed">
                <i class="bi bi-map"></i>Маршрут (ссылка на карту)
            </label>
            <input type="text" placeholder="Вставьте ссылку на маршрут" id="immersive-map-embed" class="form-input">
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-file-text"></i>Дополнительные описания</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="immersive-main-description">
                <i class="bi bi-file-text"></i>Главное описание иммерсива
            </label>
            <textarea class="form-textarea" id="immersive-main-description" placeholder="Основное описание для страницы иммерсива" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-additional-description">
                <i class="bi bi-file-earmark-text"></i>Дополнительное описание иммерсива
            </label>
            <textarea class="form-textarea" id="immersive-additional-description" placeholder="Дополнительное описание для страницы иммерсива" rows="4"></textarea>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-calendar-event"></i>Расписание</h3>
        
        <div class="form-control-group">
            <label class="form-label">
                <i class="bi bi-clock"></i>Выбор даты и времени для бронирования
            </label>
            <div id="immersive-dates-times-container">
            </div>
            <button type="button" id="add-datetime-btn" class="admin-form-button secondary">
                <i class="bi bi-plus-circle"></i>Добавить дату и время
            </button>
        </div>
    </div>

    <div class="form-buttons-group">
        <button type="button" class="admin-form-button" id="save-immersive-btn">
            <i class="bi bi-save"></i>Создать иммерсив
        </button>
        <button type="button" class="admin-form-button tertiary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i>Отменить
        </button>
    </div>
</div>

@endsection