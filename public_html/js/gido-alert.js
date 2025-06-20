/**
 * ГидО Alert - Кастомные уведомления на основе SweetAlert2
 */
const GIDO_COLORS = {
    primary: '#3752E9',
    secondary: '#F39040', 
    success: '#27ae60',
    error: '#e74c3c',
    warning: '#f39c12',
    info: '#3498db',
    dark: '#2c3e50',
    light: '#ecf0f1'
};

const baseConfig = {
    fontFamily: '"Unbounded", sans-serif',
    background: '#ffffff',
    color: '#2c3e50',
    confirmButtonColor: GIDO_COLORS.primary,
    cancelButtonColor: '#e1e5e9',
    buttonsStyling: false,
    customClass: {
        popup: 'gido-popup',
        header: 'gido-header',
        title: 'gido-title',
        content: 'gido-content',
        confirmButton: 'gido-confirm-btn',
        cancelButton: 'gido-cancel-btn',
        actions: 'swal2-actions'
    },
    backdrop: 'rgba(0, 0, 0, 0.4)',
    allowOutsideClick: false,
    allowEscapeKey: true,
    showClass: {
        popup: 'swal2-show',
        backdrop: 'swal2-backdrop-show'
    },
    hideClass: {
        popup: 'swal2-hide',
        backdrop: 'swal2-backdrop-hide'
    },
    position: 'center',
    grow: false,
    width: 'auto',
    padding: '0',
    iconHtml: false,
    cancelButtonText: 'Отмена',
    denyButtonText: 'Нет',
    didOpen: () => {
        const iconContent = document.querySelector('.swal2-icon-content');
        if (iconContent) {
            iconContent.style.display = 'none';
            iconContent.style.visibility = 'hidden';
            iconContent.style.opacity = '0';
        }
        
        const standardElements = document.querySelectorAll(
            '.swal2-success-line, .swal2-success-ring, .swal2-x-mark, .swal2-icon-content'
        );
        standardElements.forEach(el => {
            el.style.display = 'none';
            el.style.visibility = 'hidden';
            el.style.opacity = '0';
        });
        
        const cancelButton = document.querySelector('.swal2-cancel');
        if (cancelButton && cancelButton.style.display === 'none') {
            cancelButton.remove();
        }
        
        const popup = document.querySelector('.swal2-popup');
        const icon = document.querySelector('.swal2-icon');
        
        if (popup && icon) {
            const hasIconType = icon.classList.contains('swal2-success') ||
                              icon.classList.contains('swal2-error') ||
                              icon.classList.contains('swal2-warning') ||
                              icon.classList.contains('swal2-info') ||
                              icon.classList.contains('swal2-question');
            
            if (!hasIconType || icon.innerHTML.trim() === '') {
                icon.style.display = 'none';
                icon.style.margin = '0';
                icon.style.height = '0';
                icon.style.width = '0';
                
                const title = document.querySelector('.gido-title');
                if (title) {
                    title.style.marginTop = '0';
                }
            }
        }
    }
};

