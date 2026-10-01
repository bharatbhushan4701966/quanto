<?php
/**
 * CMR Universal Form Validation Handler
 * Enforces that ALL input fields across all website forms (Contact Form 7,
 * Elementor Popups, Consultation, Free Report, Job Applications, Reviews)
 * are strictly REQUIRED before submission.
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

// 2. Client-side Universal Form Validation Controller
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
                    // Skip privacy / terms / acceptance / radio / checkbox labels
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

        // Run on DOM load and whenever popups/DOM change
        function initFormValidation() {
            enforceRequiredAttributes();
            addRequiredStarsToLabels();
        }

        document.addEventListener('DOMContentLoaded', initFormValidation);
        window.addEventListener('load', initFormValidation);
        setInterval(initFormValidation, 1500);

        // Pre-Submit Validation Listener (Capturing phase to run before CF7/Elementor handlers)
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
                // Ignore elements that are hidden inside inactive tabs/drawers
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
                    // Text, textarea, number, select
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

                // Trigger browser native validation bubble if supported
                if (typeof firstInvalid.reportValidity === 'function') {
                    firstInvalid.reportValidity();
                }

                // Show validation message in form response area if present
                var responseOutput = form.querySelector('.wpcf7-response-output');
                if (responseOutput) {
                    responseOutput.innerText = 'Please fill in all required fields before submitting.';
                    responseOutput.style.display = 'block';
                    responseOutput.classList.add('wpcf7-validation-errors');
                }

                return false;
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
