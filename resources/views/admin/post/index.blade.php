@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-newspaper"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Управление новостями</h1>
                        <p class="page-subtitle">Создавайте и редактируйте новости для вашего сайта</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.create_news') }}" class="btn-create-news">
                    <i class="bi bi-plus-circle"></i> 
                    <span>Создать новость</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="news-container">
                    <div id="admin-news-list">
                        <div id="admin-news-list-container">
                            <div class="news-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка новостей...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection