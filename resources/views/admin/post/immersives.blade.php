@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Иммерсивные экскурсии</h1>
                        <p class="page-subtitle">Управляйте групповыми турами и создавайте незабываемые впечатления</p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.create_immersive') }}" class="btn-create-news">
                    <i class="bi bi-plus-circle"></i> 
                    <span>Создать иммерсив</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="immersives-container">
                    <div id="admin-immersives-list">
                        <div id="admin-immersives-list-container">
                            <div class="immersives-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка иммерсивных экскурсий...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection