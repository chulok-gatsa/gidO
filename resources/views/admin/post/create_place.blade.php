@extends('admin.admin')
@section('content')


<div id="admin-create-place-form" class="admin-form-container">
    <h2 class="admin-form-title"><i class="bi bi-geo-alt-fill"></i>Добавить место</h2>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-info-circle"></i>Основная информация</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="place-title">
                <i class="bi bi-type"></i>Заголовок
            </label>
            <input type="text" class="form-input" id="place-title" name="title" placeholder="Введите название места">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="place-short-fact">
                <i class="bi bi-lightbulb"></i>Краткий факт
            </label>
            <textarea class="form-textarea" id="place-short-fact" name="short_description" placeholder="Введите интересный краткий факт о месте" rows="2"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="place-description">
                <i class="bi bi-text-paragraph"></i>Описание
            </label>
            <textarea class="form-textarea" id="place-description" name="description" placeholder="Введите полное описание места" rows="4"></textarea>
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-images"></i>Медиафайлы</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="place-image-input">
                <i class="bi bi-image"></i>Фото
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Фото места" id="place-image-preview">
                </div>
                <label class="file-upload-btn" for="place-image-input">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="place-image-input" name="image" style="display: none;" accept="image/*">
            </div>
        </div>
    </div>

    <div class="form-buttons-group">
        <button type="button" class="admin-form-button" id="save-place-btn">
            <i class="bi bi-save"></i>Создать место
        </button>
        <button type="button" class="admin-form-button tertiary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i>Отменить
        </button>
    </div>
</div>

@endsection