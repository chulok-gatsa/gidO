document.addEventListener('DOMContentLoaded', function() {
    detectAdminPage();
    initAnimatedIcons();
    
    window.addEventListener('load', function() {
        setTimeout(function() {
            document.querySelector('.preloader').style.opacity = '0';
            setTimeout(function() {
                document.querySelector('.preloader').style.display = 'none';
            }, 500);
        }, 500);
    });
});

function initAnimatedIcons() {
    initMenuIcon();
    initFullscreenIcon();
}

function initMenuIcon() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuIcon = document.querySelector('.menu-icon');
    
    if (menuToggle && menuIcon) {
        let isMenuOpen = false;
        
        const body = document.body;
        if (body.classList.contains('sidebar-collapse')) {
            isMenuOpen = false;
        } else {
            isMenuOpen = true;
        }
        
        updateMenuIcon(isMenuOpen);
        
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            menuIcon.classList.add('pulse');
            setTimeout(() => menuIcon.classList.remove('pulse'), 600);
            
            isMenuOpen = !isMenuOpen;
            updateMenuIcon(isMenuOpen);
            
            setTimeout(() => {
                const currentState = !body.classList.contains('sidebar-collapse');
                if (currentState !== isMenuOpen) {
                    isMenuOpen = currentState;
                    updateMenuIcon(isMenuOpen);
                }
            }, 300);
        });
        
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const newState = !body.classList.contains('sidebar-collapse');
                    if (newState !== isMenuOpen) {
                        isMenuOpen = newState;
                        updateMenuIcon(isMenuOpen);
                    }
                }
            });
        });
        
        observer.observe(body, {
            attributes: true,
            attributeFilter: ['class']
        });
    }
}

function updateMenuIcon(isOpen) {
    const menuIcon = document.querySelector('.menu-icon');
    if (menuIcon) {
        const line1 = menuIcon.querySelector('.line1');
        const line2 = menuIcon.querySelector('.line2');
        const line3 = menuIcon.querySelector('.line3');
        
        if (isOpen) {
            menuIcon.classList.add('active');
            
            setTimeout(() => {
                if (line1) {
                    line1.setAttribute('x1', '6');
                    line1.setAttribute('y1', '6');
                    line1.setAttribute('x2', '18');
                    line1.setAttribute('y2', '18');
                }
                
                if (line3) {
                    line3.setAttribute('x1', '6');
                    line3.setAttribute('y1', '18');
                    line3.setAttribute('x2', '18');
                    line3.setAttribute('y2', '6');
                }
            }, 50);
        } else {
            menuIcon.classList.remove('active');
            
            setTimeout(() => {
                if (line1) {
                    line1.setAttribute('x1', '4');
                    line1.setAttribute('y1', '6');
                    line1.setAttribute('x2', '20');
                    line1.setAttribute('y2', '6');
                }
                
                if (line3) {
                    line3.setAttribute('x1', '4');
                    line3.setAttribute('y1', '18');
                    line3.setAttribute('x2', '20');
                    line3.setAttribute('y2', '18');
                }
            }, 50);
        }
    }
}

function initFullscreenIcon() {
    const fullscreenToggle = document.getElementById('fullscreen-toggle');
    const fullscreenIcon = document.querySelector('.fullscreen-icon');
    
    if (fullscreenToggle && fullscreenIcon) {
        let isFullscreen = false;
        
        isFullscreen = !!(document.fullscreenElement || 
                         document.webkitFullscreenElement || 
                         document.mozFullScreenElement || 
                         document.msFullscreenElement);
        
        updateFullscreenIcon(isFullscreen);
        
        fullscreenToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            fullscreenIcon.classList.add('pulse');
            setTimeout(() => fullscreenIcon.classList.remove('pulse'), 600);
            
            fullscreenIcon.classList.add('active');
            
            setTimeout(() => {
                fullscreenIcon.classList.remove('active');
            }, 600);
            
            setTimeout(() => {
                isFullscreen = !!(document.fullscreenElement || 
                                document.webkitFullscreenElement || 
                                document.mozFullScreenElement || 
                                document.msFullscreenElement);
                updateFullscreenIcon(isFullscreen);
            }, 100);
        });
        
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);
        
        function handleFullscreenChange() {
            isFullscreen = !!(document.fullscreenElement || 
                            document.webkitFullscreenElement || 
                            document.mozFullScreenElement || 
                            document.msFullscreenElement);
            updateFullscreenIcon(isFullscreen);
        }
    }
}

function updateFullscreenIcon(isFullscreen) {
    const fullscreenIcon = document.querySelector('.fullscreen-icon');
    if (fullscreenIcon) {
        const cornerPath = fullscreenIcon.querySelector('.corner');
        if (cornerPath) {
            if (isFullscreen) {
                cornerPath.setAttribute('d', 'M16 8l-4-4-4 4m8 8l-4 4-4-4');
                cornerPath.style.stroke = '#A185E9';
            } else {
                cornerPath.setAttribute('d', 'M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3');
                cornerPath.style.stroke = '#3752E9';
            }
        }
    }
}

function addHoverEffects() {
    const animatedIcons = document.querySelectorAll('.animated-icon');
    
    animatedIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.classList.add('glow');
            this.style.transform = 'scale(1.1) rotate(2deg)';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.classList.remove('glow');
            this.style.transform = 'scale(1) rotate(0deg)';
        });
        
        const parentLink = icon.closest('a');
        if (parentLink) {
            parentLink.addEventListener('focus', function() {
                icon.classList.add('glow');
            });
            
            parentLink.addEventListener('blur', function() {
                icon.classList.remove('glow');
            });
        }
    });
}

setTimeout(addHoverEffects, 100);

let selectedPlacesOrder = [];

function handlePlaceSelection(placeItem, isSelected) {
    const placeId = parseInt(placeItem.getAttribute('data-place-id'));
    const placeName = placeItem.getAttribute('data-place-name');
    const placeDescription = placeItem.getAttribute('data-place-description');
    
    if (isSelected) {
        if (!selectedPlacesOrder.find(place => place.id === placeId)) {
            selectedPlacesOrder.push({
                id: placeId,
                name: placeName,
                description: placeDescription
            });
        }
    } else {
        selectedPlacesOrder = selectedPlacesOrder.filter(place => place.id !== placeId);
    }
    
    updateSelectedPlacesDisplay();
}

function updateSelectedPlacesDisplay() {
    const container = document.getElementById('selected-places-list');
    if (!container) return;
    
    const countElement = document.querySelector('.selected-places-count');
    if (countElement) {
        countElement.textContent = `${selectedPlacesOrder.length} мест`;
    }
    
    if (selectedPlacesOrder.length === 0) {
        container.innerHTML = `
            <div class="empty-selected-places">
                <i class="bi bi-geo-alt"></i>
                <h4>Места не выбраны</h4>
                <p>Выберите места из списка выше, чтобы добавить их в маршрут. Вы можете изменить порядок перетаскиванием.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="selected-places-list" id="sortable-places">';
    selectedPlacesOrder.forEach((place, index) => {
        html += `
            <div class="selected-place-item" data-place-id="${place.id}">
                <div class="place-order-number">${index + 1}</div>
                <div class="selected-place-info">
                    <div class="selected-place-name">${place.name}</div>
                    <div class="selected-place-description">${place.description || 'Описание отсутствует'}</div>
                </div>
                <div class="place-actions">
                    <div class="drag-handle" title="Перетащить для изменения порядка">
                        <i class="bi bi-grip-vertical"></i>
                    </div>
                    <button type="button" class="remove-place-btn" onclick="removePlaceFromSelection(${place.id})" title="Удалить из маршрута">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
    
    initPlacesSortable();
}

function removePlaceFromSelection(placeId) {
    selectedPlacesOrder = selectedPlacesOrder.filter(place => place.id !== placeId);
    
    const checkbox = document.querySelector(`input[value="${placeId}"]`);
    if (checkbox) {
        checkbox.checked = false;
    }
    
    updateSelectedPlacesDisplay();
}

function initPlacesSortable() {
    const container = document.getElementById('sortable-places');
    if (!container) return;
    
    if (typeof Sortable !== 'undefined') {
        Sortable.create(container, {
            animation: 200,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            dragClass: 'sortable-drag',
            handle: '.drag-handle',
            onStart: function(evt) {
                evt.item.classList.add('dragging');
            },
            onEnd: function(evt) {
                evt.item.classList.remove('dragging');
                
                const newOrder = [];
                container.querySelectorAll('.selected-place-item').forEach(item => {
                    const placeId = parseInt(item.getAttribute('data-place-id'));
                    const place = selectedPlacesOrder.find(p => p.id === placeId);
                    if (place) {
                        newOrder.push(place);
                    }
                });
                selectedPlacesOrder = newOrder;
                updatePlaceNumbers();
            }
        });
    }
}

function updatePlaceNumbers() {
    const container = document.getElementById('sortable-places');
    if (!container) return;
    
    container.querySelectorAll('.selected-place-item').forEach((item, index) => {
        const numberElement = item.querySelector('.place-order-number');
        if (numberElement) {
            numberElement.textContent = index + 1;
        }
    });
    
    const countElement = document.querySelector('.selected-places-count');
    if (countElement) {
        countElement.textContent = `${selectedPlacesOrder.length} мест`;
    }
}

function confirmDelete(itemType, itemId, callback) {
    GidoAlert.delete(
        `Удалить ${itemType}?`,
        `Вы действительно хотите удалить эту ${itemType}? Это действие нельзя будет отменить!`
    ).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback(itemId);
        }
    });
}

function confirmUpdate(itemType, itemId, callback) {
    GidoAlert.confirm(
        `Обновить ${itemType}?`,
        `Вы действительно хотите обновить эту ${itemType}?`
    ).then((result) => {
        if (result.isConfirmed && typeof callback === 'function') {
            callback(itemId);
        }
    });
}

function detectAdminPage() {
    if (document.getElementById('admin-news-list')) {
        loadNewsList();
    }
    
    if (document.getElementById('admin-create-news-form')) {
        initCreateNewsForm();
    }
    
    if (document.getElementById('admin-update-news-form')) {
        const newsId = document.getElementById('admin-update-news-form').getAttribute('data-news-id');
        if (newsId && newsId !== 'null') {
            loadNewsDetails(newsId);
            initUpdateNewsForm(newsId);
        }
    }
    
    if (document.getElementById('admin-places-list')) {
        loadPlacesList();
    }
    
    if (document.getElementById('admin-create-place-form')) {
        initCreatePlaceForm();
    }
    
    if (document.getElementById('admin-update-place-form')) {
        const placeId = document.getElementById('admin-update-place-form').getAttribute('data-place-id');
        if (placeId && placeId !== 'null') {
            loadPlaceDetails(placeId);
            initUpdatePlaceForm(placeId);
        }
    }
    
    if (document.getElementById('admin-excursions-list')) {
        loadExcursionsList();
    }
    
    if (document.getElementById('admin-create-excursion-form')) {
        initCreateExcursionForm();
    }
    
    if (document.getElementById('admin-update-excursion-form')) {
        const excursionId = document.getElementById('admin-update-excursion-form').getAttribute('data-excursion-id');
        if (excursionId && excursionId !== 'null') {
            loadExcursionDetails(excursionId);
            loadPlacesForExcursion();
            initUpdateExcursionForm(excursionId);
        }
    }
    
    if (document.getElementById('admin-create-immersive-form')) {
        loadExcursionsForImmersiveSelect();
        initCreateImmersiveForm();
    }
    
    if (document.getElementById('admin-update-immersive-form')) {
        const immersiveId = document.getElementById('admin-update-immersive-form').getAttribute('data-immersive-id');
        const excursionId = document.getElementById('admin-update-immersive-form').getAttribute('data-excursion-id');
        if (immersiveId && immersiveId !== 'null' && excursionId && excursionId !== 'null') {
            loadImmersiveDetails(immersiveId, excursionId);
            initUpdateImmersiveForm(immersiveId);
        }
    }
    
    if (document.getElementById('admin-comments-list')) {
        loadPendingCommentsList();
    }
    
    if (document.getElementById('admin-applications-list')) {
        loadApplicationsList();
    }
    
    if (document.getElementById('admin-user-profile')) {
        const userId = document.getElementById('admin-user-profile').getAttribute('data-user-id');
        if (userId && userId !== 'null') {
            loadUserProfile(userId);
        }
    }

    if (document.getElementById('admin-immersives-list')) {
        loadImmersivesList();
    }
}

function loadPendingCommentsList() {
    const container = document.getElementById('admin-comments-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="comments-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка комментариев...</p>
        </div>
    `;
    
    fetch('/api/admin/comments/pending')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.comments) {
                if (data.comments.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-chat-dots"></i>
                            <h3>Нет ожидающих комментариев</h3>
                            <p>Все комментарии обработаны</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="comments-grid">';
                
                data.comments.forEach((comment, index) => {
                    const createdDate = new Date(comment.created_at).toLocaleDateString('ru-RU');
                    
                    html += `
                        <div class="comment-card" style="animation-delay: ${index * 0.1}s">
                            <div class="comment-header">
                                <div class="author-info">
                                    <div class="author-avatar">
                                        <img src="/img/personalAccount/avatar.png" alt="Аватар автора">
                                    </div>
                                    <div class="author-details">
                                        <h4>${comment.author_name || 'Аноним'}</h4>
                                        <span class="excursion-name">${comment.excursion_title || 'Неизвестная экскурсия'}</span>
                                    </div>
                                </div>
                                <span class="comment-date">
                                    <i class="bi bi-calendar3"></i> ${createdDate}
                                </span>
                            </div>
                            <div class="comment-content">
                                <p>${comment.content}</p>
                            </div>
                            <div class="comment-actions">
                                <button class="btn-action btn-approve" data-comment-id="${comment.id}">
                                    <i class="bi bi-check-circle"></i> Одобрить
                                </button>
                                <button class="btn-action btn-reject" data-comment-id="${comment.id}">
                                    <i class="bi bi-x-circle"></i> Отклонить
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                document.querySelectorAll('.btn-approve').forEach(button => {
                    button.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        approveComment(commentId);
                    });
                });
                
                document.querySelectorAll('.btn-reject').forEach(button => {
                    button.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        rejectComment(commentId);
                    });
                });
                
            } else {
                container.innerHTML = `
                    <div class="error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить комментарии</p>
                        <button class="btn-retry" onclick="loadPendingCommentsList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
            container.innerHTML = `
                <div class="error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить комментарии</p>
                    <button class="btn-retry" onclick="loadPendingCommentsList()">
                        Попробовать снова
                    </button>
                </div>
            `;
        });
}

function approveComment(commentId) {
    GidoAlert.loading('Одобряем комментарий...', 'Пожалуйста, подождите');
    
    fetch(`/api/admin/comments/${commentId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        GidoAlert.close();
        
        if (data.success) {
            loadPendingCommentsList();
            GidoAlert.success('Готово!', 'Комментарий успешно одобрен');
        } else {
            GidoAlert.error('Ошибка', 'Ошибка при одобрении комментария');
        }
    })
    .catch(error => {
        console.error('Error approving comment:', error);
        GidoAlert.close();
        GidoAlert.error('Ошибка сети', 'Ошибка при одобрении комментария');
    });
}

function rejectComment(commentId) {
    GidoAlert.input(
        'Отклонение комментария',
        'textarea',
        {
            inputPlaceholder: 'Введите причину отклонения комментария...',
            confirmButtonText: 'Отклонить комментарий',
            cancelButtonText: 'Отмена',
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Пожалуйста, укажите причину отклонения';
                }
            }
        }
    ).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value.trim();
            
            // Показываем загрузку
            GidoAlert.loading('Отклоняем комментарий...', 'Пожалуйста, подождите');
            
            // Отправляем запрос на отклонение с причиной
            fetch(`/api/admin/comments/${commentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                GidoAlert.close();
                
                if (data.success) {
                    loadPendingCommentsList();
                    GidoAlert.success('Готово!', 'Комментарий успешно отклонен');
                } else {
                    if (data.errors && data.errors.reason) {
                        GidoAlert.error('Ошибка валидации', data.errors.reason[0]);
                    } else {
                        GidoAlert.error('Ошибка', 'Ошибка при отклонении комментария');
                    }
                }
            })
            .catch(error => {
                console.error('Error rejecting comment:', error);
                GidoAlert.close();
                GidoAlert.error('Ошибка сети', 'Ошибка при отклонении комментария');
            });
        }
    });
}


function loadApplicationsList() {
    const container = document.getElementById('admin-applications-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="applications-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка заявок...</p>
        </div>
    `;
    
    fetch('/api/admin/applications')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.applications) {
                if (data.applications.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-clipboard-check"></i>
                            <h3>Нет заявок</h3>
                            <p>Новые заявки на экскурсии появятся здесь</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="applications-grid">';
                
                data.applications.forEach((application, index) => {
                    const createdDate = new Date(application.created_at).toLocaleDateString('ru-RU', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    const createdTime = new Date(application.created_at).toLocaleTimeString('ru-RU', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    const statusClass = application.status === 'approved' ? 'approved' : application.status === 'rejected' ? 'rejected' : 'pending';
                    const statusText = application.status === 'approved' ? 'Одобрена' : application.status === 'rejected' ? 'Отклонена' : 'Ожидает рассмотрения';
                    const statusIcon = application.status === 'approved' ? 'bi-check-circle-fill' : application.status === 'rejected' ? 'bi-x-circle-fill' : 'bi-clock-fill';
                    
                    const avatarSrc = '/img/personalAccount/avatar.png';
                    
                    html += `
                        <div class="application-card" style="animation-delay: ${index * 0.1}s">
                            <div class="application-header">
                                <div class="application-header-content">
                                    <div class="application-title-section">
                                        <h3 class="application-title">${application.excursion_title || 'Неизвестная экскурсия'}</h3>
                                        <p class="application-subtitle">
                                            <i class="bi bi-geo-alt"></i>
                                            Заявка на иммерсивную экскурсию
                                        </p>
                                    </div>
                                    <div class="application-meta">
                                        <span class="application-date">
                                            <i class="bi bi-calendar3"></i> ${createdDate}
                                        </span>
                                        <span class="application-id">
                                            #${application.id}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="application-info">
                                <div class="application-user-card">
                                    <div class="user-info-header">
                                        <div class="user-avatar">
                                            <img src="${avatarSrc}" alt="Аватар пользователя">
                                        </div>
                                        <div class="user-details">
                                            <h4>${application.name || 'Имя не указано'}</h4>
                                            <p>Пользователь: ${application.user_name || 'Неизвестный пользователь'}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="info-grid">
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="bi bi-envelope"></i>
                                                Email
                                            </span>
                                            <span class="info-value">${application.email}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="bi bi-telephone"></i>
                                                Телефон
                                            </span>
                                            <span class="info-value">${application.phone || 'Не указан'}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="bi bi-people"></i>
                                                Количество
                                            </span>
                                            <span class="info-value">${application.people_count} чел.</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="info-label">
                                                <i class="bi bi-clock"></i>
                                                Время подачи
                                            </span>
                                            <span class="info-value">${createdTime}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="application-status-section">
                                    <div class="status-header">
                                        <i class="bi bi-flag"></i>
                                        <span>Статус заявки</span>
                                    </div>
                                    <div class="application-status status-${statusClass}">
                                        <i class="bi ${statusIcon}"></i>
                                        ${statusText}
                                    </div>
                                </div>
                                
                                <div class="application-user-actions">
                                    <a href="/admin/users/${application.user_id}" class="btn-profile-link" title="Перейти в профиль пользователя">
                                        <i class="bi bi-person-circle"></i> 
                                        <span>Профиль пользователя</span>
                                    </a>
                                </div>
                            </div>
                            
                            ${application.status === 'pending' ? `
                                <div class="application-actions">
                                    <button class="btn-action btn-approve" data-application-id="${application.id}">
                                        <i class="bi bi-check-circle-fill"></i> 
                                        <span>Одобрить</span>
                                    </button>
                                    <button class="btn-action btn-reject" data-application-id="${application.id}">
                                        <i class="bi bi-x-circle-fill"></i> 
                                        <span>Отклонить</span>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                setTimeout(() => {
                    document.querySelectorAll('.application-card').forEach((card, index) => {
                        setTimeout(() => {
                            card.classList.add('animate-in');
                        }, index * 100);
                    });
                }, 100);
                
                document.querySelectorAll('.btn-approve').forEach(button => {
                    button.addEventListener('click', function() {
                        const applicationId = this.getAttribute('data-application-id');
                        approveApplication(applicationId);
                    });
                });
                
                document.querySelectorAll('.btn-reject').forEach(button => {
                    button.addEventListener('click', function() {
                        const applicationId = this.getAttribute('data-application-id');
                        rejectApplication(applicationId);
                    });
                });
                
            } else {
                container.innerHTML = `
                    <div class="error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить заявки</p>
                        <button class="btn-retry" onclick="loadApplicationsList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading applications:', error);
            container.innerHTML = `
                <div class="error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить заявки</p>
                    <button class="btn-retry" onclick="loadApplicationsList()">
                        Попробовать снова
                    </button>
                </div>
            `;
        });
}

function approveApplication(applicationId) {
    GidoAlert.loading('Одобряем заявку...', 'Пожалуйста, подождите');
    
    fetch(`/api/admin/applications/${applicationId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        GidoAlert.close();
        
        if (data.success) {
            loadApplicationsList();
            GidoAlert.success('Готово!', 'Заявка успешно одобрена');
        } else {
            GidoAlert.error('Ошибка', 'Ошибка при одобрении заявки');
        }
    })
    .catch(error => {
        GidoAlert.close();
        GidoAlert.error('Ошибка сети', 'Ошибка при одобрении заявки');
    });
}

function rejectApplication(applicationId) {
    GidoAlert.confirm(
        'Отклонить заявку?',
        'Вы действительно хотите отклонить эту заявку?'
    ).then((result) => {
        if (result.isConfirmed) {
            GidoAlert.loading('Отклоняем заявку...', 'Пожалуйста, подождите');
            
            fetch(`/api/admin/applications/${applicationId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                GidoAlert.close();
                
                if (data.success) {
                    loadApplicationsList();
                    GidoAlert.success('Готово!', 'Заявка успешно отклонена');
                } else {
                    GidoAlert.error('Ошибка', 'Ошибка при отклонении заявки');
                }
            })
            .catch(error => {
                console.error('Error rejecting application:', error);
                GidoAlert.close();
                GidoAlert.error('Ошибка сети', 'Ошибка при отклонении заявки');
            });
        }
    });
}


function loadUserProfile(userId) {
    const container = document.getElementById('admin-user-profile-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="profile-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка профиля...</p>
        </div>
    `;
    
    fetch(`/api/admin/users/${userId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.user) {
                const user = data.user;
                const joinDate = new Date(user.created_at).toLocaleDateString('ru-RU');
                
                const bookingsCount = user.bookings ? user.bookings.length : 0;
                const purchasesCount = user.excursion_purchases ? user.excursion_purchases.length : 0;
                const commentsCount = user.comments ? user.comments.length : 0;
                const approvedComments = user.comments ? user.comments.filter(c => c.is_approved).length : 0;
                const rejectedComments = user.comments ? user.comments.filter(c => !c.is_approved && c.rejection_reason).length : 0;
                const pendingComments = user.comments ? user.comments.filter(c => !c.is_approved && !c.rejection_reason).length : 0;
                
                container.innerHTML = `
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <i class="bi bi-person-circle"></i>
                            <h3>Профиль пользователя</h3>
                        </div>
                        <div class="profile-card-body">
                            <div class="profile-info">
                                <div class="profile-avatar">
                                    <img src="/img/personalAccount/avatar.png" alt="Аватар пользователя">
                                </div>
                                <div class="profile-details">
                                    <h4 class="profile-name">${user.name}</h4>
                                    <div class="profile-meta">
                                        <div class="profile-meta-item">
                                            <i class="bi bi-envelope"></i>
                                            <span>${user.email}</span>
                                        </div>
                                        <div class="profile-meta-item">
                                            <i class="bi bi-calendar-plus"></i>
                                            <span>Регистрация: ${joinDate}</span>
                                        </div>
                                        <div class="profile-meta-item">
                                            <i class="bi bi-shield-check"></i>
                                            <span>Роль: ${user.is_admin ? 'Администратор' : 'Пользователь'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="profile-actions">
                                ${!user.is_admin ? `
                                    <button class="btn-profile btn-make-admin" data-user-id="${user.id}">
                                        <i class="bi bi-shield-plus"></i> Сделать админом
                                    </button>
                                ` : `
                                    <button class="btn-profile btn-remove-admin" data-user-id="${user.id}">
                                        <i class="bi bi-shield-minus"></i> Убрать админа
                                    </button>
                                `}
                            </div>
                        </div>
                    </div>
                    
                    <div class="profile-stats-card">
                        <div class="profile-card-header">
                            <i class="bi bi-graph-up"></i>
                            <h3>Статистика активности</h3>
                        </div>
                        <div class="profile-stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${bookingsCount}</div>
                                    <div class="stat-label">Иммерсивных туров</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-headphones"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${purchasesCount}</div>
                                    <div class="stat-label">Аудиоэкскурсий</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-chat-dots"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${commentsCount}</div>
                                    <div class="stat-label">Всего отзывов</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-check-circle text-success"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${approvedComments}</div>
                                    <div class="stat-label">Одобренных</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-x-circle text-danger"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${rejectedComments}</div>
                                    <div class="stat-label">Отклоненных</div>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">
                                    <i class="bi bi-clock text-warning"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number">${pendingComments}</div>
                                    <div class="stat-label">На модерации</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    ${bookingsCount > 0 ? `
                    <div class="profile-section-card">
                        <div class="profile-card-header">
                            <i class="bi bi-people-fill"></i>
                            <h3>Иммерсивные туры (${bookingsCount})</h3>
                        </div>
                        <div class="profile-bookings-list">
                            ${user.bookings.map(booking => {
                                const bookingDate = new Date(booking.created_at).toLocaleDateString('ru-RU');
                                const statusText = booking.is_paid ? 'Оплачено' : 'Не оплачено';
                                const statusClass = booking.is_paid ? 'status-paid' : 'status-unpaid';
                                
                                return `
                                    <div class="booking-item">
                                        <div class="booking-info">
                                            <h4>${booking.immersive.excursion.title}</h4>
                                            <div class="booking-meta">
                                                <span class="booking-date">
                                                    <i class="bi bi-calendar3"></i> ${bookingDate}
                                                </span>
                                                <span class="booking-people">
                                                    <i class="bi bi-people"></i> ${booking.people_count} чел.
                                                </span>
                                                <span class="booking-price">
                                                    <i class="bi bi-cash"></i> ${booking.total_price} ₽
                                                </span>
                                                <span class="booking-status ${statusClass}">
                                                    <i class="bi bi-${booking.is_paid ? 'check-circle' : 'x-circle'}"></i> ${statusText}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${purchasesCount > 0 ? `
                    <div class="profile-section-card">
                        <div class="profile-card-header">
                            <i class="bi bi-headphones"></i>
                            <h3>Аудиоэкскурсии (${purchasesCount})</h3>
                        </div>
                        <div class="profile-purchases-list">
                            ${user.excursion_purchases.map(purchase => {
                                const purchaseDate = new Date(purchase.created_at).toLocaleDateString('ru-RU');
                                const statusText = purchase.is_paid ? 'Оплачено' : 'Не оплачено';
                                const statusClass = purchase.is_paid ? 'status-paid' : 'status-unpaid';
                                
                                return `
                                    <div class="purchase-item">
                                        <div class="purchase-info">
                                            <h4>${purchase.excursion.title}</h4>
                                            <div class="purchase-meta">
                                                <span class="purchase-date">
                                                    <i class="bi bi-calendar3"></i> ${purchaseDate}
                                                </span>
                                                <span class="purchase-price">
                                                    <i class="bi bi-cash"></i> ${purchase.price} ₽
                                                </span>
                                                <span class="purchase-status ${statusClass}">
                                                    <i class="bi bi-${purchase.is_paid ? 'check-circle' : 'x-circle'}"></i> ${statusText}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                    ` : ''}
                    
                    ${commentsCount > 0 ? `
                    <div class="profile-section-card">
                        <div class="profile-card-header">
                            <i class="bi bi-chat-dots"></i>
                            <h3>Отзывы пользователя (${commentsCount})</h3>
                        </div>
                        <div class="profile-comments-list">
                            ${user.comments.map(comment => {
                                const commentDate = new Date(comment.created_at).toLocaleDateString('ru-RU');
                                let statusText, statusClass, statusIcon;
                                
                                if (comment.is_approved) {
                                    statusText = 'Одобрен';
                                    statusClass = 'status-approved';
                                    statusIcon = 'check-circle';
                                } else if (comment.rejection_reason) {
                                    statusText = 'Отклонен';
                                    statusClass = 'status-rejected';
                                    statusIcon = 'x-circle';
                                } else {
                                    statusText = 'На модерации';
                                    statusClass = 'status-pending';
                                    statusIcon = 'clock';
                                }
                                
                                return `
                                    <div class="comment-item">
                                        <div class="comment-header">
                                            <h4>${comment.excursion.title}</h4>
                                            <span class="comment-status ${statusClass}">
                                                <i class="bi bi-${statusIcon}"></i> ${statusText}
                                            </span>
                                        </div>
                                        <div class="comment-content">
                                            <p>${comment.content}</p>
                                        </div>
                                        <div class="comment-meta">
                                            <span class="comment-date">
                                                <i class="bi bi-calendar3"></i> ${commentDate}
                                            </span>
                                            ${comment.rejection_reason ? `
                                                <span class="rejection-reason">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    Причина отклонения: ${comment.rejection_reason}
                                                </span>
                                            ` : ''}
                                        </div>
                                        ${!comment.is_approved && !comment.rejection_reason ? `
                                            <div class="comment-moderation-actions">
                                                <button class="btn-moderate btn-approve-comment" data-comment-id="${comment.id}">
                                                    <i class="bi bi-check-circle"></i> Одобрить
                                                </button>
                                                <button class="btn-moderate btn-reject-comment" data-comment-id="${comment.id}">
                                                    <i class="bi bi-x-circle"></i> Отклонить
                                                </button>
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                    ` : ''}
                `;
                
                const makeAdminBtn = container.querySelector('.btn-make-admin');
                const removeAdminBtn = container.querySelector('.btn-remove-admin');
                
                if (makeAdminBtn) {
                    makeAdminBtn.addEventListener('click', function() {
                        const userId = this.getAttribute('data-user-id');
                        makeUserAdmin(userId);
                    });
                }
                
                if (removeAdminBtn) {
                    removeAdminBtn.addEventListener('click', function() {
                        const userId = this.getAttribute('data-user-id');
                        removeUserAdmin(userId);
                    });
                }
                
                container.querySelectorAll('.btn-approve-comment').forEach(button => {
                    button.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        approveCommentFromProfile(commentId, userId);
                    });
                });
                
                container.querySelectorAll('.btn-reject-comment').forEach(button => {
                    button.addEventListener('click', function() {
                        const commentId = this.getAttribute('data-comment-id');
                        rejectCommentFromProfile(commentId, userId);
                    });
                });
                
            } else {
                container.innerHTML = `
                    <div class="error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить профиль пользователя</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading user profile:', error);
            container.innerHTML = `
                <div class="error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить профиль пользователя</p>
                </div>
            `;
        });
}

function makeUserAdmin(userId) {
    GidoAlert.confirm(
        'Назначить администратором?',
        'Вы действительно хотите назначить этого пользователя администратором?'
    ).then((result) => {
        if (result.isConfirmed) {
            GidoAlert.loading('Назначаем администратором...', 'Пожалуйста, подождите');
            
            fetch(`/api/admin/users/${userId}/make-admin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                GidoAlert.close();
                
                if (data.success) {
                    loadUserProfile(userId);
                    GidoAlert.success('Готово!', 'Пользователь назначен администратором');
                } else {
                    GidoAlert.error('Ошибка', 'Ошибка при назначении администратором');
                }
            })
            .catch(error => {
                console.error('Error making user admin:', error);
                GidoAlert.close();
                GidoAlert.error('Ошибка сети', 'Ошибка при назначении администратором');
            });
        }
    });
}

function removeUserAdmin(userId) {
    GidoAlert.confirm(
        'Убрать права администратора?',
        'Вы действительно хотите убрать права администратора у этого пользователя?'
    ).then((result) => {
        if (result.isConfirmed) {
            GidoAlert.loading('Убираем права администратора...', 'Пожалуйста, подождите');
            
            fetch(`/api/admin/users/${userId}/remove-admin`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                GidoAlert.close();
                
                if (data.success) {
                    loadUserProfile(userId);
                    GidoAlert.success('Готово!', 'Права администратора убраны');
                } else {
                    GidoAlert.error('Ошибка', 'Ошибка при удалении прав администратора');
                }
            })
            .catch(error => {
                console.error('Error removing user admin:', error);
                GidoAlert.close();
                GidoAlert.error('Ошибка сети', 'Ошибка при удалении прав администратора');
            });
        }
    });
}

function approveCommentFromProfile(commentId, userId) {
    GidoAlert.loading('Одобряем комментарий...', 'Пожалуйста, подождите');
    
    fetch(`/api/admin/comments/${commentId}/approve`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        GidoAlert.close();
        
        if (data.success) {
            loadUserProfile(userId);
            GidoAlert.success('Готово!', 'Комментарий одобрен');
        } else {
            GidoAlert.error('Ошибка', 'Ошибка при одобрении комментария');
        }
    })
    .catch(error => {
        console.error('Error approving comment:', error);
        GidoAlert.close();
        GidoAlert.error('Ошибка сети', 'Ошибка при одобрении комментария');
    });
}

function rejectCommentFromProfile(commentId, userId) {
    GidoAlert.input(
        'Отклонение комментария',
        'textarea',
        {
            inputPlaceholder: 'Укажите причину отклонения комментария...',
            confirmButtonText: 'Отклонить комментарий',
            cancelButtonText: 'Отмена',
            inputValidator: (value) => {
                if (!value || value.trim() === '') {
                    return 'Пожалуйста, укажите причину отклонения';
                }
            }
        }
    ).then((result) => {
        if (result.isConfirmed) {
            const reason = result.value.trim();
            
            GidoAlert.loading('Отклоняем комментарий...', 'Пожалуйста, подождите');
            
            fetch(`/api/admin/comments/${commentId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    reason: reason
                })
            })
            .then(response => response.json())
            .then(data => {
                GidoAlert.close();
                
                if (data.success) {
                    loadUserProfile(userId);
                    GidoAlert.success('Готово!', 'Комментарий отклонен');
                } else {
                    if (data.errors && data.errors.reason) {
                        GidoAlert.error('Ошибка валидации', data.errors.reason[0]);
                    } else {
                        GidoAlert.error('Ошибка', 'Ошибка при отклонении комментария');
                    }
                }
            })
            .catch(error => {
                console.error('Error rejecting comment:', error);
                GidoAlert.close();
                GidoAlert.error('Ошибка сети', 'Ошибка при отклонении комментария');
            });
        }
    });
}

function loadNewsList() {
    const container = document.getElementById('admin-news-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="news-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка новостей...</p>
        </div>
    `;
    
    fetch('/api/admin/news')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.news) {
                if (data.news.length === 0) {
                    container.innerHTML = `
                        <div class="news-empty-state">
                            <div class="empty-icon">
                                <i class="bi bi-newspaper"></i>
                            </div>
                            <h3>Новостей пока нет</h3>
                            <p>Создайте первую новость, чтобы начать</p>
                            <a href="/admin/news/create" class="btn-create-first">
                                Создать новость
                            </a>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="news-grid">';
                
                data.news.forEach((newsItem, index) => {
                    const imagePath = newsItem.main_image ? `/storage/${newsItem.main_image}` : '/img/logo.png';
                    const description = newsItem.description || 'Описание отсутствует';
                    const truncatedDescription = description.length > 120 ? description.substring(0, 120) + '...' : description;
                    const createdDate = newsItem.created_at ? new Date(newsItem.created_at).toLocaleDateString('ru-RU') : '';
                    
                    html += `
                        <div class="news-card" style="animation-delay: ${index * 0.1}s">
                            <div class="news-card-image">
                                <img src="${imagePath}" alt="${newsItem.title}" loading="lazy">
                                <div class="news-card-overlay">
                                    <div class="news-actions">
                                        <a href="/admin/news/edit/${newsItem.id}" class="action-btn edit-btn" title="Редактировать">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="action-btn delete-btn delete-news" data-news-id="${newsItem.id}" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="news-card-content">
                                <div class="news-meta">
                                    <span class="news-date">
                                        <i class="bi bi-calendar3"></i> ${createdDate}
                                    </span>
                                    <span class="news-status published">
                                        <i class="bi bi-check-circle"></i> Опубликовано
                                    </span>
                                </div>
                                <h3 class="news-title">${newsItem.title}</h3>
                                <p class="news-description">${truncatedDescription}</p>
                                <div class="news-card-footer">
                                    <a href="/admin/news/edit/${newsItem.id}" class="btn-edit">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                
                container.innerHTML = html;
                
                document.querySelectorAll('.delete-news').forEach(button => {
                    button.addEventListener('click', function() {
                        const newsId = this.getAttribute('data-news-id');
                        deleteNews(newsId);
                    });
                });
                
                setTimeout(() => {
                    document.querySelectorAll('.news-card').forEach(card => {
                        card.classList.add('animate-in');
                    });
                }, 100);
                
            } else {
                container.innerHTML = `
                    <div class="news-error-state">
                        <div class="error-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить новости</p>
                        <button class="btn-retry" onclick="loadNewsList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading news:', error);
            container.innerHTML = `
                <div class="news-error-state">
                    <div class="error-icon">
                        <i class="bi bi-wifi-off"></i>
                    </div>
                    <h3>Ошибка соединения</h3>
                    <p>Проверьте подключение к интернету</p>
                    <button class="btn-retry" onclick="loadNewsList()">
                        Попробовать снова
                    </button>
                </div>
            `;
        });
}

function initCreateNewsForm() {
    const imageInput = document.getElementById('news-image-input');
    const imagePreview = document.getElementById('news-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const saveButton = document.getElementById('save-news-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            createNews();
        });
    }
}

function createNews() {
    const title = document.getElementById('news-title').value;
    const description = document.getElementById('news-description').value;
    const url = document.getElementById('news-url').value;
    const imageInput = document.getElementById('news-image-input');
    
    if (!title || !description || !imageInput.files[0]) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля (заголовок, описание, изображение).');
        return;
    }
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('url', url);
    formData.append('main_image', imageInput.files[0]);
    
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/api/admin/news', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            GidoAlert.success('Готово!', 'Новость успешно создана!').then(() => {
                window.location.href = '/admin/news';
            });
        } else {
            if (data.errors) {
                let errorMsg = 'Произошли следующие ошибки:\n';
                for (const field in data.errors) {
                    errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                }
                GidoAlert.error('Ошибки валидации', errorMsg);
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при создании новости.');
            }
        }
    })
    .catch(error => {
        console.error('Error creating news:', error);
        GidoAlert.error('Ошибка сети', 'Произошла ошибка при создании новости.');
    });
}

function loadNewsDetails(newsId) {
    fetch(`/api/admin/news/${newsId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.news) {
                document.getElementById('news-title').value = data.news.title;
                document.getElementById('news-description').value = data.news.description;
                document.getElementById('news-url').value = data.news.url || '';
                
                if (data.news.main_image) {
                    document.getElementById('news-image-preview').src = `/storage/${data.news.main_image}`;
                }
            } else {
                GidoAlert.error('Ошибка', 'Ошибка загрузки данных новости.');
            }
        })
        .catch(error => {
            console.error('Error loading news details:', error);
            GidoAlert.error('Ошибка сети', 'Ошибка загрузки данных новости.');
        });
}

function initUpdateNewsForm(newsId) {
    const imageInput = document.getElementById('news-image-input');
    const imagePreview = document.getElementById('news-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const updateButton = document.getElementById('update-news-btn');
    if (updateButton) {
        updateButton.addEventListener('click', function() {
            updateNews(newsId);
        });
    }
}

function updateNews(newsId) {
    const title = document.getElementById('news-title').value;
    const description = document.getElementById('news-description').value;
    const url = document.getElementById('news-url').value;
    const imageInput = document.getElementById('news-image-input');
    
    if (!title || !description) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля (заголовок, описание).');
        return;
    }
    
    confirmUpdate('новость', newsId, function() {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        formData.append('url', url);
        
        if (imageInput.files.length > 0) {
            formData.append('main_image', imageInput.files[0]);
        }
        
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        
        fetch(`/api/admin/news/${newsId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Новость успешно обновлена!').then(() => {
                    window.location.href = '/admin/news';
                });
            } else {
                if (data.errors) {
                    let errorMsg = 'Произошли следующие ошибки:\n';
                    for (const field in data.errors) {
                        errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                    }
                    GidoAlert.error('Ошибки валидации', errorMsg);
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при обновлении новости.');
                }
            }
        })
        .catch(error => {
            console.error('Error updating news:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при обновлении новости.');
        });
    });
}

function deleteNews(newsId) {
    confirmDelete('новость', newsId, function(newsId) {
        fetch(`/api/admin/news/${newsId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Новость успешно удалена!');
                loadNewsList();
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при удалении новости.');
            }
        })
        .catch(error => {
            console.error('Error deleting news:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении новости.');
        });
    });
}

function loadPlacesList() {
    const container = document.getElementById('admin-places-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="places-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка мест...</p>
        </div>
    `;
    
    fetch('/api/admin/places')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.places) {
                if (data.places.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-geo-alt-fill"></i>
                            <h3>Нет мест</h3>
                            <p>Создайте первое интересное место для ваших экскурсий</p>
                            <a href="/admin/places/create" class="btn-create-first">
                                Создать первое место
                            </a>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="places-grid">';
                
                data.places.forEach((place, index) => {
                    const imagePath = place.image ? `/storage/${place.image}` : '/img/welcome/add_photo.png';
                    const description = place.short_description || place.description || 'Описание отсутствует';
                    const createdDate = new Date(place.created_at).toLocaleDateString('ru-RU');
                    
                    html += `
                        <div class="place-card" style="animation-delay: ${index * 0.1}s">
                            <div class="card-image-container">
                                <img src="${imagePath}" alt="${place.name}" loading="lazy">
                                <div class="card-type-badge place-badge">
                                    <i class="bi bi-geo-alt-fill"></i> Место
                                </div>
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="/admin/places/edit/${place.id}" class="card-action-btn" title="Редактировать">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="card-action-btn delete-btn delete-place" data-place-id="${place.id}" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-meta">
                                    <div class="card-date">
                                        <i class="bi bi-calendar3"></i>
                                        ${createdDate}
                                    </div>
                                    <div class="card-status status-published">
                                        <i class="bi bi-check-circle"></i>
                                        Активно
                                    </div>
                                </div>
                                <h3 class="card-title">${place.name}</h3>
                                <div class="card-footer">
                                    <a href="/admin/places/edit/${place.id}" class="card-edit-btn">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                setTimeout(() => {
                    document.querySelectorAll('.place-card').forEach(card => {
                        card.classList.add('animate-in');
                    });
                }, 100);
                
                document.querySelectorAll('.delete-place').forEach(button => {
                    button.addEventListener('click', function() {
                        const placeId = this.getAttribute('data-place-id');
                        deletePlace(placeId);
                    });
                });
            } else {
                container.innerHTML = `
                    <div class="news-error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить список мест</p>
                        <button class="btn-retry" onclick="loadPlacesList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading places:', error);
            container.innerHTML = `
                <div class="news-error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить список мест</p>
                    <button class="btn-retry" onclick="loadPlacesList()">
                        Попробовать снова
                    </button>
                </div>
            `;
        });
}

function initCreatePlaceForm() {
    const imageInput = document.getElementById('place-image-input');
    const imagePreview = document.getElementById('place-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const saveButton = document.getElementById('save-place-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            createPlace();
        });
    }
}

function createPlace() {
    const title = document.getElementById('place-title').value;
    const shortFact = document.getElementById('place-short-fact').value;
    const description = document.getElementById('place-description').value;
    const imageInput = document.getElementById('place-image-input');
    
    if (!title || !description) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля (название, описание).');
        return;
    }
    
    const formData = new FormData();
    formData.append('name', title);
    formData.append('description', description);
    formData.append('short_description', shortFact);
    
    if (imageInput.files.length > 0) {
        formData.append('image', imageInput.files[0]);
    }
    
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/api/admin/places', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            GidoAlert.success('Готово!', 'Место успешно создано!').then(() => {
                window.location.href = '/admin/places';
            });
        } else {
            if (data.errors) {
                let errorMsg = 'Произошли следующие ошибки:\n';
                for (const field in data.errors) {
                    errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                }
                GidoAlert.error('Ошибки валидации', errorMsg);
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при создании места.');
            }
        }
    })
    .catch(error => {
        console.error('Error creating place:', error);
        GidoAlert.error('Ошибка сети', 'Произошла ошибка при создании места.');
    });
}

function loadPlaceDetails(placeId) {
    fetch(`/api/admin/places/${placeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.place) {
                document.getElementById('place-title').value = data.place.name;
                document.getElementById('place-short-fact').value = data.place.short_description || '';
                document.getElementById('place-description').value = data.place.description;
                
                if (data.place.image) {
                    document.getElementById('place-image-preview').src = `/storage/${data.place.image}`;
                }
            } else {
                GidoAlert.error('Ошибка', 'Ошибка загрузки данных места.');
            }
        })
        .catch(error => {
            console.error('Error loading place details:', error);
            GidoAlert.error('Ошибка сети', 'Ошибка загрузки данных места.');
        });
}

function initUpdatePlaceForm(placeId) {
    const imageInput = document.getElementById('place-image-input');
    const imagePreview = document.getElementById('place-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const updateButton = document.getElementById('update-place-btn');
    if (updateButton) {
        updateButton.addEventListener('click', function() {
            updatePlace(placeId);
        });
    }
}

function updatePlace(placeId) {
    const title = document.getElementById('place-title').value;
    const shortFact = document.getElementById('place-short-fact').value;
    const description = document.getElementById('place-description').value;
    const imageInput = document.getElementById('place-image-input');
    
    if (!title || !shortFact || !description) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля.');
        return;
    }
    
    confirmUpdate('место', placeId, function() {
        const formData = new FormData();
        formData.append('name', title);
        formData.append('short_description', shortFact);
        formData.append('description', description);
        
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }
        
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        
        fetch(`/api/admin/places/${placeId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Место успешно обновлено!').then(() => {
                    window.location.href = '/admin/places';
                });
            } else {
                if (data.errors) {
                    let errorMsg = 'Произошли следующие ошибки:\n';
                    for (const field in data.errors) {
                        errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                    }
                    GidoAlert.error('Ошибки валидации', errorMsg);
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при обновлении места.');
                }
            }
        })
        .catch(error => {
            console.error('Error updating place:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при обновлении места.');
        });
    });
}

function deletePlace(placeId) {
    confirmDelete('место', placeId, function() {
        fetch(`/api/admin/places/${placeId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Место успешно удалено!');
                loadPlacesList();
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при удалении места.');
            }
        })
        .catch(error => {
            console.error('Error deleting place:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении места.');
        });
    });
}


function loadExcursionsList() {
    const container = document.getElementById('admin-excursions-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="excursions-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка экскурсий...</p>
        </div>
    `;
    
    fetch('/api/admin/excursions')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.excursions) {
                if (data.excursions.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-map"></i>
                            <h3>Нет экскурсий</h3>
                            <p>Создайте свою первую экскурсию - аудиогид или иммерсивный тур</p>
                            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem;">
                                <a href="/admin/excursions/create" class="btn-create-first">
                                    Создать аудиогид
                                </a>
                                <a href="/admin/immersives/create" class="btn-create-immersive">
                                    Создать иммерсив
                                </a>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                let audioHtml = '<div class="category-header"><i class="bi bi-headphones"></i><h2>Аудиоэкскурсии</h2></div><div class="excursions-grid">';
                let immersiveHtml = '<div class="category-header"><i class="bi bi-people-fill"></i><h2>Иммерсивные экскурсии</h2></div><div class="excursions-grid">';
                
                let hasAudio = false;
                let hasImmersive = false;
                let audioIndex = 0;
                let immersiveIndex = 0;
                
                data.excursions.forEach(excursion => {
                    const imagePath = excursion.main_image ? `/storage/${excursion.main_image}` : '/img/welcome/add_photo.png';
                    const createdDate = new Date(excursion.created_at).toLocaleDateString('ru-RU');
                    const description = excursion.description || 'Описание отсутствует';
                    
                    const isAudio = excursion.type === 'audio';
                    const currentIndex = isAudio ? audioIndex++ : immersiveIndex++;
                    
                    const html = `
                        <div class="${isAudio ? 'excursion' : 'immersive'}-card" style="animation-delay: ${currentIndex * 0.1}s">
                            <div class="card-image-container">
                                <img src="${imagePath}" alt="${excursion.title}" loading="lazy">
                                <div class="card-type-badge ${isAudio ? 'excursion' : 'immersive'}-badge">
                                    <i class="bi bi-${isAudio ? 'headphones' : 'people-fill'}"></i> ${isAudio ? 'Аудиогид' : 'Иммерсив'}
                                </div>
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        <a href="${excursion.type === 'immersive' ? `/booking/${excursion.id}` : `/excursion/${excursion.id}`}" target="_blank" class="card-action-btn" title="Просмотр">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="${excursion.type === 'immersive' ? `/admin/immersives/edit/${excursion.id}/${excursion.excursion_id || excursion.id}` : `/admin/excursions/edit/${excursion.id}`}" class="card-action-btn" title="Редактировать">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="card-action-btn delete-btn delete-excursion" data-excursion-id="${excursion.id}" title="Удалить">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-meta">
                                    <div class="card-date">
                                        <i class="bi bi-calendar3"></i>
                                        ${createdDate}
                                    </div>
                                    <div class="card-status status-published">
                                        <i class="bi bi-check-circle"></i>
                                        Активна
                                    </div>
                                </div>
                                <h3 class="card-title">${excursion.title}</h3>
                                <div class="card-footer">
                                    <a href="${excursion.type === 'immersive' ? `/admin/immersives/edit/${excursion.id}/${excursion.excursion_id || excursion.id}` : `/admin/excursions/edit/${excursion.id}`}" class="card-edit-btn">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    if (excursion.type === 'audio') {
                        audioHtml += html;
                        hasAudio = true;
                    } else if (excursion.type === 'immersive') {
                        immersiveHtml += html;
                        hasImmersive = true;
                    }
                });
                
                audioHtml += '</div>';
                immersiveHtml += '</div>';
                
                let finalHtml = '';
                
                if (hasAudio) {
                    finalHtml += audioHtml;
                } else {
                    finalHtml += `
                        <div class="category-header">
                            <i class="bi bi-headphones"></i>
                            <h2>Аудиоэкскурсии</h2>
                        </div>
                        <div class="empty-state">
                            <i class="empty-icon bi bi-music-note"></i>
                            <h3>Нет аудиогидов</h3>
                            <p>Создайте первый аудиогид для самостоятельных экскурсий</p>
                            <a href="/admin/excursions/create" class="btn-create-first">
                                Создать аудиогид
                            </a>
                        </div>
                    `;
                }
                
                if (hasImmersive) {
                    finalHtml += immersiveHtml;
                } else {
                    finalHtml += `
                        <div class="category-header">
                            <i class="bi bi-people-fill"></i>
                            <h2>Иммерсивные экскурсии</h2>
                        </div>
                        <div class="empty-state">
                            <i class="empty-icon bi bi-people"></i>
                            <h3>Нет иммерсивов</h3>
                            <p>Создайте первый иммерсивный тур для групповых экскурсий</p>
                            <a href="/admin/immersives/create" class="btn-create-first">
                                Создать иммерсив
                            </a>
                        </div>
                    `;
                }
                
                container.innerHTML = finalHtml;
                
                setTimeout(() => {
                    document.querySelectorAll('.excursion-card, .immersive-card').forEach(card => {
                        card.classList.add('animate-in');
                    });
                }, 100);
                
                document.querySelectorAll('.delete-excursion').forEach(button => {
                    button.addEventListener('click', function() {
                        const excursionId = this.getAttribute('data-excursion-id');
                        deleteExcursion(excursionId);
                    });
                });
            } else {
                container.innerHTML = `
                    <div class="news-error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить список экскурсий</p>
                        <button class="btn-retry" onclick="loadExcursionsList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading excursions:', error);
            container.innerHTML = `
                <div class="news-error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить список экскурсий</p>
                    <button class="btn-retry" onclick="loadExcursionsList()">
                        Попробовать снова
                    </button>
                </div>
            `;
        });
}

function loadPlacesForExcursion() {
    const container = document.getElementById('excursion-places-list');
    if (!container) return;
    
    fetch('/api/admin/places')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.places) {
                if (data.places.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-geo-alt"></i>
                            <h3>Нет мест</h3>
                            <p>Сначала <a href="/admin/places/create">добавьте места</a> для создания экскурсии</p>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="places-grid">';
                
                data.places.forEach((place, index) => {
                    const description = place.short_description || place.description || 'Описание отсутствует';
                    html += `
                        <div class="place-selection-item" data-place-id="${place.id}" data-place-name="${place.name}" data-place-description="${description}">
                            <div class="place-checkbox-container">
                                <input type="checkbox" class="place-checkbox" value="${place.id}" id="place${place.id}">
                                <div class="place-info">
                                    <div class="place-name">${place.name}</div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                document.querySelectorAll('.place-selection-item').forEach(item => {
                    item.addEventListener('click', function(e) {
                        if (e.target.classList.contains('place-checkbox')) return;
                        
                        const checkbox = this.querySelector('.place-checkbox');
                        checkbox.checked = !checkbox.checked;
                        
                        if (checkbox.checked) {
                            this.classList.add('selected');
                        } else {
                            this.classList.remove('selected');
                        }
                        
                        handlePlaceSelection(this, checkbox.checked);
                    });
                });
                
                document.querySelectorAll('.place-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const placeItem = this.closest('.place-selection-item');
                        
                        if (this.checked) {
                            placeItem.classList.add('selected');
                        } else {
                            placeItem.classList.remove('selected');
                        }
                        
                        handlePlaceSelection(placeItem, this.checked);
                    });
                });
                
                initPlacesSortable();
            } else {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить места</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Проверьте подключение к интернету</p>
                </div>
            `;
        });
}

function initCreateExcursionForm() {
    selectedPlacesOrder = [];
    
    const imageInput = document.getElementById('excursion-main-image-input');
    const imagePreview = document.getElementById('excursion-main-image-preview');
    
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const audioPreviewImageInput = document.getElementById('excursion-audio-preview-image-input');
    const audioPreviewImage = document.getElementById('excursion-audio-preview-image');
    
    if (audioPreviewImageInput && audioPreviewImage) {
        audioPreviewImageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    audioPreviewImage.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const audioDemoInput = document.getElementById('excursion-audio-demo-input');
    const audioDemoName = document.getElementById('excursion-audio-demo-name');
    
    if (audioDemoInput && audioDemoName) {
        audioDemoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                audioDemoName.textContent = this.files[0].name;
            } else {
                audioDemoName.textContent = '';
            }
        });
    }
    
    const audioFullInput = document.getElementById('excursion-audio-full-input');
    const audioFullName = document.getElementById('excursion-audio-full-name');
    
    if (audioFullInput && audioFullName) {
        audioFullInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                audioFullName.textContent = this.files[0].name;
            } else {
                audioFullName.textContent = '';
            }
        });
    }
    
    const saveButton = document.getElementById('save-excursion-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            createExcursion();
        });
    }
    
    loadPlacesForExcursion();
    
    initSightsList();
    
    $('#add-sight-btn').on('click', function() {
        addSightItem();
    });
}

function initSightsList() {
    $('#sights-list-container').empty();
    
    addSightItem();
}

function addSightItem(sightData = null) {
    const container = document.getElementById('sights-list-container');
    if (!container) return;
    
    const sightId = 'sight-' + (document.querySelectorAll('.sight-item').length + 1);
    
    const html = `
        <div class="sight-item" style="margin-bottom: 15px; background-color: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; align-items: center;">
                <i class="bi bi-signpost" style="margin-right: 10px; color: #3752E9;"></i>
                <div class="form-control-group" style="flex-grow: 1; margin-bottom: 0;">
                    <input type="text" id="${sightId}-name" class="form-input sight-name" placeholder="Название достопримечательности" value="${sightData?.name || ''}">
                </div>
                <button type="button" class="remove-sight-btn" style="margin-left: 10px; background: none; border: none; color: #FF6A2B; cursor: pointer;">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    
    const removeBtn = container.querySelector('.sight-item:last-child .remove-sight-btn');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            this.closest('.sight-item').remove();
        });
    }
}

function getSightsListData() {
    const sightsList = [];
    
    $('#sights-list-container .sight-item').each(function(index) {
        const id = $(this).find('.sight-id').val() || (index + 1);
        const name = $(this).find('.sight-name').val();
        
        if (name.trim() !== '') {
            sightsList.push({
                id: id,
                name: name
            });
        }
    });
    
    return sightsList;
}

function createExcursion() {
    const title = document.getElementById('excursion-title').value;
    const description = document.getElementById('excursion-description').value;
    const shortDescription = document.getElementById('excursion-short-description').value;
    const routeLength = document.getElementById('excursion-route-length').value;
    const duration = document.getElementById('excursion-duration').value;
    const startingPoint = document.getElementById('excursion-starting-point').value;
    const mapEmbed = document.getElementById('excursion-map-embed').value;
    const type = document.getElementById('excursion-type').value;
    const price = document.getElementById('excursion-price').value || 0; 
    
    const mainImageInput = document.getElementById('excursion-main-image-input');
    const audioPreviewImageInput = document.getElementById('excursion-audio-preview-image-input');
    const audioDemoInput = document.getElementById('excursion-audio-demo-input');
    const audioFullInput = document.getElementById('excursion-audio-full-input');
    
    const selectedPlaces = selectedPlacesOrder.map(place => place.id);
    
    if (!title || !description || !shortDescription || !routeLength || !duration || !startingPoint || !mapEmbed || !type) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные текстовые поля.');
        return;
    }
    
    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('short_description', shortDescription);
    formData.append('route_length', routeLength);
    formData.append('duration', duration);
    formData.append('starting_point', startingPoint);
    formData.append('map_embed', mapEmbed);
    formData.append('type', type); 
    formData.append('price', price); 
    
    if (mainImageInput.files[0]) {
        formData.append('main_image', mainImageInput.files[0]);
    }
    
    if (audioPreviewImageInput.files[0]) {
        formData.append('audio_preview', audioPreviewImageInput.files[0]);
    }
    
    if (audioDemoInput.files[0]) {
        formData.append('audio_demo', audioDemoInput.files[0]);
    }
    
    if (audioFullInput.files[0]) {
        formData.append('audio_full', audioFullInput.files[0]);
    }
    
    selectedPlaces.forEach((placeId, index) => {
        formData.append(`places[${index}]`, placeId);
    });
    
    const sightsList = getSightsListData();
    
    formData.append('sights_list', JSON.stringify(sightsList));
    
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('/api/admin/excursions', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            GidoAlert.success('Готово!', 'Экскурсия успешно создана!').then(() => {
                window.location.href = '/admin/excursions';
            });
        } else {
            if (data.errors) {
                let errorMsg = 'Произошли следующие ошибки:\n';
                for (const field in data.errors) {
                    errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                }
                GidoAlert.error('Ошибки валидации', errorMsg);
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при создании экскурсии.');
            }
        }
    })
    .catch(error => {
        console.error('Error creating excursion:', error);
        GidoAlert.error('Ошибка сети', 'Произошла ошибка при создании экскурсии.');
    });
}

function deleteExcursion(excursionId) {
    confirmDelete('экскурсию', excursionId, function() {
        fetch(`/api/admin/excursions/${excursionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Экскурсия успешно удалена!');
                loadExcursionsList();
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при удалении экскурсии.');
            }
        })
        .catch(error => {
            console.error('Error deleting excursion:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении экскурсии.');
        });
    });
}

function loadImmersivesList() {
    const container = document.getElementById('admin-immersives-list-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="immersives-loading">
            <div class="loading-spinner"></div>
            <p>Загрузка иммерсивов...</p>
        </div>
    `;
    
    fetch('/api/admin/immersives')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.immersives) {
                if (data.immersives.length === 0) {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="empty-icon bi bi-people-fill"></i>
                            <h3>Нет иммерсивов</h3>
                            <p>Создайте свой первый иммерсивный тур для групповых экскурсий</p>
                            <a href="/admin/immersives/create" class="btn-create-first">
                                Создать первый иммерсив
                            </a>
                        </div>
                    `;
                    return;
                }
                
                let html = '<div class="immersives-grid">';
                
                data.immersives.forEach((immersive, index) => {
                    const imagePath = immersive.excursion && immersive.excursion.main_image 
                        ? `/storage/${immersive.excursion.main_image}` 
                        : '/img/welcome/add_photo.png';
                    
                    const title = immersive.excursion ? immersive.excursion.title : 'Без названия';
                    const description = immersive.excursion ? immersive.excursion.description : 'Описание отсутствует';
                    const excursionId = immersive.excursion ? immersive.excursion.id : '';
                    
                    const datesCount = immersive.dates ? immersive.dates.length : 0;
                    const datesText = datesCount > 0 ? `${datesCount} дат(а/ы)` : 'Нет дат';
                    
                    const createdDate = new Date(immersive.created_at).toLocaleDateString('ru-RU');
                    
                    html += `
                        <div class="immersive-card" style="animation-delay: ${index * 0.1}s">
                            <div class="card-image-container">
                                <img src="${imagePath}" alt="${title}" loading="lazy">
                                <div class="card-type-badge immersive-badge">
                                    <i class="bi bi-people-fill"></i> Иммерсив
                                </div>
                                <div class="card-overlay">
                                    <div class="card-actions">
                                        ${excursionId ? `<a href="/booking/${excursionId}" target="_blank" class="card-action-btn" title="Просмотр"><i class="bi bi-eye"></i></a>` : ''}
                                        <a href="/admin/immersives/edit/${immersive.id}/${excursionId}" class="card-action-btn" title="Редактировать"><i class="bi bi-pencil"></i></a>
                                        <button class="card-action-btn delete-btn delete-immersive" data-immersive-id="${immersive.id}" title="Удалить"><i class="bi bi-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="card-meta">
                                    <div class="card-date">
                                        <i class="bi bi-calendar3"></i>
                                        ${createdDate}
                                    </div>
                                    <div class="card-status status-published">
                                        <i class="bi bi-check-circle"></i>
                                        Активен
                                    </div>
                                </div>
                                <h3 class="card-title">${title}</h3>
                                <div class="card-footer">
                                    <a href="/admin/immersives/edit/${immersive.id}/${excursionId}" class="card-edit-btn">
                                        <i class="bi bi-pencil"></i> Редактировать
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                html += '</div>';
                container.innerHTML = html;
                
                setTimeout(() => {
                    document.querySelectorAll('.immersive-card').forEach(card => {
                        card.classList.add('animate-in');
                    });
                }, 100);
                
                document.querySelectorAll('.delete-immersive').forEach(button => {
                    button.addEventListener('click', function() {
                        const immersiveId = this.getAttribute('data-immersive-id');
                        deleteImmersive(immersiveId);
                    });
                });
            } else {
                container.innerHTML = `
                    <div class="news-error-state">
                        <i class="error-icon bi bi-exclamation-triangle"></i>
                        <h3>Ошибка загрузки</h3>
                        <p>Не удалось загрузить список иммерсивов</p>
                        <button class="btn-retry" onclick="loadImmersivesList()">
                            Попробовать снова
                        </button>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading immersives:', error);
            container.innerHTML = `
                <div class="news-error-state">
                    <i class="error-icon bi bi-exclamation-triangle"></i>
                    <h3>Ошибка загрузки</h3>
                    <p>Не удалось загрузить список иммерсивов</p>
                    <button class="btn-retry" onclick="loadImmersivesList()">
                        <i class="bi bi-arrow-clockwise"></i> Попробовать снова
                    </button>
                </div>
            `;
        });
}

function deleteImmersive(immersiveId) {
    confirmDelete('иммерсив', immersiveId, function() {
        fetch(`/api/admin/immersives/${immersiveId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Иммерсив успешно удален!');
                loadImmersivesList();
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при удалении иммерсива.');
            }
        })
        .catch(error => {
            console.error('Error deleting immersive:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении иммерсива.');
        });
    });
}


function loadExcursionsForImmersiveSelect() {
    const selectElement = document.getElementById('immersive-excursion-select');
    if (!selectElement) return;
    
    fetch('/api/admin/excursions')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.excursions) {
                const audioExcursions = data.excursions.filter(excursion => excursion.type === 'audio');
                
                if (audioExcursions.length === 0) {
                    selectElement.innerHTML = '<option value="">Нет доступных экскурсий</option>';
                    return;
                }
                
                let html = '<option value="">Выберите экскурсию...</option>';
                
                audioExcursions.forEach(excursion => {
                    html += `<option value="${excursion.id}">${excursion.title}</option>`;
                });
                
                selectElement.innerHTML = html;
            } else {
                selectElement.innerHTML = '<option value="">Ошибка загрузки экскурсий</option>';
            }
        })
        .catch(error => {
            console.error('Error loading excursions for select:', error);
            selectElement.innerHTML = '<option value="">Ошибка загрузки экскурсий</option>';
        });
}

function initCreateImmersiveForm() {
    const mainImageInput = document.getElementById('immersive-main-image-input');
    const mainImagePreview = document.getElementById('immersive-main-image-preview');
    
    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const audioPreviewInput = document.getElementById('immersive-audio-preview-input');
    const audioPreviewImage = document.getElementById('immersive-audio-preview-image');
    
    if (audioPreviewInput && audioPreviewImage) {
        audioPreviewInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    audioPreviewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const audioDemoInput = document.getElementById('immersive-audio-demo-input');
    const audioDemoName = document.getElementById('immersive-audio-demo-name');
    
    if (audioDemoInput && audioDemoName) {
        audioDemoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                audioDemoName.textContent = this.files[0].name;
            } else {
                audioDemoName.textContent = 'Нет файла';
            }
        });
    }
    
    const addDateTimeBtn = document.getElementById('add-datetime-btn');
    const datesTimesContainer = document.getElementById('immersive-dates-times-container');
    
    if (addDateTimeBtn && datesTimesContainer) {
        let dateTimeBlockCount = 0;
        
        addDateTimeBtn.addEventListener('click', function() {
            const blockId = Date.now();
            
            const dateTimeBlock = document.createElement('div');
            dateTimeBlock.className = 'datetime-block';
            dateTimeBlock.style.marginBottom = '20px';
            dateTimeBlock.style.padding = '15px';
            dateTimeBlock.style.border = '1px solid #ddd';
            dateTimeBlock.style.borderRadius = '5px';
            dateTimeBlock.style.backgroundColor = '#f9f9f9';
            dateTimeBlock.id = `datetime-block-${blockId}`;
            
            dateTimeBlock.innerHTML = `
                <h4>Дата и время #${++dateTimeBlockCount}</h4>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="flex: 1; margin-right: 10px;">
                        <label for="date-${blockId}">Дата</label>
                        <input type="date" id="date-${blockId}" name="dates[]" class="form-control" style="width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label>Доступное время</label>
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-11-${blockId}" name="times[${blockId}][]" value="11:00">
                                <label for="time-11-${blockId}">11:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-12-${blockId}" name="times[${blockId}][]" value="12:00">
                                <label for="time-12-${blockId}">12:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-13-${blockId}" name="times[${blockId}][]" value="13:00">
                                <label for="time-13-${blockId}">13:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-14-${blockId}" name="times[${blockId}][]" value="14:00">
                                <label for="time-14-${blockId}">14:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-15-${blockId}" name="times[${blockId}][]" value="15:00">
                                <label for="time-15-${blockId}">15:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-16-${blockId}" name="times[${blockId}][]" value="16:00">
                                <label for="time-16-${blockId}">16:00</label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-datetime-btn" data-block-id="${blockId}">Удалить</button>
            `;
            
            datesTimesContainer.appendChild(dateTimeBlock);
            
            dateTimeBlock.querySelector('.remove-datetime-btn').addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                const blockToRemove = document.getElementById(`datetime-block-${blockId}`);
                if (blockToRemove) {
                    blockToRemove.remove();
                    dateTimeBlockCount--;
                }
            });
        });
        
        addDateTimeBtn.click();
    }
    
    const saveButton = document.getElementById('save-immersive-btn');
    if (saveButton) {
        saveButton.addEventListener('click', function() {
            createImmersive();
        });
    }
}

function createImmersive() {
    const title = document.getElementById('immersive-title').value;
    const description = document.getElementById('immersive-description').value;
    const shortDescription = document.getElementById('immersive-short-description').value;
    const routeLength = document.getElementById('immersive-route-length').value;
    const duration = document.getElementById('immersive-duration').value;
    const pricePerPerson = document.getElementById('immersive-price').value;
    const startingPoint = document.getElementById('immersive-starting-point').value;
    const mapEmbed = document.getElementById('immersive-map-embed').value;
    const mainDescription = document.getElementById('immersive-main-description').value;
    const additionalDescription = document.getElementById('immersive-additional-description').value;
    const mainImageInput = document.getElementById('immersive-main-image-input');

    if (!title || !description || !routeLength || !duration || !pricePerPerson || !mainDescription || !additionalDescription) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля.');
        return;
    }

    const dateTimesData = [];
    let isValid = true;
    const dateTimeBlocks = document.querySelectorAll('#immersive-dates-times-container .datetime-block');

    if (dateTimeBlocks.length === 0) {
        GidoAlert.warning('Добавьте даты', 'Пожалуйста, добавьте хотя бы одну дату и время.');
        return;
    }

    dateTimeBlocks.forEach(block => {
        const dateInput = block.querySelector('input[name="dates[]"]');
        const date = dateInput.value;
        const blockId = block.id.split('-').pop();
        const timeCheckboxes = block.querySelectorAll(`input[name="times[${blockId}][]"]:checked`);
        const timesForDate = [];

        if (!date) {
            GidoAlert.warning('Выберите дату', 'Пожалуйста, выберите дату для всех блоков.');
            isValid = false;
            return;
        }

        if (timeCheckboxes.length === 0) {
            GidoAlert.warning('Выберите время', `Пожалуйста, выберите хотя бы одно время для даты ${date}.`);
            isValid = false;
            return;
        }

        timeCheckboxes.forEach(checkbox => {
            timesForDate.push(checkbox.value);
        });

        dateTimesData.push({ date: date, times: timesForDate });
    });

    if (!isValid) return; 

    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('short_description', shortDescription);
    formData.append('route_length', routeLength);
    formData.append('duration', duration);
    formData.append('type', 'immersive');
    formData.append('starting_point', startingPoint);
    formData.append('map_embed', mapEmbed);
    formData.append('main_description', mainDescription);
    formData.append('additional_description', additionalDescription);
    formData.append('price_per_person', pricePerPerson);

    if (mainImageInput && mainImageInput.files[0]) {
        formData.append('main_image', mainImageInput.files[0]);
    }

    const audioDemoInput = document.getElementById('immersive-audio-demo-input');
    if (audioDemoInput && audioDemoInput.files[0]) {
        formData.append('audio_demo', audioDemoInput.files[0]);
    }

    const audioPreviewInput = document.getElementById('immersive-audio-preview-input');
    if (audioPreviewInput && audioPreviewInput.files[0]) {
        formData.append('audio_preview', audioPreviewInput.files[0]);
    }

    if (dateTimesData.length > 0) {
    formData.append('date_times', JSON.stringify(dateTimesData));
    }

    fetch('/api/admin/immersives/create-direct', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData 
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            GidoAlert.success('Готово!', 'Иммерсив успешно создан!').then(() => {
                window.location.href = '/admin/immersives';
            });
        } else {
            if (data.errors) {
                let errorMsg = 'Произошли следующие ошибки:';
                for (const field in data.errors) {
                    if (field === 'date_times') { 
                         errorMsg += `- ${data.errors[field].join('\n- ')}`;
                    } else if (field.startsWith('date_times.')) {
                        const parts = field.split('.');
                        const index = parts[1];
                        const subField = parts[2];
                        if (subField === 'date') {
                             errorMsg += `- Ошибка в дате блока #${parseInt(index)+1}: ${data.errors[field].join('\n- ')}`;
                        } else if (subField === 'times') {
                             errorMsg += `- Ошибка во времени блока #${parseInt(index)+1}: ${data.errors[field].join('\n- ')}`;
                        }
                    }
                     else {
                         errorMsg += `- ${data.errors[field].join('\n- ')}`;
                    }
                }
                GidoAlert.error('Ошибки валидации', errorMsg);
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при создании иммерсива.');
            }
        }
    })
    .catch(error => {
        console.error('Error creating immersive:', error);
        GidoAlert.error('Ошибка сети', 'Произошла ошибка при создании иммерсива.');
    });
}

function loadImmersiveDetails(immersiveId, excursionId) {
    fetch(`/api/admin/immersives/${immersiveId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.immersive) {
                const immersive = data.immersive;
                const excursion = immersive.excursion;
                
                if (excursion) {
                    document.getElementById('immersive-title').value = excursion.title;
                    document.getElementById('immersive-excursion-description').value = excursion.description;
                    document.getElementById('immersive-excursion-short-description').value = excursion.short_description || '';
                    document.getElementById('immersive-route-length').value = excursion.route_length;
                    document.getElementById('immersive-duration').value = excursion.duration;
                    document.getElementById('immersive-starting-point').value = excursion.starting_point;
                    document.getElementById('immersive-map-embed').value = excursion.map_embed;
                    
                    if (excursion.main_image) {
                        document.getElementById('immersive-main-image-preview').src = `/storage/${excursion.main_image}`;
                    }
                }
                
                document.getElementById('immersive-price').value = immersive.price_per_person;
                document.getElementById('immersive-main-description').value = immersive.main_description;
                document.getElementById('immersive-additional-description').value = immersive.additional_description;
                
                if (immersive.audio_demo) {
                    const audioDemoName = document.getElementById('immersive-audio-demo-name');
                    if (audioDemoName) {
                        audioDemoName.textContent = 'Файл загружен: ' + immersive.audio_demo.split('/').pop();
                    }
                }
                
                if (immersive.audio_preview) {
                    const audioPreviewImage = document.getElementById('immersive-audio-preview-image');
                    if (audioPreviewImage) {
                        audioPreviewImage.src = `/storage/${immersive.audio_preview}`;
                    }
                }
                
                loadExistingImmersiveDates(immersive);
            } else {
                GidoAlert.error('Ошибка', 'Ошибка загрузки данных иммерсива.');
            }
        })
        .catch(error => {
            console.error('Error loading immersive details:', error);
            GidoAlert.error('Ошибка сети', 'Ошибка загрузки данных иммерсива.');
        });
}

function loadExistingImmersiveDates(immersive) {
    const container = document.getElementById('immersive-existing-dates-times');
    if (!container) return;
    
    if (!immersive.dates || immersive.dates.length === 0) {
        container.innerHTML = '<div class="empty-state"><i class="bi bi-calendar-x"></i><p>Нет существующих дат</p></div>';
        return;
    }
    
    let html = '';
    
    immersive.dates.forEach(dateObj => {
        const dateFormatted = new Date(dateObj.event_date).toLocaleDateString('ru-RU');
        const times = dateObj.all_times || dateObj.times || [];
        
        html += `
            <div class="date-block">
                <div class="date-header">
                    <div class="date-title">
                        <i class="bi bi-calendar3"></i>
                        ${dateFormatted}
                    </div>
                    <button type="button" class="admin-form-button admin-delete-btn delete-date" data-date-id="${dateObj.id}" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">
                        <i class="bi bi-trash"></i> Удалить дату
                    </button>
                </div>
        `;
        
        if (times.length > 0) {
            html += '<div class="times-list">';
            times.forEach(timeObj => {
                const timeFormatted = timeObj.event_time.slice(0, 5); // Берем только HH:MM
                html += `
                    <div class="time-tag">
                        ${timeFormatted}
                        <i class="bi bi-x-circle delete-time" data-time-id="${timeObj.id}" title="Удалить время"></i>
                    </div>
                `;
            });
            html += '</div>';
        } else {
            html += '<p style="color: #666; font-style: italic;">Нет доступного времени</p>';
        }
        
        html += '</div>';
    });
    
    container.innerHTML = html;
    
    container.querySelectorAll('.delete-date').forEach(button => {
        button.addEventListener('click', function() {
            const dateId = this.getAttribute('data-date-id');
            deleteImmersiveDate(dateId);
        });
    });
    
    container.querySelectorAll('.delete-time').forEach(button => {
        button.addEventListener('click', function() {
            const timeId = this.getAttribute('data-time-id');
            deleteImmersiveTime(timeId);
        });
    });
}

function deleteImmersiveDate(dateId) {
    GidoAlert.delete('Удалить дату?', 'Вы действительно хотите удалить эту дату и все связанное с ней время?').then((result) => {
        if (result.isConfirmed) {
        fetch(`/api/admin/immersives/date/${dateId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Дата успешно удалена!');
                const immersiveId = document.getElementById('admin-update-immersive-form').getAttribute('data-immersive-id');
                const excursionId = document.getElementById('admin-update-immersive-form').getAttribute('data-excursion-id');
                loadImmersiveDetails(immersiveId, excursionId);
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при удалении даты.');
            }
        })
        .catch(error => {
            console.error('Error deleting immersive date:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении даты.');
        });
        }
    });
}

