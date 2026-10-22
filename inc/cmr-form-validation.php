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
                    var inputName = (input.name || '').toLowerCase();
                    var inputId = (input.id || '').toLowerCase();
                    if (inputName === 'coupon_code' || 
                        inputId === 'coupon_code' || 
                        input.classList.contains('cmr-coupon-input') || 
                        inputName === 'billing_address_2' || 
                        inputName === 'order_comments' || 
                        input.hasAttribute('data-optional') ||
                        input.closest('.cmr-promo-box') ||
                        input.closest('.cmr-coupon-form')) {
                        input.removeAttribute('required');
                        input.required = false;
                        input.removeAttribute('aria-required');
                        input.classList.remove('cmr-input-error');
                        return;
                    }

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

        // Render Success State inside Modal / Form Container (Clears form, hides form & headings, shows clean Thank You card)
        function showFormSuccessState(form, customMsg) {
            if (!form) return;

            // 1. Clear all form inputs, textareas, selects, and email OTP verification badges
            try {
                form.reset();
                form.querySelectorAll('input, textarea, select').forEach(function(inp) {
                    if (inp.type !== 'submit' && inp.type !== 'button' && inp.type !== 'hidden') {
                        inp.value = '';
                    }
                    inp.removeAttribute('data-email-verified');
                    inp.readOnly = false;
                    inp.style.removeProperty('padding-right');
                    inp.classList.remove('cmr-input-error');
                });
                form.querySelectorAll('.cmr-email-badge-container').forEach(function(bc) {
                    bc.innerHTML = '';
                });
                form.querySelectorAll('.cmr-email-unverified-notice').forEach(function(n) {
                    n.remove();
                });
            } catch(e) {}

            // Prevent duplicate firing
            if (form.getAttribute('data-cmr-success-shown') === 'true') {
                return;
            }
            form.setAttribute('data-cmr-success-shown', 'true');

            var cf7Msg = customMsg;
            if (!cf7Msg) {
                var responseOutput = form.querySelector('.wpcf7-response-output');
                if (responseOutput && responseOutput.innerText && responseOutput.innerText.trim()) {
                    cf7Msg = responseOutput.innerText.trim();
                }
            }

            if (!cf7Msg || cf7Msg === 'Please fill in all required fields before submitting.' || cf7Msg.indexOf('Validation error') !== -1 || cf7Msg.length < 5 || cf7Msg.toLowerCase().indexOf('limit') !== -1) {
                cf7Msg = 'Your request has been successfully submitted.<br>Our team will review your details and get back to you shortly.';
            }

            // Target the modal column or form container
            var modalColumn = form.closest('.elementor-element-a26a79a, .cmr-modal-right, .cmr-modal-form-wrapper');
            if (!modalColumn) {
                var popupModal = form.closest('.elementor-popup-modal, .dialog-widget-content');
                if (popupModal) {
                    modalColumn = form.closest('.e-con.e-child, .elementor-column') || popupModal;
                }
            }

            var container = modalColumn || form.closest('.custom-subscribe-form, .custom-report-form, .custom-consultation-form, .cmr-modal-form-wrapper, .wpcf7, .cmr-form') || form.parentElement;

            // Hide the form itself
            form.style.setProperty('display', 'none', 'important');

            // Hide all siblings/widgets inside the container (e.g. heading "Subscribe to Insights", icon list "No spam...", etc.)
            if (container && container !== form) {
                var siblings = container.querySelectorAll('h1, h2, h3, h4, p, label, .elementor-widget-heading, .elementor-widget-icon-list, .subscription-heading, .subscription-options, .sub-desc, .privacy-check, .wpcf7-response-output');
                siblings.forEach(function(el) {
                    if (!el.closest('.cmr-form-success-wrapper')) {
                        el.style.setProperty('display', 'none', 'important');
                    }
                });
            }

            // Remove any previous success wrapper
            var targetParent = container || form.parentElement;
            var prevSuccess = targetParent.querySelector('.cmr-form-success-wrapper');
            if (prevSuccess) prevSuccess.remove();

            // Create clean centered Thank You screen
            var successWrapper = document.createElement('div');
            successWrapper.className = 'cmr-form-success-wrapper';
            successWrapper.innerHTML = 
                '<div class="cmr-form-success-icon">' +
                    '<svg viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#10b981" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">' +
                        '<polyline points="20 6 9 17 4 12"></polyline>' +
                    '</svg>' +
                '</div>' +
                '<h3>Thank You!</h3>' +
                '<p>' + cf7Msg + '</p>';

            targetParent.appendChild(successWrapper);

            // If inside a popup modal, auto-close after 2.5 seconds (2500ms)
            if (form.closest('.elementor-popup-modal, .dialog-widget, #cmr-review-modal')) {
                setTimeout(function() {
                    cmrCloseModalAndReset();
                }, 2500);
            }
        }

        // Cookie persistence helpers for 24h form rate limits
        function cmrSetCookie(name, val, hours) {
            var expires = "";
            if (hours) {
                var date = new Date();
                date.setTime(date.getTime() + (hours * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + encodeURIComponent(val || "") + expires + "; path=/; SameSite=Lax";
        }

        function cmrGetCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }

        function cmrRecordSuccessfulSubmission(form) {
            if (!form) return;
            var formId = cmrGetFormId(form);
            if (!formId) return;

            var relatedIds = cmrGetRelatedFormIds(formId);
            var maxLimit = (typeof window.CMR_RATE_LIMIT_MAX !== 'undefined') ? window.CMR_RATE_LIMIT_MAX : 1;
            var curCount = 0;
            try {
                relatedIds.forEach(function(rid) {
                    var item = localStorage.getItem('cmr_limit_' + rid) || cmrGetCookie('cmr_limit_' + rid);
                    if (item) {
                        try {
                            var parsed = JSON.parse(item);
                            if (Date.now() - (parsed.timestamp || 0) < 24 * 3600 * 1000) {
                                if (parsed.count > curCount) curCount = parsed.count;
                            }
                        } catch(err) {
                            curCount = 1;
                        }
                    }
                });
            } catch(e) {}
            curCount++;
            var payload = JSON.stringify({ count: curCount, timestamp: Date.now() });
            relatedIds.forEach(function(rid) {
                try {
                    localStorage.setItem('cmr_limit_' + rid, payload);
                    sessionStorage.setItem('cmr_limit_' + rid, '1');
                    cmrSetCookie('cmr_limit_' + rid, payload, 24);
                } catch(e) {}
                if (!window.CMR_RATE_LIMITS) window.CMR_RATE_LIMITS = {};
                window.CMR_RATE_LIMITS[rid] = { count: curCount, max: maxLimit, blocked: true };
            });

            // Permanently disable submit button on current form and any related instances
            cmrDisableSubmitButton(form);
            relatedIds.forEach(function(rid) {
                document.querySelectorAll('form[id*="wpcf7-f' + rid + '"], div[data-wpcf7-id="' + rid + '"] form').forEach(function(f) {
                    cmrDisableSubmitButton(f);
                });
            });
        }

        // Listen for Contact Form 7 native AJAX success event
        document.addEventListener('wpcf7mailsent', function(e) {
            var form = e.target;
            var msg = (e.detail && e.detail.apiResponse && e.detail.apiResponse.message) ? e.detail.apiResponse.message : null;
            if (form) {
                cmrRecordSuccessfulSubmission(form);
                showFormSuccessState(form, msg);
            }
        });

        // Listen for Elementor Pro Form native AJAX success event
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('submit_success', function(evt, response) {
                var form = evt.target;
                if (form) {
                    cmrRecordSuccessfulSubmission(form);
                    showFormSuccessState(form);
                }
            });
        }

        // Listen for CF7 validation / spam / limit rejections
        document.addEventListener('wpcf7invalid', function(e) {
            var form = e.target;
            var msg = (e.detail && e.detail.apiResponse && e.detail.apiResponse.message) ? e.detail.apiResponse.message : '';
            if (form && (msg.toLowerCase().includes('limit') || msg.toLowerCase().includes('24 hours') || msg.toLowerCase().includes('tomorrow'))) {
                cmrDisableSubmitButton(form, msg);
            }
        });

        // Rate Limiter Helpers: Check if form reached daily submission limit (1 per 24 hours) and disable button
        function cmrGetFormId(form) {
            if (!form) return '';
            var inputWpcf7 = form.querySelector('input[name="_wpcf7"]');
            if (inputWpcf7 && inputWpcf7.value) return inputWpcf7.value.trim();

            var wrap = form.closest('[data-wpcf7-id]');
            if (wrap && wrap.getAttribute('data-wpcf7-id')) return wrap.getAttribute('data-wpcf7-id').trim();

            if (form.id && form.id.includes('wpcf7-f')) {
                var match = form.id.match(/wpcf7-f(\d+)/);
                if (match && match[1]) return match[1];
            }

            if (form.id === 'cmr-job-form' || form.closest('#cmr-job-form')) return 'job_application';

            return form.getAttribute('data-form-id') || form.id || '';
        }

        function cmrGetRelatedFormIds(formId) {
            if (formId === '8573' || formId === '7935') {
                return ['7935', '8573'];
            }
            return [formId];
        }

        function cmrIsFormRateLimited(form) {
            if (!form) return false;
            var formId = cmrGetFormId(form);
            if (!formId) return false;
            var relatedIds = cmrGetRelatedFormIds(formId);
            var maxLimit = (typeof window.CMR_RATE_LIMIT_MAX !== 'undefined') ? window.CMR_RATE_LIMIT_MAX : 1;

            // 1. Check server-provided rate limits (live DB status for this IP)
            if (window.CMR_RATE_LIMITS) {
                for (var i = 0; i < relatedIds.length; i++) {
                    var rInfo = window.CMR_RATE_LIMITS[relatedIds[i]];
                    if (rInfo && (rInfo.blocked || rInfo.count >= maxLimit)) {
                        return true;
                    }
                }
            }

            // 2. Check Cookie (persists across cached pages and modals)
            for (var k = 0; k < relatedIds.length; k++) {
                if (cmrGetCookie('cmr_limit_' + relatedIds[k])) {
                    return true;
                }
            }

            // 3. Check sessionStorage
            try {
                for (var s = 0; s < relatedIds.length; s++) {
                    if (sessionStorage.getItem('cmr_limit_' + relatedIds[s])) {
                        return true;
                    }
                }
            } catch(e) {}

            // 4. Check localStorage (persists in browser across cached pages for 24h)
            try {
                for (var j = 0; j < relatedIds.length; j++) {
                    var item = localStorage.getItem('cmr_limit_' + relatedIds[j]);
                    if (item) {
                        var parsed = JSON.parse(item);
                        var now = Date.now();
                        if (now - parsed.timestamp < 24 * 3600 * 1000) {
                            if (parsed.count >= maxLimit) {
                                return true;
                            }
                        } else {
                            localStorage.removeItem('cmr_limit_' + relatedIds[j]);
                        }
                    }
                }
            } catch(e) {}

            return false;
        }

        function cmrDisableSubmitButton(form, msg) {
            if (!form) return;
            var submitBtn = form.querySelector('input[type="submit"], button[type="submit"], .wpcf7-submit');
            if (!submitBtn) return;

            submitBtn.disabled = true;
            submitBtn.setAttribute('disabled', 'disabled');
            submitBtn.classList.add('cmr-limit-disabled');
            submitBtn.style.setProperty('opacity', '0.55', 'important');
            submitBtn.style.setProperty('cursor', 'not-allowed', 'important');
            submitBtn.style.setProperty('pointer-events', 'auto', 'important');
            submitBtn.style.setProperty('background', '#9ca3af', 'important');
            submitBtn.style.setProperty('background-color', '#9ca3af', 'important');
            submitBtn.style.setProperty('background-image', 'none', 'important');
            submitBtn.style.setProperty('border-color', '#9ca3af', 'important');
            submitBtn.style.setProperty('color', '#ffffff', 'important');
            submitBtn.style.setProperty('filter', 'grayscale(1)', 'important');
            submitBtn.style.setProperty('box-shadow', 'none', 'important');
            submitBtn.style.setProperty('transform', 'none', 'important');

            // Set cursor: not-allowed on parent wrap so the blocked cursor shows everywhere over the button
            var wrap = submitBtn.closest('.wpcf7-submit-wrap, .cmr-submit-wrap, p, .elementor-button-wrapper');
            if (wrap) {
                wrap.style.setProperty('cursor', 'not-allowed', 'important');
            }

            // Remove any notice if present
            var existingNotice = form.querySelector('.cmr-limit-notice');
            if (existingNotice) {
                existingNotice.remove();
            }
        }

        function cmrCheckAndApplyRateLimits() {
            var forms = document.querySelectorAll(
                'form.wpcf7-form, ' +
                '.elementor-popup-modal form, ' +
                '.custom-report-form, ' +
                '.custom-consultation-form, ' +
                '.custom-subscribe-form, ' +
                '#cmr-job-form, ' +
                '.cmr-modal-form-wrapper form'
            );
            forms.forEach(function(form) {
                if (cmrIsFormRateLimited(form)) {
                    cmrDisableSubmitButton(form);
                }
            });
        }

        // Initialize validators
        function initFormValidation() {
            enforceRequiredAttributes();
            addRequiredStarsToLabels();
            cmrCheckAndApplyRateLimits();
        }

        document.addEventListener('DOMContentLoaded', initFormValidation);
        window.addEventListener('load', initFormValidation);
        if (typeof jQuery !== 'undefined') {
            jQuery(document).on('elementor/popup/show', initFormValidation);
        }

        // Pre-Submit Validation & AJAX Submission Listener
        document.addEventListener('submit', function(e) {
            var form = e.target;
            if (!form || form.tagName !== 'FORM') return;

            // Exclude search forms and WooCommerce forms (Checkout, Cart, Account, Login)
            if (form.classList.contains('search-form') || 
                form.getAttribute('role') === 'search' ||
                form.classList.contains('woocommerce-checkout') ||
                form.classList.contains('woocommerce-cart-form') ||
                form.classList.contains('woocommerce-form-login') ||
                form.classList.contains('woocommerce-form-register') ||
                form.classList.contains('checkout') ||
                form.closest('.woocommerce-checkout') ||
                form.closest('.cmr-checkout-wrap')) return;

            // Block submission if 24h limit reached
            if (cmrIsFormRateLimited(form)) {
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) {
                    e.stopImmediatePropagation();
                }
                cmrDisableSubmitButton(form, 'You have reached the maximum daily limit of 1 submission for this form. Please try again after 24 hours.');
                return false;
            }

            var invalidFields = [];
            var inputs = form.querySelectorAll(
                'input:not([type="hidden"]):not([type="submit"]):not([type="button"]):not([type="reset"]), textarea, select'
            );

            inputs.forEach(function(input) {
                if (input.offsetParent === null && !input.closest('.elementor-popup-modal') && !input.closest('#cmr-review-modal-overlay')) {
                    return;
                }

                // Skip optional fields, coupon codes, and reCAPTCHA response elements
                var inputName = (input.name || '').toLowerCase();
                var inputId = (input.id || '').toLowerCase();
                if (inputName === 'g-recaptcha-response' ||
                    input.classList.contains('g-recaptcha-response') ||
                    inputName === 'coupon_code' || 
                    inputId === 'coupon_code' || 
                    input.classList.contains('cmr-coupon-input') || 
                    inputName === 'billing_address_2' || 
                    inputName === 'order_comments' || 
                    input.hasAttribute('data-optional') ||
                    input.closest('.cmr-promo-box') ||
                    input.closest('.cmr-coupon-form')) {
                    input.classList.remove('cmr-input-error');
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
                } else if (type === 'email' || input.name === 'your-email' || input.name === 'email' || input.name === 'email-address') {
                    if (!val || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                        isInvalid = true;
                    } else if (input.getAttribute('data-email-verified') !== 'true') {
                        isInvalid = true;
                        var controlWrap = input.closest('.cmr-email-field-wrapper, .wpcf7-form-control-wrap') || input;
                        var fieldContainer = input.closest('.form-field, .cmr-input-group, p') || controlWrap.parentElement;
                        var existingNotice = fieldContainer ? fieldContainer.querySelector('.cmr-email-unverified-notice') : null;
                        if (!existingNotice) {
                            var notice = document.createElement('span');
                            notice.className = 'cmr-email-unverified-notice';
                            notice.textContent = 'Please click "Verify Email" to verify your email before submitting.';
                            if (controlWrap.nextSibling) {
                                controlWrap.parentNode.insertBefore(notice, controlWrap.nextSibling);
                            } else {
                                controlWrap.parentNode.appendChild(notice);
                            }
                        }
                        var vBtn = (controlWrap.parentElement || input.parentElement).querySelector('.cmr-verify-badge, .cmr-verify-email-trigger-btn');
                        if (vBtn) {
                            vBtn.classList.add('cmr-pulse-attention');
                        }
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

            // Google reCAPTCHA v2 Validation
            var recaptchaContainer = form.querySelector('.g-recaptcha, .wpcf7-recaptcha, .elementor-g-recaptcha, [data-name*="recaptcha"]');
            var recaptchaResponseEl = form.querySelector('textarea[name="g-recaptcha-response"], input[name="g-recaptcha-response"]');

            if (recaptchaContainer || recaptchaResponseEl) {
                var hasCaptchaSolved = false;
                var widgetId = recaptchaContainer ? (recaptchaContainer.getAttribute('data-widget-id') || recaptchaContainer._grecaptchaWidgetId) : null;

                if (recaptchaResponseEl && recaptchaResponseEl.value.trim() !== '') {
                    hasCaptchaSolved = true;
                } else if (typeof grecaptcha !== 'undefined' && typeof grecaptcha.getResponse === 'function') {
                    try {
                        var resp = (widgetId !== null && widgetId !== undefined) ? grecaptcha.getResponse(widgetId) : grecaptcha.getResponse();
                        if (resp && resp.trim() !== '') {
                            hasCaptchaSolved = true;
                        }
                    } catch(e) {
                        try {
                            if (grecaptcha.getResponse().trim() !== '') {
                                hasCaptchaSolved = true;
                            }
                        } catch(e2) {}
                    }
                }

                var visualBox = recaptchaContainer || (recaptchaResponseEl ? recaptchaResponseEl.parentElement : null);
                if (!hasCaptchaSolved) {
                    invalidFields.push(visualBox || recaptchaResponseEl);
                    if (visualBox && (visualBox.offsetHeight > 10 || visualBox.querySelector('iframe'))) {
                        visualBox.style.setProperty('outline', '2px solid #ef4444', 'important');
                        visualBox.style.setProperty('outline-offset', '2px', 'important');
                        visualBox.style.setProperty('border-radius', '4px', 'important');
                        visualBox.classList.add('cmr-input-error');
                    }
                } else {
                    if (visualBox) {
                        visualBox.style.removeProperty('outline');
                        visualBox.style.removeProperty('outline-offset');
                        visualBox.classList.remove('cmr-input-error');
                    }
                }
            }

            if (invalidFields.length > 0) {
                e.preventDefault();
                e.stopPropagation();
                if (e.stopImmediatePropagation) {
                    e.stopImmediatePropagation();
                }

                var firstInvalid = invalidFields[0];
                if (firstInvalid && typeof firstInvalid.focus === 'function' && firstInvalid.tagName !== 'DIV') {
                    firstInvalid.focus();
                } else if (firstInvalid && typeof firstInvalid.scrollIntoView === 'function') {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                var responseOutput = form.querySelector('.wpcf7-response-output');
                if (responseOutput) {
                    responseOutput.style.display = 'none';
                }

                return false;
            }

            // All validation checks passed!
            // Intercept Contact Form 7 / Modal forms to handle seamlessly via AJAX:
            // Prevents full page reload and prevents URL from appending #wpcf7-...
            if (form.classList.contains('wpcf7-form') || form.querySelector('input[name="_wpcf7"]') || form.closest('.elementor-popup-modal') || form.closest('.custom-subscribe-form')) {
                // If it's the review modal or custom job form, let their dedicated handlers process:
                if (!form.id.includes('cmr-job-form') && !form.classList.contains('comment-form')) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.stopImmediatePropagation) {
                        e.stopImmediatePropagation();
                    }

                    // Check submission lock to prevent double/multiple clicks while request is running
                    if (form.getAttribute('data-cmr-submitting') === 'true') {
                        return false;
                    }
                    form.setAttribute('data-cmr-submitting', 'true');

                    // 1. Submit Button Feedback & Loading Animation
                    var submitBtn = form.querySelector('input[type="submit"], button[type="submit"]');
                    if (submitBtn) {
                        var origVal = submitBtn.value || submitBtn.innerText || 'Submit';
                        submitBtn.setAttribute('data-original-val', origVal);
                        submitBtn.classList.add('cmr-btn-submitting');
                        submitBtn.disabled = true;
                        submitBtn.style.setProperty('pointer-events', 'none', 'important');

                        var lower = origVal.toLowerCase();
                        var loadingText = 'Submitting...';
                        if (lower.indexOf('subscri') !== -1) {
                            loadingText = 'Subscribing...';
                        } else if (lower.indexOf('report') !== -1 || lower.indexOf('download') !== -1) {
                            loadingText = 'Processing...';
                        }

                        if (submitBtn.tagName === 'INPUT') {
                            submitBtn.value = loadingText;
                        } else {
                            submitBtn.innerText = loadingText;
                        }
                    }

                    function restoreButton() {
                        form.removeAttribute('data-cmr-submitting');
                        if (!submitBtn) return;
                        submitBtn.disabled = false;
                        submitBtn.style.removeProperty('pointer-events');
                        submitBtn.classList.remove('cmr-btn-submitting');
                        var orig = submitBtn.getAttribute('data-original-val');
                        if (orig) {
                            if (submitBtn.tagName === 'INPUT') submitBtn.value = orig;
                            else submitBtn.innerText = orig;
                        }
                    }

                    // 2. Build Submission Payload
                    var formData = new FormData(form);
                    var formId = cmrGetFormId(form);

                    // 3. Submit via Contact Form 7 REST API
                    var restUrl = (window.wpcf7 && window.wpcf7.api && window.wpcf7.api.root) ?
                        (window.wpcf7.api.root + window.wpcf7.api.namespace + '/contact-forms/' + formId + '/feedback') :
                        ('/wp-json/contact-form-7/v1/contact-forms/' + formId + '/feedback');

                    fetch(restUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) {
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }
                        return res.json();
                    })
                    .then(function(data) {
                        var isRateLimited = data && (
                            (data.message && (
                                data.message.toLowerCase().indexOf('limit') !== -1 ||
                                data.message.toLowerCase().indexOf('24 hours') !== -1 ||
                                data.message.toLowerCase().indexOf('tomorrow') !== -1
                            )) ||
                            (data.cmr_rate_limit && data.cmr_rate_limit.blocked)
                        );

                        if (isRateLimited) {
                            // User is repeating submission / blocked by rate limiter:
                            // DO NOT show Thank You message!
                            // Permanently disable submit button and block cursor.
                            form.removeAttribute('data-cmr-submitting');
                            restoreButton();
                            cmrRecordSuccessfulSubmission(form);
                            cmrDisableSubmitButton(form);

                            var respOut = form.querySelector('.wpcf7-response-output');
                            if (respOut) {
                                respOut.style.display = 'block';
                                respOut.className = 'wpcf7-response-output wpcf7-validation-errors';
                                respOut.textContent = data.message || 'You have reached the maximum limit of 1 submission for this form in 24 hours. Please try again tomorrow.';
                            }
                            return;
                        }

                        if (data && data.status === 'mail_sent') {
                            // First time submission:
                            // Reset form fields, disable button with blocked cursor, and SHOW THANK YOU MESSAGE!
                            form.removeAttribute('data-cmr-submitting');
                            cmrRecordSuccessfulSubmission(form);
                            cmrDisableSubmitButton(form);
                            try { form.reset(); } catch(e) {}
                            try {
                                form.dispatchEvent(new CustomEvent('wpcf7mailsent', { bubbles: true, detail: { apiResponse: data } }));
                            } catch(ev) {}
                            showFormSuccessState(form, data.message);
                        } else if (data && (data.status === 'validation_failed' || data.status === 'spam' || data.status === 'mail_failed')) {
                            restoreButton();
                            var respOut = form.querySelector('.wpcf7-response-output');
                            if (respOut) {
                                respOut.style.display = 'block';
                                respOut.className = 'wpcf7-response-output wpcf7-validation-errors';
                                respOut.textContent = data.message || 'Submission failed. Please check the fields and try again.';
                            } else {
                                alert(data.message || 'Submission failed. Please check the fields and try again.');
                            }
                        } else {
                            restoreButton();
                        }
                    })
                    .catch(function(err) {
                        // Fallback: Submit to form.action via AJAX without page reload
                        var actionUrl = form.action || window.location.href;
                        fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(function() {
                            form.removeAttribute('data-cmr-submitting');
                            cmrRecordSuccessfulSubmission(form);
                            cmrDisableSubmitButton(form);
                            try { form.reset(); } catch(e) {}
                            showFormSuccessState(form);
                        })
                        .catch(function() {
                            restoreButton();
                            alert('Network error while submitting. Please check your internet connection.');
                        });
                    });

                    return false;
                }
            }
        }, true);

        // Intercept clicks on disabled or rate-limited buttons to ensure cursor is blocked and no submit triggers
        document.addEventListener('click', function(e) {
            var btn = e.target ? e.target.closest('input[type="submit"], button[type="submit"], .wpcf7-submit') : null;
            if (btn) {
                var form = btn.closest('form');
                if (btn.disabled || btn.classList.contains('cmr-limit-disabled') || (form && cmrIsFormRateLimited(form))) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (e.stopImmediatePropagation) {
                        e.stopImmediatePropagation();
                    }
                    if (form) {
                        cmrDisableSubmitButton(form);
                    }
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

        // Clear reCAPTCHA error styling once user clicks or interacts with the captcha
        document.addEventListener('click', function(e) {
            var target = e.target;
            var box = target ? target.closest('.g-recaptcha, .wpcf7-recaptcha, .elementor-g-recaptcha, [data-name*="recaptcha"]') : null;
            if (box) {
                var timer = setInterval(function() {
                    var form = box.closest('form');
                    var ta = form ? form.querySelector('textarea[name="g-recaptcha-response"]') : null;
                    var solved = (ta && ta.value.trim() !== '');
                    if (!solved && typeof grecaptcha !== 'undefined' && grecaptcha.getResponse) {
                        try { solved = (grecaptcha.getResponse().trim() !== ''); } catch(err) {}
                    }
                    if (solved) {
                        box.style.removeProperty('outline');
                        box.style.removeProperty('outline-offset');
                        box.classList.remove('cmr-input-error');
                        clearInterval(timer);
                    }
                }, 400);
                setTimeout(function() { clearInterval(timer); }, 20000);
            }
        });

        // Auto-reset reCAPTCHA on Contact Form 7 events so user is never stuck with an expired/used token
        ['wpcf7invalid', 'wpcf7spam', 'wpcf7mailfailed'].forEach(function(evtName) {
            document.addEventListener(evtName, function(e) {
                var form = e.target;
                if (!form) return;
                var rcEl = form.querySelector('.wpcf7-recaptcha, .g-recaptcha');
                if (rcEl && typeof grecaptcha !== 'undefined' && typeof grecaptcha.reset === 'function') {
                    var wid = rcEl.getAttribute('data-widget-id') || rcEl._grecaptchaWidgetId;
                    try {
                        if (wid !== null && wid !== undefined) {
                            grecaptcha.reset(wid);
                        } else {
                            grecaptcha.reset();
                        }
                    } catch(err) {}
                }
                var ta = form.querySelector('textarea[name="g-recaptcha-response"]');
                if (ta) ta.value = '';
            });
        });
        // Clean up URL hash if page had #wpcf7-... left over from previous standard submits
        function cmrCleanUrlHash() {
            if (window.location.hash && window.location.hash.indexOf('wpcf7-') !== -1) {
                if (window.history && window.history.replaceState) {
                    window.history.replaceState(null, document.title, window.location.pathname + window.location.search);
                }
            }
        }
        cmrCleanUrlHash();
    })();
    </script>
    <?php
});

