@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-map"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Управление экскурсиями</h1>
                        <p class="page-subtitle">Создавайте аудиогиды и иммерсивные экскурсии для незабываемых путешествий</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="action-buttons">
                    <a href="{{ route('admin.create_excursion') }}" class="btn-create-news">
                        <i class="bi bi-headphones"></i> 
                        <span>Создать аудиогид</span>
                    </a>
                    <a href="{{ route('admin.create_immersive') }}" class="btn-create-immersive">
                        <i class="bi bi-people-fill"></i> 
                        <span>Создать иммерсив</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="excursions-container">
                    <div id="admin-excursions-list">
                        <div id="admin-excursions-list-container">
                            <div class="excursions-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка экскурсий...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof loadExcursionsList === 'function') {
        loadExcursionsList();
    }
});
</script>

@endsection