function deleteImmersiveTime(timeId) {
    GidoAlert.delete('Удалить время?', 'Вы действительно хотите удалить это время?').then((result) => {
        if (result.isConfirmed) {
        fetch(`/api/admin/immersives/time/${timeId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Время успешно удалено!');
                const immersiveId = document.getElementById('admin-update-immersive-form').getAttribute('data-immersive-id');
                const excursionId = document.getElementById('admin-update-immersive-form').getAttribute('data-excursion-id');
                loadImmersiveDetails(immersiveId, excursionId);
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при удалении времени.');
            }
        })
        .catch(error => {
            console.error('Error deleting immersive time:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при удалении времени.');
        });
        }
    });
}

function initUpdateImmersiveForm(immersiveId) {
    const mainImageInput = document.getElementById('immersive-main-image-input');
    const mainImagePreview = document.getElementById('immersive-main-image-preview');
    
    if (mainImageInput && mainImagePreview) {
        mainImageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    const audioPreviewInput = document.getElementById('immersive-audio-preview-input');
    const audioPreviewImage = document.getElementById('immersive-audio-preview-image');
    
    if (audioPreviewInput && audioPreviewImage) {
        audioPreviewInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    audioPreviewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    const audioDemoInput = document.getElementById('immersive-audio-demo-input');
    const audioDemoName = document.getElementById('immersive-audio-demo-name');
    
    if (audioDemoInput && audioDemoName) {
        audioDemoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                audioDemoName.textContent = this.files[0].name;
            } else {
                audioDemoName.textContent = 'Нет файла';
            }
        });
    }
    
    const addDateTimeBtn = document.getElementById('add-new-datetime-btn');
    const datesTimesContainer = document.getElementById('immersive-new-dates-times-container');
    
    if (addDateTimeBtn && datesTimesContainer) {
        let dateTimeBlockCount = 0;
        
        addDateTimeBtn.addEventListener('click', function() {
            const blockId = Date.now();
            
            const dateTimeBlock = document.createElement('div');
            dateTimeBlock.className = 'datetime-block';
            dateTimeBlock.style.marginBottom = '20px';
            dateTimeBlock.style.padding = '15px';
            dateTimeBlock.style.border = '1px solid #ddd';
            dateTimeBlock.style.borderRadius = '5px';
            dateTimeBlock.style.backgroundColor = '#f9f9f9';
            dateTimeBlock.id = `datetime-block-${blockId}`;
            
            dateTimeBlock.innerHTML = `
                <h4>Новая дата и время #${++dateTimeBlockCount}</h4>
                <div style="display: flex; margin-bottom: 10px;">
                    <div style="flex: 1; margin-right: 10px;">
                        <label for="date-${blockId}">Дата</label>
                        <input type="date" id="date-${blockId}" name="new_dates[]" class="form-control" style="width: 100%;">
                    </div>
                    <div style="flex: 1;">
                        <label>Доступное время</label>
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-11-${blockId}" name="new_times[${blockId}][]" value="11:00">
                                <label for="time-11-${blockId}">11:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-12-${blockId}" name="new_times[${blockId}][]" value="12:00">
                                <label for="time-12-${blockId}">12:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-13-${blockId}" name="new_times[${blockId}][]" value="13:00">
                                <label for="time-13-${blockId}">13:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-14-${blockId}" name="new_times[${blockId}][]" value="14:00">
                                <label for="time-14-${blockId}">14:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-15-${blockId}" name="new_times[${blockId}][]" value="15:00">
                                <label for="time-15-${blockId}">15:00</label>
                            </div>
                            <div style="margin-right: 10px; margin-bottom: 5px;">
                                <input type="checkbox" id="time-16-${blockId}" name="new_times[${blockId}][]" value="16:00">
                                <label for="time-16-${blockId}">16:00</label>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-danger remove-datetime-btn" data-block-id="${blockId}">Удалить</button>
            `;
            
            datesTimesContainer.appendChild(dateTimeBlock);
            
            dateTimeBlock.querySelector('.remove-datetime-btn').addEventListener('click', function() {
                const blockId = this.getAttribute('data-block-id');
                const blockToRemove = document.getElementById(`datetime-block-${blockId}`);
                if (blockToRemove) {
                    blockToRemove.remove();
                    dateTimeBlockCount--;
                }
            });
        });
    }
    
    const updateButton = document.getElementById('update-immersive-btn');
    if (updateButton) {
        updateButton.addEventListener('click', function() {
            updateImmersive(immersiveId);
        });
    }
}

function updateImmersive(immersiveId) {
    const title = document.getElementById('immersive-title').value;
    const description = document.getElementById('immersive-excursion-description').value;
    const shortDescription = document.getElementById('immersive-excursion-short-description').value;
    const routeLength = document.getElementById('immersive-route-length').value;
    const duration = document.getElementById('immersive-duration').value;
    const startingPoint = document.getElementById('immersive-starting-point').value;
    const mapEmbed = document.getElementById('immersive-map-embed').value;
    const mainDescription = document.getElementById('immersive-main-description').value;
    const additionalDescription = document.getElementById('immersive-additional-description').value;
    const pricePerPerson = document.getElementById('immersive-price').value;
    const mainImageInput = document.getElementById('immersive-main-image-input');

    if (!title || !description || !routeLength || !duration || !mainDescription || !additionalDescription || !pricePerPerson) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные поля.');
        return;
    }

    const newDateTimesData = [];
    let isValid = true;
    const newDateTimeBlocks = document.querySelectorAll('#immersive-new-dates-times-container .datetime-block');

    newDateTimeBlocks.forEach(block => {
        const dateInput = block.querySelector('input[name="new_dates[]"]');
        const date = dateInput.value;
         const blockId = block.id.split('-').pop();
        const timeCheckboxes = block.querySelectorAll(`input[name="new_times[${blockId}][]"]:checked`);
        const timesForDate = [];

        if (!date && timeCheckboxes.length === 0) {
             return;
        }

                if (!date) {
            GidoAlert.warning('Выберите дату', 'Пожалуйста, выберите дату для всех добавляемых блоков или удалите пустые блоки.');
            isValid = false;
        }

        if (timeCheckboxes.length === 0 && date) { 
            GidoAlert.warning('Выберите время', `Пожалуйста, выберите хотя бы одно время для новой даты ${date}.`);
            isValid = false;
        }

        if (date && timeCheckboxes.length > 0) {
            timeCheckboxes.forEach(checkbox => {
                timesForDate.push(checkbox.value);
            });
            newDateTimesData.push({ date: date, times: timesForDate });
        }
    });


    if (!isValid) return;

    const formData = new FormData();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('short_description', shortDescription);
    formData.append('route_length', routeLength);
    formData.append('duration', duration);
    formData.append('starting_point', startingPoint);
    formData.append('map_embed', mapEmbed);
    formData.append('main_description', mainDescription);
    formData.append('additional_description', additionalDescription);
    formData.append('price_per_person', pricePerPerson);

    if (mainImageInput && mainImageInput.files[0]) {
        formData.append('main_image', mainImageInput.files[0]);
    }

    const audioDemoInput = document.getElementById('immersive-audio-demo-input');
    if (audioDemoInput && audioDemoInput.files[0]) {
        formData.append('audio_demo', audioDemoInput.files[0]);
    }

    const audioPreviewInput = document.getElementById('immersive-audio-preview-input');
    if (audioPreviewInput && audioPreviewInput.files[0]) {
        formData.append('audio_preview', audioPreviewInput.files[0]);
    }

    if (newDateTimesData.length > 0) {
        formData.append('new_date_times', JSON.stringify(newDateTimesData));
    }
    
    fetch(`/api/admin/immersives/${immersiveId}/update-direct`, {
        method: 'POST', 
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json' 
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            GidoAlert.success('Готово!', 'Иммерсив успешно обновлен!').then(() => {
                window.location.href = '/admin/immersives';
            });
        } else {
            if (data.errors) {
                let errorMsg = `Произошли следующие ошибки:\n`; 
                 for (const field in data.errors) {
                     if (field === 'new_date_times') {
                          errorMsg += `- ${data.errors[field].join('\n- ')}\n`; 
                     } else if (field.startsWith('new_date_times.')) {
                         const parts = field.split('.');
                         const index = parts[1];
                         const subField = parts[2];
                         if (subField === 'date') {
                              errorMsg += `- Ошибка в новой дате блока #${parseInt(index)+1}: ${data.errors[field].join('\n- ')}\n`; 
                         } else if (subField === 'times') {
                              errorMsg += `- Ошибка во времени новой даты блока #${parseInt(index)+1}: ${data.errors[field].join('\n- ')}\n`; 
                         }
                     }
                      else {
                          errorMsg += `- ${data.errors[field].join('\n- ')}\n`; 
                     }
                 }
                GidoAlert.error('Ошибки валидации', errorMsg);
            } else {
                GidoAlert.error('Ошибка', data.message || 'Произошла ошибка при обновлении иммерсива.');
            }
        }
    })
    .catch(error => {
        console.error('Error updating immersive:', error);
        GidoAlert.error('Ошибка сети', 'Произошла ошибка при обновлении иммерсива. Проверьте консоль для деталей.'); 
    });
}

function loadExcursionDetails(excursionId) {
    fetch(`/api/admin/excursions/${excursionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.excursion) {
                const excursion = data.excursion;
                
                if (excursion) {
                    document.getElementById('excursion-title').value = excursion.title;
                    document.getElementById('excursion-description').value = excursion.description;
                    document.getElementById('excursion-short-description').value = excursion.short_description || '';
                    document.getElementById('excursion-route-length').value = excursion.route_length;
                    document.getElementById('excursion-duration').value = excursion.duration;
                    document.getElementById('excursion-starting-point').value = excursion.starting_point;
                    document.getElementById('excursion-map-embed').value = excursion.map_embed;
                
                    if (excursion.main_image) {
                        document.getElementById('excursion-main-image-preview').src = `/storage/${excursion.main_image}`;
                    }
                }
                
                document.getElementById('excursion-price').value = excursion.price;
                
                setTimeout(() => {
                    const linkedPlaceIds = data.linkedPlaceIds || [];
                    const linkedPlaces = data.linkedPlaces || [];
                    
                    selectedPlacesOrder = [];
                    
                    if (linkedPlaces && linkedPlaces.length > 0) {
                        linkedPlaces.forEach(place => {
                            selectedPlacesOrder.push({ 
                                id: place.id.toString(), 
                                name: place.name 
                            });
                        });
                    } else {
                        linkedPlaceIds.forEach(placeId => {
                            const placeElement = document.querySelector(`[data-place-id="${placeId}"]`);
                            if (placeElement) {
                                const placeName = placeElement.getAttribute('data-place-name');
                                selectedPlacesOrder.push({ 
                                    id: placeId.toString(), 
                                    name: placeName 
                                });
                            }
                        });
                    }
                    
                    $('.place-checkbox').each(function() {
                        $(this).prop('checked', linkedPlaceIds.includes(parseInt($(this).val())));
                    });
                    
                    updateSelectedPlacesDisplay();
                }, 500);
                
                $('#sights-list-container').empty();
                if (excursion.sights_list && excursion.sights_list.length > 0) {
                    excursion.sights_list.forEach(function(sight) {
                        addSightItem(sight);
                    });
                } else {
                    addSightItem();
                }
            } else {
                GidoAlert.error('Ошибка', 'Ошибка загрузки данных экскурсии.');
            }
        })
        .catch(error => {
            GidoAlert.error('Ошибка сети', 'Ошибка загрузки данных экскурсии.');
        });
}

function initUpdateExcursionForm(excursionId) {
    if (!excursionId) {
        const excursionIdData = document.getElementById('admin-update-excursion-form').dataset.excursionId;
        if (excursionIdData && excursionIdData !== 'null') {
            excursionId = excursionIdData;
        } else {
            GidoAlert.error('Ошибка', 'ID экскурсии не найден');
            return;
        }
    }
    
    loadPlacesForExcursion();
    initSightsList();
    
    $('#add-sight-btn').on('click', function() {
        addSightItem();
    });
    
    loadExcursionDetails(excursionId);
    
    $('#update-excursion-btn').on('click', function() {
        updateExcursion(excursionId);
    });
    
    $('#excursion-main-image-input').on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#excursion-main-image-preview').attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    $('#excursion-audio-preview-image-input').on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#excursion-audio-preview-image').attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    $('#excursion-audio-demo-input').on('change', function() {
        if (this.files && this.files[0]) {
            $('#excursion-audio-demo-name').text(this.files[0].name);
        }
    });
    
    $('#excursion-audio-full-input').on('change', function() {
        if (this.files && this.files[0]) {
            $('#excursion-audio-full-name').text(this.files[0].name);
        }
    });
}

function updateExcursion(excursionId) {
    const title = document.getElementById('excursion-title').value;
    const description = document.getElementById('excursion-description').value;
    const shortDescription = document.getElementById('excursion-short-description').value;
    const routeLength = document.getElementById('excursion-route-length').value;
    const duration = document.getElementById('excursion-duration').value;
    const startingPoint = document.getElementById('excursion-starting-point').value;
    const mapEmbed = document.getElementById('excursion-map-embed').value;
    const type = document.getElementById('excursion-type').value; 
    const price = document.getElementById('excursion-price').value || 0; 
    
    const mainImageInput = document.getElementById('excursion-main-image-input');
    const audioPreviewImageInput = document.getElementById('excursion-audio-preview-image-input');
    const audioDemoInput = document.getElementById('excursion-audio-demo-input');
    const audioFullInput = document.getElementById('excursion-audio-full-input');
    
    const selectedPlaces = selectedPlacesOrder.map(place => place.id);
    
    if (!title || !description || !shortDescription || !routeLength || !duration || !startingPoint || !mapEmbed || !type) {
        GidoAlert.warning('Заполните поля', 'Пожалуйста, заполните все обязательные текстовые поля.');
        return;
    }
    
    confirmUpdate('экскурсию', excursionId, function() {
        const formData = new FormData();
        formData.append('title', title);
        formData.append('description', description);
        formData.append('short_description', shortDescription);
        formData.append('route_length', routeLength);
        formData.append('duration', duration);
        formData.append('starting_point', startingPoint);
        formData.append('map_embed', mapEmbed);
        formData.append('type', type); 
        formData.append('price', price); 
        
        if (mainImageInput.files[0]) {
            formData.append('main_image', mainImageInput.files[0]);
        }
        
        if (audioPreviewImageInput.files[0]) {
            formData.append('audio_preview', audioPreviewImageInput.files[0]);
        }
        
        if (audioDemoInput.files[0]) {
            formData.append('audio_demo', audioDemoInput.files[0]);
        }
        
        if (audioFullInput.files[0]) {
            formData.append('audio_full', audioFullInput.files[0]);
        }
        
        selectedPlaces.forEach((placeId, index) => {
            formData.append(`places[${index}]`, placeId);
        });
        
        const sightsList = getSightsListData();
        
        formData.append('sights_list', JSON.stringify(sightsList));
        
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('_method', 'PUT');
        
        fetch(`/api/admin/excursions/${excursionId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                GidoAlert.success('Готово!', 'Экскурсия успешно обновлена!').then(() => {
                    window.location.href = '/admin/excursions';
                });
            } else {
                if (data.errors) {
                    let errorMsg = 'Произошли следующие ошибки:\n';
                    for (const field in data.errors) {
                        errorMsg += `- ${data.errors[field].join('\n- ')}\n`;
                    }
                    GidoAlert.error('Ошибки валидации', errorMsg);
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при обновлении экскурсии.');
                }
            }
        })
        .catch(error => {
            console.error('Error updating excursion:', error);
            GidoAlert.error('Ошибка сети', 'Произошла ошибка при обновлении экскурсии.');
        });
    });
}