const GidoAlert = {
    success(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'success',
            title: title,
            text: text,
            confirmButtonText: 'Отлично!',
            showCancelButton: options.showCancelButton !== undefined ? options.showCancelButton : false,
            timer: options.timer || 4000,
            timerProgressBar: true,
            customClass: {
                ...baseConfig.customClass,
                popup: 'gido-popup success-type'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                if (options.showCancelButton === false || options.showCancelButton === undefined) {
                    const cancelButton = document.querySelector('.swal2-cancel');
                    if (cancelButton) {
                        cancelButton.remove();
                    }
                }
                
                const icon = document.querySelector('.swal2-icon.swal2-success');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    error(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'error',
            title: title,
            text: text,
            confirmButtonText: 'Понятно',
            showCancelButton: options.showCancelButton !== undefined ? options.showCancelButton : false,
            customClass: {
                ...baseConfig.customClass,
                popup: 'gido-popup error-type'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                if (options.showCancelButton === false || options.showCancelButton === undefined) {
                    const cancelButton = document.querySelector('.swal2-cancel');
                    if (cancelButton) {
                        cancelButton.remove();
                    }
                }
                
                const icon = document.querySelector('.swal2-icon.swal2-error');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    warning(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonText: 'Хорошо',
            showCancelButton: options.showCancelButton !== undefined ? options.showCancelButton : false,
            customClass: {
                ...baseConfig.customClass,
                popup: 'gido-popup warning-type'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                if (options.showCancelButton === false || options.showCancelButton === undefined) {
                    const cancelButton = document.querySelector('.swal2-cancel');
                    if (cancelButton) {
                        cancelButton.remove();
                    }
                }
                
                const icon = document.querySelector('.swal2-icon.swal2-warning');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    info(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'info',
            title: title,
            text: text,
            confirmButtonText: 'Понятно',
            showCancelButton: options.showCancelButton !== undefined ? options.showCancelButton : false,
            customClass: {
                ...baseConfig.customClass,
                popup: 'gido-popup info-type'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                if (options.showCancelButton === false || options.showCancelButton === undefined) {
                    const cancelButton = document.querySelector('.swal2-cancel');
                    if (cancelButton) {
                        cancelButton.remove();
                    }
                }
                
                const icon = document.querySelector('.swal2-icon.swal2-info');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    confirm(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: 'Да, подтвердить',
            cancelButtonText: 'Отмена',
            reverseButtons: true,
            focusConfirm: false,
            focusCancel: true,
            didOpen: () => {
                baseConfig.didOpen();
                
                const icon = document.querySelector('.swal2-icon.swal2-question');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    delete(title = 'Удалить элемент?', text = 'Это действие нельзя будет отменить!', options = {}) {
        return Swal.fire({
            ...baseConfig,
            icon: 'warning',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: 'Да, удалить!',
            cancelButtonText: 'Отмена',
            reverseButtons: true,
            focusConfirm: false,
            focusCancel: true,
            customClass: {
                ...baseConfig.customClass,
                confirmButton: 'gido-delete-btn',
                cancelButton: 'gido-cancel-btn'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                const icon = document.querySelector('.swal2-icon.swal2-warning');
                if (icon && options.customIcon) {
                    icon.style.setProperty('--custom-icon', options.customIcon);
                }
            },
            ...options
        });
    },

    loading(title = 'Загрузка...', text = 'Пожалуйста, подождите', options = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            text: text,
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            showCancelButton: false,
            didOpen: () => {
                baseConfig.didOpen();
                
                Swal.showLoading();
            },
            willClose: () => {
            },
            ...options
        });
    },

    close() {
        Swal.close();
    },

    isVisible() {
        return Swal.isVisible();
    },

    toast(type = 'success', title, options = {}) {
        const Toast = Swal.mixin({
            toast: true,
            position: options.position || 'top-end',
            showConfirmButton: false,
            timer: options.timer || 4000,
            timerProgressBar: true,
            background: type === 'success' ? GIDO_COLORS.success : 
                       type === 'error' ? GIDO_COLORS.error :
                       type === 'warning' ? GIDO_COLORS.warning : GIDO_COLORS.info,
            color: '#ffffff',
            customClass: {
                popup: `gido-toast toast-${type}`,
                title: 'swal2-title'
            },
            didOpen: (toast) => {
                const iconContent = toast.querySelector('.swal2-icon-content');
                if (iconContent) {
                    iconContent.style.display = 'none';
                    iconContent.style.visibility = 'hidden';
                    iconContent.style.opacity = '0';
                }
                
                const standardElements = toast.querySelectorAll(
                    '.swal2-success-line, .swal2-success-ring, .swal2-x-mark, .swal2-icon-content'
                );
                standardElements.forEach(el => {
                    el.style.display = 'none';
                    el.style.visibility = 'hidden';
                    el.style.opacity = '0';
                });
                
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
                
                const icon = toast.querySelector('.swal2-icon');
                if (icon) {
                    icon.classList.add(`swal2-${type}`);
                }
            },
            willClose: () => {
            }
        });

        return Toast.fire({
            icon: type,
            title: title,
            ...options
        });
    },

    input(title, inputType = 'text', options = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            input: inputType,
            inputPlaceholder: options.placeholder || 'Введите значение...',
            showCancelButton: true,
            confirmButtonText: 'Отправить',
            cancelButtonText: 'Отмена',
            reverseButtons: true,
            focusConfirm: false,
            preConfirm: (value) => {
                if (!value && options.required !== false) {
                    Swal.showValidationMessage('Поле не может быть пустым!');
                    return false;
                }
                if (options.validator && typeof options.validator === 'function') {
                    const validationResult = options.validator(value);
                    if (validationResult !== true) {
                        Swal.showValidationMessage(validationResult);
                        return false;
                    }
                }
                return value;
            },
            inputValidator: (value) => {
                if (!value && options.required !== false) {
                    return 'Поле не может быть пустым!';
                }
                if (options.validator && typeof options.validator === 'function') {
                    const result = options.validator(value);
                    return result === true ? null : result;
                }
            },
            ...options
        });
    },

    select(title, inputOptions, selectOptions = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            input: 'select',
            inputOptions: inputOptions,
            inputPlaceholder: selectOptions.placeholder || 'Выберите опцию...',
            showCancelButton: true,
            confirmButtonText: 'Выбрать',
            cancelButtonText: 'Отмена',
            reverseButtons: true,
            focusConfirm: false,
            inputValidator: (value) => {
                if (!value) {
                    return 'Выберите одну из опций!';
                }
            },
            ...selectOptions
        });
    },

    html(title, htmlContent, options = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            html: htmlContent,
            confirmButtonText: options.confirmButtonText || 'Закрыть',
            showCancelButton: options.showCancelButton || false,
            cancelButtonText: options.cancelButtonText || 'Отмена',
            width: options.width || 'auto',
            ...options
        });
    },

    steps(steps, options = {}) {
        let currentStep = 0;
        
        const showStep = (stepIndex) => {
            if (stepIndex >= steps.length) {
                return Promise.resolve({ isConfirmed: true, value: 'completed' });
            }
            
            const step = steps[stepIndex];
            const isLastStep = stepIndex === steps.length - 1;
            const isFirstStep = stepIndex === 0;
            
            return Swal.fire({
                ...baseConfig,
                title: step.title,
                text: step.text || '',
                html: step.html || '',
                icon: step.icon || 'info',
                showCancelButton: true,
                confirmButtonText: isLastStep ? 'Завершить' : 'Далее',
                cancelButtonText: isFirstStep ? 'Отмена' : 'Назад',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    baseConfig.didOpen();
                    
                    const icon = document.querySelector('.swal2-icon');
                    if (icon && step.customIcon) {
                        icon.style.setProperty('--custom-icon', step.customIcon);
                    }
                },
                ...step.options
            }).then((result) => {
                if (result.isConfirmed) {
                    return showStep(stepIndex + 1);
                } else if (result.dismiss === Swal.DismissReason.cancel && !isFirstStep) {
                    return showStep(stepIndex - 1);
                } else {
                    return Promise.resolve({ isConfirmed: false, dismiss: result.dismiss });
                }
            });
        };
        
        return showStep(0);
    },

    customIcon(title, text, svgIcon, options = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            text: text,
            iconHtml: svgIcon,
            customClass: {
                ...baseConfig.customClass,
                icon: 'gido-custom-icon'
            },
            ...options
        });
    },

    noIcon(title, text = '', options = {}) {
        return Swal.fire({
            ...baseConfig,
            title: title,
            text: text,
            icon: undefined,
            iconHtml: false,
            confirmButtonText: 'Закрыть',
            showCancelButton: options.showCancelButton !== undefined ? options.showCancelButton : false,
            customClass: {
                ...baseConfig.customClass,
                popup: 'gido-popup gido-no-icon'
            },
            didOpen: () => {
                baseConfig.didOpen();
                
                if (options.showCancelButton === false || options.showCancelButton === undefined) {
                    const cancelButton = document.querySelector('.swal2-cancel');
                    if (cancelButton) {
                        cancelButton.remove();
                    }
                }
                
                const icon = document.querySelector('.swal2-icon');
                if (icon) {
                    icon.style.display = 'none';
                    icon.style.margin = '0';
                    icon.style.height = '0';
                    icon.style.width = '0';
                }
                
                const title = document.querySelector('.gido-title');
                if (title) {
                    title.style.marginTop = '0';
                }
            },
            ...options
        });
    }
};

window.GidoAlert = GidoAlert;

GidoAlert.utils = {
    quickSuccess: (message) => GidoAlert.toast('success', message),
    quickError: (message) => GidoAlert.toast('error', message),
    quickWarning: (message) => GidoAlert.toast('warning', message),
    quickInfo: (message) => GidoAlert.toast('info', message),
    
    customConfirm: (title, text, confirmText, cancelText) => {
        return GidoAlert.confirm(title, text, {
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        });
    },
    
    autoClose: (type, title, text, duration = 3000) => {
        return GidoAlert[type](title, text, {
            timer: duration,
            timerProgressBar: true
        });
    },

    icons: {
        success: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#27ae60" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20,6 9,17 4,12"></polyline></svg>`,
        error: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>`,
        warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#f39c12" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="m12 17 .01 0"/></svg>`,
        info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3498db" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m12 16-4-4 4-4 4 4-4 4"/><path d="M12 8h.01"/></svg>`,
        question: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3752E9" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="m12 17 .01 0"/></svg>`,
        loading: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#3752E9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>`
    },

    createCustomIcon: (svgContent, color = '#3752E9', size = '24') => {
        return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${size} ${size}" fill="none" stroke="${color}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${svgContent}</svg>`;
    },

    simpleMessage: (title, text) => GidoAlert.noIcon(title, text, {
        confirmButtonText: 'Понятно'
    }),
    validation: (title, text) => GidoAlert.noIcon(title, text, { 
        confirmButtonText: 'Исправить',
        customClass: {
            ...baseConfig.customClass,
            popup: 'gido-popup gido-no-icon validation-popup'
        }
    }),
    notification: (title, text) => GidoAlert.noIcon(title, text, {
        timer: 3000,
        timerProgressBar: true,
        confirmButtonText: 'Закрыть'
    })
}; 