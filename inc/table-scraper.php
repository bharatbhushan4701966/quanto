<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
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
    
    // Add margin-top 100px, a responsive scrolling wrapper, and hide any blog sidebar so the table gets full container width!
    $extracted_content = '<style>
    .blog-detail-page .widget-area, .blog-detail-page aside, .blog-detail-page .col-lg-4 { display: none !important; } 
    .blog-detail-page .col-lg-8, .blog-detail-page .col-md-8 { flex: 0 0 100% !important; max-width: 100% !important; }
    .cmr-table-container { margin-top: 100px; width: 100%; overflow-x: auto; display: block; }
    </style>';
    $extracted_content .= '<div class="cmr-table-container">';
    $extracted_content .= '[table id=' . $tid . ' responsive="scroll" /]';
    $extracted_content .= '</div>';

    $post_data = array(
        'post_title'    => wp_strip_all_tags( $title ),
        'post_content'  => $extracted_content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'meta_input'    => array(
            '_scraped_from_table' => $tid,
        )
    );

    $post_id = wp_insert_post( $post_data );
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error("Failed to create post: " . $post_id->get_error_message());
    }

    wp_send_json_success( ['post_id' => $post_id] );
}
