@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-chat-left-text"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Модерация отзывов</h1>
                        <p class="page-subtitle">Управляйте отзывами пользователей и модерируйте контент</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="comments-container">
                    <div id="admin-comments-list">
                        <div id="admin-comments-list-container">
                            <div class="comments-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка отзывов...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection