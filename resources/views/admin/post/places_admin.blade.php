@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Управление местами</h1>
                        <p class="page-subtitle">Создавайте и редактируйте интересные места для экскурсий</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.create_place') }}" class="btn-create-news">
                    <i class="bi bi-plus-circle"></i> 
                    <span>Создать место</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="places-container">
                    <div id="admin-places-list">
                        <div id="admin-places-list-container">
                            <div class="places-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка мест...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection