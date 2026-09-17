<?php
/**
 * CMR WooCommerce Product Badge Module
 *
 * Allows setting custom badges (Featured, New, Trending, Hot, Custom, None)
 * on WooCommerce products during add/edit and rendering them across report sections.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * 1. Add Meta Box to WooCommerce Product Edit Screen
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
 * 2. Render Product Badge Meta Box HTML & Live Preview
 */
function cmr_render_product_badge_meta_box( $post ) {
    wp_nonce_field( 'cmr_save_product_badge', 'cmr_product_badge_nonce' );

    $badge_type  = get_post_meta( $post->ID, '_cmr_product_badge_type', true );
    $badge_text  = get_post_meta( $post->ID, '_cmr_product_badge_text', true );
    $badge_color = get_post_meta( $post->ID, '_cmr_product_badge_color', true );
    $badge_icon  = get_post_meta( $post->ID, '_cmr_product_badge_icon', true );

    if ( empty( $badge_type ) ) {
        $badge_type = 'none';
    }
    ?>
    <style>
        .cmr-badge-meta-wrap label {
            display: block;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 4px;
            color: #374151;
        }
        .cmr-badge-meta-wrap select,
        .cmr-badge-meta-wrap input[type="text"] {
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            padding: 6px 8px;
            font-size: 13px;
            box-sizing: border-box;
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
        .cmr-badge-preview-empty {
            color: #94a3b8;
            font-size: 12px;
            font-style: italic;
        }
    </style>

    <div class="cmr-badge-meta-wrap">
        <label for="cmr_product_badge_type"><?php esc_html_e( 'Select Badge Type:', 'quanto' ); ?></label>
        <select id="cmr_product_badge_type" name="_cmr_product_badge_type">
            <option value="none" <?php selected( $badge_type, 'none' ); ?>><?php esc_html_e( 'None (No Badge)', 'quanto' ); ?></option>
            <option value="featured" <?php selected( $badge_type, 'featured' ); ?>><?php esc_html_e( 'Featured (🔖 Purple)', 'quanto' ); ?></option>
            <option value="new" <?php selected( $badge_type, 'new' ); ?>><?php esc_html_e( 'New (✓ Orange)', 'quanto' ); ?></option>
            <option value="trending" <?php selected( $badge_type, 'trending' ); ?>><?php esc_html_e( 'Trending (⚡ Cyan)', 'quanto' ); ?></option>
            <option value="hot" <?php selected( $badge_type, 'hot' ); ?>><?php esc_html_e( 'Hot (🔥 Red)', 'quanto' ); ?></option>
            <option value="custom" <?php selected( $badge_type, 'custom' ); ?>><?php esc_html_e( 'Custom Badge...', 'quanto' ); ?></option>
        </select>

        <div id="cmr_badge_custom_fields" style="<?php echo ( 'custom' === $badge_type || ! empty( $badge_text ) || ! empty( $badge_color ) || ! empty( $badge_icon ) ) ? '' : 'display:none;'; ?>">
            <label for="cmr_product_badge_text"><?php esc_html_e( 'Custom Text (optional):', 'quanto' ); ?></label>
            <input type="text" id="cmr_product_badge_text" name="_cmr_product_badge_text" value="<?php echo esc_attr( $badge_text ); ?>" placeholder="<?php esc_attr_e( 'e.g. EXCLUSIVE, BESTSELLER', 'quanto' ); ?>" />

            <label for="cmr_product_badge_color"><?php esc_html_e( 'Custom Color (optional hex):', 'quanto' ); ?></label>
            <input type="text" id="cmr_product_badge_color" name="_cmr_product_badge_color" value="<?php echo esc_attr( $badge_color ); ?>" placeholder="<?php esc_attr_e( 'e.g. #6b46c1', 'quanto' ); ?>" />

            <label for="cmr_product_badge_icon"><?php esc_html_e( 'Custom FontAwesome Icon (optional):', 'quanto' ); ?></label>
            <input type="text" id="cmr_product_badge_icon" name="_cmr_product_badge_icon" value="<?php echo esc_attr( $badge_icon ); ?>" placeholder="<?php esc_attr_e( 'e.g. fa-solid fa-star', 'quanto' ); ?>" />
        </div>

        <label style="margin-top: 4px;"><?php esc_html_e( 'Live Preview:', 'quanto' ); ?></label>
        <div class="cmr-badge-preview-box">
            <span id="cmr_badge_preview_pill" class="cmr-badge-preview-pill" style="display:none;">
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
            new:      { text: 'NEW', icon: 'fa-solid fa-circle-check', color: '#ea580c' },
            trending: { text: 'TRENDING', icon: 'fa-solid fa-bolt', color: '#06b6d4' },
            hot:      { text: 'HOT', icon: 'fa-solid fa-fire', color: '#ef4444' }
        };

        var typeSelect = document.getElementById('cmr_product_badge_type');
        var textInput  = document.getElementById('cmr_product_badge_text');
        var colorInput = document.getElementById('cmr_product_badge_color');
        var iconInput  = document.getElementById('cmr_product_badge_icon');
        var customWrap = document.getElementById('cmr_badge_custom_fields');

        var pillEl  = document.getElementById('cmr_badge_preview_pill');
        var iconEl  = document.getElementById('cmr_badge_preview_icon');
        var textEl  = document.getElementById('cmr_badge_preview_text');
        var emptyEl = document.getElementById('cmr_badge_preview_empty');

        function updatePreview() {
            var type = typeSelect ? typeSelect.value : 'none';
            if (type === 'custom') {
                if (customWrap) customWrap.style.display = 'block';
            }

            if (type === 'none') {
                if (pillEl) pillEl.style.display = 'none';
                if (emptyEl) emptyEl.style.display = 'inline';
                return;
            }

            var p = presets[type] || { text: 'CUSTOM', icon: 'fa-solid fa-tag', color: '#6b46c1' };
            var text = (textInput && textInput.value.trim() !== '') ? textInput.value.trim() : p.text;
            var color = (colorInput && colorInput.value.trim() !== '') ? colorInput.value.trim() : p.color;
            var icon = (iconInput && iconInput.value.trim() !== '') ? iconInput.value.trim() : p.icon;

            if (textEl) textEl.textContent = text;
            if (iconEl) iconEl.className = icon;
            if (pillEl) {
                pillEl.style.color = color;
                pillEl.style.display = 'inline-flex';
            }
            if (emptyEl) emptyEl.style.display = 'none';
        }

        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    if (customWrap) customWrap.style.display = 'block';
                } else if (!textInput.value && !colorInput.value && !iconInput.value) {
                    if (customWrap) customWrap.style.display = 'none';
                }
                updatePreview();
            });
        }
        if (textInput) textInput.addEventListener('input', updatePreview);
        if (colorInput) colorInput.addEventListener('input', updatePreview);
        if (iconInput) iconInput.addEventListener('input', updatePreview);

        updatePreview();
    })();
    </script>
    <?php
}

