document.addEventListener('DOMContentLoaded', function() {
    initApp();
    
    $(document).on('click', '.nav-link', function(e) {
        if ($(this).attr('href') && $(this).attr('href').indexOf('/admin') !== -1) {
            return true;
        }
        
        e.preventDefault();
        const page = $(this).data('page');
        navigateTo(page);
    });

    $(document).on('click', '.news-link', function(e) {
        e.preventDefault();
        const newsId = $(this).data('news-id');
        navigateToNews(newsId);
    });

    $(document).on('click', '.excursion-link', function(e) {
        e.preventDefault();
        const excursionId = $(this).data('excursion-id');
        navigateToExcursion(excursionId);
    });
    
    window.addEventListener('popstate', handlePopState);
    
    const burger = document.getElementById('burger');
    if (burger) {
        burger.addEventListener('click', function() {
            const nav = document.querySelector('.navigation');
            nav.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    window.openMenu = function() {
        document.getElementById("overlay").style.display = "block";
        document.getElementById("menu").style.display = "block";
    };
    
    window.closeMenu = function() {
        document.getElementById("overlay").style.display = "none";
        document.getElementById("menu").style.display = "none";
    };
    
    window.closePurchaseModal = function() {
        document.getElementById("purchase-overlay").style.display = "none";
        document.getElementById("purchase-confirmation-modal").style.display = "none";
    };
    
    window.purchaseExcursion = function(excursionId) {
        
        $.ajax({
            url: `/api/user/excursion/${excursionId}/purchase`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    GidoAlert.success('Экскурсия приобретена!', response.message || 'Экскурсия успешно приобретена!', {showCancelButton: false});
                    loadExcursionDetails(excursionId);
                    closePurchaseModal();
                    
                    if (window.location.pathname === '/personalAccount') {
                        setTimeout(() => {
                            $(document).trigger('updateUserExcursions');
                        }, 500);
                    }
                } else {
                    GidoAlert.error('Ошибка покупки', response.message || 'Ошибка при покупке экскурсии.', {showCancelButton: false});
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    GidoAlert.warning('Требуется авторизация', 'Необходимо авторизоваться для покупки экскурсии.', {showCancelButton: false});
                    navigateTo('login');
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при покупке экскурсии. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
                }
            }
        });
    };
    
    window.toggleFAQ = function(element) {
        const content = element.nextElementSibling;
        if (content) {
            const isActive = element.classList.contains('active');
            content.style.display = isActive ? "none" : "block";
            element.classList.toggle("active");
            
            const icon = element.querySelector('.toggle-icon');
            if (icon) {
                icon.textContent = isActive ? '+' : '+';
            }
        }
    };
});

function initApp() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    checkAuth();
    
    initImmersivesDataFromSSR();
}

function handlePopState() {
    const url = window.location.pathname;
    const urlParts = url.split('/').filter(Boolean);
    
    if (url === '/' || url === '') {
        showPage('welcome');
    } else if (urlParts[0] === 'catalogExcursions') {
        showPage('catalogExcursions');
        loadExcursions();
    } else if (urlParts[0] === 'blog') {
        if (urlParts.length > 1) {
            showPage('news');
            loadNewsDetails(urlParts[1]);
        } else {
            showPage('blog');
            loadNews();
        }
    } else if (urlParts[0] === 'aboutUs') {
        showPage('aboutUs');
    } else if (urlParts[0] === 'login') {
        if (currentUser) {
            navigateTo('welcome');
            return;
        }
        showPage('login');
    } else if (urlParts[0] === 'registration') {
        if (currentUser) {
            navigateTo('welcome');
            return;
        }
        showPage('registration');
    } else if (urlParts[0] === 'personalAccount') {
        if (!currentUser) {
            navigateTo('login');
            return;
        }
        showPage('personalAccount');
        loadUserProfile();
    } else if (urlParts[0] === 'editProfile') {
        if (!currentUser) {
            navigateTo('login');
            return;
        }
        showPage('editProfile');
        loadUserProfileForEdit();
    } else if (urlParts[0] === 'excursion' && urlParts.length > 1) {
        if (!currentUser) {
            sessionStorage.setItem('redirectAfterLogin', url);
            navigateTo('login');
            return;
        }
        showPage('excursion');
        loadExcursionDetails(urlParts[1]);
    } else if (urlParts[0] === 'booking' && urlParts.length > 1) {
        if (!currentUser) {
            sessionStorage.setItem('redirectAfterLogin', url);
            navigateTo('login');
            return;
        }
        showPage('booking');
        loadImmersiveDetails(urlParts[1]); 
    } else {
        showPage('404');
    }
}

function showPage(pageName) {
    $('.page').hide();
    $(`.page-${pageName}`).show();
    
    if (typeof isBookingInProgress !== 'undefined') {
        isBookingInProgress = false;
    }
    if (typeof lastSelectedTimeId !== 'undefined') {
        lastSelectedTimeId = null;
    }
    if (typeof timeSelectionTimeout !== 'undefined' && timeSelectionTimeout) {
        clearTimeout(timeSelectionTimeout);
        timeSelectionTimeout = null;
    }
    

    
    updateActiveMenuItem(pageName);
    
    initPageFunctionality(pageName);
    
    setPageTitle(pageName);
}

function setPageTitle(pageName) {
    let title = 'ГидО - ';
    
    switch(pageName) {
        case 'welcome':
            title += 'Главная';
            break;
        case 'catalogExcursions':
            title += 'Каталог экскурсий';
            break;
        case 'blog':
            title += 'Блог';
            break;
        case 'aboutUs':
            title += 'О нас';
            break;
        case 'login':
            title += 'Вход';
            break;
        case 'registration':
            title += 'Регистрация';
            break;
        case 'personalAccount':
            title += 'Личный кабинет';
            break;
        case 'editProfile':
            title += 'Редактирование профиля';
            break;
        case 'excursion':
            title += 'Экскурсия';
            break;
        case 'booking':
            title += 'Бронирование';
            break;
        case 'news':
            title += 'Новость';
            break;
        case '404':
            title += 'Страница не найдена';
            break;
    }
    
    document.title = title;
}

function updateActiveMenuItem(pageName) {
    $('.nav_ani, .nav_ani_foot').removeClass('active');
    $(`.nav-link[data-page="${pageName}"]`).addClass('active');
}

function initPageFunctionality(pageName) {
    switch(pageName) {
        case 'welcome':
            initWelcomePage();
            break;
        case 'catalogExcursions':
            initCatalogPage();
            break;
        case 'blog':
            initBlogPage();
            break;
        case 'aboutUs':
            initAboutUsPage();
            break;
        case 'login':
            initLoginPage();
            break;
        case 'registration':
            initRegistrationPage();
            break;
        case 'personalAccount':
            initPersonalAccountPage();
            break;
        case 'editProfile':
            initEditProfilePage();
            break;
        case 'excursion':
            initExcursionPage();
            break;
        case 'booking':
            initBookingPage();
            break;
        case 'news':
            initNewsPage();
            break;
    }
}

function navigateTo(page) {
    let url;
    
    switch(page) {
        case 'welcome':
            url = '/';
            break;
        default:
            url = `/${page}`;
    }
    
    history.pushState({page}, '', url);
    
    showPage(page);
}

function navigateToNews(newsId) {
    const url = `/blog/${newsId}`;
    history.pushState({page: 'news', id: newsId}, '', url);
    
    showPage('news');
    loadNewsDetails(newsId);
}

function navigateToExcursion(excursionId) {
    if (!currentUser) {
        sessionStorage.setItem('redirectExcursionId', excursionId);
        navigateTo('login');
        return;
    }

    const excursionLink = $(`.excursion-link[data-excursion-id="${excursionId}"]`);
    const excursionType = excursionLink.length > 0 ? excursionLink.data('excursion-type') : null;
    
    if (excursionType) {
        let url;
        let page;
        
        if (excursionType === 'immersive') {
            url = `/booking/${excursionId}`;
            page = 'booking';
        } else {
            url = `/excursion/${excursionId}`;
            page = 'excursion';
        }
        
        history.pushState({page, id: excursionId}, '', url);
        
        showPage(page);
        if (page === 'booking') {
           loadImmersiveDetails(excursionId);
        } else {
            loadExcursionDetails(excursionId);
        }
        return;
    }
    
    $.ajax({
        url: `/api/excursion/${excursionId}/type`,
        method: 'GET',
        success: function(response) {
            let url;
            let page;
            
            if (response.type === 'immersive') {
                url = `/booking/${excursionId}`;
                page = 'booking';
            } else {
                url = `/excursion/${excursionId}`;
                page = 'excursion';
            }
            
            history.pushState({page, id: excursionId}, '', url);
            
            showPage(page);
            if (page === 'booking') {
                loadImmersiveDetails(excursionId);
            } else {
                loadExcursionDetails(excursionId);
            }
        },
        error: function() {
            const url = `/excursion/${excursionId}`;
            history.pushState({page: 'excursion', id: excursionId}, '', url);
            
            showPage('excursion');
            loadExcursionDetails(excursionId);
        }
    });
}

function checkAuth() {
    fetch('/api/auth/user', {
        method: 'GET',
        headers: { 
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentUser = data.user;
            updateUIForAuthenticatedUser(data.user);
        } else {
            currentUser = null;
            updateUIForGuest();
        }
        
        handlePopState();
    })
    .catch(error => {
        // console.error('Ошибка проверки авторизации:', error);
        currentUser = null;
        updateUIForGuest();
        
        handlePopState();
    });
}

function updateUIForAuthenticatedUser(user) {
    const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
    
    const redirectExcursionId = sessionStorage.getItem('redirectExcursionId');
    
    sessionStorage.removeItem('redirectAfterLogin');
    sessionStorage.removeItem('redirectExcursionId');
    
    if (redirectUrl) {
        window.location.href = redirectUrl;
    } else if (redirectExcursionId) {
        navigateToExcursion(redirectExcursionId);
    }
}

function updateUIForGuest() {
}


function initWelcomePage() {
    loadWelcomePageComments();
    initWelcomePageTextRotation();
}

function loadWelcomePageComments() {
    $.ajax({
        url: '/api/comments/featured',
        method: 'GET',
        success: function(response) {
            if (response.success && response.comments.length > 0) {
                const commentsContainer = $('#welcome-comments');
                commentsContainer.empty();
                
                response.comments.forEach(function(comment) {
                    const excursionTitle = comment.excursion && comment.excursion.title ? comment.excursion.title : 'Экскурсия';
                    const commentHtml = `
                        <div class="welcome-comment">
                            <div class="my_name_comment">
                                <img src="/img/personalAccount/avatar.png" alt="" class="avatar_comment">
                                <h4 class="title_name">${comment.user.name}</h4>
                                <div class="comment-status">
                                    <div class="approved" title="Одобрен"></div>
                                </div>
                            </div>
                            <p class="title_name2">${excursionTitle.toLowerCase()}</p>
                            <p class="descript_comment">${comment.content}</p>
                        </div>
                    `;
                    commentsContainer.append(commentHtml);
                });
            } else {
                const commentsContainer = $('#welcome-comments');
                commentsContainer.empty().html(`
                    <div class="empty-state">
                        <div class="empty-state-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\'/%3E%3C/svg%3E');"></div>
                        <h3 class="empty-state-title">Пока нет отзывов</h3>
                        <p class="empty-state-description">Станьте первым, кто поделится впечатлениями о наших экскурсиях!</p>
                        <a href="#" class="empty-state-action nav-link" data-page="catalogExcursions">Найти экскурсию</a>
                    </div>
                `);
            }
        },
        error: function() {
            const commentsContainer = $('#welcome-comments');
            commentsContainer.empty().html(`
                <div class="empty-state">
                    <div class="empty-state-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%23ef4444\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z\'/%3E%3C/svg%3E');"></div>
                    <h3 class="empty-state-title">Не удалось загрузить отзывы</h3>
                    <p class="empty-state-description">Пожалуйста, попробуйте обновить страницу позже</p>
                </div>
            `);
        }
    });
}

function initWelcomePageTextRotation() {
    const welcomePageTextElement = document.getElementById('text');
    const welcomePageImageElement = document.getElementById('myImage');
    
    if (welcomePageTextElement && welcomePageImageElement) {
        const welcomeTexts = ['Ночная прогулка', 'Астраханский Кремль', 'Забытые улицы'];
        let welcomeCurrentIndex = 0;

        window.addEventListener('scroll', function() {
            const scrollPosition = window.scrollY;
            const rotation = scrollPosition % 360;

            welcomePageImageElement.style.transform = `rotate(${rotation}deg)`;

            if (scrollPosition > 0) {
                welcomePageTextElement.style.opacity = 0;
                setTimeout(() => {
                    welcomeCurrentIndex = Math.floor(scrollPosition / 100) % welcomeTexts.length;
                    welcomePageTextElement.textContent = welcomeTexts[welcomeCurrentIndex];
                    welcomePageTextElement.style.opacity = 1;
                }, 300);
            }
        });
    }
}

function initCatalogPage() {
    initCustomSelect();
    
    initCatalogImageAnimation();
    
    initCatalogArrowScroll();
    
    loadExcursions();
}

function loadExcursions(type = null) {
    let url = '/api/excursions';
    if (type) {
        url += `?type=${type}`;
    }
    
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            if (response.success && response.excursions.length > 0) {
                const container = $('#excursions-container');
                container.empty();
                
                response.excursions.forEach(function(excursion) {
                    const imagePath = excursion.main_image 
                        ? `/storage/${excursion.main_image}` 
                        : `/img/catalogExcursions/excursion_img${(Math.floor(Math.random() * 6) + 1)}.png`;
                        
                    const excursionHtml = `
                        <div class="image_ex" data-excursion-type="${excursion.type}">
                            <img src="${imagePath}" alt="${excursion.title}" class="excursion_img">
                            <p class="title_excursion">${excursion.title.toUpperCase()}</p>
                            <a href="#" class="excursion-link" data-excursion-id="${excursion.id}" data-excursion-type="${excursion.type}"><img src="/img/catalogExcursions/block3_arrow.png" alt="" class="arrow_block3"></a>
                        </div>
                    `;
                    container.append(excursionHtml);
                });
                
                $('.excursion-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const excursionId = $(this).data('excursion-id');
                    navigateToExcursion(excursionId);
                });
            } else {
                const container = $('#excursions-container');
                const typeMessage = type ? 
                    (type === 'audio' ? 'аудиоэкскурсий' : 'иммерсивных экскурсий') : 
                    'экскурсий';
                
                container.empty().html(`
                    <div class="empty-block">
                        <div class="empty-block-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                        <h3 class="empty-block-title">Не найдено ${typeMessage}</h3>
                        <p class="empty-block-description">Скоро здесь появятся новые интересные экскурсии!</p>
                        ${type ? `<a href="#" onclick="loadExcursions()" class="empty-block-action">Показать все экскурсии</a>` : ''}
                    </div>
                `);
            }
        },
        error: function() {
            const container = $('#excursions-container');
            container.empty().html(`
                <div class="empty-block">
                    <div class="empty-block-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="empty-block-title">Не удалось загрузить экскурсии</h3>
                    <p class="empty-block-description">Пожалуйста, попробуйте обновить страницу позже</p>
                </div>
            `);
        }
    });
}

function initCustomSelect() {
    const catalogCustomSelect = document.getElementById('customSelect');
    const catalogOptionList = document.getElementById('optionList');
    const catalogOptionItems = document.querySelectorAll('#optionList .option-item');

    if (catalogCustomSelect && catalogOptionList) {
        catalogCustomSelect.addEventListener('click', (event) => {
            event.stopPropagation();
            
            catalogCustomSelect.classList.toggle('active');
            catalogOptionList.classList.toggle('show');
        });

        catalogOptionItems.forEach(item => {
            item.addEventListener('click', (event) => {
                event.stopPropagation();
                
                const selectedText = event.target.textContent;
                const selectedValue = event.target.getAttribute('data-value');

                catalogCustomSelect.innerHTML = selectedText + '<img src="/img/catalogExcursions/arrow_block2.png" alt="arrow" class="arrow-icon">';
                catalogCustomSelect.setAttribute('data-selected-value', selectedValue);
                
                catalogCustomSelect.classList.remove('active');
                catalogOptionList.classList.remove('show');

                filterExcursions(selectedValue);
            });
        });

        document.addEventListener('click', (event) => {
            if (!catalogCustomSelect.contains(event.target) && !catalogOptionList.contains(event.target)) {
                catalogCustomSelect.classList.remove('active');
                catalogOptionList.classList.remove('show');
            }
        });
    }
}

function filterExcursions(selectedType) {
    if (selectedType === 'all' || !selectedType) {
        loadExcursions();
    } else {
        loadExcursions(selectedType);
    }
}

function initCatalogImageAnimation() {
    const catalogImage1 = document.getElementById('image1');
    const catalogImage2 = document.getElementById('image2');
    const catalogImage3 = document.getElementById('image3');

    if (catalogImage1 && catalogImage2 && catalogImage3) {
        catalogImage1.style.opacity = 1;

        setTimeout(() => {
            catalogImage2.classList.add('visible');
            setTimeout(() => {
                catalogImage2.classList.add('reset');
            }, 500);

            setTimeout(() => {
                catalogImage3.classList.add('visible');
            }, 1000);
        }, 1000);
    }
}

function initCatalogArrowScroll() {
    const catalogArrow = document.getElementById('arrow');
    const catalogBlock2 = document.getElementById('block2');
    
    if (catalogArrow && catalogBlock2) {
        catalogArrow.addEventListener('click', function() {
            catalogBlock2.scrollIntoView({ behavior: 'smooth' });
        });
    }
}

function initBlogPage() {
    initBlogPageTextRotation();
    
    loadNews();
}

function loadNews() {
    $.ajax({
        url: '/api/news',
        method: 'GET',
        success: function(response) {
            if (response.success && response.news.data && response.news.data.length > 0) {
                const newsContainer = $('#news-container');
                newsContainer.empty();
                
                response.news.data.forEach(function(news) {
                    const formattedDate = new Date(news.created_at).toLocaleDateString('ru-RU');
                    
                    let imagePath;
                    if (news.image_path) {
                        imagePath = news.image_path;
                    } else if (news.main_image) {
                        imagePath = '/storage/' + news.main_image;
                    } else {
                        imagePath = '/img/blog/no-image.png';
                    }
                    
                    const newsHtml = `
                        <div class="news_block">
                            <img src="${imagePath}" alt="${news.title}" class="img_news">
                            <div class="news-content-wrapper">
                                <h3 class="title_news_1">${news.title}</h3>
                                <p class="title_news_2">${news.description || news.subtitle || ''}</p>
                                <div class="news-meta">
                                    <span class="news-date">${formattedDate}</span>
                                    <a href="#" class="news-link arrow_block3" data-news-id="${news.id}"></a>
                                </div>
                            </div>
                        </div>
                    `;
                    newsContainer.append(newsHtml);
                });
            } else {
                const newsContainer = $('#news-container');
                newsContainer.empty().html(`
                    <div class="empty-block">
                        <div class="empty-block-icon"><i class="fa-solid fa-newspaper"></i></div>
                        <h3 class="empty-block-title">Пока нет новостей</h3>
                        <p class="empty-block-description">Скоро здесь появятся интересные статьи и новости о нашем городе!</p>
                    </div>
                `);
            }
        },
        error: function() {
            const newsContainer = $('#news-container');
            newsContainer.empty().html(`
                <div class="empty-block">
                    <div class="empty-block-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="empty-block-title">Не удалось загрузить новости</h3>
                    <p class="empty-block-description">Пожалуйста, попробуйте обновить страницу позже</p>
                </div>
            `);
        }
    });
}

function loadNewsDetails(newsId) {
    $.ajax({
        url: `/api/news/${newsId}`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.news) {
                const news = response.news;
                const formattedDate = new Date(news.created_at).toLocaleDateString('ru-RU');
                
                $('#news-title').text(news.title);
                $('#news-date').text(formattedDate);
                
                let imagePath;
                if (news.image_path) {
                    imagePath = news.image_path;
                } else if (news.main_image) {
                    imagePath = '/storage/' + news.main_image;
                } else {
                    imagePath = '/img/blog/no-image.png';
                }
                
                $('#news-image-main').attr('src', imagePath);
                
                if (news.url && news.url.trim() !== '') {
                    $('#news-source-link').attr('href', news.url);
                    $('#news-source').show();
                } else {
                    $('#news-source').hide();
                }
                
                $('#news-content').html(news.content || news.description || '');
            } else {
                alert('Не удалось загрузить новость.');
                navigateTo('blog');
            }
        },
        error: function() {
            alert('Не удалось загрузить новость.');
            navigateTo('blog');
        }
    });
}

function initAboutUsPage() {
    startAboutUsTextAnimation();
    
    initAboutUsFadeImages();
    
    initAboutUsImageAnimation();
}

function startAboutUsTextAnimation() {
    const aboutUsTextElements = [
        { id: 'text1', content: 'Очередные скучные и сухие факты о городе?' },
        { id: 'text2', content: 'Надоели стандартные экскурсии с сухими фактами? Погрузитесь в мир аудиоэкскурсий и иммерсивных прогулок, которые точно перевернут ваше представление о городе.' },
        { id: 'text3', content: 'Когда следующий иммерсив?))' }
    ];

    let delay = 0;
    const delayBetweenAnimations = 500;

    aboutUsTextElements.forEach((text) => {
        const element = document.getElementById(text.id);
        if (element) {
            setTimeout(() => {
                fadeInText(element, text.content);
            }, delay);
            delay += 500 + delayBetweenAnimations;
        }
    });
}

function fadeInText(element, content) {
    if (!element) return;
    element.textContent = content;
    element.style.opacity = 0;
    element.style.display = 'block';

    let startTime = null;
    const fadeInDuration = 500;

    function fadeIn(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        element.style.opacity = Math.min(progress / fadeInDuration, 1);

        if (progress < fadeInDuration) {
            requestAnimationFrame(fadeIn);
        }
    }
    requestAnimationFrame(fadeIn);
}

function initAboutUsFadeImages() {
    const aboutUsFadeImages = document.querySelectorAll('.block2 .fade');
    if (aboutUsFadeImages.length > 0) {
        let aboutUsFadeCurrentIndex = 0;
        function showNextAboutUsFadeImage() {
            if (aboutUsFadeImages.length === 0) return;
            aboutUsFadeImages[aboutUsFadeCurrentIndex].style.opacity = 0;
            aboutUsFadeCurrentIndex = (aboutUsFadeCurrentIndex + 1) % aboutUsFadeImages.length;
            aboutUsFadeImages[aboutUsFadeCurrentIndex].style.opacity = 1;
        }
        setInterval(showNextAboutUsFadeImage, 1000);
        showNextAboutUsFadeImage();
    }
}

function initAboutUsImageAnimation() {
    const aboutUsImageBlock = document.getElementById('imageBlock');
    const aboutUsAnimatedImage = document.getElementById('imageElement');
    
    if (aboutUsImageBlock && aboutUsAnimatedImage) {
        let aboutUsTimeoutId;
        window.addEventListener('scroll', function() {
            const rect = aboutUsImageBlock.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom >= 0) {
                clearTimeout(aboutUsTimeoutId);
                aboutUsTimeoutId = setTimeout(() => {
                    aboutUsAnimatedImage.classList.add('visible');
                }, 300);
            } else {
                clearTimeout(aboutUsTimeoutId);
                aboutUsAnimatedImage.classList.remove('visible');
            }
        });
    }
}

function initBlogPageTextRotation() {
    const blogTexts = ['Новости', 'Советы', 'Рассказы жителей'];
    let blogCurrentIndex = 0;
    
    window.addEventListener('scroll', function() {
        if (!document.querySelector('.page-blog:not([style*="display: none"])')) {
            return;
        }
        
        const blogPageTextElement = document.querySelector('.page-blog #text');
        const blogPageImageElement = document.querySelector('.page-blog #myImage');
        
        if (!blogPageTextElement || !blogPageImageElement) {
            return;
        }
        
        const scrollPosition = window.scrollY;
        const rotation = scrollPosition % 360;
        
        blogPageImageElement.style.transform = `rotate(${rotation}deg)`;
        
        if (scrollPosition > 0) {
            blogPageTextElement.style.opacity = 0;
            setTimeout(() => {
                blogCurrentIndex = Math.floor(scrollPosition / 100) % blogTexts.length;
                blogPageTextElement.textContent = blogTexts[blogCurrentIndex];
                blogPageTextElement.style.opacity = 1;
            }, 300);
        }
    });
}

function initLoginPage() {
    $('#login-button').on('click', function(e) {
        e.preventDefault();
        loginUser();
    });
    
    $('#login-login, #login-password').on('keypress', function(e) {
        if (e.which === 13) {
            loginUser();
        }
    });
    
    $('#login-agreement').on('change', function() {
        if ($(this).is(':checked')) {
            $('#login-agreement-error').hide();
        }
    });
}

function loginUser() {
    $('#login-error-message').hide();
    $('.invalid-feedback').hide();
    $('.is-invalid').removeClass('is-invalid');
    
    const agreementChecked = $('#login-agreement').is(':checked');
    if (!agreementChecked) {
        $('#login-agreement-error').show();
        return;
    } else {
        $('#login-agreement-error').hide();
    }
    
    const submitButton = $('#login-button');
    const originalButtonText = submitButton.html();
    submitButton.prop('disabled', true).css('pointer-events', 'none').html('Вход...');

    const loginData = {
        login: $('#login-login').val(),
        password: $('#login-password').val(),
    };

    $.ajax({
        url: '/api/auth/login',
        method: 'POST',
        data: loginData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                currentUser = response.user;
                updateUIForAuthenticatedUser(response.user);
                
                const redirectUrl = sessionStorage.getItem('redirectAfterLogin');
                const redirectExcursionId = sessionStorage.getItem('redirectExcursionId');
                
                sessionStorage.removeItem('redirectAfterLogin');
                sessionStorage.removeItem('redirectExcursionId');
                
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                } else if (redirectExcursionId) {
                    navigateToExcursion(redirectExcursionId);
                } else {
                    window.location.reload();
                }
            } else {
                $('#login-error-message').text(response.message || 'Неверный логин или пароль.').show();
                submitButton.prop('disabled', false).css('pointer-events', 'auto').html(originalButtonText);
            }
        },
        error: function(xhr) {
            // console.error("Login Error:", xhr);
            if (xhr.status === 401 || (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message && !xhr.responseJSON.errors)) {
                $('#login-error-message').text(xhr.responseJSON.message || 'Неверный логин или пароль.').show();
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors){
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    const input = $('#login-' + field);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]).show();
                });
            } else {
                $('#login-error-message').text('Произошла ошибка при входе. Пожалуйста, попробуйте еще раз.').show();
            }
            submitButton.prop('disabled', false).css('pointer-events', 'auto').html(originalButtonText);
        }
    });
}

function initRegistrationPage() {
    $('#register-button').on('click', function(e) {
        e.preventDefault();
        registerUser();
    });
    
    $('#register-name, #register-login, #register-email, #register-password, #register-password_confirmation').on('keypress', function(e) {
        if (e.which === 13) {
            registerUser();
        }
    });
    
    $('#register-agreement').on('change', function() {
        if ($(this).is(':checked')) {
            $('#register-agreement-error').hide();
        }
    });
}

function registerUser() {
    $('.invalid-feedback').hide();
    $('.is-invalid').removeClass('is-invalid');
    
    const agreementChecked = $('#register-agreement').is(':checked');
    if (!agreementChecked) {
        $('#register-agreement-error').show();
        return;
    } else {
        $('#register-agreement-error').hide();
    }
    
    const submitButton = $('#register-button');
    const originalButtonText = submitButton.html();
    submitButton.prop('disabled', true).css('pointer-events', 'none').html('Регистрация...');

    const name = $('#register-name').val();
    const login = $('#register-login').val();
    const email = $('#register-email').val();
    const password = $('#register-password').val();
    const password_confirmation = $('#register-password_confirmation').val();

    let hasError = false;
    if (password.length < 6) {
        $('#register-password').addClass('is-invalid');
        $('#register-password').siblings('.invalid-feedback').show();
        hasError = true;
    }
    
    if (password_confirmation.length < 6) {
        $('#register-password_confirmation').addClass('is-invalid');
        $('#register-password_confirmation').siblings('.invalid-feedback').show();
        hasError = true;
    }
    
    if (hasError) {
        submitButton.prop('disabled', false).css('pointer-events', 'auto').html(originalButtonText);
        return;
    }

    const registrationData = {
        name: name,
        login: login,
        email: email,
        password: password,
        password_confirmation: password_confirmation,
    };

    $.ajax({
        url: '/api/auth/register',
        method: 'POST',
        data: registrationData,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                updateUIForAuthenticatedUser(response.user);
                window.location.reload();
            } else {
                if (response.errors) {
                    $.each(response.errors, function(field, messages) {
                        const input = $('#register-' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]).show();
                    });
                } else {
                    GidoAlert.error('Ошибка регистрации', response.message || 'Ошибка регистрации.', {showCancelButton: false});
                }
                submitButton.prop('disabled', false).css('pointer-events', 'auto').html(originalButtonText);
            }
        },
        error: function(xhr) {
            // console.error("Registration Error:", xhr);
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    const input = $('#register-' + field);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]).show();
                });
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при регистрации. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
            }
            submitButton.prop('disabled', false).css('pointer-events', 'auto').html(originalButtonText);
        }
    });
}

function initPersonalAccountPage() {
    loadUserProfile();
    
    $(document).off('updateUserExcursions.personalAccount').on('updateUserExcursions.personalAccount', function() {
        refreshUserExcursions();
    });
}

function loadUserProfile() {
    $.ajax({
        url: '/api/user/profile',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const user = response.user;
                const excursionPurchases = response.excursionPurchases || [];
                const comments = response.comments || [];
                const bookings = response.bookings || [];
                
                $('#profile-name').text(user.name);
                
                $('#profile-comments-count').text(`мои комментарии: ${comments.length}`);
                
                const excursionsContainer = $('#user-excursions');
                const excursionsHtml = excursionsContainer.html().trim();
                const hasSSRExcursions = excursionsHtml.includes('block2_img_content') || excursionsHtml.includes('empty-block');
                
                if (!hasSSRExcursions) {
                    excursionsContainer.empty();
                    
                    if (excursionPurchases.length > 0 || bookings.length > 0) {
                        excursionPurchases.forEach(function(purchase) {
                            const imagePath = purchase.excursion.main_image 
                                ? `/storage/${purchase.excursion.main_image}` 
                                : '/img/personalAccount/excursuion.png';
                            
                            const excursionHtml = `
                                <div class="block2_img_content">
                                    <img src="${imagePath}" alt="" class="my_excurs">
                                    <div class="excursion-info">
                                        <h4 class="name_excursion">${purchase.excursion.title}</h4>
                                        <div class="excursion-meta">
                                            <span class="excursion-type">${purchase.excursion.type === 'audio' ? 'Аудио' : 'Иммерсив'}</span>
                                            <a href="#" class="arrow_block3 excursion-link" data-excursion-id="${purchase.excursion.id}" data-excursion-type="${purchase.excursion.type}"></a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            excursionsContainer.append(excursionHtml);
                        });
                        
                        bookings.forEach(function(booking) {
                            if (booking.immersive && booking.immersive.excursion) {
                                const excursion = booking.immersive.excursion;
                                const dateString = booking.date ? new Date(booking.date.event_date).toLocaleDateString('ru-RU') : '';
                                const timeString = booking.time ? booking.time.event_time : '';
                                const dateTimeInfo = dateString && timeString ? ` (${dateString}, ${timeString})` : '';
                                
                                const imagePath = excursion.main_image 
                                    ? `/storage/${excursion.main_image}` 
                                    : '/img/personalAccount/excursuion.png';
                                
                                const excursionHtml = `
                                    <div class="block2_img_content">
                                        <img src="${imagePath}" alt="" class="my_excurs">
                                        <div class="excursion-info">
                                            <h4 class="name_excursion">${excursion.title}${dateTimeInfo}</h4>
                                            <div class="excursion-meta">
                                                <span class="excursion-type">Иммерсив</span>
                                                <a href="#" class="arrow_block3 excursion-link" data-excursion-id="${excursion.id}" data-excursion-type="${excursion.type}"></a>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                excursionsContainer.append(excursionHtml);
                            }
                        });
                    } else {
                        excursionsContainer.html(`
                            <div class="empty-block">
                                <div class="empty-block-icon"><i class="fa-solid fa-route"></i></div>
                                <h3 class="empty-block-title">Нет приобретенных экскурсий</h3>
                                <p class="empty-block-description">Исследуйте наш каталог и выберите экскурсию, которая вас заинтересует!</p>
                                <a href="#" class="empty-block-action nav-link" data-page="catalogExcursions">Перейти в каталог</a>
                            </div>
                        `);
                    }
                }
                
                const commentsContainer = $('#user-comments');
                const commentsHtml = commentsContainer.html().trim();
                const hasSSRComments = commentsHtml.includes('comment_block_right') || commentsHtml.includes('empty-block');
                
                if (!hasSSRComments) {
                    commentsContainer.empty();
                    
                    if (comments.length > 0) {
                        comments.forEach(function(comment) {
                            let statusClass = '';
                            let statusText = '';
                            let statusIcon = '';
                            
                            if (comment.is_approved) {
                                statusClass = 'approved';
                                statusText = 'Одобрен';
                                statusIcon = '✓';
                            } else if (comment.is_rejected) {
                                statusClass = 'rejected';
                                statusText = 'Отклонен';
                                statusIcon = '✗';
                            } else {
                                statusClass = 'pending';
                                statusText = 'На модерации';
                                statusIcon = '⏳';
                            }
                            
                            const commentDate = comment.created_at ? new Date(comment.created_at).toLocaleDateString('ru-RU') : '';
                            
                            const commentHtml = `
                                <div class="comment-card">
                                    <div class="comment-header">
                                        <div class="comment-excursion">${comment.excursion ? comment.excursion.title : 'Экскурсия'}</div>
                                        <div class="comment-status ${statusClass}" ${comment.is_rejected && comment.rejection_reason ? `onclick="openRejectionReasonModal('${comment.rejection_reason}')" style="cursor: pointer;"` : ''}>
                                            <span class="status-icon">${statusIcon}</span>
                                            <span class="status-text">${statusText}</span>
                                        </div>
                                    </div>
                                    <div class="comment-content">${comment.content}</div>
                                    ${commentDate ? `<div class="comment-date">Отправлено: ${commentDate}</div>` : ''}
                                </div>
                            `;
                            commentsContainer.append(commentHtml);
                        });
                    } else {
                        commentsContainer.html(`
                            <div class="empty-state">
                                <div class="empty-state-icon">💬</div>
                                <h3 class="empty-state-title">Вы еще не оставили отзывов</h3>
                                <p class="empty-state-description">После прохождения экскурсии вы сможете поделиться своими впечатлениями!</p>
                            </div>
                        `);
                    }
                }
            } else {
                navigateTo('login');
            }
        },
        error: function() {
            navigateTo('login');
        }
    });
}

function initEditProfilePage() {
    loadUserProfileForEdit();
    
    $('#update-profile-button').on('click', function(e) {
        e.preventDefault();
        updateProfile();
    });
    
    $('#update-password-button').on('click', function(e) {
        e.preventDefault();
        updatePassword();
    });
}

function loadUserProfileForEdit() {
    $.ajax({
        url: '/api/user/profile',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const user = response.user;
                
                $('#name').val(user.name);
                $('#login').val(user.login);
            } else {
                navigateTo('login');
            }
        },
        error: function() {
            navigateTo('login');
        }
    });
}

function updateProfile() {
    $('.invalid-feedback').hide();
    $('.is-invalid').removeClass('is-invalid');
    
    const name = $('#name').val().trim();
    const login = $('#login').val().trim();
    
    let isValid = true;
    
    if (name === '') {
        $('#name').addClass('is-invalid');
        $('#name-error').text('Поле "Имя" обязательно для заполнения').show();
        isValid = false;
    }
    
    if (login === '') {
        $('#login').addClass('is-invalid');
        $('#login-error').text('Поле "Логин" обязательно для заполнения').show();
        isValid = false;
    }
    
    if (!isValid) return;
    
    $.ajax({
        url: '/api/user/profile',
        method: 'PUT',
        data: {
            name: name,
            login: login
        },
        success: function(response) {
            if (response.success) {
                GidoAlert.success('Профиль обновлен', 'Изменения успешно сохранены!', {showCancelButton: false});
            } else {
                if (response.errors) {
                    $.each(response.errors, function(field, messages) {
                        const input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]).show();
                    });
                } else {
                    GidoAlert.error('Ошибка', response.message || 'Произошла ошибка при обновлении профиля', {showCancelButton: false});
                }
            }
        },
        error: function(xhr) {
            // console.error("Profile Update Error:", xhr);
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function(field, messages) {
                    const input = $('#' + field);
                    input.addClass('is-invalid');
                    input.siblings('.invalid-feedback').text(messages[0]).show();
                });
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при обновлении профиля', {showCancelButton: false});
            }
        }
    });
}

function updatePassword() {
    $('.invalid-feedback').hide();
    $('.is-invalid').removeClass('is-invalid');
    
    const currentPassword = $('#current_password').val();
    const password = $('#password').val();
    const passwordConfirmation = $('#password_confirmation').val();
    
    let isValid = true;
    
    if (currentPassword === '') {
        $('#current_password').addClass('is-invalid');
        $('#current_password-error').text('Введите текущий пароль').show();
        isValid = false;
    }
    
    if (password === '') {
        $('#password').addClass('is-invalid');
        $('#password-error').text('Введите новый пароль').show();
        isValid = false;
    } else if (password.length < 6) {
        $('#password').addClass('is-invalid');
        $('#password-error').text('Пароль должен содержать не менее 6 символов').show();
        isValid = false;
    }
    
    if (passwordConfirmation === '') {
        $('#password_confirmation').addClass('is-invalid');
        $('#password_confirmation-error').text('Повторите новый пароль').show();
        isValid = false;
    } else if (password !== passwordConfirmation) {
        $('#password_confirmation').addClass('is-invalid');
        $('#password_confirmation-error').text('Пароли не совпадают').show();
        isValid = false;
    }
    
    if (!isValid) return;
    
    $.ajax({
        url: '/api/auth/password',
        method: 'POST',
        data: {
            current_password: currentPassword,
            password: password,
            password_confirmation: passwordConfirmation
        },
        success: function(response) {
            if (response.success) {
                GidoAlert.success('Пароль успешно изменен', 'Изменения успешно сохранены!', {showCancelButton: false});
                openMenu();
                $('#current_password, #password, #password_confirmation').val('');
            } else {
                if (response.errors) {
                    $.each(response.errors, function(field, messages) {
                        const input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]).show();
                    });
                } else if (response.message === 'Текущий пароль указан неверно') {
                    $('#current_password').addClass('is-invalid');
                    $('#current_password-error').text(response.message).show();
                } else {
                    GidoAlert.error('Ошибка', response.message || 'Произошла ошибка при изменении пароля', {showCancelButton: false});
                    openMenu();
                }
            }
        },
        error: function(xhr) {
            // console.error("Password Update Error:", xhr);
            if (xhr.status === 422 && xhr.responseJSON) {
                if (xhr.responseJSON.message === 'Текущий пароль указан неверно') {
                    $('#current_password').addClass('is-invalid');
                    $('#current_password-error').text(xhr.responseJSON.message).show();
                } else if (xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(field, messages) {
                        const input = $('#' + field);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(messages[0]).show();
                    });
                }
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при изменении пароля', {showCancelButton: false});
                openMenu();
            }
        }
    });
}

function initExcursionPage() {
    setupAudioPlayer('.page-excursion');
    
    $('#comment-submit').on('click', function(e) {
        e.preventDefault();
        submitComment();
    });
    
    $('.mega_link').on('click', function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });
}


function setupAudioPlayer(pageSelector) {
    // console.log(`Настройка аудиоплеера для ${pageSelector}`);
    
    const $audio = $(`${pageSelector} #audio`);
    if (!$audio.length) {
        // console.error(`Аудио элемент не найден на странице ${pageSelector}`);
        return;
    }
    
    const audio = $audio[0];
    const playButton = $(`${pageSelector} #play-button`);
    const progressBar = $(`${pageSelector} #progress-bar`);
    const currentTime = $(`${pageSelector} #current-time`);
    const durationTime = $(`${pageSelector} #duration-time`);
    
    if (!playButton.length || !progressBar.length || !currentTime.length) {
        // console.error(`Не найдены элементы управления аудио на странице ${pageSelector}`);
        return;
    }
    
    playButton.off('click.audioPlayer');
    progressBar.off('input.audioPlayer');
    $audio.off('timeupdate.audioPlayer loadedmetadata.audioPlayer ended.audioPlayer error.audioPlayer loadstart.audioPlayer canplay.audioPlayer play.audioPlayer pause.audioPlayer');
    
    audio.preload = 'metadata';
    
    $audio.on('error.audioPlayer', function() {
        if (audio.error) {
            // console.error(`Код ошибки: ${audio.error.code}, сообщение: ${audio.error.message}`);
        }
    });
    
    $audio.on('loadstart.audioPlayer', function() {
        currentTime.text('Загрузка...');
        if (durationTime.length) {
            durationTime.text('...');
        }
    });
    
    playButton.on('click.audioPlayer', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (audio.paused) {
            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                }).catch(error => {
                    console.error(`Ошибка воспроизведения аудио на странице ${pageSelector}:`, error);
                });
            }
        } else {
            audio.pause();
        }
    });
    
    $audio.on('timeupdate.audioPlayer', function() {
        if (!isNaN(audio.duration) && audio.duration > 0) {
            const percent = (audio.currentTime / audio.duration) * 100;
            progressBar.val(percent);
            currentTime.text(formatTime(audio.currentTime));
            
            if (durationTime.length && durationTime.text() !== formatTime(audio.duration)) {
                durationTime.text(formatTime(audio.duration));
            }
        }
    });
    
    progressBar.on('input.audioPlayer', function() {
        if (!isNaN(audio.duration) && audio.duration > 0) {
            const time = (progressBar.val() / 100) * audio.duration;
            audio.currentTime = time;
        }
    });
    
    $audio.on('loadedmetadata.audioPlayer', function() {
        if (!isNaN(audio.duration)) {
            progressBar.val(0);
            currentTime.text('0:00');
            
            if (durationTime.length) {
                durationTime.text(formatTime(audio.duration));
            }
        }
    });
    
    $audio.on('canplay.audioPlayer', function() {
        if (!isNaN(audio.duration) && currentTime.text() === 'Загрузка...') {
            currentTime.text('0:00');
            if (durationTime.length) {
                durationTime.text(formatTime(audio.duration));
            }
        }
    });
    
    $audio.on('play.audioPlayer', function() {
        playButton.text('⏸');
    });
    
    $audio.on('pause.audioPlayer', function() {
        playButton.text('▶');
    });
    
    $audio.on('ended.audioPlayer', function() {
        playButton.text('▶');
        progressBar.val(0);
        audio.currentTime = 0;
        currentTime.text('0:00');
    });
    
    if (audio.src) {
        // console.log(`Загрузка аудио ${audio.src} для страницы ${pageSelector}`);
        try {
            audio.load();
        } catch (e) {
            // console.error(`Ошибка при загрузке аудио для ${pageSelector}:`, e);
        }
    } else {
        // console.warn(`Источник аудио не установлен для страницы ${pageSelector}`);
    }
}

function loadExcursionDetails(excursionId) {
    $.ajax({
        url: `/api/excursions/${excursionId}`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const excursion = response.excursion;
                
                document.title = `ГидО - ${excursion.title}`;
                
                $('#excursion-title').text(excursion.title);
                $('#excursion-subtitle').text(excursion.short_description || '');
                $('#excursion-description').text(excursion.description || '');
                
                $('#excursion-distance').text(`${excursion.route_length || ''} км`);
                $('#excursion-type').text(excursion.type === 'immersive' ? 'иммерсив' : 'аудиогид');
                $('#excursion-duration').text(`${excursion.duration || ''}`);
                
                if (excursion.main_image) {
                    $('#excursion-image').attr('src', `/storage/${excursion.main_image}`).addClass('block1_image');
                }
                
                $('#excursion-about-title').text('на этой экскурсии можно познакомиться с...');
                
                $('#excursion-about-content').empty();
                
                if ($('#excursion-sights-list').length === 0) {
                    $('#excursion-about-content').append('<div id="excursion-sights-list"></div>');
                }
                
                renderSightsList(response.sightsList);
                
                $('#excursion-start-point').text(excursion.starting_point || '');
                
                if (excursion.map_embed) {
                    $('#excursion-map').attr('src', excursion.map_embed);
                }
                
                if (excursion.audio_preview) {
                    $('.audio_preview .preview').attr('src', `/storage/${excursion.audio_preview}`);
                }
                
                if (excursion.audio_demo) {
                    $('#audio').attr('src', `/storage/${excursion.audio_demo}`);
                    $('#audio')[0].load();
                }
                
                if (excursion.places && excursion.places.length > 0) {
                    const placesContainer = $('#excursion-places');
                    placesContainer.empty();
                    
                    excursion.places.forEach(function(place, index) {
                        const placeHtml = `
                            <div class="sight" id="anchor${index+1}">
                                <div class="left_block">
                                    <img src="${place.image ? '/storage/' + place.image : '/img/excursion/block4_img' + (index % 3 + 1) + '.png'}" alt="" class="sight_image">
                                </div>
                                <div class="right_block">
                                    <h1 class="main_title_block4">${place.name || 'Точка маршрута ' + (index + 1)}</h1>
                                    <p class="quote_block4">${place.short_description || ''}</p>
                                    <p class="descript_block4">${place.description || ''}</p>
                                </div>
                            </div>
                        `;
                        placesContainer.append(placeHtml);
                    });
                    
                    $('#places-block').show();
                } else {
                    $('#places-block').hide();
                }
                
                updateExcursionButtons(excursion, response.purchased);
                
                loadExcursionComments(excursion.id, excursion.approved_comments);
                
                checkAuthForCommentForm(excursion.id);
                
                renderSightsList(response.sightsList);
            } else {
                showPage('404');
            }
        },
        error: function(error) {
            console.error('Ошибка при запросе данных экскурсии:', error);
        }
    });
}

function updateExcursionButtons(excursion, isPurchased) {
    const buttonsContainer = $('#excursion-buttons');
    if (!buttonsContainer.length) return;
    
    buttonsContainer.empty();
    
    if (isPurchased) {
        buttonsContainer.html(`
            <a href="#" class="but2_block1" id="listen-full-button" data-audio-path="${excursion.audio_full ? '/storage/' + excursion.audio_full : ''}">Слушать полную версию</a>
        `);
        
        $('#listen-full-button').off('click.fullButton').on('click.fullButton', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const audioPath = $(this).data('audio-path');
            const audioElement = $('#audio')[0];
            
            $('#audio').attr('src', audioPath);
            
            $('html, body').animate({
                scrollTop: $('#aud').offset().top - 80
            }, 500, function() {
                audioElement.load();
                const playPromise = audioElement.play();
                if (playPromise !== undefined) {
                    playPromise.then(() => {
                    }).catch(error => {
                        console.error('Ошибка воспроизведения полного аудио:', error);
                    });
                }
            });
        });
    } else {
        const priceText = excursion.price ? `${excursion.price} руб.` : 'Цена по запросу';
        buttonsContainer.html(`
            <a href="#" class="but1_block1" id="listen-preview-button">Послушай отрывок</a>
            <p class="apostr">или</p>
            <a href="#" class="but2_block1" id="purchase-button" data-excursion-id="${excursion.id}" data-title="${excursion.title}" data-price="${priceText}">
                <span class="purchase-text">Купить полную версию</span>
                <span class="purchase-price">${priceText}</span>
            </a>
        `);
        
        $('#listen-preview-button').off('click.previewButton').on('click.previewButton', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const audioElement = $('#audio')[0];
            
            $('html, body').animate({
                scrollTop: $('#aud').offset().top - 80
            }, 500, function() {
                if (excursion.audio_demo) {
                    $('#audio').attr('src', `/storage/${excursion.audio_demo}`);
                    audioElement.load();
                    const playPromise = audioElement.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                        }).catch(error => {
                            console.error('Ошибка воспроизведения демо аудио:', error);
                        });
                    }
                } else {
                    console.warn('Демо аудио не найдено для экскурсии');
                }
            });
        });
        
        $('#purchase-button').off('click').on('click', function(e) {
            e.preventDefault();
            const excursionId = $(this).data('excursion-id');
            const title = $(this).data('title');
            const price = $(this).data('price');
            
            if (currentUser) {
                 openPurchaseModal(excursionId, title, price);
            } else {
                GidoAlert.warning('Требуется авторизация', 'Необходимо авторизоваться для покупки экскурсии.');
                navigateTo('login');
            }
        });
    }
}

function openPurchaseModal(excursionId, title, price) {
    document.getElementById('purchase-modal-excursion-id').value = excursionId;
    document.getElementById('purchase-modal-title').textContent = title;
    document.getElementById('purchase-modal-price').textContent = price;
    document.getElementById('purchase-confirmation-modal').style.display = 'block';
    document.getElementById('purchase-overlay').style.display = 'block';
}

function closePurchaseModal() {
    $('#purchase-confirmation-modal').hide();
    $('#purchase-overlay').hide();
}

function purchaseExcursion(excursionId) {
    
    $.ajax({
        url: `/api/user/excursion/${excursionId}/purchase`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                GidoAlert.success('Экскурсия приобретена!', response.message || 'Экскурсия успешно приобретена!', {showCancelButton: false});
                loadExcursionDetails(excursionId);
                closePurchaseModal();
                
                if (window.location.pathname === '/personalAccount') {
                    setTimeout(() => {
                        $(document).trigger('updateUserExcursions');
                    }, 500);
                }
            } else {
                GidoAlert.error('Ошибка покупки', response.message || 'Ошибка при покупке экскурсии.', {showCancelButton: false});
            }
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                GidoAlert.warning('Требуется авторизация', 'Необходимо авторизоваться для покупки экскурсии.', {showCancelButton: false});
                navigateTo('login');
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при покупке экскурсии. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
            }
        }
    });
}

function loadExcursionComments(excursionId, approvedComments) {
    const commentsContainer = $('#excursion-comments');
    commentsContainer.empty();
    
    if (approvedComments && approvedComments.length > 0) {
        approvedComments.forEach(function(comment) {
            const commentHtml = `
                <div class="comm">
                    <div class="my_name_comment">
                        <img src="/img/personalAccount/avatar.png" alt="" class="avatar_comment">
                        <h4 class="title_name">${comment.user.name}</h4>
                        <div class="comment-status">
                            <div class="approved" title="Одобрен"></div>
                        </div>
                    </div>
                    <p class="title_name2">отзыв о экскурсии</p>
                    <p class="descript_comment">${comment.content}</p>
                </div>
            `;
            commentsContainer.append(commentHtml);
        });
    } else {
        commentsContainer.html(`
            <div class="empty-state">
                <div class="empty-state-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\'/%3E%3C/svg%3E');"></div>
                <h3 class="empty-state-title">Пока нет отзывов об этой экскурсии</h3>
                <p class="empty-state-description">Станьте первым, кто поделится своими впечатлениями!</p>
            </div>
        `);
    }
}

function checkAuthForCommentForm(excursionId) {
    $.ajax({
        url: '/api/auth/user',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $.ajax({
                    url: `/api/user/excursion/${excursionId}/has-comment`,
                    method: 'GET',
                    success: function(commentResponse) {
                        if (commentResponse.success && commentResponse.hasComment) {
                            $('#excursion-comments-form').hide();
                            if ($('#excursion-comment-already-exists').length === 0) {
                                $('#excursion-comments').after(
                                    '<div id="excursion-comment-already-exists" class="comment-notification">' +
                                    '<div class="notification-content">' +
                                    '<i class="fas fa-check-circle notification-icon"></i>' +
                                    '<p class="notification-text">Спасибо! Вы уже оставили отзыв для этой экскурсии.</p>' +
                                    '</div></div>'
                                );
                            } else {
                                $('#excursion-comment-already-exists').show();
                            }
                        } else {
                            $.ajax({
                                url: `/api/user/excursion/${excursionId}/check-purchase`,
                                method: 'GET',
                                success: function(purchaseResponse) {
                                    if (purchaseResponse.success && purchaseResponse.hasPurchase) {
                                        $('#excursion-comments-form').show();
                                        $('#excursion-comments-form').data('excursion-id', excursionId);
                                        $('#excursion-comment-already-exists').hide();
                                        
                                        if ($('#excursion-need-purchase').length > 0) {
                                            $('#excursion-need-purchase').hide();
                                        }
                                    } else {
                                        $('#excursion-comments-form').hide();
                                        
                                        if ($('#excursion-need-purchase').length === 0) {
                                            $('#excursion-comments').after(
                                                '<div id="excursion-need-purchase" class="comment-notification">' +
                                                '<div class="notification-content">' +
                                                '<i class="fas fa-info-circle notification-icon" style="color: #f5a742;"></i>' +
                                                '<p class="notification-text">Чтобы оставить отзыв, вам необходимо сначала приобрести эту экскурсию.</p>' +
                                                '</div></div>'
                                            );
                                        } else {
                                            $('#excursion-need-purchase').show();
                                        }
                                    }
                                },
                                error: function() {
                                    $('#excursion-comments-form').hide();
                                }
                            });
                        }
                    },
                    error: function() {
                        $('#excursion-comments-form').hide();
                    }
                });
            } else {
                $('#excursion-comments-form').hide();
                
                if ($('#excursion-need-auth').length === 0) {
                    $('#excursion-comments').after(
                        '<div id="excursion-need-auth" class="comment-notification">' +
                        '<div class="notification-content">' +
                        '<i class="fas fa-user-lock notification-icon" style="color: #3498db;"></i>' +
                        '<p class="notification-text">Чтобы оставить отзыв, вам необходимо <a href="#" onclick="navigateTo(\'login\'); return false;">войти</a> в свой аккаунт.</p>' +
                        '</div></div>'
                    );
                } else {
                    $('#excursion-need-auth').show();
                }
                
                if ($('#excursion-need-purchase').length > 0) {
                    $('#excursion-need-purchase').hide();
                }
                if ($('#excursion-comment-already-exists').length > 0) {
                    $('#excursion-comment-already-exists').hide();
                }
            }
        },
        error: function() {
            $('#excursion-comments-form').hide();
        }
    });
}

function submitComment() {
    let content;
    let excursionId;
    let isImmersive = false;
    let formSelector;
    let submitButton;
    
    if ($('#immersive-comments-form:visible').length > 0) {
        content = $('#immersive-comment-content').val().trim();
        excursionId = $('#immersive-comments-form').data('excursion-id');
        isImmersive = true;
        formSelector = '#immersive-comments-form';
        submitButton = $('#immersive-comment-submit');
    } else {
        content = $('#comment-content').val().trim();
        excursionId = $('#excursion-comments-form').data('excursion-id');
        formSelector = '#excursion-comments-form';
        submitButton = $('#comment-submit');
    }
    
    if (content === '') {
        GidoAlert.warning('Поле пустое', 'Пожалуйста, введите текст комментария', {showCancelButton: false});
        return;
    }
    
    if (!excursionId) {
        GidoAlert.error('Ошибка', 'Ошибка: не удалось определить ID экскурсии', {showCancelButton: false});
        return;
    }
    
    if (submitButton.prop('disabled')) {
        return;
    }
    
    submitButton.prop('disabled', true);
    submitButton.text('Отправка...');
    
    $.ajax({
        url: `/api/user/excursion/${excursionId}/comment`,
        method: 'POST',
        data: {
            content: content
        },
        success: function(response) {
            if (response.success) {
                GidoAlert.success('Комментарий отправлен', response.message || 'Ваш комментарий отправлен на модерацию', {showCancelButton: false});
                if (isImmersive) {
                    $('#immersive-comment-content').val('');
                    $('#immersive-comments-form').hide();
                    if ($('#immersive-comment-already-exists').length === 0) {
                        $('#immersive-comments').after(
                            '<div id="immersive-comment-already-exists" class="comment-notification">' +
                            '<div class="notification-content">' +
                            '<i class="fas fa-check-circle notification-icon"></i>' +
                            '<p class="notification-text">Спасибо! Вы уже оставили отзыв для этого иммерсива.</p>' +
                            '</div></div>'
                        );
                    } else {
                        $('#immersive-comment-already-exists').show();
                    }
                } else {
                    $('#comment-content').val('');
                    $('#excursion-comments-form').hide();
                    if ($('#excursion-comment-already-exists').length === 0) {
                        $('#excursion-comments').after(
                            '<div id="excursion-comment-already-exists" class="comment-notification">' +
                            '<div class="notification-content">' +
                            '<i class="fas fa-check-circle notification-icon"></i>' +
                            '<p class="notification-text">Спасибо! Вы уже оставили отзыв для этой экскурсии.</p>' +
                            '</div></div>'
                        );
                    } else {
                        $('#excursion-comment-already-exists').show();
                    }
                }
            } else {
                GidoAlert.error('Ошибка отправки', response.message || 'Ошибка при отправке комментария', {showCancelButton: false});
                submitButton.prop('disabled', false);
                submitButton.text('Отправить');
            }
        },
        error: function(xhr) {
            submitButton.prop('disabled', false);
            submitButton.text('Отправить');
            
            if (xhr.status === 401) {
                GidoAlert.warning('Требуется авторизация', 'Необходимо авторизоваться для отправки комментария', {showCancelButton: false});
                navigateTo('login');
            } else if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.message) {
                GidoAlert.error('Ошибка валидации', xhr.responseJSON.message, {showCancelButton: false});
            } else {
                GidoAlert.error('Ошибка', 'Произошла ошибка при отправке комментария. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
            }
        }
    });
}

function initBookingPage() {
    setupAudioPlayer('.page-booking');
    
    initBookingForm();
    
    initPaymentFields();
    
    $('#booking-form').on('submit', function(e) {
        e.preventDefault();
        
        if (validateBookingForm()) {
            processPayment();
        }
    });
    

}

function initPaymentFields() {
    $('#card-number').on('input', function() {
        const value = $(this).val().replace(/\D/g, '');
        const formattedValue = formatCardNumber(value);
        $(this).val(formattedValue);
    });
    
    $('#card-expiry').on('input', function() {
        const value = $(this).val().replace(/\D/g, '');
        const formattedValue = formatCardExpiry(value);
        $(this).val(formattedValue);
    });
    
    $('#card-cvv').on('input', function() {
        const value = $(this).val().replace(/\D/g, '');
        $(this).val(value);
    });
}

function formatCardNumber(value) {
    const regex = /(\d{1,4})(\d{1,4})?(\d{1,4})?(\d{1,4})?/;
    const groups = value.match(regex);
    
    if (!groups) return value;
    
    let formatted = groups[1] || '';
    if (groups[2]) formatted += ' ' + groups[2];
    if (groups[3]) formatted += ' ' + groups[3];
    if (groups[4]) formatted += ' ' + groups[4];
    
    return formatted;
}

function formatCardExpiry(value) {
    if (value.length >= 3) {
        return value.slice(0, 2) + '/' + value.slice(2, 4);
    } else if (value.length === 2) {
        return value + '/';
    }
    return value;
}

function initBookingForm() {
    fillBookingFormIfAuthenticated();
}

function validateBookingForm() {
    let isValid = true;
    
    const name = $('#booking-name').val().trim();
    if (!name) {
        $('#booking-name-error').show();
        isValid = false;
    } else {
        $('#booking-name-error').hide();
    }
    
    const email = $('#booking-email').val().trim();
    if (!email || !isValidEmail(email)) {
        $('#booking-email-error').show();
        isValid = false;
    } else {
        $('#booking-email-error').hide();
    }
    
    const peopleCount = parseInt($('#booking-people-count').val());
    if (!peopleCount || peopleCount < 1) {
        $('#booking-people-count-error').show();
        isValid = false;
    } else {
        $('#booking-people-count-error').hide();
    }
    
    const cardNumber = $('#card-number').val().trim().replace(/\s/g, '');
    if (!cardNumber || cardNumber.length < 16) {
        $('#card-number-error').show();
        isValid = false;
    } else {
        $('#card-number-error').hide();
    }
    
    const cardExpiry = $('#card-expiry').val().trim();
    if (!cardExpiry || cardExpiry.length < 5) {
        $('#card-expiry-error').show();
        isValid = false;
    } else {
        $('#card-expiry-error').hide();
    }
    
    const cardCvv = $('#card-cvv').val().trim();
    if (!cardCvv || cardCvv.length < 3) {
        $('#card-cvv-error').show();
        isValid = false;
    } else {
        $('#card-cvv-error').hide();
    }
    
    return isValid;
}

let isBookingInProgress = false;

function processPayment() {
    if (isBookingInProgress) {
        console.log('Бронирование уже в процессе, игнорируем повторный запрос');
        return;
    }
    
    const submitButton = $('#booking-form button[type="submit"]');
    const originalText = submitButton.text();
    
    isBookingInProgress = true;
    submitButton.prop('disabled', true);
    submitButton.text('Обработка платежа...');
    
    const immersiveId = $('#immersive_id').val();
    const timeId = $('#time_id').val();
    const dateId = $('#date_id').val();
    
    if (immersiveId && timeId && GIDO_IMMERSIVES_DATA[immersiveId]) {
        const immersive = GIDO_IMMERSIVES_DATA[immersiveId];
        let timeFound = false;
        
        for (let date of immersive.dates) {
            const targetTime = date.times.find(t => t.id == timeId);
            if (targetTime && !targetTime.is_available) {
                isBookingInProgress = false;
                submitButton.prop('disabled', false);
                submitButton.text(originalText);
                closeBookingMenu();
                
                GidoAlert.error('Время недоступно', 'Это время уже забронировано. Пожалуйста, выберите другое время.', {showCancelButton: false});
                
                removeBookedTimeFromUI(timeId);
                return;
            }
            if (targetTime) {
                timeFound = true;
                break;
            }
        }
        
        if (!timeFound) {
            isBookingInProgress = false;
            submitButton.prop('disabled', false);
            submitButton.text(originalText);
            closeBookingMenu();
            
            GidoAlert.error('Ошибка', 'Выбранное время не найдено. Пожалуйста, обновите страницу и попробуйте снова.', {showCancelButton: false});
            return;
        }
    }
    
    const formData = {
        immersive_id: immersiveId,
        date_id: dateId,
        time_id: timeId,
        name: $('#booking-name').val().trim(),
        email: $('#booking-email').val().trim(),
        starting_point: $('#booking-starting-point').val().trim(),
        people_count: parseInt($('#booking-people-count').val()),
        card_number: $('#card-number').val().trim().replace(/\s/g, ''),
        card_expiry: $('#card-expiry').val().trim(),
        card_cvv: $('#card-cvv').val().trim()
    };
    
    $.ajax({
        url: '/api/user/booking',
        method: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            isBookingInProgress = false;
            submitButton.prop('disabled', false);
            submitButton.text(originalText);
            
            if (response.success) {
                if (immersiveId && timeId) {
                    const updated = updateImmersiveTimeAvailability(immersiveId, timeId, false);
                    if (updated) {
                        console.log(`Время ${timeId} помечено как недоступное в локальных данных`);
                        
                        removeBookedTimeFromUI(timeId);
                    }
                }
                
                closeBookingMenu();
                
                GidoAlert.success('Оплата завершена!', 'Оплата прошла успешно! Бронирование подтверждено.', {showCancelButton: false}).then(() => {
                    navigateTo('personalAccount');
                    
                    setTimeout(() => {
                        $(document).trigger('updateUserExcursions');
                    }, 500);
                });
            } else {
                closeBookingMenu();
                
                const errorMessage = response.message || 'Произошла ошибка при бронировании';
                GidoAlert.error('Ошибка бронирования', errorMessage, {showCancelButton: false});
                
                if (response.errors && response.errors.time_id && immersiveId && timeId) {
                    updateImmersiveTimeAvailability(immersiveId, timeId, false);
                    removeBookedTimeFromUI(timeId);
                }
            }
        },
        error: function(xhr) {
            isBookingInProgress = false;
            submitButton.prop('disabled', false);
            submitButton.text(originalText);
            
            closeBookingMenu();
            
            let errorMessage = 'Произошла ошибка при обработке запроса';
            
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                const firstError = Object.values(errors)[0];
                if (Array.isArray(firstError) && firstError.length > 0) {
                    errorMessage = firstError[0];
                }
                
                if (errors.time_id && immersiveId && timeId) {
                    updateImmersiveTimeAvailability(immersiveId, timeId, false);
                    removeBookedTimeFromUI(timeId);
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            GidoAlert.error('Ошибка бронирования', errorMessage, {showCancelButton: false});
        }
    });
}

function loadImmersiveDetails(immersiveId) {
    
    if (GIDO_IMMERSIVES_DATA[immersiveId]) {
        const immersiveData = GIDO_IMMERSIVES_DATA[immersiveId];
        const excursion = immersiveData.excursion;
        
        
        document.title = `ГидО - Бронирование ${excursion.title}`;

        $('#immersive-title').text(excursion.title);
        $('#immersive-subtitle').text(excursion.subtitle || '');
        $('#immersive-description').text(excursion.description || '');

        $('#immersive-distance').text(`${excursion.distance || ''} км`);
        $('#immersive-duration').text(`${excursion.duration || ''}`);

        const pricePerPerson = immersiveData.price_per_person || 0;
        $('#immersive-price').text(`Стоимость: ${pricePerPerson} руб/чел.`);
        $('#price_per_person').data('price', pricePerPerson);

        const immersiveAboutContent = $('#immersive-about-content');
        immersiveAboutContent.empty();
        
        if (immersiveData.main_description || immersiveData.additional_description) {
            if (immersiveData.main_description) {
                immersiveAboutContent.append(`<p class="descript1_block2">${immersiveData.main_description}</p>`);
            }
            if (immersiveData.additional_description) {
                immersiveAboutContent.append(`<p class="descript2_block2">${immersiveData.additional_description}</p>`);
            }
        } else {
            immersiveAboutContent.append(`
                <p class="descript1_block2">Информация о прогулке будет добавлена в ближайшее время.</p>
            `);
        }

        if (immersiveData.audio_demo || immersiveData.audio_preview) {
            const audioContainer = $('#immersive-audio-demo');
            audioContainer.show();
            
            if (immersiveData.audio_preview) {
                $('#immersive-audio-preview-img').attr('src', immersiveData.audio_preview);
            }
            
            if (immersiveData.audio_demo) {
                $('#immersive-audio').attr('src', immersiveData.audio_demo);
                initImmersiveAudioPlayer();
            }
        } else {
            $('#immersive-audio-demo').hide();
        }

        if (excursion.image_path) {
            $('#immersive-image').attr('src', excursion.image_path).addClass('block1_image');
        } else {
            $('#immersive-image').attr('src', '/img/excursion/block1_img1.png').addClass('block1_image');
        }
        
        let startingPoint = excursion.starting_point || 'Астраханский кремль';
        $('#booking-starting-point').val(startingPoint);
        $('#immersive-start-point').text(startingPoint);

        if (excursion.map_embed) {
            $('#immersive-map').attr('src', excursion.map_embed);
        } else {
            $('#immersive-map').attr('src', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d88124.40108446526!2d47.9601279331609!3d46.35446825169958!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x41a90f65a17a041b%3A0xa2d9c19c92f3b38!2z0JDRgdGC0YDQsNC40L3RjA!5e0!3m2!1sru!2sru!4v1620000000000!5m2!1sru!2sru');
        }

        $('#immersive_id').val(immersiveData.id);

        loadImmersiveDates(immersiveData.id);

        loadImmersiveComments(excursion.id, []);
        
        checkAuthForImmersiveCommentForm(excursion.id);

        fillBookingFormIfAuthenticated();
        
        $('#immersive-listen-preview-button').off('click.immersivePreview').on('click.immersivePreview', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const audioElement = document.getElementById('immersive-audio');
            
            $('html, body').animate({
                scrollTop: $('#immersive-audio-demo').offset().top - 80
            }, 500, function() {
                if (immersiveData.audio_demo) {
                    $('#immersive-audio').attr('src', immersiveData.audio_demo);
                    audioElement.load();
                    const playPromise = audioElement.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                        }).catch(error => {
                            console.error('Ошибка воспроизведения демо аудио иммерсива:', error);
                        });
                    }
                } else {
                    console.warn('Демо аудио не найдено для иммерсива');
                }
            });
        });
        
        return;
    }

    $.ajax({
        url: `/api/excursion/${immersiveId}`,
        method: 'GET',
        success: function(response) {
            if (response.success && response.excursion && response.excursion.type === 'immersive') {
                const excursion = response.excursion;
                const immersive = response.immersive || {};

                document.title = `ГидО - Бронирование ${excursion.title}`;

                $('#immersive-title').text(excursion.title);
                $('#immersive-subtitle').text(excursion.short_description || '');
                $('#immersive-description').text(excursion.description || '');

                $('#immersive-distance').text(`${excursion.route_length || ''} км`);
                $('#immersive-duration').text(`${excursion.duration || ''}`);

                const pricePerPerson = immersive.price_per_person || excursion.price || 0;
                $('#immersive-price').text(`Стоимость: ${pricePerPerson} руб/чел.`);
                $('#price_per_person').data('price', pricePerPerson);

                const immersiveAboutContent = $('#immersive-about-content');
                immersiveAboutContent.empty();
                
                if (immersive && (immersive.main_description || immersive.additional_description)) {
                    if (immersive.main_description) {
                        immersiveAboutContent.append(`<p class="descript1_block2">${immersive.main_description}</p>`);
                    }
                    if (immersive.additional_description) {
                        immersiveAboutContent.append(`<p class="descript2_block2">${immersive.additional_description}</p>`);
                    }
                } else {
                    immersiveAboutContent.append(`
                        <p class="descript1_block2">Информация о прогулке будет добавлена в ближайшее время.</p>
                    `);
                }

                // Обработка аудио файлов
                if (immersive.audio_demo_url || immersive.audio_preview_url) {
                    const audioContainer = $('#immersive-audio-demo');
                    audioContainer.show();
                    
                    if (immersive.audio_preview_url) {
                        $('#immersive-audio-preview-img').attr('src', immersive.audio_preview_url);
                    }
                    
                    if (immersive.audio_demo_url) {
                        $('#immersive-audio').attr('src', immersive.audio_demo_url);
                        initImmersiveAudioPlayer();
                    }
                } else {
                    $('#immersive-audio-demo').hide();
                }

                if (excursion.main_image) {
                    $('#immersive-image').attr('src', `/storage/${excursion.main_image}`).addClass('block1_image');
                } else {
                    $('#immersive-image').attr('src', '/img/excursion/block1_img1.png').addClass('block1_image');
                }
                
                let startingPoint = excursion.starting_point || 'Астраханский кремль';
                $('#booking-starting-point').val(startingPoint);
                $('#immersive-start-point').text(startingPoint);

                if (excursion.map_embed) {
                    $('#immersive-map').attr('src', excursion.map_embed);
                } else {
                    $('#immersive-map').attr('src', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d88124.40108446526!2d47.9601279331609!3d46.35446825169958!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x41a90f65a17a041b%3A0xa2d9c19c92f3b38!2z0JDRgdGC0YDQsNC40L3RjA!5e0!3m2!1sru!2sru!4v1620000000000!5m2!1sru!2sru');
                }

                $('#immersive_id').val(immersive.id || 0);

                if (immersive.id) {
                    loadImmersiveDates(immersive.id);
                } else {
                    showDummyDates();
                }

                loadImmersiveComments(excursion.id, excursion.approved_comments);
                
                checkAuthForImmersiveCommentForm(excursion.id);

                fillBookingFormIfAuthenticated();
                
                $('#immersive-listen-preview-button').off('click.immersivePreview').on('click.immersivePreview', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const audioElement = document.getElementById('immersive-audio');
                    
                    // Скроллим к аудиоплееру
                    $('html, body').animate({
                        scrollTop: $('#immersive-audio-demo').offset().top - 80
                    }, 500, function() {
                        if (immersive.audio_demo_url) {
                            $('#immersive-audio').attr('src', immersive.audio_demo_url);
                            audioElement.load();
                            const playPromise = audioElement.play();
                            if (playPromise !== undefined) {
                                playPromise.then(() => {
                                }).catch(error => {
                                    console.error('Ошибка воспроизведения демо аудио иммерсива:', error);
                                });
                            }
                        } else {
                            console.warn('Демо аудио не найдено для иммерсива');
                        }
                    });
                });

            } else {
                showPage('404');
            }
        },
        error: function(error) {
            showDummyDates();
            fillBookingFormIfAuthenticated();
        }
    });
}

function loadImmersiveDates(immersiveId, useDummyData = false) {
    
    const datesContainer = $('#booking-dates');
    
    if (GIDO_IMMERSIVES_DATA[immersiveId] && GIDO_IMMERSIVES_DATA[immersiveId].dates) {
        const dates = GIDO_IMMERSIVES_DATA[immersiveId].dates;
        
        if (dates.length > 0) {
            datesContainer.empty();

            dates.forEach(function(date, index) {
                const dateButton = $(`<button id="date-${date.id}" onclick="selectDate(${date.id})" class="switch">${date.display_date}</button>`);
                datesContainer.append(dateButton);

                if (index === 0) {
                    dateButton.addClass('active');
                    $('#date_id').val(date.id);
                    $('#booking-date-display').val(date.display_date);
                    loadImmersiveTimes(date.id);
                }
            });
            
            window.selectDate = function(dateId) {
                $('#booking-dates .switch').removeClass('active');
                $(`#date-${dateId}`).addClass('active');

                $('#date_id').val(dateId);
                
                const selectedDate = dates.find(d => d.id == dateId);
                const dateText = selectedDate ? selectedDate.display_date : '';
                $('#booking-date-display').val(dateText);

                $('#time_id').val('');
                $('#booking-time-display').val('');
                $('#total_price_display').text('0 руб.');
                $('#total_price_input').val(0);
                $('#booking-people-count').val(1);

                loadImmersiveTimes(dateId);
            };
            
            return;
        }
    }
    
    if (useDummyData) {
        showDummyDates();
    } else {
        datesContainer.html('<div class="no-dates-message">К сожалению, нет доступных дат для бронирования. Пожалуйста, посетите нас позже.</div>');
    }
}

function showDummyDates() {
    const datesContainer = $('#booking-dates');
    datesContainer.empty();

    const today = new Date();
    for (let i = 0; i < 6; i++) {
        const date = new Date(today);
        date.setDate(today.getDate() + i);
        const formattedDate = date.toLocaleDateString('ru-RU', {day: 'numeric', month: 'long'});
        const dummyDateId = `dummy-${i+1}`;
        const dateButton = $(`<button id="date-${dummyDateId}" onclick="selectDate('${dummyDateId}')" class="switch">${formattedDate}</button>`);
        datesContainer.append(dateButton);
        
        if (i === 0) {
            dateButton.addClass('active');
            $('#date_id').val(dummyDateId);
            $('#booking-date-display').val(formattedDate);
            showDummyTimes(dummyDateId);
        }
    }
    
     if (typeof window.selectDate !== 'function') {
        window.selectDate = function(dateId) {
            $('#booking-dates .switch').removeClass('active');
            $(`#date-${dateId}`).addClass('active');

            if (typeof dateId === 'string' && dateId.startsWith('dummy-')) {
                $('#date_id').val(dateId);
            } else {
                $('#date_id').val(dateId);
            }
            
            const dateText = $(`#date-${dateId}`).text();
            $('#booking-date-display').val(dateText);

            $('#time_id').val('');
            $('#booking-time-display').val('');
            $('#total_price_display').text('0 руб.');
            $('#total_price_input').val(0);
            $('#booking-people-count').val(1);

            showDummyTimes(dateId);
        };
    }
}

let lastSelectedTimeId = null;
let timeSelectionTimeout = null;

function loadImmersiveTimes(dateId) {
    let cleanDateId = dateId;
    if (typeof dateId === 'string' && dateId.startsWith('date-')) {
        cleanDateId = dateId.replace('date-', '');
    }
        
    const timesContainer = $('#booking-times');
    
    let foundTimes = [];
    let currentImmersiveId = null;
    
    for (const immersiveId in GIDO_IMMERSIVES_DATA) {
        const immersive = GIDO_IMMERSIVES_DATA[immersiveId];
        const targetDate = immersive.dates.find(d => d.id == cleanDateId);
        if (targetDate) {
            foundTimes = (targetDate.times || []).filter(time => time.is_available === true);
            currentImmersiveId = immersiveId;
            break;
        }
    }
    
    if (foundTimes.length > 0) {
        timesContainer.empty();
        
        foundTimes.forEach(function(time) {
            const timeHtml = `
                <div id="time-${time.id}" class="main_title">
                    <a href="#" class="time_booking" onclick="selectTimeWithDebounce('${time.id}', '${time.formatted_time}')">${time.formatted_time}</a>
                </div>
            `;
            timesContainer.append(timeHtml);
        });
        
        if (typeof window.selectTime !== 'function') {
            window.selectTime = function(timeId, timeText) {
                selectTimeWithDebounce(timeId, timeText);
            };
        }
        
        return;
    }
    
    if (typeof dateId === 'string' && dateId.startsWith('dummy-')) {
        showDummyTimes(dateId);
        return;
    }

    timesContainer.html('<div class="no-times-message">Для выбранной даты нет доступных сеансов. Пожалуйста, выберите другую дату.</div>');
    
    if (typeof window.selectTime !== 'function') {
        window.selectTime = function(timeId, timeText) {
            selectTimeWithDebounce(timeId, timeText);
        };
    }
}

// Функция с debounce для предотвращения быстрых повторных кликов
function selectTimeWithDebounce(timeId, timeText) {
    if (lastSelectedTimeId === timeId) {
        return;
    }
    
    if (timeSelectionTimeout) {
        clearTimeout(timeSelectionTimeout);
    }
    
    timeSelectionTimeout = setTimeout(() => {
        lastSelectedTimeId = null;
    }, 2000);
    
    lastSelectedTimeId = timeId;
    
    selectTime(timeId, timeText);
}

function selectTime(timeId, timeText) {
    const immersiveId = $('#immersive_id').val();
    if (immersiveId && GIDO_IMMERSIVES_DATA[immersiveId]) {
        const immersive = GIDO_IMMERSIVES_DATA[immersiveId];
        let timeAvailable = false;
        
        for (let date of immersive.dates) {
            const targetTime = date.times.find(t => t.id == timeId);
            if (targetTime && targetTime.is_available) {
                timeAvailable = true;
                break;
            }
        }
        
        if (!timeAvailable) {
            GidoAlert.error('Время недоступно', 'Это время уже забронировано. Пожалуйста, выберите другое время.', {showCancelButton: false});
            removeBookedTimeFromUI(timeId);
            lastSelectedTimeId = null;
            return;
        }
    }
    
    $('#time_id').val(timeId);
    $('#booking-time-display').val(timeText);

    openBookingMenu();

    calculateTotal();
}

function showDummyTimes(dateId) {
    const timesContainer = $('#booking-times');
    timesContainer.empty();
    
    const times = ['11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
    
    times.forEach(function(time, index) {
        const dummyTimeId = `dummy-${dateId}-${index+1}`;
        const timeHtml = `
            <div id="time-${dummyTimeId}" class="main_title">
                <a href="#" class="time_booking" onclick="selectTime('${dummyTimeId}', '${time}')">${time}</a>
            </div>
        `;
        timesContainer.append(timeHtml);
    });
    
    if (typeof window.selectTime !== 'function') {
        window.selectTime = function(timeId, timeText) {
            $('#time_id').val(timeId);
            $('#booking-time-display').val(timeText);

            openBookingMenu();

            calculateTotal();
        };
    }
}

function loadImmersiveComments(immersiveId, approvedComments) {
    const commentsContainer = $('#immersive-comments');
    commentsContainer.empty();
    
    if (approvedComments && approvedComments.length > 0) {
        approvedComments.forEach(function(comment) {
            const commentHtml = `
                <div class="comm">
                    <div class="my_name_comment">
                        <img src="/img/personalAccount/avatar.png" alt="" class="avatar_comment">
                        <h4 class="title_name">${comment.user.name}</h4>
                        <div class="comment-status">
                            <div class="approved" title="Одобрен"></div>
                        </div>
                    </div>
                    <p class="title_name2">отзыв о иммерсиве</p>
                    <p class="descript_comment">${comment.content}</p>
                </div>
            `;
            commentsContainer.append(commentHtml);
        });
    } else {
        commentsContainer.html(`
            <div class="empty-state">
                <div class="empty-state-icon" style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'%236b7280\'%3E%3Cpath stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z\'/%3E%3C/svg%3E');"></div>
                <h3 class="empty-state-title">Пока нет отзывов об этом иммерсивном опыте</h3>
                <p class="empty-state-description">Станьте первым, кто поделится своими впечатлениями!</p>
            </div>
        `);
    }
}

function calculateTotal() {
    const peopleCountInput = $('#booking-people-count');
    let peopleCount = parseInt(peopleCountInput.val()) || 1;

    if (peopleCount < 1) {
        peopleCount = 1;
        peopleCountInput.val(1);
    }

    const pricePerPerson = parseFloat($('#price_per_person').data('price')) || 0;
    const totalPrice = peopleCount * pricePerPerson;
    
    $('#total_price_display').text(totalPrice.toFixed(0) + ' руб.');
    $('#total_price_input').val(totalPrice);
}

function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60).toString().padStart(2, '0');
    return `${minutes}:${secs}`;
}

function initNewsPage() {
}


$(document).on('submit', '#booking-form', function(e) {
    e.preventDefault();
    
    let isValid = true;
    $('.is-invalid').removeClass('is-invalid');
    
    const name = $('#booking-name').val().trim();
    const email = $('#booking-email').val().trim();
    const peopleCount = parseInt($('#booking-people-count').val()) || 0;
    
    if (name === '') {
        $('#booking-name').addClass('is-invalid');
        $('#booking-name-error').text('Пожалуйста, введите имя').show();
        isValid = false;
    }
    
    if (email === '') {
        $('#booking-email').addClass('is-invalid');
        $('#booking-email-error').text('Пожалуйста, введите email').show();
        isValid = false;
    } else if (!isValidEmail(email)) {
        $('#booking-email').addClass('is-invalid');
        $('#booking-email-error').text('Пожалуйста, введите корректный email').show();
        isValid = false;
    }
    
    if (peopleCount < 1) {
        $('#booking-people-count').addClass('is-invalid');
        $('#booking-people-count-error').text('Укажите количество человек (минимум 1)').show();
        isValid = false;
    }
    
    if (isValid) {
        const formData = {
            immersive_id: $('#immersive_id').val(),
            date_id: $('#date_id').val(),
            time_id: $('#time_id').val(),
            name: name,
            email: email,
            starting_point: $('#booking-starting-point').val(),
            people_count: peopleCount,
            total_price: $('#total_price_input').val()
        };
        
        $.ajax({
            url: '/api/user/booking',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    closeBookingMenu();
                    
                    GidoAlert.success('Оплата прошла успешно!', 'Бронирование подтверждено. Экскурсия добавлена в ваш профиль.', {showCancelButton: false})
                    .then(() => {
                        navigateTo('personalAccount');
                        
                        setTimeout(() => {
                            $(document).trigger('updateUserExcursions');
                        }, 500);
                    });
                } else {
                    GidoAlert.error('Ошибка бронирования', 'Произошла ошибка при создании бронирования. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
                }
            },
            error: function(xhr) {
                if (xhr.status === 422 && xhr.responseJSON) {
                    const errors = xhr.responseJSON.errors;
                    for (const field in errors) {
                        $(`#booking-${field}`).addClass('is-invalid');
                        $(`#booking-${field}-error`).text(errors[field][0]).show();
                    }
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при создании бронирования. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
                }
            }
        });
    }
});

$(document).on('input change', '#booking-people-count', function() {
    calculateTotal();
});

function isValidEmail(email) {
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}


function renderSightsList(sightsList) {
    const container = $('#excursion-sights-list');
    container.empty();
    
    if (sightsList && sightsList.length > 0) {
        sightsList.forEach(function(sight, index) {
            const anchorId = `anchor${index + 1}`;
            const html = `<a href="#${anchorId}" class="mega_link sight-anchor-link" data-anchor="${anchorId}">${index + 1}. ${sight.name}</a><br>`;
            container.append(html);
        });
        
        container.find('.sight-anchor-link').on('click', function(e) {
            e.preventDefault();
            const anchorId = $(this).data('anchor');
            const targetElement = $(`#${anchorId}`);
            
            if (targetElement.length) {
                $('html, body').animate({
                    scrollTop: targetElement.offset().top - 80
                }, 800);
            }
        });
    } else {
        container.append('<p>Информация о местах экскурсии будет доступна позже</p>');
    }
}


function fillBookingFormIfAuthenticated() {
    $.ajax({
        url: '/api/auth/user',
        method: 'GET',
        success: function(response) {
            if (response.success && response.user) {
                const user = response.user;
                if (!$('#booking-email').val()) {
                    $('#booking-email').val(user.email);
                }
            }
        },
        error: function() {
            // console.log('Не удалось получить данные пользователя для предзаполнения формы.');
        }
    });
}

function selectDate(dateId) {
    $('#booking-dates .switch').removeClass('active');
    $(`#${dateId}`).addClass('active');

    $('#date_id').val(dateId);
    
    const dateText = $(`#${dateId}`).text();
    $('#booking-date-display').val(dateText);

    $('#time_id').val('');
    $('#booking-time-display').val('');
    $('#total_price_display').text('0 руб.');
    $('#total_price_input').val(0);
    $('#booking-people-count').val(1);
    
    $('#booking-times').empty();
    
    let times;
    
    switch(dateId) {
        case 'date-1':
            times = ['10:00', '12:00', '14:00', '16:00'];
            break;
        case 'date-2':
            times = ['09:00', '11:00', '13:00', '15:00', '17:00'];
            break;
        case 'date-3':
            times = ['12:00', '15:00', '18:00'];
            break;
        case 'date-4':
            times = ['10:30', '13:30', '16:30'];
            break;
        case 'date-5':
            times = ['09:00', '12:00', '15:00', '18:00'];
            break;
        case 'date-6':
            times = ['11:00', '14:00', '17:00'];
            break;
        default:
            times = ['12:00', '14:00', '16:00'];
    }
    
    times.forEach((time, index) => {
        const timeId = `time-${dateId}-${index}`;
        const timeHtml = `
            <div id="${timeId}" class="main_title">
                <a href="#" class="time_booking" onclick="selectTime('${timeId}', '${time}')">${time}</a>
            </div>
        `;
        $('#booking-times').append(timeHtml);
    });
}

function openBookingMenu() {
    $('#booking-menu').show();
    $('#booking-overlay').show();
}

function closeBookingMenu() {
    $('#booking-menu').hide();
    $('#booking-overlay').hide();
    
    isBookingInProgress = false;
    lastSelectedTimeId = null;
    if (timeSelectionTimeout) {
        clearTimeout(timeSelectionTimeout);
        timeSelectionTimeout = null;
    }
}

function calculateTotal() {
    const peopleCountInput = $('#booking-people-count');
    let peopleCount = parseInt(peopleCountInput.val()) || 1;

    if (peopleCount < 1) {
        peopleCount = 1;
        peopleCountInput.val(1);
    }

    const pricePerPerson = parseFloat($('#price_per_person').data('price')) || 0;
    const totalPrice = peopleCount * pricePerPerson;
    
    $('#total_price_display').text(totalPrice.toFixed(0) + ' руб.');
    $('#total_price_input').val(totalPrice);
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}


function checkAuthForImmersiveCommentForm(excursionId) {
    $.ajax({
        url: '/api/auth/user',
        method: 'GET',
        success: function(response) {
            if (response.success && response.user) {
                $.ajax({
                    url: `/api/user/excursion/${excursionId}/has-comment`,
                    method: 'GET',
                    success: function(commentResponse) {
                        if (commentResponse.success && commentResponse.hasComment) {
                            $('#immersive-comments-form').hide();
                            if ($('#immersive-comment-already-exists').length === 0) {
                                $('#immersive-comments').after(
                                    '<div id="immersive-comment-already-exists" class="comment-notification">' +
                                    '<div class="notification-content">' +
                                    '<i class="fas fa-check-circle notification-icon"></i>' +
                                    '<p class="notification-text">Спасибо! Вы уже оставили отзыв для этого иммерсива.</p>' +
                                    '</div></div>'
                                );
                            } else {
                                $('#immersive-comment-already-exists').show();
                            }
                        } else {
                            $.ajax({
                                url: `/api/user/bookings/check/${excursionId}`,
                                method: 'GET',
                                success: function(bookingResponse) {
                                    if (bookingResponse.success && bookingResponse.hasBooking) {
                                        $('#immersive-comments-form').show();
                                        $('#immersive-comments-form').data('excursion-id', excursionId);
                                        $('#immersive-comments-need-booking').hide();
                                        $('#immersive-comments-need-auth').hide();
                                        $('#immersive-comment-already-exists').hide();
                                    } else {
                                        $('#immersive-comments-form').hide();
                                        $('#immersive-comments-need-booking').show();
                                        if ($('#immersive-comments-need-booking').html() === '') {
                                            $('#immersive-comments-need-booking').html(
                                                '<div class="notification-content">' +
                                                '<i class="fas fa-info-circle notification-icon" style="color: #f5a742;"></i>' +
                                                '<p class="notification-text">Чтобы оставить отзыв, вам необходимо сначала забронировать и посетить этот иммерсивный опыт.</p>' +
                                                '</div>'
                                            );
                                        }
                                        $('#immersive-comments-need-auth').hide();
                                        $('#immersive-comment-already-exists').hide();
                                    }
                                },
                                error: function() {
                                    $('#immersive-comments-form').hide();
                                    $('#immersive-comments-need-booking').show();
                                    if ($('#immersive-comments-need-booking').html() === '') {
                                        $('#immersive-comments-need-booking').html(
                                            '<div class="notification-content">' +
                                            '<i class="fas fa-info-circle notification-icon" style="color: #f5a742;"></i>' +
                                            '<p class="notification-text">Чтобы оставить отзыв, вам необходимо сначала забронировать и посетить этот иммерсивный опыт.</p>' +
                                            '</div>'
                                        );
                                    }
                                    $('#immersive-comments-need-auth').hide();
                                }
                            });
                        }
                    },
                    error: function() {
                        $('#immersive-comments-form').hide();
                    }
                });
            } else {
                $('#immersive-comments-form').hide();
                $('#immersive-comments-need-booking').hide();
                $('#immersive-comments-need-auth').show();
                if ($('#immersive-comments-need-auth').html() === '') {
                    $('#immersive-comments-need-auth').html(
                        '<div class="notification-content">' +
                        '<i class="fas fa-user-lock notification-icon" style="color: #3498db;"></i>' +
                        '<p class="notification-text">Чтобы оставить отзыв, вам необходимо <a href="#" onclick="navigateTo(\'login\'); return false;">войти</a> в свой аккаунт.</p>' +
                        '</div>'
                    );
                }
            }
        },
        error: function() {
            $('#immersive-comments-form').hide();
            $('#immersive-comments-need-auth').show();
            if ($('#immersive-comments-need-auth').html() === '') {
                $('#immersive-comments-need-auth').html(
                    '<div class="notification-content">' +
                    '<i class="fas fa-user-lock notification-icon" style="color: #3498db;"></i>' +
                    '<p class="notification-text">Чтобы оставить отзыв, вам необходимо <a href="#" onclick="navigateTo(\'login\'); return false;">войти</a> в свой аккаунт.</p>' +
                    '</div>'
                );
            }
        }
    });
}


