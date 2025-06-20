@extends('admin.admin')
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <div class="page-header">
                    <div class="page-icon">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <div class="page-info">
                        <h1 class="page-title">Заявки на бронирование</h1>
                        <p class="page-subtitle">Управляйте заявками пользователей на иммерсивные экскурсии</p>
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
                <div class="applications-container">
                    <div id="admin-applications-list">
                        <div id="admin-applications-list-container">
                            <div class="applications-loading">
                                <div class="loading-spinner"></div>
                                <p>Загрузка заявок...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection