<?php
/**
 * Global Modal & Popup Enhancements
 * - Centers all modals (Elementor Popups, Bootstrap, Magnific Popup, Review Modals, etc.)
 * - Adds a translucent dark overlay with backdrop blur to show blurred background content
 * - Prevents background page scrolling while any modal is open
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'wp_head', 'cmr_global_modal_styles', 999 );
function cmr_global_modal_styles() {
    ?>
    <style id="cmr-global-modal-styles">
        /* -------------------------------------------------------------
         * 1. BODY & HTML SCROLL LOCKING WHEN MODAL IS ACTIVE
         * ------------------------------------------------------------- */
        html.cmr-modal-open,
        body.cmr-modal-open,
        html.elementor-popup-modal-open,
        body.elementor-popup-modal-open,
        body.modal-open {
            overflow: hidden !important;
            height: 100% !important;
            touch-action: none !important;
            -webkit-overflow-scrolling: auto !important;
            overscroll-behavior: none !important;
        }

        /* -------------------------------------------------------------
         * 2. ELEMENTOR POPUPS & LIGHTBOXES
         * ------------------------------------------------------------- */
        .dialog-widget.dialog-type-lightbox,
        .dialog-widget.dialog-lightbox-widget,
        .elementor-popup-modal.dialog-type-lightbox,
        .dialog-type-lightbox {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 999999 !important;
            overflow-y: auto !important;
            overscroll-behavior: contain !important;
            padding: 20px !important;
            box-sizing: border-box !important;
        }

        /* Backdrop element if Elementor renders separate backdrop */
        .dialog-backdrop,
        .dialog-lightbox-backdrop {
            background-color: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            position: fixed !important;
            inset: 0 !important;
            z-index: 999998 !important;
        }

        /* Modal Dialog / Content Container */
        .dialog-widget.dialog-type-lightbox .dialog-widget-content,
        .dialog-widget.dialog-lightbox-widget .dialog-widget-content,
        .elementor-popup-modal .dialog-widget-content {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            right: auto !important;
            bottom: auto !important;
            transform: none !important;
            margin: auto !important;
            max-height: 90vh !important;
            max-width: 95vw !important;
            overflow-y: auto !important;
            border-radius: 16px !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.4) !important;
            box-sizing: border-box !important;
        }

        /* Fix internal popup elements centering */
        .elementor-popup-modal .dialog-message {
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 100% !important;
        }

        /* -------------------------------------------------------------
         * 3. BOOTSTRAP MODALS
         * ------------------------------------------------------------- */
        .modal-backdrop,
        .modal-backdrop.show {
            background-color: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 1050 !important;
        }

        .modal {
            position: fixed !important;
            inset: 0 !important;
            z-index: 1055 !important;
            display: none;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            overscroll-behavior: contain !important;
        }

        .modal.show {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 20px !important;
        }

        .modal-dialog {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: calc(100vh - 60px) !important;
            margin: auto !important;
            max-width: 600px;
            width: 100%;
        }

        .modal-content {
            border-radius: 16px !important;
            border: none !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35) !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
        }

        /* -------------------------------------------------------------
         * 4. MAGNIFIC POPUP (LIGHTBOX)
         * ------------------------------------------------------------- */
        .mfp-bg {
            background: rgba(15, 23, 42, 0.5) !important;
            opacity: 1 !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 100000 !important;
            position: fixed !important;
            inset: 0 !important;
        }

        .mfp-wrap {
            position: fixed !important;
            inset: 0 !important;
            z-index: 100001 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow-y: auto !important;
            overscroll-behavior: contain !important;
            padding: 20px !important;
            box-sizing: border-box !important;
        }

        .mfp-container {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            position: relative !important;
        }

        .mfp-content {
            margin: auto !important;
            vertical-align: middle !important;
            position: relative !important;
            max-height: 90vh !important;
            max-width: 95vw !important;
        }

        /* -------------------------------------------------------------
         * 5. REVIEW MODALS (CMR CUSTOM)
         * ------------------------------------------------------------- */
        #cmr-review-modal-overlay,
        #cmr-review-modal {
            position: fixed !important;
            inset: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            background: rgba(15, 23, 42, 0.5) !important;
            backdrop-filter: blur(10px) !important;
            -webkit-backdrop-filter: blur(10px) !important;
            z-index: 2147483647 !important;
            align-items: center !important;
            justify-content: center !important;
            overflow-y: auto !important;
            overscroll-behavior: contain !important;
            padding: 20px !important;
            box-sizing: border-box !important;
        }

        #cmr-review-modal-box,
        .cmr-review-modal-content {
            margin: auto !important;
            position: relative !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
            border-radius: 16px !important;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.35) !important;
        }
    </style>
    <?php
}

