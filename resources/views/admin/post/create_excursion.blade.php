@extends('admin.admin')
@section('content')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>


<div id="admin-create-excursion-form" class="admin-form-container">
    <h2 class="admin-form-title"><i class="bi bi-headphones"></i>Добавить экскурсию (Аудиогид)</h2>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-info-circle"></i>Основная информация</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="excursion-title">
                <i class="bi bi-type"></i>Заголовок
            </label>
            <input type="text" class="form-input" id="excursion-title" placeholder="Введите заголовок экскурсии">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-description">
                <i class="bi bi-text-paragraph"></i>Описание
            </label>
            <textarea class="form-textarea" id="excursion-description" placeholder="Введите полное описание экскурсии" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-short-description">
                <i class="bi bi-quote"></i>Цитата
            </label>
            <textarea class="form-textarea" id="excursion-short-description" placeholder="Введите краткую цитату для экскурсии" rows="2"></textarea>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-gear"></i>Параметры экскурсии</h3>
        
        <div class="form-row">
            <div class="form-control-group">
                <label class="form-label" for="excursion-route-length">
                    <i class="bi bi-signpost"></i>Длина маршрута (км)
                </label>
                <input type="number" step="0.1" placeholder="Например: 2.5" id="excursion-route-length" class="form-input number-input">
            </div>

            <div class="form-control-group">
                <label class="form-label" for="excursion-duration">
                    <i class="bi bi-clock"></i>Время прохождения
                </label>
                <input type="text" placeholder="Например: 1 час 30 минут" id="excursion-duration" class="form-input">
            </div>
        </div>

        <div class="form-row">
            <div class="form-control-group">
                <label class="form-label">
                    <i class="bi bi-tag"></i>Вид экскурсии
                </label>
                <input type="text" value="Аудиогид" id="excursion-type-display" class="form-input readonly-input" readonly>
                <input type="hidden" id="excursion-type" value="audio">
            </div>

            <div class="form-control-group">
                <label class="form-label" for="excursion-price">
                    <i class="bi bi-currency-ruble"></i>Цена (руб.)
                </label>
                <input type="number" step="1" placeholder="Цена за аудиогид" id="excursion-price" class="form-input number-input">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-images"></i>Медиафайлы</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="excursion-main-image-input">
                <i class="bi bi-image"></i>Главное фото
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Главное фото" id="excursion-main-image-preview">
                </div>
                <label class="file-upload-btn" for="excursion-main-image-input">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="excursion-main-image-input" style="display: none;" accept="image/*">
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-audio-preview-image-input">
                <i class="bi bi-image"></i>Превью аудиозаписи (Изображение)
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Превью аудио" id="excursion-audio-preview-image">
                </div>
                <label class="file-upload-btn" for="excursion-audio-preview-image-input">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="excursion-audio-preview-image-input" style="display: none;" accept="image/*">
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-audio-demo-input">
                <i class="bi bi-file-earmark-music"></i>Ознакомительный кусок аудио
            </label>
            <div class="audio-upload-container">
                <i class="bi bi-music-note-beamed"></i>
                <div>
                    <input type="file" id="excursion-audio-demo-input" class="form-input" accept="audio/*">
                    <div class="audio-name" id="excursion-audio-demo-name"></div>
                </div>
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-audio-full-input">
                <i class="bi bi-file-earmark-music"></i>Полное аудио
            </label>
            <div class="audio-upload-container">
                <i class="bi bi-music-note-beamed"></i>
                <div>
                    <input type="file" id="excursion-audio-full-input" class="form-input" accept="audio/*">
                    <div class="audio-name" id="excursion-audio-full-name"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-map"></i>Маршрут и география</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="excursion-starting-point">
                <i class="bi bi-geo-alt"></i>Начальная точка маршрута
            </label>
            <div class="input-with-icon">
                <input type="text" placeholder="Например: Лебединое озеро" id="excursion-starting-point" class="form-input">
                <i class="bi bi-geo"></i>
            </div>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="excursion-map-embed">
                <i class="bi bi-map"></i>Маршрут (ссылка на карту)
            </label>
            <div class="input-with-icon">
                <input type="text" placeholder="Вставьте ссылку на маршрут (например, Google Maps, Yandex Карты)" id="excursion-map-embed" class="form-input">
                <i class="bi bi-link-45deg"></i>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-signpost-split"></i>Достопримечательности</h3>
        
        <div class="form-control-group">
            <label class="form-label">
                <i class="bi bi-list-ul"></i>Список достопримечательностей
            </label>
            <div id="sights-list-container">
            </div>
            <button type="button" class="admin-form-button secondary" id="add-sight-btn">
                <i class="bi bi-plus-circle"></i>Добавить достопримечательность
            </button>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-pin-map"></i>Выбор мест</h3>
        
        <div class="form-control-group">
            <div class="places-selection-container">
                <div class="places-selection-title">
                    <i class="bi bi-pin-map"></i>Выбор мест для маршрута
                </div>
                <div id="excursion-places-list">
                    <div class="places-loading">
                        <div class="loading-spinner"></div>
                        <p>Загрузка мест...</p>
                    </div>
                </div>
            </div>
            
            <div class="selected-places-section">
                <div class="selected-places-header">
                    <div class="selected-places-title">
                        <i class="bi bi-list-ol"></i>Выбранные места
                    </div>
                    <div class="selected-places-count">0 мест</div>
                </div>
                <div id="selected-places-list">
                    <div class="empty-selected-places">
                        <i class="bi bi-geo-alt"></i>
                        <h4>Места не выбраны</h4>
                        <p>Выберите места из списка выше, чтобы добавить их в маршрут. Вы можете изменить порядок перетаскиванием.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-buttons-group">
        <button type="button" class="admin-form-button" id="save-excursion-btn">
            <i class="bi bi-save"></i>Сохранить экскурсию
        </button>
        <button type="button" class="admin-form-button tertiary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i>Отменить
        </button>
    </div>
</div>

@endsection