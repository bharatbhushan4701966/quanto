<?php
/**
 * CMR WooCommerce Product Badge Module
 *
 * Allows setting custom badges (Featured, New, Trending, Hot, Custom, None)
 * on WooCommerce products during add/edit and rendering them across report sections.
 * Supports:
 * - Custom badge text
 * - Color picker & hex code with quick swatches
 * - FontAwesome icons with quick icon selector chips
 * - Custom Icon Image / SVG via URL or WP Media Library upload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Enqueue WordPress Media Library & FontAwesome on Product Edit Screens
 */
add_action( 'admin_enqueue_scripts', 'cmr_product_badge_admin_scripts' );
function cmr_product_badge_admin_scripts( $hook ) {
    global $post_type;
    if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && 'product' === $post_type ) {
        wp_enqueue_media();
        wp_enqueue_style( 'fontawesome-style-admin', get_theme_file_uri( '/assets/css/all.css' ), array(), '6.7.2' );
    }
}

/**
 * 2. Add Meta Box to WooCommerce Product Edit Screen
 */
add_action( 'add_meta_boxes', 'cmr_add_product_badge_meta_box' );
function cmr_add_product_badge_meta_box() {
    add_meta_box(
        'cmr_product_badge_meta_box',
        __( 'Report Badge / Ribbon', 'quanto' ),
        'cmr_render_product_badge_meta_box',
        'product',
        'side',
        'high'
    );
}

/**
 * 3. Render Product Badge Meta Box HTML & Live Preview
 */
function cmr_render_product_badge_meta_box( $post ) {
    wp_nonce_field( 'cmr_save_product_badge', 'cmr_product_badge_nonce' );

    $badge_type     = get_post_meta( $post->ID, '_cmr_product_badge_type', true );
    $badge_text     = get_post_meta( $post->ID, '_cmr_product_badge_text', true );
    $badge_color    = get_post_meta( $post->ID, '_cmr_product_badge_color', true );
    $badge_icon     = get_post_meta( $post->ID, '_cmr_product_badge_icon', true );
    $badge_icon_url = get_post_meta( $post->ID, '_cmr_product_badge_icon_url', true );

    if ( empty( $badge_type ) ) {
        $badge_type = 'none';
    }
    ?>
    <link rel="stylesheet" href="<?php echo esc_url( get_theme_file_uri( '/assets/css/all.css' ) ); ?>?ver=6.7.2" />
    <style>
        .cmr-badge-meta-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        }
        .cmr-badge-meta-wrap label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 4px;
            color: #374151;
        }
        .cmr-badge-meta-wrap select,
        .cmr-badge-meta-wrap input[type="text"],
        .cmr-badge-meta-wrap input[type="url"] {
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 13px;
            box-sizing: border-box;
        }
        .cmr-badge-color-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
        }
        .cmr-badge-color-row input[type="color"] {
            width: 38px;
            height: 34px;
            padding: 2px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            cursor: pointer;
            background: #fff;
            flex-shrink: 0;
            box-sizing: border-box;
        }
        .cmr-badge-color-row input[type="text"] {
            flex: 1;
            margin-bottom: 0;
        }
        .cmr-badge-swatches {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 12px;
        }
        .cmr-badge-swatch {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #d1d5db;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .cmr-badge-swatch:hover {
            transform: scale(1.2);
        }
        .cmr-badge-icon-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            margin-bottom: 10px;
        }
        .cmr-badge-icon-chip {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 11px;
            cursor: pointer;
            color: #374151;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            line-height: 1.2;
            transition: all 0.15s ease;
        }
        .cmr-badge-icon-chip i {
            font-size: 12px;
            color: #4b5563;
        }
        .cmr-badge-icon-chip:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
            color: #111827;
        }
        .cmr-badge-icon-chip.active {
            background: #ede9fe !important;
            border-color: #8b5cf6 !important;
            color: #6b46c1 !important;
            font-weight: 600;
        }
        .cmr-badge-icon-chip.active i {
            color: #6b46c1 !important;
        }
        .cmr-badge-url-row {
            display: flex;
            gap: 6px;
            align-items: center;
            margin-bottom: 12px;
        }
        .cmr-badge-url-row input {
            flex: 1;
            margin-bottom: 0 !important;
        }
        .cmr-badge-url-row .button {
            white-space: nowrap;
            padding: 0 8px !important;
            height: 32px !important;
            line-height: 30px !important;
            font-size: 12px !important;
        }
        .cmr-badge-preview-box {
            background: #0b0f19;
            border-radius: 6px;
            padding: 14px;
            text-align: center;
            margin-top: 10px;
        }
        .cmr-badge-preview-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffffff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 4px 10px;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            transition: all 0.2s ease;
        }
        .cmr-badge-preview-pill i {
            font-size: 12px;
            display: inline-block;
        }
        .cmr-badge-preview-pill img {
            width: 14px;
            height: 14px;
            object-fit: contain;
            vertical-align: middle;
            display: inline-block;
        }
        .cmr-badge-preview-empty {
            color: #94a3b8;
            font-size: 12px;
            font-style: italic;
        }
        .cmr-badge-hint {
            font-size: 11px;
            color: #6b7280;
            margin-top: -6px;
            margin-bottom: 10px;
        }
    </style>

    <div class="cmr-badge-meta-wrap">
        <label for="cmr_product_badge_type"><?php esc_html_e( 'Select Badge Type:', 'quanto' ); ?></label>
        <select id="cmr_product_badge_type" name="_cmr_product_badge_type">
            <option value="none" <?php selected( $badge_type, 'none' ); ?>><?php esc_html_e( 'None', 'quanto' ); ?></option>
            <option value="featured" <?php selected( $badge_type, 'featured' ); ?>><?php esc_html_e( 'Featured', 'quanto' ); ?></option>
            <option value="new" <?php selected( $badge_type, 'new' ); ?>><?php esc_html_e( 'New', 'quanto' ); ?></option>
            <option value="trending" <?php selected( $badge_type, 'trending' ); ?>><?php esc_html_e( 'Trending', 'quanto' ); ?></option>
            <option value="hot" <?php selected( $badge_type, 'hot' ); ?>><?php esc_html_e( 'Hot', 'quanto' ); ?></option>
            <option value="custom" <?php selected( $badge_type, 'custom' ); ?>><?php esc_html_e( 'Custom Badge...', 'quanto' ); ?></option>
        </select>

        <div id="cmr_badge_custom_fields" style="<?php echo ( 'none' !== $badge_type ) ? '' : 'display:none;'; ?>">
            
            <!-- Custom Badge Text -->
            <label for="cmr_product_badge_text"><?php esc_html_e( 'Badge Text:', 'quanto' ); ?></label>
            <input type="text" id="cmr_product_badge_text" name="_cmr_product_badge_text" value="<?php echo esc_attr( $badge_text ); ?>" placeholder="<?php esc_attr_e( 'e.g. FEATURED, BESTSELLER, EXCLUSIVE', 'quanto' ); ?>" />

            <!-- Badge Color with Picker & Swatches -->
            <label for="cmr_product_badge_color"><?php esc_html_e( 'Badge Color:', 'quanto' ); ?></label>
            <div class="cmr-badge-color-row">
                <input type="color" id="cmr_badge_color_picker" value="<?php echo esc_attr( ! empty( $badge_color ) ? $badge_color : '#6b46c1' ); ?>" title="<?php esc_attr_e( 'Pick a color', 'quanto' ); ?>" />
                <input type="text" id="cmr_product_badge_color" name="_cmr_product_badge_color" value="<?php echo esc_attr( $badge_color ); ?>" placeholder="#6b46c1" />
            </div>
            <div class="cmr-badge-swatches">
                <span class="cmr-badge-swatch" data-color="#6b46c1" style="background:#6b46c1;" title="Purple"></span>
                <span class="cmr-badge-swatch" data-color="#ea580c" style="background:#ea580c;" title="Orange"></span>
                <span class="cmr-badge-swatch" data-color="#06b6d4" style="background:#06b6d4;" title="Cyan"></span>
                <span class="cmr-badge-swatch" data-color="#ef4444" style="background:#ef4444;" title="Red"></span>
                <span class="cmr-badge-swatch" data-color="#10b981" style="background:#10b981;" title="Emerald"></span>
                <span class="cmr-badge-swatch" data-color="#2563eb" style="background:#2563eb;" title="Blue"></span>
                <span class="cmr-badge-swatch" data-color="#f59e0b" style="background:#f59e0b;" title="Amber"></span>
                <span class="cmr-badge-swatch" data-color="#ec4899" style="background:#ec4899;" title="Pink"></span>
                <span class="cmr-badge-swatch" data-color="#111827" style="background:#111827;" title="Black"></span>
            </div>

            <!-- Custom Icon Image / SVG via URL or WP Media -->
            <label for="cmr_product_badge_icon_url"><?php esc_html_e( 'Icon Image / SVG URL (Optional):', 'quanto' ); ?></label>
            <div class="cmr-badge-url-row">
                <input type="url" id="cmr_product_badge_icon_url" name="_cmr_product_badge_icon_url" value="<?php echo esc_attr( $badge_icon_url ); ?>" placeholder="https://.../icon.svg" />
                <button type="button" class="button" id="cmr_badge_upload_btn" title="<?php esc_attr_e( 'Upload or choose image from Media Library', 'quanto' ); ?>"><span class="dashicons dashicons-upload" style="font-size:16px; line-height:30px;"></span></button>
                <button type="button" class="button" id="cmr_badge_clear_url_btn" title="<?php esc_attr_e( 'Clear image URL', 'quanto' ); ?>">✕</button>
            </div>
            <div class="cmr-badge-hint"><?php esc_html_e( 'Paste an SVG or image URL, or click Upload to select from Media Library.', 'quanto' ); ?></div>

            <!-- FontAwesome Icon Class & Quick Chips -->
            <label for="cmr_product_badge_icon"><?php esc_html_e( 'Or FontAwesome Icon:', 'quanto' ); ?></label>
            <input type="text" id="cmr_product_badge_icon" name="_cmr_product_badge_icon" value="<?php echo esc_attr( $badge_icon ); ?>" placeholder="fa-solid fa-bookmark" />
            <div class="cmr-badge-icon-chips">
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-bookmark"><i class="fa-solid fa-bookmark"></i> Bookmark</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-circle-check"><i class="fa-solid fa-circle-check"></i> Check</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-bolt"><i class="fa-solid fa-bolt"></i> Bolt</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-fire"><i class="fa-solid fa-fire"></i> Fire</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-star"><i class="fa-solid fa-star"></i> Star</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-crown"><i class="fa-solid fa-crown"></i> Crown</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-trophy"><i class="fa-solid fa-trophy"></i> Trophy</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-rocket"><i class="fa-solid fa-rocket"></i> Rocket</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-gem"><i class="fa-solid fa-gem"></i> Gem</span>
                <span class="cmr-badge-icon-chip" data-icon="fa-solid fa-tag"><i class="fa-solid fa-tag"></i> Tag</span>
            </div>
        </div>

        <label style="margin-top: 6px;"><?php esc_html_e( 'Live Preview:', 'quanto' ); ?></label>
        <div class="cmr-badge-preview-box">
            <span id="cmr_badge_preview_pill" class="cmr-badge-preview-pill" style="display:none;">
                <img id="cmr_badge_preview_img" src="" alt="" style="display:none;" />
                <i id="cmr_badge_preview_icon" class="fa-solid fa-bookmark"></i>
                <span id="cmr_badge_preview_text">Featured</span>
            </span>
            <span id="cmr_badge_preview_empty" class="cmr-badge-preview-empty"><?php esc_html_e( 'No badge will be shown.', 'quanto' ); ?></span>
        </div>
    </div>

    <script>
    (function() {
        var presets = {
            featured: { text: 'FEATURED', icon: 'fa-solid fa-bookmark', color: '#6b46c1' },
            new:      { text: 'NEW',      icon: 'fa-solid fa-circle-check', color: '#ea580c' },
            trending: { text: 'TRENDING', icon: 'fa-solid fa-bolt',     color: '#06b6d4' },
            hot:      { text: 'HOT',      icon: 'fa-solid fa-fire',     color: '#ef4444' },
            custom:   { text: 'CUSTOM',   icon: 'fa-solid fa-tag',      color: '#6b46c1' }
        };

        var typeSelect   = document.getElementById('cmr_product_badge_type');
        var textInput    = document.getElementById('cmr_product_badge_text');
        var colorInput   = document.getElementById('cmr_product_badge_color');
        var colorPicker  = document.getElementById('cmr_badge_color_picker');
        var iconInput    = document.getElementById('cmr_product_badge_icon');
        var iconUrlInput = document.getElementById('cmr_product_badge_icon_url');
        var customWrap   = document.getElementById('cmr_badge_custom_fields');

        var pillEl     = document.getElementById('cmr_badge_preview_pill');
        var previewImg = document.getElementById('cmr_badge_preview_img');
        var iconEl     = document.getElementById('cmr_badge_preview_icon');
        var textEl     = document.getElementById('cmr_badge_preview_text');
        var emptyEl    = document.getElementById('cmr_badge_preview_empty');

        var uploadBtn   = document.getElementById('cmr_badge_upload_btn');
        var clearUrlBtn = document.getElementById('cmr_badge_clear_url_btn');

        function updatePreview() {
            var type = typeSelect ? typeSelect.value : 'none';

            if (type === 'none') {
                if (customWrap) customWrap.style.display = 'none';
                if (pillEl) pillEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'inline';
                return;
            }

            if (customWrap) customWrap.style.display = 'block';

            var p = presets[type] || presets.custom;
            var text     = (textInput && textInput.value.trim() !== '') ? textInput.value.trim() : p.text;
            var color    = (colorInput && colorInput.value.trim() !== '') ? colorInput.value.trim() : p.color;
            var icon     = (iconInput && iconInput.value.trim() !== '') ? iconInput.value.trim() : p.icon;
            var iconUrl  = (iconUrlInput && iconUrlInput.value.trim() !== '') ? iconUrlInput.value.trim() : '';

            if (textEl) textEl.textContent = text;

            // Icon URL vs FontAwesome
            if (iconUrl !== '') {
                if (previewImg) {
                    previewImg.src = iconUrl;
                    previewImg.style.display = 'inline-block';
                }
                if (iconEl) iconEl.style.display = 'none';
            } else {
                if (previewImg) previewImg.style.display = 'none';
                if (iconEl) {
                    iconEl.className = icon;
                    iconEl.style.display = 'inline-block';
                }
            }

            // Color
            if (pillEl) {
                pillEl.style.color = color;
                pillEl.style.display = 'inline-flex';
            }
            if (colorPicker && color.match(/^#[0-9a-fA-F]{6}$/)) {
                colorPicker.value = color;
            }

            // Highlight active icon chip
            var currentIcon = (iconInput && iconInput.value.trim() !== '') ? iconInput.value.trim() : icon;
            var chips = document.querySelectorAll('.cmr-badge-icon-chip');
            chips.forEach(function(chip) {
                if (chip.getAttribute('data-icon') === currentIcon && iconUrl === '') {
                    chip.classList.add('active');
                } else {
                    chip.classList.remove('active');
                }
            });

            if (emptyEl) emptyEl.style.display = 'none';
        }

        // Event: Type change
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                var type = this.value;
                if (type !== 'none' && presets[type]) {
                    textInput.value = presets[type].text;
                    colorInput.value = presets[type].color;
                    if (colorPicker) colorPicker.value = presets[type].color;
                    iconInput.value = presets[type].icon;
                    if (iconUrlInput) iconUrlInput.value = '';
                }
                updatePreview();
            });
        }

        // Event: Text input
        if (textInput) textInput.addEventListener('input', updatePreview);

        // Event: Color picker & text input
        if (colorPicker) {
            colorPicker.addEventListener('input', function() {
                if (colorInput) colorInput.value = this.value;
                updatePreview();
            });
        }
        if (colorInput) {
            colorInput.addEventListener('input', function() {
                if (colorPicker && this.value.match(/^#[0-9a-fA-F]{6}$/)) {
                    colorPicker.value = this.value;
                }
                updatePreview();
            });
        }

        // Event: Swatches
        var swatches = document.querySelectorAll('.cmr-badge-swatch');
        swatches.forEach(function(swatch) {
            swatch.addEventListener('click', function() {
                var col = this.getAttribute('data-color');
                if (colorInput) colorInput.value = col;
                if (colorPicker) colorPicker.value = col;
                updatePreview();
            });
        });

        // Event: Icon chips
        var chips = document.querySelectorAll('.cmr-badge-icon-chip');
        chips.forEach(function(chip) {
            chip.addEventListener('click', function() {
                var ic = this.getAttribute('data-icon');
                if (iconInput) iconInput.value = ic;
                if (iconUrlInput) iconUrlInput.value = ''; // Clear URL if choosing standard icon
                updatePreview();
            });
        });

        // Event: Icon class input
        if (iconInput) {
            iconInput.addEventListener('input', function() {
                if (this.value.trim() !== '' && iconUrlInput) {
                    iconUrlInput.value = ''; // Clear URL if typing icon class
                }
                updatePreview();
            });
        }

        // Event: Icon URL input
        if (iconUrlInput) iconUrlInput.addEventListener('input', updatePreview);

        // Event: Upload button (WP Media Frame)
        if (uploadBtn && typeof wp !== 'undefined' && wp.media) {
            uploadBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var frame = wp.media({
                    title: 'Select or Upload Badge Icon (SVG / PNG / JPG)',
                    button: { text: 'Use this Icon' },
                    multiple: false
                });
                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    if (attachment && attachment.url) {
                        if (iconUrlInput) iconUrlInput.value = attachment.url;
                        updatePreview();
                    }
                });
                frame.open();
            });
        }

        // Event: Clear Icon URL button
        if (clearUrlBtn) {
            clearUrlBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (iconUrlInput) iconUrlInput.value = '';
                updatePreview();
            });
        }

        updatePreview();
    })();
    </script>
    <?php
}

/**
 * 4. Save Badge Meta on Product Save
 */
add_action( 'save_post_product', 'cmr_save_product_badge_meta' );
function cmr_save_product_badge_meta( $post_id ) {
    if ( ! isset( $_POST['cmr_product_badge_nonce'] ) || ! wp_verify_nonce( $_POST['cmr_product_badge_nonce'], 'cmr_save_product_badge' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $badge_type = isset( $_POST['_cmr_product_badge_type'] ) ? sanitize_text_field( $_POST['_cmr_product_badge_type'] ) : 'none';
    update_post_meta( $post_id, '_cmr_product_badge_type', $badge_type );

    $badge_text = isset( $_POST['_cmr_product_badge_text'] ) ? sanitize_text_field( $_POST['_cmr_product_badge_text'] ) : '';
    update_post_meta( $post_id, '_cmr_product_badge_text', $badge_text );

    $badge_color = isset( $_POST['_cmr_product_badge_color'] ) ? sanitize_text_field( $_POST['_cmr_product_badge_color'] ) : '';
    update_post_meta( $post_id, '_cmr_product_badge_color', $badge_color );

    $badge_icon = isset( $_POST['_cmr_product_badge_icon'] ) ? sanitize_text_field( $_POST['_cmr_product_badge_icon'] ) : '';
    update_post_meta( $post_id, '_cmr_product_badge_icon', $badge_icon );

    $badge_icon_url = isset( $_POST['_cmr_product_badge_icon_url'] ) ? esc_url_raw( trim( $_POST['_cmr_product_badge_icon_url'] ) ) : '';
    update_post_meta( $post_id, '_cmr_product_badge_icon_url', $badge_icon_url );
}

/**
 * 5. Helper to Get Product Badge Configuration
 *
 * @param WC_Product|int|WP_Post|null $product
 * @return array
 */
function cmr_get_product_badge( $product = null ) {
    if ( ! $product ) {
        global $product;
    }

    $product_id = 0;
    if ( is_a( $product, 'WC_Product' ) ) {
        $product_id = $product->get_id();
    } elseif ( is_numeric( $product ) ) {
        $product_id = absint( $product );
    } elseif ( is_a( $product, 'WP_Post' ) ) {
        $product_id = $product->ID;
    } else {
        $product_id = get_the_ID();
    }

    if ( ! $product_id ) {
        return array( 'show' => false );
    }

    $badge_type  = get_post_meta( $product_id, '_cmr_product_badge_type', true );
    $custom_text = get_post_meta( $product_id, '_cmr_product_badge_text', true );
    $custom_col  = get_post_meta( $product_id, '_cmr_product_badge_color', true );
    $custom_icon = get_post_meta( $product_id, '_cmr_product_badge_icon', true );
    $custom_url  = get_post_meta( $product_id, '_cmr_product_badge_icon_url', true );

    // If not set, or explicitly 'none', do not show
    if ( empty( $badge_type ) || 'none' === $badge_type ) {
        return array( 'show' => false );
    }

    $badge = array(
        'show'     => true,
        'type'     => $badge_type,
        'text'     => '',
        'icon'     => '',
        'icon_url' => ! empty( $custom_url ) ? $custom_url : '',
        'color'    => '',
    );

    switch ( $badge_type ) {
        case 'featured':
            $badge['text']  = ! empty( $custom_text ) ? $custom_text : 'Featured';
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-bookmark';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#6b46c1';
            break;

        case 'new':
            $badge['text']  = ! empty( $custom_text ) ? $custom_text : 'New';
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-circle-check';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#ea580c';
            break;

        case 'trending':
            $badge['text']  = ! empty( $custom_text ) ? $custom_text : 'Trending';
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-bolt';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#06b6d4';
            break;

        case 'hot':
            $badge['text']  = ! empty( $custom_text ) ? $custom_text : 'Hot';
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-fire';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#ef4444';
            break;

        case 'custom':
            $badge['text']  = ! empty( $custom_text ) ? $custom_text : 'Custom';
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-tag';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#6b46c1';
            break;

        default:
            return array( 'show' => false );
    }

    if ( ! empty( $custom_url ) ) {
        $badge['icon_url'] = $custom_url;
    }

    return $badge;
}

/**
 * 6. Helper to Render Product Badge HTML
 *
 * @param WC_Product|int|WP_Post|null $product
 * @param string $class CSS class name (e.g. cmr-fr-badge, cmr-tn-badge, cmr-lr-badge)
 */
function cmr_render_product_badge( $product = null, $class = 'cmr-lr-badge' ) {
    $badge = cmr_get_product_badge( $product );
    if ( empty( $badge['show'] ) || empty( $badge['text'] ) ) {
        return;
    }

    $color_style = ! empty( $badge['color'] ) ? ' style="color: ' . esc_attr( $badge['color'] ) . ';"' : '';
    echo '<div class="' . esc_attr( $class ) . '"' . $color_style . '>';
    if ( ! empty( $badge['icon_url'] ) ) {
        echo '<img class="cmr-badge-custom-icon" src="' . esc_url( $badge['icon_url'] ) . '" alt="" style="width: 14px; height: 14px; max-width: 14px; max-height: 14px; object-fit: contain; vertical-align: middle; display: inline-block; flex-shrink: 0; margin-right: 4px;" /> ';
    } elseif ( ! empty( $badge['icon'] ) ) {
        echo '<i class="' . esc_attr( $badge['icon'] ) . '"></i> ';
    }
    echo esc_html( $badge['text'] );
    echo '</div>';
}
