<?php
// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Add menu page
add_action( 'admin_menu', 'cmr_table_scraper_menu' );
function cmr_table_scraper_menu() {
    add_management_page(
        'Table Scraper',
        'Table Scraper',
        'manage_options',
        'cmr-table-scraper',
        'cmr_table_scraper_page'
    );
}

// Admin Page Content
function cmr_table_scraper_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $message = '';

    if ( isset( $_POST['run_scraper'] ) && check_admin_referer( 'run_scraper_action', 'run_scraper_nonce' ) ) {
        $table_id = intval( $_POST['table_id'] );
        $post_type = sanitize_text_field( $_POST['post_type'] );
        $css_selector = sanitize_text_field( $_POST['css_selector'] );
        
        $message = cmr_run_table_scraper( $table_id, $post_type, $css_selector );
    }

    ?>
    <div class="wrap">
        <h1>Table Scraper Automation</h1>
        <p>This tool reads a TablePress table, finds URLs in the rows, fetches the live URL content, and creates pages automatically.</p>
        
        <?php if ( $message ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php echo wp_kses_post( $message ); ?></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field( 'run_scraper_action', 'run_scraper_nonce' ); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="table_id">TablePress Table ID</label></th>
                    <td>
                        <input name="table_id" type="number" id="table_id" value="1" class="regular-text" required>
                        <p class="description">Enter the ID of the TablePress table (e.g. 12).</p>
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
                <input type="submit" name="run_scraper" id="submit" class="button button-primary" value="Run Scraper">
            </p>
        </form>
    </div>
    <?php
}

// Scraper Logic
function cmr_run_table_scraper( $table_id, $post_type, $css_selector ) {
    if ( ! class_exists( 'TablePress' ) ) {
        return 'Error: TablePress is not active.';
    }

    try {
        $table = TablePress::$model_table->load( $table_id );
    } catch ( Exception $e ) {
        return 'Error loading table: ' . $e->getMessage();
    }

    if ( ! $table || empty( $table['data'] ) ) {
        return 'Table is empty or not found.';
    }

    $created_count = 0;
    $errors = [];

    // Loop through table data (skipping header row)
    foreach ( $table['data'] as $index => $row ) {
        if ( $index === 0 ) continue; // Skip header

        // Find URL in row
        $url = '';
        $title = 'Scraped Page ' . time() . rand(10,99);
        
        foreach ( $row as $cell ) {
            // Very basic URL detection
            if ( filter_var( $cell, FILTER_VALIDATE_URL ) ) {
                $url = $cell;
            } elseif ( !empty($cell) && empty($title) ) {
                $title = sanitize_text_field( $cell );
            }
        }

        if ( empty( $url ) ) continue;

        // Fetch URL content
        $response = wp_remote_get( $url );
        if ( is_wp_error( $response ) ) {
            $errors[] = "Failed to fetch $url: " . $response->get_error_message();
            continue;
        }

        $html = wp_remote_retrieve_body( $response );
        if ( empty( $html ) ) {
            $errors[] = "Empty response from $url";
            continue;
        }

        // Basic DOM parsing to extract content
        $extracted_content = $html; // Fallback to raw HTML
        
        if ( class_exists('DOMDocument') ) {
            $dom = new DOMDocument();
            @$dom->loadHTML( mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8') );
            
            // Try to extract title
            $title_nodes = $dom->getElementsByTagName('title');
            if ( $title_nodes->length > 0 ) {
                $title = $title_nodes->item(0)->nodeValue;
            }

            // If selector is body, we just use body
            if ( $css_selector === 'body' ) {
                $body_nodes = $dom->getElementsByTagName('body');
                if ( $body_nodes->length > 0 ) {
                    $extracted_content = $dom->saveHTML( $body_nodes->item(0) );
                }
            } else {
                // This is a naive ID/Class finder. Full CSS selector parsing requires external libs.
                // We'll just do basic ID and Class extraction.
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
                    $errors[] = "Could not find selector '$css_selector' on $url";
                }
            }
        }

        // Create the post
        $post_data = array(
            'post_title'    => wp_strip_all_tags( $title ),
            'post_content'  => $extracted_content,
            'post_status'   => 'publish',
            'post_type'     => $post_type,
            'meta_input'    => array(
                '_scraped_source_url' => $url,
                '_scraped_from_table' => $table_id,
            )
        );

        $post_id = wp_insert_post( $post_data );
        if ( ! is_wp_error( $post_id ) ) {
            $created_count++;
        } else {
            $errors[] = "Failed to create post for $url: " . $post_id->get_error_message();
        }
    }

    $result_msg = "<strong>Successfully created $created_count pages!</strong>";
    if ( ! empty( $errors ) ) {
        $result_msg .= "<br>Errors encountered:<br>" . implode( "<br>", $errors );
    }

    return $result_msg;
}