/**
 * 3. Save Badge Meta on Product Save
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

    $badge_color = isset( $_POST['_cmr_product_badge_color'] ) ? sanitize_hex_color( $_POST['_cmr_product_badge_color'] ) : '';
    if ( ! $badge_color && isset( $_POST['_cmr_product_badge_color'] ) ) {
        $badge_color = sanitize_text_field( $_POST['_cmr_product_badge_color'] );
    }
    update_post_meta( $post_id, '_cmr_product_badge_color', $badge_color );

    $badge_icon = isset( $_POST['_cmr_product_badge_icon'] ) ? sanitize_text_field( $_POST['_cmr_product_badge_icon'] ) : '';
    update_post_meta( $post_id, '_cmr_product_badge_icon', $badge_icon );
}

/**
 * 4. Helper to Get Product Badge Configuration
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

    // If not set, or explicitly 'none', do not show
    if ( empty( $badge_type ) || 'none' === $badge_type ) {
        return array( 'show' => false );
    }

    $badge = array(
        'show'  => true,
        'type'  => $badge_type,
        'text'  => '',
        'icon'  => '',
        'color' => '',
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
            if ( empty( $custom_text ) ) {
                return array( 'show' => false );
            }
            $badge['text']  = $custom_text;
            $badge['icon']  = ! empty( $custom_icon ) ? $custom_icon : 'fa-solid fa-tag';
            $badge['color'] = ! empty( $custom_col )  ? $custom_col  : '#6b46c1';
            break;

        default:
            return array( 'show' => false );
    }

    return $badge;
}

/**
 * 5. Helper to Render Product Badge HTML
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
    if ( ! empty( $badge['icon'] ) ) {
        echo '<i class="' . esc_attr( $badge['icon'] ) . '"></i> ';
    }
    echo esc_html( $badge['text'] );
    echo '</div>';
}
