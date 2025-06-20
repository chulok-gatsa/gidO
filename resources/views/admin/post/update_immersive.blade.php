@extends('admin.admin')
@section('content')

<div id="admin-update-immersive-form" class="admin-form-container" data-excursion-id="{{ $excursionId ?? 'null' }}" data-immersive-id="{{ $immersiveId ?? 'null' }}">
    <h2 class="admin-form-title"><i class="bi bi-people-fill"></i>Редактировать иммерсив</h2>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-info-circle"></i>Основные данные иммерсива</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="immersive-title">
                <i class="bi bi-type"></i>Заголовок
            </label>
            <input type="text" class="form-input" id="immersive-title" placeholder="Заголовок">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-excursion-description">
                <i class="bi bi-text-paragraph"></i>Описание экскурсии
            </label>
            <textarea class="form-textarea" id="immersive-excursion-description" placeholder="Описание" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-excursion-short-description">
                <i class="bi bi-quote"></i>Цитата экскурсии
            </label>
            <textarea class="form-textarea" id="immersive-excursion-short-description" placeholder="Цитата" rows="2"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-route-length">
                <i class="bi bi-signpost"></i>Длина маршрута (км)
            </label>
            <input type="number" step="0.1" placeholder="км" id="immersive-route-length" class="form-input number-input">
        </div>

        <div class="form-control-group">
            <label class="form-label">
                <i class="bi bi-tag"></i>Вид экскурсии
            </label>
            <input type="text" value="Иммерсив" class="form-input" readonly>
            <input type="hidden" id="immersive-type" value="immersive">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-duration">
                <i class="bi bi-clock"></i>Время прохождения
            </label>
            <input type="text" class="form-input" placeholder="Время" id="immersive-duration">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-starting-point">
                <i class="bi bi-geo-alt"></i>Начальная точка маршрута
            </label>
            <input type="text" class="form-input" placeholder="Начало" id="immersive-starting-point">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-map-embed">
                <i class="bi bi-map"></i>Маршрут (ссылка)
            </label>
            <input type="text" class="form-input" placeholder="Ссылка на карту" id="immersive-map-embed">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-main-image-input">
                <i class="bi bi-image"></i>Главное фото
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Главное фото" id="immersive-main-image-preview">
                </div>
                <label class="file-upload-btn" for="immersive-main-image-input">
                    <i class="bi bi-upload"></i>Изменить фото
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
                    <i class="bi bi-upload"></i>Изменить аудио файл
                </label>
                <input type="file" id="immersive-audio-demo-input" style="display: none;" accept="audio/*">
                <div class="audio-name" id="immersive-audio-demo-name" style="color: #666; font-size: 0.9rem;">Нет файла</div>
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
                    <i class="bi bi-upload"></i>Изменить превью
                </label>
                <input type="file" id="immersive-audio-preview-input" style="display: none;" accept="image/*">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-pencil-square"></i>Дополнительные данные иммерсива</h3>

        <div class="form-control-group">
            <label class="form-label" for="immersive-price">
                <i class="bi bi-currency-ruble"></i>Стоимость за человека (руб.)
            </label>
            <input type="number" step="1" min="0" placeholder="Например: 1000" id="immersive-price" class="form-input price-input">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-main-description">
                <i class="bi bi-file-text"></i>Главное описание иммерсива
            </label>
            <textarea class="form-textarea" id="immersive-main-description" placeholder="Описание для страницы иммерсива" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="immersive-additional-description">
                <i class="bi bi-file-earmark-text"></i>Дополнительное описание иммерсива
            </label>
            <textarea class="form-textarea" id="immersive-additional-description" placeholder="Дополнительное описание" rows="4"></textarea>
        </div>
    </div>

    <div class="form-section">
        <div class="form-control-group">
            <label class="form-label">
                <i class="bi bi-calendar-date"></i>Существующие даты и время
            </label>
            <div id="immersive-existing-dates-times" class="dates-times-container">
                <div class="loading-indicator">
                    <i class="bi bi-arrow-repeat"></i> Загрузка существующих дат...
                </div>
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label">
                <i class="bi bi-calendar-plus"></i>Добавить новую дату и время:
            </label>
            <div id="immersive-new-dates-times-container">
            </div>
            <button type="button" id="add-new-datetime-btn" class="admin-form-button secondary">
                <i class="bi bi-plus-circle"></i>Добавить дату и время
            </button>
        </div>
    </div>

    <div class="form-buttons-group">
        <button type="button" class="admin-form-button" id="update-immersive-btn">
            <i class="bi bi-save"></i>Обновить иммерсив
        </button>
        <button type="button" class="admin-form-button tertiary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i>Отменить
        </button>
    </div>
</div>

@endsection 