add_action( 'wp_footer', 'cmr_global_modal_script', 999 );
function cmr_global_modal_script() {
    ?>
    <script id="cmr-global-modal-js">
    (function() {
        // Function to lock or unlock scroll
        function updateModalScrollLock() {
            var isAnyModalOpen = false;

            // Check Elementor Popups
            var elemPopups = document.querySelectorAll('.dialog-widget.dialog-type-lightbox, .elementor-popup-modal');
            for (var i = 0; i < elemPopups.length; i++) {
                var el = elemPopups[i];
                var style = window.getComputedStyle(el);
                if (style && style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0') {
                    isAnyModalOpen = true;
                    break;
                }
            }

            // Check Bootstrap Modals
            if (!isAnyModalOpen) {
                var bsModals = document.querySelectorAll('.modal.show, .modal[style*="display: block"]');
                if (bsModals.length > 0) isAnyModalOpen = true;
            }

            // Check Magnific Popups
            if (!isAnyModalOpen) {
                var mfp = document.querySelector('.mfp-wrap.mfp-ready, .mfp-bg.mfp-ready');
                if (mfp) isAnyModalOpen = true;
            }

            // Check CMR Review Modals
            if (!isAnyModalOpen) {
                var revOverlay = document.getElementById('cmr-review-modal-overlay');
                if (revOverlay && (revOverlay.classList.contains('cmr-open') || revOverlay.style.display === 'flex' || revOverlay.style.display === 'block')) {
                    isAnyModalOpen = true;
                }
                var revModal = document.getElementById('cmr-review-modal');
                if (revModal && (revModal.style.display === 'flex' || revModal.style.display === 'block')) {
                    isAnyModalOpen = true;
                }
            }

            if (isAnyModalOpen) {
                document.documentElement.classList.add('cmr-modal-open');
                document.body.classList.add('cmr-modal-open');
            } else {
                document.documentElement.classList.remove('cmr-modal-open');
                document.body.classList.remove('cmr-modal-open');
            }
        }

        // Setup MutationObserver to automatically catch dynamically rendered dialogs
        document.addEventListener('DOMContentLoaded', function() {
            var observer = new MutationObserver(function(mutations) {
                updateModalScrollLock();
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true,
                attributes: true,
                attributeFilter: ['class', 'style']
            });

            // jQuery Elementor & Bootstrap Event listeners if available
            if (window.jQuery) {
                jQuery(document).on('elementor/popup/show', function() {
                    updateModalScrollLock();
                });
                jQuery(document).on('elementor/popup/hide', function() {
                    setTimeout(updateModalScrollLock, 50);
                });
                jQuery(document).on('shown.bs.modal show.bs.modal', function() {
                    updateModalScrollLock();
                });
                jQuery(document).on('hidden.bs.modal hide.bs.modal', function() {
                    setTimeout(updateModalScrollLock, 50);
                });
                jQuery(document).on('mfpOpen', function() {
                    updateModalScrollLock();
                });
                jQuery(document).on('mfpClose', function() {
                    setTimeout(updateModalScrollLock, 50);
                });
            }

            // Initial check
            updateModalScrollLock();
        });
    })();
    </script>
    <?php
}
