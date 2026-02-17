/**
 * Adverto Master Plugin Admin JavaScript
 * Beautiful Material Design interactions and animations
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        AdvertoAdmin.init();
    });

    // Main Admin Object
    const AdvertoAdmin = {
        
        // Initialize all functionality
        init: function() {
            this.initMaterialDesign();
            this.initTooltips();
            this.initModals();
            this.initColorPickers();
            this.initMediaUploads();
            this.initAnimations();
            this.initFormValidation();
            this.initProgressBars();
            this.bindEvents();
            
            console.log('Adverto Master Admin initialized with Material Design');
        },

        // Initialize Material Design components
        initMaterialDesign: function() {
            // Add ripple effect to buttons
            this.addRippleEffect();
            
            // Initialize floating labels
            this.initFloatingLabels();
            
            // Initialize switches
            this.initSwitches();
            
            // Add Material Design classes
            this.enhanceElements();
        },

        // Add ripple effect to buttons
        addRippleEffect: function() {
            $('.adverto-btn').on('click', function(e) {
                const button = $(this);
                const ripple = $('<span class="adverto-ripple"></span>');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.css({
                    width: size + 'px',
                    height: size + 'px',
                    left: x + 'px',
                    top: y + 'px',
                    position: 'absolute',
                    borderRadius: '50%',
                    background: 'rgba(255, 255, 255, 0.4)',
                    transform: 'scale(0)',
                    animation: 'adverto-ripple-effect 0.6s linear'
                });
                
                button.append(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
            
            // Add ripple animation to CSS
            if (!$('#adverto-ripple-css').length) {
                $('head').append(`
                    <style id="adverto-ripple-css">
                        @keyframes adverto-ripple-effect {
                            to {
                                transform: scale(2);
                                opacity: 0;
                            }
                        }
                        .adverto-btn {
                            position: relative;
                            overflow: hidden;
                        }
                    </style>
                `);
            }
        },

        // Initialize floating labels
        initFloatingLabels: function() {
            $('.adverto-input-floating input').on('focus blur input', function() {
                const input = $(this);
                const label = input.next('label');
                
                if (input.is(':focus') || input.val() !== '') {
                    label.addClass('active');
                } else {
                    label.removeClass('active');
                }
            });
        },

        // Initialize switches
        initSwitches: function() {
            $('.adverto-switch input').on('change', function() {
                const $switch = $(this).parent();
                if (this.checked) {
                    $switch.addClass('checked');
                } else {
                    $switch.removeClass('checked');
                }
            });
        },

        // Enhance existing elements with Material Design
        enhanceElements: function() {
            // Convert WordPress buttons to Material Design
            $('.button-primary').addClass('adverto-btn adverto-btn-primary');
            $('.button-secondary').addClass('adverto-btn adverto-btn-secondary');
            
            // Enhance form elements
            $('input[type="text"], input[type="email"], input[type="password"], textarea, select')
                .not('.adverto-input, .adverto-textarea, .adverto-select')
                .addClass('adverto-input');
        },

        // Initialize tooltips
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                const element = $(this);
                const tooltipText = element.data('tooltip');
                
                element.addClass('adverto-tooltip');
                element.append(`<span class="adverto-tooltip-text">${tooltipText}</span>`);
            });
        },

        // Initialize modals
        initModals: function() {
            // Modal triggers
            $(document).on('click', '[data-modal]', function(e) {
                e.preventDefault();
                const modalId = $(this).data('modal');
                AdvertoAdmin.openModal(modalId);
            });
            
            // Close modal
            $(document).on('click', '.adverto-modal-close, .adverto-modal', function(e) {
                if (e.target === this) {
                    AdvertoAdmin.closeModal();
                }
            });
            
            // ESC key to close modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    AdvertoAdmin.closeModal();
                }
            });
        },

        // Open modal
        openModal: function(modalId) {
            const modal = $('#' + modalId);
            if (modal.length) {
                modal.addClass('active');
                $('body').addClass('modal-open');
                
                // Focus trap
                this.trapFocus(modal);
            }
        },

        // Close modal
        closeModal: function() {
            $('.adverto-modal').removeClass('active');
            $('body').removeClass('modal-open');
        },

        // Trap focus within modal
        trapFocus: function(modal) {
            const focusableElements = modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            const firstFocusable = focusableElements.first();
            const lastFocusable = focusableElements.last();
            
            firstFocusable.focus();
            
            modal.off('keydown.focustrap').on('keydown.focustrap', function(e) {
                if (e.key === 'Tab') {
                    if (e.shiftKey) {
                        if ($(document.activeElement).is(firstFocusable)) {
                            e.preventDefault();
                            lastFocusable.focus();
                        }
                    } else {
                        if ($(document.activeElement).is(lastFocusable)) {
                            e.preventDefault();
                            firstFocusable.focus();
                        }
                    }
                }
            });
        },

        // Initialize color pickers
        initColorPickers: function() {
            if ($.fn.wpColorPicker) {
                $('.adverto-color-picker').wpColorPicker({
                    change: function(event, ui) {
                        // Custom change handler
                        $(this).trigger('adverto:colorchange', ui.color.toString());
                    }
                });
            }
        },

        // Initialize media uploads
        initMediaUploads: function() {
            $(document).on('click', '.adverto-upload-btn', function(e) {
                e.preventDefault();
                
                const button = $(this);
                const targetInput = $(button.data('target'));
                
                const frame = wp.media({
                    title: button.data('title') || 'Select Media',
                    button: {
                        text: button.data('button-text') || 'Use this media'
                    },
                    multiple: button.data('multiple') || false
                });
                
                frame.on('select', function() {
                    const attachment = frame.state().get('selection').first().toJSON();
                    targetInput.val(attachment.url);
                    targetInput.trigger('change');
                    
                    // Update preview if exists
                    const preview = $(targetInput.data('preview'));
                    if (preview.length) {
                        if (attachment.type === 'image') {
                            preview.html(`<img src="${attachment.url}" class="adverto-image-preview" alt="Preview">`);
                        } else {
                            preview.html(`<span>${attachment.filename}</span>`);
                        }
                    }
                });
                
                frame.open();
            });
        },

        // Initialize animations
        initAnimations: function() {
            // Animate elements when they come into view
            this.initScrollAnimations();
            
            // Animate stats counters
            this.animateCounters();
            
            // Animate progress bars
            this.animateProgressBars();
        },

        // Initialize scroll animations
        initScrollAnimations: function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('adverto-fade-in');
                    }
                });
            }, observerOptions);
            
            $('.adverto-card, .adverto-stat-card').each(function() {
                observer.observe(this);
            });
        },

        // Animate counters
        animateCounters: function() {
            $('.adverto-stat-number[data-count]').each(function() {
                const element = $(this);
                const target = parseInt(element.data('count'));
                let current = 0;
                const duration = 2000;
                const increment = target / (duration / 16);
                
                const timer = setInterval(function() {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    element.text(Math.floor(current));
                }, 16);
            });
        },

        // Animate progress bars
        animateProgressBars: function() {
            $('.adverto-progress-bar[data-progress]').each(function() {
                const bar = $(this);
                const progress = bar.data('progress');
                
                setTimeout(function() {
                    bar.css('width', progress + '%');
                }, 100);
            });
        },

        // Initialize form validation
        initFormValidation: function() {
            // Real-time validation
            $('.adverto-input, .adverto-textarea').on('blur', function() {
                AdvertoAdmin.validateField($(this));
            });
            
            // Form submission validation
            $('.adverto-form').on('submit', function(e) {
                let isValid = true;
                
                $(this).find('.adverto-input, .adverto-textarea').each(function() {
                    if (!AdvertoAdmin.validateField($(this))) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    AdvertoAdmin.showAlert('Please fix the errors before submitting.', 'error');
                }
            });
        },

        // Validate individual field
        validateField: function(field) {
            const value = field.val().trim();
            const rules = field.data('validate');
            let isValid = true;
            let errorMessage = '';
            
            if (rules) {
                if (rules.includes('required') && value === '') {
                    isValid = false;
                    errorMessage = 'This field is required.';
                }
                
                if (rules.includes('email') && value !== '' && !this.isValidEmail(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address.';
                }
                
                if (rules.includes('url') && value !== '' && !this.isValidURL(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid URL.';
                }
            }
            
            // Remove existing error
            field.removeClass('error');
            field.next('.adverto-field-error').remove();
            
            if (!isValid) {
                field.addClass('error');
                field.after(`<div class="adverto-field-error">${errorMessage}</div>`);
            }
            
            return isValid;
        },

        // Initialize progress bars
        initProgressBars: function() {
            // Auto-update progress bars
            $('.adverto-progress[data-auto-progress]').each(function() {
                const progressBar = $(this);
                const bar = progressBar.find('.adverto-progress-bar');
                let progress = 0;
                
                const timer = setInterval(function() {
                    progress += Math.random() * 10;
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(timer);
                    }
                    bar.css('width', progress + '%');
                }, 200);
            });
        },

        // Bind all events
        bindEvents: function() {
            // Smooth scrolling for anchor links
            $(document).on('click', 'a[href^="#"]', function(e) {
                const target = $($(this).attr('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 100
                    }, 500);
                }
            });
            
            // Auto-hide alerts
            $('.adverto-alert[data-auto-hide]').each(function() {
                const alert = $(this);
                const delay = alert.data('auto-hide') || 5000;
                
                setTimeout(function() {
                    AdvertoAdmin.hideAlert(alert);
                }, delay);
            });
            
            // Confirm dialogs
            $(document).on('click', '[data-confirm]', function(e) {
                const message = $(this).data('confirm');
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        },

        // Utility Functions
        
        // Show alert
        showAlert: function(message, type = 'info', autoHide = true) {
            const alertTypes = {
                'info': 'info',
                'success': 'check_circle',
                'warning': 'warning',
                'error': 'error'
            };
            
            const icon = alertTypes[type] || 'info';
            const alert = $(`
                <div class="adverto-alert ${type} adverto-fade-in" ${autoHide ? 'data-auto-hide="5000"' : ''}>
                    <i class="material-icons">${icon}</i>
                    <span>${message}</span>
                    <button type="button" class="adverto-alert-close" onclick="AdvertoAdmin.hideAlert($(this).parent())">
                        <i class="material-icons">close</i>
                    </button>
                </div>
            `);
            
            $('.adverto-content').prepend(alert);
            
            if (autoHide) {
                setTimeout(function() {
                    AdvertoAdmin.hideAlert(alert);
                }, 5000);
            }
        },

        // Hide alert
        hideAlert: function(alert) {
            alert.fadeOut(300, function() {
                alert.remove();
            });
        },

        // Show loading
        showLoading: function(container, message = 'Loading...') {
            const loading = $(`
                <div class="adverto-loading">
                    <div class="adverto-spinner"></div>
                    <div class="adverto-loading-text adverto-loading-pulse">${message}</div>
                </div>
            `);
            
            $(container).html(loading);
        },

        // Hide loading
        hideLoading: function(container) {
            $(container).find('.adverto-loading').remove();
        },

        // Validation helpers
        isValidEmail: function(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        },

        isValidURL: function(url) {
            try {
                new URL(url);
                return true;
            } catch (e) {
                return false;
            }
        },

        // AJAX helper
        ajaxRequest: function(action, data = {}, options = {}) {
            const defaults = {
                type: 'POST',
                url: adverto_ajax.ajax_url,
                data: {
                    action: action,
                    nonce: adverto_ajax.nonce,
                    ...data
                },
                beforeSend: function() {
                    if (options.loadingContainer) {
                        AdvertoAdmin.showLoading(options.loadingContainer, options.loadingMessage);
                    }
                },
                complete: function() {
                    if (options.loadingContainer) {
                        AdvertoAdmin.hideLoading(options.loadingContainer);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    AdvertoAdmin.showAlert('An error occurred. Please try again.', 'error');
                }
            };
            
            return $.ajax($.extend(defaults, options));
        }
    };

    // Make AdvertoAdmin globally available
    window.AdvertoAdmin = AdvertoAdmin;

})(jQuery);