function openRejectionReasonModal(reason) {
    GidoAlert.warning('Комментарий не был опубликован', `Причина отклонения: ${reason}`, {
        showCancelButton: false,
        confirmButtonText: 'Понятно'
    });
}

function closeRejectionReasonModal() {
}

function validatePurchaseForm() {
    let isValid = true;
    $('.invalid-feedback').hide();
    $('.is-invalid').removeClass('is-invalid');

    const cardNumber = $('#purchase-card-number').val().trim().replace(/\s/g, '');
    if (!cardNumber || cardNumber.length < 16) {
        $('#purchase-card-number').addClass('is-invalid');
        $('#purchase-card-number-error').show();
        isValid = false;
    }

    const cardExpiry = $('#purchase-card-expiry').val().trim();
    if (!cardExpiry || cardExpiry.length < 5) {
        $('#purchase-card-expiry').addClass('is-invalid');
        $('#purchase-card-expiry-error').show();
        isValid = false;
    } else {
        const parts = cardExpiry.split('/');
        const month = parseInt(parts[0], 10);
        const year = parseInt(parts[1], 10);
        const currentYear = new Date().getFullYear() % 100;
        const currentMonth = new Date().getMonth() + 1;

        if (month < 1 || month > 12 || year < currentYear || (year === currentYear && month < currentMonth)) {
            $('#purchase-card-expiry').addClass('is-invalid');
            $('#purchase-card-expiry-error').text('Неверный срок действия').show();
            isValid = false;
        }
    }

    const cardCvv = $('#purchase-card-cvv').val().trim();
    if (!cardCvv || cardCvv.length < 3) {
        $('#purchase-card-cvv').addClass('is-invalid');
        $('#purchase-card-cvv-error').show();
        isValid = false;
    }

    return isValid;
}

function processPurchase() {
    const confirmButton = $('#confirm-purchase-button');
    const originalText = confirmButton.text();
    const excursionId = $('#purchase-modal-excursion-id').val();

    if (!excursionId) {
        GidoAlert.error('Ошибка', 'Ошибка: не удалось определить ID экскурсии.', {showCancelButton: false});
        return;
    }

    confirmButton.prop('disabled', true).css('pointer-events', 'none');
    confirmButton.text('Обработка...');

    setTimeout(() => {
        
        $.ajax({
            url: `/api/user/excursion/${excursionId}/purchase`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    GidoAlert.success('Экскурсия приобретена!', response.message || 'Экскурсия успешно приобретена!', {showCancelButton: false});
                    loadExcursionDetails(excursionId);
                    closePurchaseModal();
                    
                    if (window.location.pathname === '/personalAccount') {
                        setTimeout(() => {
                            $(document).trigger('updateUserExcursions');
                        }, 500);
                    }
                } else {
                    GidoAlert.error('Ошибка покупки', response.message || 'Ошибка при покупке экскурсии.', {showCancelButton: false});
                }
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    GidoAlert.warning('Требуется авторизация', 'Необходимо авторизоваться для покупки экскурсии.', {showCancelButton: false});
                    navigateTo('login');
                } else {
                    GidoAlert.error('Ошибка', 'Произошла ошибка при покупке экскурсии. Пожалуйста, попробуйте еще раз.', {showCancelButton: false});
                }
            },
            complete: function() {
                confirmButton.prop('disabled', false).css('pointer-events', 'auto');
                confirmButton.text(originalText);
            }
        });

    }, 1500);
}

$('#purchase-card-number').on('input', function() {
    const value = $(this).val().replace(/\D/g, '');
    const formattedValue = formatCardNumber(value);
    $(this).val(formattedValue);
});

$('#purchase-card-expiry').on('input', function() {
    const value = $(this).val().replace(/\D/g, '');
    const formattedValue = formatCardExpiry(value);
    $(this).val(formattedValue);
});

$('#purchase-card-cvv').on('input', function() {
    const value = $(this).val().replace(/\D/g, '');
    $(this).val(value);
});

$('#confirm-purchase-button').off('click').on('click', function(e) {
    e.preventDefault();
    if (validatePurchaseForm()) {
        processPurchase();
    }
});

function initImmersiveAudioPlayer() {
    const audio = document.getElementById('immersive-audio');
    const playButton = document.getElementById('immersive-play-button');
    const progressBar = document.getElementById('immersive-progress-bar');
    const currentTimeSpan = document.getElementById('immersive-current-time');
    const durationTimeSpan = document.getElementById('immersive-duration-time');
    
    if (!audio || !playButton || !progressBar || !currentTimeSpan) {
        return;
    }
    
    audio.preload = 'metadata';
    
    audio.addEventListener('loadstart', function() {
        currentTimeSpan.textContent = 'Загрузка...';
        if (durationTimeSpan) {
            durationTimeSpan.textContent = '...';
        }
    });
    
    audio.addEventListener('loadedmetadata', function() {
        progressBar.max = audio.duration;
        currentTimeSpan.textContent = '0:00';
        if (durationTimeSpan) {
            durationTimeSpan.textContent = formatTime(audio.duration);
        }
    });
    
    audio.addEventListener('canplay', function() {
        if (currentTimeSpan.textContent === 'Загрузка...') {
            currentTimeSpan.textContent = '0:00';
            if (durationTimeSpan && !isNaN(audio.duration)) {
                durationTimeSpan.textContent = formatTime(audio.duration);
            }
        }
    });
    
    audio.addEventListener('timeupdate', function() {
        currentTimeSpan.textContent = formatTime(audio.currentTime);
        progressBar.value = audio.currentTime;
        
        if (durationTimeSpan && !isNaN(audio.duration) && durationTimeSpan.textContent !== formatTime(audio.duration)) {
            durationTimeSpan.textContent = formatTime(audio.duration);
        }
    });
    
    if (window.immersivePlayButtonHandler) {
        playButton.removeEventListener('click', window.immersivePlayButtonHandler);
    }
    
    window.immersivePlayButtonHandler = function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (audio.paused) {
            const playPromise = audio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                }).catch(error => {
                    //console.error('Ошибка воспроизведения иммерсивного аудио:', error);
                });
            }
        } else {
            audio.pause();
        }
    }
    
    playButton.addEventListener('click', window.immersivePlayButtonHandler);
    
    progressBar.addEventListener('input', function() {
        audio.currentTime = progressBar.value;
    });
    
    audio.addEventListener('play', function() {
        playButton.textContent = '⏸';
    });
    
    audio.addEventListener('pause', function() {
        playButton.textContent = '▶';
    });
    
    audio.addEventListener('ended', function() {
        playButton.textContent = '▶';
        progressBar.value = 0;
        currentTimeSpan.textContent = '0:00';
    });
    
    audio.addEventListener('error', function() {
        currentTimeSpan.textContent = 'Ошибка';
        if (durationTimeSpan) {
            durationTimeSpan.textContent = 'Ошибка';
        }
    });
}

function refreshUserExcursions() {
    $.ajax({
        url: '/api/user/profile',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const excursionPurchases = response.excursionPurchases || [];
                const bookings = response.bookings || [];
                
                const excursionsContainer = $('#user-excursions');
                excursionsContainer.empty();
                
                if (excursionPurchases.length > 0 || bookings.length > 0) {
                    excursionPurchases.forEach(function(purchase) {
                        const imagePath = purchase.excursion.main_image 
                            ? `/storage/${purchase.excursion.main_image}` 
                            : '/img/personalAccount/excursuion.png';
                            
                        const excursionHtml = `
                            <div class="block2_img_content">
                                <img src="${imagePath}" alt="" class="my_excurs">
                                <div class="excursion-info">
                                    <h4 class="name_excursion">${purchase.excursion.title}</h4>
                                    <div class="excursion-meta">
                                        <span class="excursion-type">${purchase.excursion.type === 'audio' ? 'Аудио' : 'Иммерсив'}</span>
                                        <a href="#" class="arrow_block3 excursion-link" data-excursion-id="${purchase.excursion.id}" data-excursion-type="${purchase.excursion.type}"></a>
                                    </div>
                                </div>
                            </div>
                        `;
                        excursionsContainer.append(excursionHtml);
                    });
                    
                    bookings.forEach(function(booking) {
                        if (booking.immersive && booking.immersive.excursion) {
                            const excursion = booking.immersive.excursion;
                            const dateString = booking.date ? new Date(booking.date.event_date).toLocaleDateString('ru-RU') : '';
                            const timeString = booking.time ? booking.time.event_time : '';
                            const dateTimeInfo = dateString && timeString ? ` (${dateString}, ${timeString})` : '';
                            
                            const imagePath = excursion.main_image 
                                ? `/storage/${excursion.main_image}` 
                                : '/img/personalAccount/excursuion.png';
                            
                            const excursionHtml = `
                                <div class="block2_img_content">
                                    <img src="${imagePath}" alt="" class="my_excurs">
                                    <div class="excursion-info">
                                        <h4 class="name_excursion">${excursion.title}${dateTimeInfo}</h4>
                                        <div class="excursion-meta">
                                            <span class="excursion-type">Иммерсив</span>
                                            <a href="#" class="arrow_block3 excursion-link" data-excursion-id="${excursion.id}" data-excursion-type="${excursion.type}"></a>
                                        </div>
                                    </div>
                                </div>
                            `;
                            excursionsContainer.append(excursionHtml);
                        }
                    });
                    
                } else {
                    excursionsContainer.html(`
                        <div class="empty-block">
                            <div class="empty-block-icon"><i class="fa-solid fa-route"></i></div>
                            <h3 class="empty-block-title">Нет приобретенных экскурсий</h3>
                            <p class="empty-block-description">Исследуйте наш каталог и выберите экскурсию, которая вас заинтересует!</p>
                            <a href="#" class="empty-block-action nav-link" data-page="catalogExcursions">Перейти в каталог</a>
                        </div>
                    `);
                }
                
-                $('.excursion-link').off('click').on('click', function(e) {
                    e.preventDefault();
                    const excursionId = $(this).data('excursion-id');
                    navigateToExcursion(excursionId);
                });
            }
        },
        error: function() {
            console.error('Не удалось обновить список экскурсий');
        }
    });
}

let GIDO_IMMERSIVES_DATA = {};

function initImmersivesDataFromSSR() {
    try {
        const dataScript = document.getElementById('GIDO_BOOKING_DATES');
        if (dataScript && dataScript.textContent) {
            GIDO_IMMERSIVES_DATA = JSON.parse(dataScript.textContent);
            console.log('SSR данные иммерсивов загружены:', Object.keys(GIDO_IMMERSIVES_DATA).length, 'иммерсивов');
        }
    } catch (error) {
        console.error('Ошибка при загрузке SSR данных иммерсивов:', error);
        GIDO_IMMERSIVES_DATA = {};
    }
}

function updateImmersiveTimeAvailability(immersiveId, timeId, isAvailable = false) {
    if (!GIDO_IMMERSIVES_DATA[immersiveId]) {
        return false;
    }
    
    let updated = false;
    const immersive = GIDO_IMMERSIVES_DATA[immersiveId];
    
    for (let date of immersive.dates) {
        const timeIndex = date.times.findIndex(time => time.id == timeId);
        if (timeIndex !== -1) {
            date.times[timeIndex].is_available = isAvailable;
            updated = true;
            console.log(`Обновлена доступность времени ${timeId} для иммерсива ${immersiveId}: ${isAvailable}`);
            break;
        }
    }
    
    return updated;
}

function removeBookedTimeFromUI(timeId) {
    const timeElement = $(`#time-${timeId}`);
    if (timeElement.length > 0) {
        timeElement.fadeOut(300, function() {
            $(this).remove();
            
            const remainingTimes = $('#booking-times .main_title:visible');
            if (remainingTimes.length === 0) {
                $('#booking-times').html('<div class="no-times-message">Все времена для выбранной даты забронированы. Пожалуйста, выберите другую дату.</div>');
            }
        });
    }
}



function getAvailableTimesCount(immersiveId, dateId) {
    if (!GIDO_IMMERSIVES_DATA[immersiveId]) {
        return 0;
    }
    
    const immersive = GIDO_IMMERSIVES_DATA[immersiveId];
    const targetDate = immersive.dates.find(d => d.id == dateId);
    
    if (!targetDate) {
        return 0;
    }
    
    return targetDate.times.filter(time => time.is_available === true).length;
}