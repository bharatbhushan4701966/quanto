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
        $css_selector = sanitize_text_field( $_POST['css_selector'] );
        
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
                    if ( $table && ! empty( $table['data'] ) ) {
                        foreach ( $table['data'] as $index => $row ) {
                            if ( $index === 0 ) continue; // Skip header
                            foreach ( $row as $cell ) {
                                if ( preg_match( '/https?:\/\/[^\s"\'<>]+/', $cell, $matches ) ) {
                                    $urls_to_scrape[] = [
                                        'url' => $matches[0],
                                        'tid' => $tid
                                    ];
                                }
                            }
                        }
                    }
                } catch (Exception $e) {}
            }
        }
    }

    ?>
    <div class="wrap">
        <h1>AJAX Table Scraper Automation</h1>
        <p>This tool reads a TablePress table, finds URLs in the rows, fetches the live URL content, and creates pages automatically. It uses AJAX to prevent Cloudflare timeouts.</p>
        
        <?php if ( empty($urls_to_scrape) ) : ?>
        <form method="post" action="">
            <?php wp_nonce_field( 'run_scraper_action', 'run_scraper_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="table_id">TablePress Table ID</label></th>
                    <td>
                        <input name="table_id" type="text" id="table_id" value="all" class="regular-text" required>
                        <p class="description">Enter the ID of the TablePress table (e.g. 12), or type <code>all</code> to scrape all tables.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="post_type">Create As (Post Type)</label></th>
                    <td>
                        <input name="post_type" type="text" id="post_type" value="page" class="regular-text" required>
                        <p class="description">Examples: page, post, record, etc.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="css_selector">CSS Selector to Extract</label></th>
                    <td>
                        <input name="css_selector" type="text" id="css_selector" value="body" class="regular-text">
                        <p class="description">Enter a CSS class or ID (e.g., <code>.entry-content</code> or <code>#main</code>) to extract specific content. Leave as <code>body</code> to get everything.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="prepare_scraper" id="submit" class="button button-primary" value="Prepare Scraper">
            </p>
        </form>
        <?php else: ?>
            
            <div id="scraper-progress-container" style="max-width: 600px; margin-top: 20px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px;">
                <h3>Scraping Progress</h3>
                <p>Found <strong><?php echo count($urls_to_scrape); ?></strong> URLs. Do not close this page.</p>
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
                var cssSelector = "<?php echo esc_js($css_selector); ?>";

                function logMessage(msg, isError = false) {
                    var color = isError ? 'red' : 'green';
                    $('#scraper-log').prepend('<div style="color:' + color + ';">' + msg + '</div>');
                }

                function processNext() {
                    if (current >= total) {
                        $('#scraper-status').html('<strong>Finished! All pages processed.</strong>');
                        return;
                    }

                    var item = urls[current];
                    $('#scraper-status').text('Processing (' + (current+1) + '/' + total + '): ' + item.url);

                    $.post(ajaxurl, {
                        action: 'cmr_scrape_single',
                        url: item.url,
                        tid: item.tid,
                        post_type: postType,
                        css_selector: cssSelector,
                        _ajax_nonce: '<?php echo wp_create_nonce("cmr_scrape_single_nonce"); ?>'
                    }, function(response) {
                        if (response.success) {
                            logMessage('✅ Created page for ' + item.url);
                        } else {
                            logMessage('❌ Failed for ' + item.url + ': ' + (response.data || 'Unknown error'), true);
                        }
                    }).fail(function(xhr) {
                        logMessage('❌ Server error for ' + item.url + ': ' + xhr.statusText, true);
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

    $url = esc_url_raw($_POST['url']);
    $tid = intval($_POST['tid']);
    $post_type = sanitize_text_field($_POST['post_type']);
    $css_selector = sanitize_text_field($_POST['css_selector']);

    if (empty($url)) {
        wp_send_json_error('Empty URL');
    }

    $response = wp_remote_get( $url, ['timeout' => 30] );
    if ( is_wp_error( $response ) ) {
        wp_send_json_error("Failed to fetch: " . $response->get_error_message());
    }

    $html = wp_remote_retrieve_body( $response );
    if ( empty( $html ) ) {
        wp_send_json_error("Empty response from URL");
    }

    $title = 'Scraped Page ' . time() . rand(10,99);
    $extracted_content = $html; // Fallback

    if ( class_exists('DOMDocument') ) {
        $dom = new DOMDocument();
        @$dom->loadHTML( mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') );
        
        $title_nodes = $dom->getElementsByTagName('title');
        if ( $title_nodes->length > 0 ) {
            $title = $title_nodes->item(0)->nodeValue;
        }

        if ( $css_selector === 'body' ) {
            $body_nodes = $dom->getElementsByTagName('body');
            if ( $body_nodes->length > 0 ) {
                $extracted_content = $dom->saveHTML( $body_nodes->item(0) );
            }
        } else {
            $xpath = new DOMXPath($dom);
            $query = '';
            if ( strpos($css_selector, '#') === 0 ) {
                $id = substr($css_selector, 1);
                $query = "//*[@id='$id']";
            } elseif ( strpos($css_selector, '.') === 0 ) {
                $class = substr($css_selector, 1);
                $query = "//*[contains(concat(' ', normalize-space(@class), ' '), ' $class ')]";
            } else {
                $query = "//" . $css_selector; // Just tag name
            }

            $elements = $xpath->query($query);
            if ( $elements && $elements->length > 0 ) {
                $extracted_content = $dom->saveHTML( $elements->item(0) );
            } else {
                wp_send_json_error("Could not find selector '$css_selector'");
            }
        }
    }

    $post_data = array(
        'post_title'    => wp_strip_all_tags( $title ),
        'post_content'  => $extracted_content,
        'post_status'   => 'publish',
        'post_type'     => $post_type,
        'meta_input'    => array(
            '_scraped_source_url' => $url,
            '_scraped_from_table' => $tid,
        )
    );

    $post_id = wp_insert_post( $post_data );
    if ( is_wp_error( $post_id ) ) {
        wp_send_json_error("Failed to create post: " . $post_id->get_error_message());
    }

    wp_send_json_success( ['post_id' => $post_id] );
}
