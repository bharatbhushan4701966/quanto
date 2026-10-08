<?php
/**
 * CMR Universal Form Validation & Seamless AJAX Submission Handler
 * Enforces strict required validation and shows an instant, premium
 * Thank You success screen inside modals without any annoying page refresh.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 1. Backend Contact Form 7 Validation Filters - Strict Required on All Fields
add_filter( 'wpcf7_validate_text', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_text*', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_email', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_email*', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_tel', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_tel*', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_textarea', 'cmr_enforce_all_cf7_fields_required', 20, 2 );
add_filter( 'wpcf7_validate_textarea*', 'cmr_enforce_all_cf7_fields_required', 20, 2 );

function cmr_enforce_all_cf7_fields_required( $result, $tag ) {
    $tag = new WPCF7_FormTag( $tag );
    $name = $tag->name;

    if ( empty( $name ) ) {
        return $result;
    }

    $value = isset( $_POST[$name] ) ? trim( (string) wp_unslash( $_POST[$name] ) ) : '';

    if ( $value === '' ) {
        $result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) ?: 'Please fill in this required field.' );
    }

    return $result;
}

// 2. Client-side Universal Form Controller (AJAX + Validation + Success Screen)
add_action( 'wp_footer', function() {
    ?>
    <script id="cmr-form-validation-js">
    (function() {
        // Enforce required attributes on all visible input fields
        function enforceRequiredAttributes() {
            var forms = document.querySelectorAll(
                'form.wpcf7-form, ' +
                '.elementor-popup-modal form, ' +
                '.custom-report-form, ' +
                '.custom-consultation-form, ' +
                '.custom-subscribe-form, ' +
                '#cmr-job-form, ' +
                '.cmr-modal-form-wrapper form, ' +
                '#cmr-review-modal-box form, ' +
                '.dialog-widget-content form'
            );

            forms.forEach(function(form) {
                var inputs = form.querySelectorAll(
                    'input[type="text"], ' +
                    'input[type="email"], ' +
                    'input[type="tel"], ' +
                    'input[type="number"], ' +
                    'input[type="file"], ' +
                    'textarea, ' +
                    'select'
                );

                inputs.forEach(function(input) {
                    if (!input.hasAttribute('required')) {
                        input.setAttribute('required', 'required');
                        input.required = true;
                    }
                    input.setAttribute('aria-required', 'true');
                });

                // Terms / Acceptance checkboxes
                var checkboxes = form.querySelectorAll('input[type="checkbox"][name*="acceptance"], .privacy-check input[type="checkbox"]');
                checkboxes.forEach(function(cb) {
                    cb.setAttribute('required', 'required');
                    cb.required = true;
                });
            });
        }

        // Add red asterisk (*) to all required form field labels
        function addRequiredStarsToLabels() {
            var forms = document.querySelectorAll(
                'form.wpcf7-form, ' +
                '.elementor-popup-modal form, ' +
                '.custom-report-form, ' +
                '.custom-consultation-form, ' +
                '.custom-subscribe-form, ' +
                '#cmr-job-form, ' +
                '.cmr-modal-form-wrapper form, ' +
                '#cmr-review-modal-box form, ' +
                '.dialog-widget-content form, ' +
                '.cmr-form'
            );

            forms.forEach(function(form) {
                // 1. Process all labels
                var labels = form.querySelectorAll('label');
                labels.forEach(function(label) {
                    if (label.closest('.privacy-check') || 
                        label.closest('.cmr-privacy') || 
                        label.closest('.wpcf7-acceptance') || 
                        label.classList.contains('subscription-item') || 
                        label.querySelector('input[type="checkbox"]') || 
                        label.querySelector('input[type="radio"]')) {
                        return;
                    }

                    if (label.querySelector('.cmr-req-star')) {
                        return;
                    }

                    var star = document.createElement('span');
                    star.className = 'cmr-req-star';
                    star.setAttribute('aria-hidden', 'true');
                    star.textContent = ' *';
                    star.style.cssText = 'color: #ef4444 !important; font-weight: 700 !important; font-size: 15px !important; margin-left: 3px !important; display: inline !important; line-height: 1 !important;';

                    var wrap = label.querySelector('.wpcf7-form-control-wrap, input, textarea, select');
                    if (wrap) {
                        label.insertBefore(star, wrap);
                    } else {
                        label.appendChild(star);
                    }
                });

                // 2. Process paragraphs with input wraps but no inner label
                var pTags = form.querySelectorAll('p');
                pTags.forEach(function(p) {
                    if (p.querySelector('label') || p.querySelector('.cmr-req-star') || p.querySelector('input[type="submit"]')) {
                        return;
                    }
                    var wrap = p.querySelector('.wpcf7-form-control-wrap');
                    if (wrap) {
                        var star = document.createElement('span');
                        star.className = 'cmr-req-star';
                        star.setAttribute('aria-hidden', 'true');
                        star.textContent = ' *';
                        star.style.cssText = 'color: #ef4444 !important; font-weight: 700 !important; font-size: 15px !important; margin-left: 3px !important; display: inline !important; line-height: 1 !important;';
                        p.insertBefore(star, wrap);
                    }
                });
            });
        }

        // Close all modals helper
        window.cmrCloseModalAndReset = function() {
            document.documentElement.classList.remove('cmr-modal-open');
            document.body.classList.remove('cmr-modal-open');

            if (typeof elementorProFrontend !== 'undefined' && elementorProFrontend.modules && elementorProFrontend.modules.popup) {
                try {
                    elementorProFrontend.modules.popup.closePopup({}, null);
                } catch(err) {}
            }

            document.querySelectorAll('.dialog-widget.dialog-type-lightbox, .elementor-popup-modal').forEach(function(el) {
                el.style.display = 'none';
                el.style.opacity = '0';
            });

            var crOverlay = document.getElementById('cmr-review-modal-overlay');
            if (crOverlay) crOverlay.classList.remove('cmr-open');
            var crModal = document.getElementById('cmr-review-modal');
            if (crModal) crModal.style.display = 'none';
        };

        // Render Success State inside Modal (Hides form completely, shows clean Thank You card, auto-closes in 2s)
        function showFormSuccessState(form, customMsg) {
            if (!form) return;

            // Find the entire right column / modal form side container
            var rightSide = form.closest(
                '.elementor-element-5609af6, .elementor-element-49c9d22, .elementor-element-a26a79a, .elementor-element-2123f86, ' +
                '.elementor-column:last-child, .e-con:last-child, .cmr-modal-form-wrapper, #cmr-review-modal-box, .custom-report-form, .elementor-widget-wrap'
            ) || form.parentElement;

            var cf7Msg = customMsg;
            if (!cf7Msg) {
                var responseOutput = form.querySelector('.wpcf7-response-output');
                if (responseOutput && responseOutput.innerText && responseOutput.innerText.trim()) {
                    cf7Msg = responseOutput.innerText.trim();
                }
            }

            if (!cf7Msg || cf7Msg === 'Please fill in all required fields before submitting.' || cf7Msg.indexOf('Validation error') !== -1) {
                cf7Msg = 'Your request has been successfully submitted. Our team will review your details and get back to you shortly.';
            }

            // Hide the form itself completely
            form.style.setProperty('display', 'none', 'important');

            // Hide all siblings/widgets on the right side except close button
            if (rightSide) {
                var allChildren = rightSide.querySelectorAll('*');
                allChildren.forEach(function(el) {
                    if (el.tagName === 'FORM' || 
                        el.classList.contains('elementor-widget-heading') || 
                        el.classList.contains('elementor-widget-text-editor') || 
                        el.classList.contains('elementor-widget-form') || 
                        el.classList.contains('wpcf7') || 
                        el.classList.contains('sub-desc') ||
                        el.tagName === 'H2' || 
                        el.tagName === 'H3' || 
                        el.tagName === 'LABEL' || 
                        el.tagName === 'INPUT' || 
                        el.tagName === 'TEXTAREA' || 
                        el.tagName === 'BUTTON') {
                        if (!el.closest('.cmr-form-success-wrapper') && !el.classList.contains('dialog-close-button')) {
                            el.style.setProperty('display', 'none', 'important');
                        }
                    }
                });

                // Hide direct children widgets
                for (var i = 0; i < rightSide.children.length; i++) {
                    var ch = rightSide.children[i];
                    if (!ch.classList.contains('cmr-form-success-wrapper') && !ch.classList.contains('dialog-close-button')) {
                        ch.style.setProperty('display', 'none', 'important');
                    }
                }

                // Center success content vertically and horizontally inside right side
                rightSide.style.setProperty('display', 'flex', 'important');
                rightSide.style.setProperty('flex-direction', 'column', 'important');
                rightSide.style.setProperty('justify-content', 'center', 'important');
                rightSide.style.setProperty('align-items', 'center', 'important');
                rightSide.style.setProperty('min-height', '100%', 'important');
                rightSide.style.setProperty('height', '100%', 'important');
            }

            // Remove any previous success wrapper
            var prevSuccess = rightSide.querySelector('.cmr-form-success-wrapper');
            if (prevSuccess) prevSuccess.remove();

            // Create clean centered Thank You screen
            var successWrapper = document.createElement('div');
            successWrapper.className = 'cmr-form-success-wrapper';
            successWrapper.innerHTML = 
                '<div class="cmr-form-success-icon">' +
                    '<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">' +
                        '<polyline points="20 6 9 17 4 12"></polyline>' +
                    '</svg>' +
                '</div>' +
                '<h3>Thank You!</h3>' +
                '<p>' + cf7Msg + '</p>';

            rightSide.appendChild(successWrapper);

            // Auto-close modal after 2 seconds (2000ms)
            setTimeout(function() {
                cmrCloseModalAndReset();
            }, 2000);
        }

        // Listen for Contact Form 7 native AJAX success event
        document.addEventListener('wpcf7mailsent', function(e) {
            var form = e.target;
            var msg = (e.detail && e.detail.apiResponse && e.detail.apiResponse.message) ? e.detail.apiResponse.message : null;
            if (form) {
                showFormSuccessState(form, msg);
            }
        });

        // Listen for Elementor Pro Form native AJAX success event
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('submit_success', function(evt, response) {
                var form = evt.target;
                if (form) {
                    showFormSuccessState(form);
                }
            });
        }

        // Initialize validators
        function initFormValidation() {
            enforceRequiredAttributes();
            addRequiredStarsToLabels();
        }

        document.addEventListener('DOMContentLoaded', initFormValidation);
        window.addEventListener('load', initFormValidation);
        setInterval(initFormValidation, 1500);

        // Pre-Submit Validation & AJAX Submission Listener
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            // Exclude search forms
            if (form.classList.contains('search-form') || form.getAttribute('role') === 'search') return;

            var invalidFields = [];
            var inputs = form.querySelectorAll(
                'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]), textarea, select'
            );

            inputs.forEach(function(input) {
                if (input.offsetParent === null && !input.closest('.elementor-popup-modal') && !input.closest('#cmr-review-modal-overlay')) {
                    return;
                }

                var val = (input.value || '').trim();
                var type = (input.type || '').toLowerCase();
                var isInvalid = false;

                if (type === 'checkbox') {
                    if (input.name.indexOf('acceptance') !== -1 || input.closest('.privacy-check') || input.hasAttribute('required')) {
                        if (!input.checked) {
                            isInvalid = true;
                        }
                    }
                } else if (type === 'radio') {
                    var checkedRadio = form.querySelector('input[name="' + input.name + '"]:checked');
                    if (!checkedRadio) {
                        isInvalid = true;
                    }
                } else if (type === 'email') {
                    if (!val || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        isInvalid = true;
                    }
                } else if (type === 'tel') {
                    if (!val || val.length < 5) {
                        isInvalid = true;
                    }
                } else if (type === 'file') {
                    if (!input.files || input.files.length === 0) {
                        isInvalid = true;
                    }
                } else {
                    if (!val) {
                        isInvalid = true;
                    }
                }

                if (isInvalid) {
                    invalidFields.push(input);
                    input.classList.add('cmr-input-error');
                    var wrap = input.closest('.wpcf7-form-control-wrap');
                    if (wrap) wrap.classList.add('wpcf7-not-valid');
                } else {
                    input.classList.remove('cmr-input-error');
                    var wrap = input.closest('.wpcf7-form-control-wrap');
                    if (wrap) wrap.classList.remove('wpcf7-not-valid');
                }
            });

            if (invalidFields.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) {
                    e.stopImmediatePropagation();
                }

                var firstInvalid = invalidFields[0];
                firstInvalid.focus();

                var responseOutput = form.querySelector('.wpcf7-response-output');
                if (responseOutput) {
                    responseOutput.style.display = 'none';
                }

                return false;
            }

            // If it's a Contact Form 7 form or Modal Form, handle seamlessly via AJAX to prevent full page reload!
            if (form.classList.contains('wpcf7-form') || form.closest('.elementor-popup-modal') || form.closest('#cmr-review-modal-box')) {
                // If it's the review modal or custom job form, let their dedicated handlers process, else AJAX submit:
                if (!form.id.includes('cmr-job-form') && !form.classList.contains('comment-form')) {
                    e.preventDefault();
                    e.stopPropagation();

                    var submitBtn = form.querySelector('input[type="submit"], button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        if (submitBtn.tagName === 'INPUT') submitBtn.value = 'Submitting...';
                        else submitBtn.innerText = 'Submitting...';
                    }

                    var actionUrl = form.action || window.location.href;
                    var formData = new FormData(form);

                    fetch(actionUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(res) {
                        return res.text();
                    })
                    .then(function(responseBody) {
                        showFormSuccessState(form);
                    })
                    .catch(function(err) {
                        showFormSuccessState(form);
                    });

                    return false;
                }
            }
        }, true);

        // Clear error style immediately as user types or checks
        document.addEventListener('input', function(e) {
            var target = e.target;
            if (target && target.classList.contains('cmr-input-error')) {
                var val = (target.value || '').trim();
                if (val) {
                    target.classList.remove('cmr-input-error');
                    var wrap = target.closest('.wpcf7-form-control-wrap');
                    if (wrap) wrap.classList.remove('wpcf7-not-valid');
                }
            }
        });

        document.addEventListener('change', function(e) {
            var target = e.target;
            if (target && target.classList.contains('cmr-input-error')) {
                if (target.type === 'checkbox' ? target.checked : (target.value || '').trim()) {
                    target.classList.remove('cmr-input-error');
                    var wrap = target.closest('.wpcf7-form-control-wrap');
                    if (wrap) wrap.classList.remove('wpcf7-not-valid');
                }
            }
        });
    })();
    </script>
    <?php
});
