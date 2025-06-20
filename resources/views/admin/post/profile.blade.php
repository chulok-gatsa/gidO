@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-person-circle"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Профиль пользователя</h1>
                        <p class="page-subtitle">Просмотр детальной информации о пользователе и его активности</p>
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
                <div class="profile-container">
                    <div id="admin-user-profile" data-user-id="{{ $userId ?? 'null' }}">
                        <div id="admin-user-profile-container">
                            <div class="profile-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка профиля пользователя...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection