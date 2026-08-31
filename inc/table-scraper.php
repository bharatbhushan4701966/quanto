<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// =====================================================================
// Dynamically inject CSS for scraper-created table pages (via wp_head)
// This means CSS changes take effect immediately without recreating pages
// =====================================================================
add_action( 'wp_head', 'cmr_table_scraper_styles' );
function cmr_table_scraper_styles() {
    if ( ! is_singular() ) return;
    $post_id = get_the_ID();
    if ( ! get_post_meta( $post_id, '_scraped_from_table', true ) ) return;
    ?>
    <style>
    .cmr-table-container { width: 100%; display: block; margin-top: 140px !important; padding: 0 20px 40px 20px; box-sizing: border-box; }
    .cmr-table-container table { font-size: 11px !important; width: 100% !important; table-layout: fixed !important; border-right: 1px solid #333 !important; }
    .cmr-table-container table th, .cmr-table-container table td { padding: 6px 5px !important; line-height: 1.3 !important; word-wrap: break-word !important; overflow-wrap: break-word !important; white-space: normal !important; }
    </style>
    <?php
}

// Add menu page
add_action( 'admin_menu', 'cmr_table_scraper_menu' );
function cmr_table_scraper_menu() {
    add_menu_page(
        'Table Scraper',
        'Table Scraper',
        'manage_options',
        'cmr-table-scraper',
        'cmr_table_scraper_page',
        'dashicons-download',
        20
    );
}

// Enqueue admin scripts for AJAX
add_action('admin_enqueue_scripts', function($hook) {
    if ( $hook === 'toplevel_page_cmr-table-scraper' ) {
        // Enqueue nothing specific, just inline script later
    }
});

// Admin Page Content
function cmr_table_scraper_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $urls_to_scrape = [];
    $message = '';
    $post_type = 'page';
    $css_selector = 'body';
    $table_id = '';

    if ( isset( $_POST['prepare_scraper'] ) && check_admin_referer( 'run_scraper_action', 'run_scraper_nonce' ) ) {
        $table_id = sanitize_text_field( $_POST['table_id'] );
        $post_type = sanitize_text_field( $_POST['post_type'] );
        
        if ( class_exists( 'TablePress' ) ) {
            $tables_to_process = [];
            if ( $table_id === 'all' ) {
                $tables_to_process = TablePress::$model_table->load_all();
            } else {
                $tables_to_process = [ intval( $table_id ) ];
            }

            foreach ( $tables_to_process as $tid ) {
                try {
                    $table = TablePress::$model_table->load( $tid );
                    if ( $table ) {
                        $urls_to_scrape[] = [
                            'tid' => $tid,
                            'name' => $table['name']
                        ];
                    }
                } catch (Exception $e) {}
            }
        }
    }

    ?>
    <div class="wrap">
        <h1>Batch Create Table Pages</h1>
        <p>This tool reads your TablePress tables and automatically creates 1 WordPress page for each table, with the shortcode embedded inside.</p>
        
        <?php if ( empty($urls_to_scrape) ) : ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'run_scraper_action', 'run_scraper_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="table_id">TablePress Table ID</label></th>
                    <td>
                        <input name="table_id" type="text" id="table_id" value="all" class="regular-text" required>
                        <p class="description">Enter the ID of the TablePress table (e.g. 12), or type <code>all</code> to create pages for all tables.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="post_type">Create As (Post Type)</label></th>
                    <td>
                        <input name="post_type" type="text" id="post_type" value="post" class="regular-text" required>
                        <p class="description">Examples: post, page, record, etc. (Using 'post' gives it the blog header style!)</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="prepare_scraper" id="submit" class="button button-primary" value="Create Pages">
            </p>
        </form>
        <?php else: ?>
            
            <div id="scraper-progress-container" style="max-width: 600px; margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3>Page Creation Progress</h3>
                <p>Found <strong><?php echo count($urls_to_scrape); ?></strong> tables. Do not close this page.</p>
                <div style="width: 100%; background: #e2e4e7; border-radius: 3px; height: 20px; margin-bottom: 10px;">
                    <div id="scraper-progress-bar" style="width: 0%; background: #2271b1; height: 20px; border-radius: 3px; transition: width 0.3s;"></div>
                </div>
                <p id="scraper-status">Starting...</p>
                
                <div id="scraper-log" style="margin-top: 15px; max-height: 200px; overflow-y: auto; background: #f0f0f1; padding: 10px; font-family: monospace; font-size: 12px; border: 1px solid #ccc;">
                </div>
            </div>

            <script>
            jQuery(document).ready(function($) {
                var urls = <?php echo json_encode($urls_to_scrape); ?>;
                var total = urls.length;
                var current = 0;
                var postType = "<?php echo esc_js($post_type); ?>";

                function logMessage(msg, isError = false) {
                    var color = isError ? 'red' : 'green';
                    $('#scraper-log').prepend('<div style="color:' + color + ';">' + msg + '</div>');
                }

                function processNext() {
                    if (current >= total) {
                        $('#scraper-status').html('<strong>Finished! All pages created.</strong>');
                        return;
                    }

                    var item = urls[current];
                    $('#scraper-status').text('Processing (' + (current+1) + '/' + total + '): Table ' + item.tid + ' - ' + item.name);

                    $.post(ajaxurl, {
                        action: 'cmr_scrape_single',
                        tid: item.tid,
                        name: item.name,
                        post_type: postType,
                        _ajax_nonce: '<?php echo wp_create_nonce("cmr_scrape_single_nonce"); ?>'
                    }, function(response) {
                        if (response.success) {
                            logMessage('✅ Created page for Table ' + item.tid + ' ("' + item.name + '")');
                        } else {
                            logMessage('❌ Failed for Table ' + item.tid + ': ' + (response.data || 'Unknown error'), true);
                        }
                    }).fail(function(xhr) {
                        logMessage('❌ Server error for Table ' + item.tid + ': ' + xhr.statusText, true);
                    }).always(function() {
                        current++;
                        var percent = Math.round((current / total) * 100);
                        $('#scraper-progress-bar').css('width', percent + '%');
                        processNext();
                    });
                }

                // Start process
                processNext();
            });
            </script>

        <?php endif; ?>
    </div>
    <?php
}

// AJAX Handler
add_action('wp_ajax_cmr_scrape_single', 'cmr_scrape_single_handler');
function cmr_scrape_single_handler() {
    check_ajax_referer('cmr_scrape_single_nonce');

    if ( ! current_user_can('manage_options') ) {
        wp_send_json_error('Permission denied');
    }

    $tid = intval($_POST['tid']);
    $name = sanitize_text_field($_POST['name']);
    $post_type = sanitize_text_field($_POST['post_type']);

    if (empty($tid)) {
        wp_send_json_error('Empty Table ID');
    }

    $title = $name ? $name : 'Table ' . $tid;

    // No inline CSS needed — styles are injected via wp_head hook above
    $extracted_content = '<div class="cmr-table-container">';
    $extracted_content .= '[table id=' . $tid . ' responsive="scroll" /]';
    $extracted_content .= '</div>';

    $post_data = array(
        'post_title'    => wp_strip_all_tags( $title ),
        'post_content'  => $extracted_content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'meta_input'    => array(
            '_scraped_from_table'  => $tid,
            '_wp_page_template'    => 'elementor_header_footer', // Elementor Full Width = no container
            '_cmr_use_blog_header' => '1',                       // Forces blog-header via header.php
            '_cmr_is_gated'        => '1',                       // Gated content flag
        )
    );

    $post_id = wp_insert_post( $post_data );
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error("Failed to create post: " . $post_id->get_error_message());
    }

    wp_send_json_success( ['post_id' => $post_id] );
}

/**
 * Gate content for all pages created by Table Scraper / TablePress
 */
add_filter( 'the_content', 'cmr_gate_scraped_table_content', 999 );
function cmr_gate_scraped_table_content( $content ) {
    if ( ! is_singular() ) {
        return $content;
    }

    $post_id = get_the_ID();

    // Check if post is marked as scraped/table page or contains TablePress table shortcode
    $is_table_page = get_post_meta( $post_id, '_scraped_from_table', true ) 
                  || get_post_meta( $post_id, '_cmr_is_gated', true )
                  || ( is_string($content) && ( strpos($content, '[table id=') !== false || strpos($content, 'cmr-table-container') !== false ) );

    if ( ! $is_table_page ) {
        return $content;
    }

    // If user is logged in, show full content
    if ( is_user_logged_in() ) {
        return $content;
    }

    // Gate content for non-logged in users
    $login_url = home_url( '/my-account/' );

    $gated_html = '
    <div class="cmr-gated-wrapper" style="position: relative; width: 100%; overflow: hidden; margin-top: 130px; margin-bottom: 50px;">
        <!-- Blurred Preview -->
        <div class="cmr-gated-preview" style="filter: blur(8px); opacity: 0.25; user-select: none; pointer-events: none; max-height: 420px; overflow: hidden;">
            ' . $content . '
        </div>

        <!-- Lock Overlay Card -->
        <div class="cmr-gated-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; min-height: 380px; display: flex; align-items: center; justify-content: center; background: rgba(255, 255, 255, 0.88); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 10; padding: 20px; box-sizing: border-box;">
            <div class="cmr-gated-card" style="max-width: 500px; width: 100%; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.03); padding: 40px 32px; text-align: center; font-family: \'Instrument Sans\', sans-serif;">
                <div class="cmr-gated-icon-box" style="width: 60px; height: 60px; margin: 0 auto 20px; background: rgba(72, 32, 176, 0.08); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #4820B0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                
                <h3 style="font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 12px; line-height: 1.3;">
                    Exclusive Industry Intelligence
                </h3>
                
                <p style="font-size: 14px; color: #64748b; margin: 0 0 28px; line-height: 1.6;">
                    This data page is restricted to registered members. Please sign in or register to unlock full access to this intelligence table.
                </p>

                <a href="' . esc_url( $login_url ) . '" class="cmr-gated-btn" style="display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 15px 28px; background: #4820B0; color: #ffffff; font-size: 15px; font-weight: 600; text-decoration: none; border-radius: 30px; transition: background 0.2s ease, transform 0.2s ease; box-shadow: 0 4px 14px rgba(72, 32, 176, 0.35); box-sizing: border-box;">
                    Sign In to Unlock Access
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-left: 8px;">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </a>
            </div>
        </div>
    </div>';

    return $gated_html;
}
