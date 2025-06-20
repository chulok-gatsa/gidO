@extends('admin.admin')
@section('content')


<div id="admin-create-news-form" class="admin-form-container">
    <h2 class="admin-form-title"><i class="bi bi-newspaper"></i>Создать новость</h2>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-info-circle"></i>Основная информация</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="news-title">
                <i class="bi bi-type"></i>Заголовок
            </label>
            <input type="text" class="form-input" id="news-title" name="title" placeholder="Заголовок">
        </div>

        <div class="form-control-group">
            <label class="form-label" for="news-description">
                <i class="bi bi-text-paragraph"></i>Описание
            </label>
            <textarea class="form-textarea" id="news-description" name="description" placeholder="Описание" rows="4"></textarea>
        </div>

        <div class="form-control-group">
            <label class="form-label" for="news-url">
                <i class="bi bi-link-45deg"></i>Ссылка на новость
            </label>
            <input type="text" class="form-input" id="news-url" name="url" placeholder="Ссылка на новость (необязательно)">
        </div>
    </div>

    <div class="form-section">
        <h3 class="form-section-title"><i class="bi bi-images"></i>Медиафайлы</h3>
        
        <div class="form-control-group">
            <label class="form-label" for="news-image-input">
                <i class="bi bi-image"></i>Главное фото
            </label>
            <div class="file-upload-container">
                <div class="file-upload-preview">
                    <img src="/img/welcome/add_photo.png" alt="Предпросмотр фото" id="news-image-preview">
                </div>
                <label for="news-image-input" class="file-upload-btn">
                    <i class="bi bi-upload"></i>Выбрать изображение
                </label>
                <input type="file" id="news-image-input" name="main_image" style="display: none;" accept="image/*">
            </div>
        </div>
    </div>

    <div class="form-buttons-group">
        <button type="button" class="admin-form-button" id="save-news-btn">
            <i class="bi bi-save"></i>Создать новость
        </button>
        <button type="button" class="admin-form-button tertiary" onclick="window.history.back()">
            <i class="bi bi-arrow-left"></i>Отменить
        </button>
    </div>
</div>

@endsection