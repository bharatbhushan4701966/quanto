<?php
require_once( __DIR__ . '/../../../wp-load.php' );
header('Content-Type: text/plain');

$theme_widget = __DIR__ . '/inc/widgets/process.php';
$plugin_widget = WP_PLUGIN_DIR . '/quanto-core/addons/widgets/process.php';

if (file_exists($theme_widget) && file_exists($plugin_widget)) {
    $theme_content = file_get_contents($theme_widget);
    $res = file_put_contents($plugin_widget, $theme_content);
    echo "Wrote $res bytes to $plugin_widget\n";
}

$content = file_get_contents($plugin_widget);
if (strpos($content, 'card_bg_color') !== false) {
    echo "SUCCESS: quanto-core process.php has been updated with card_bg_color!\n";
} else {
    echo "ERROR: card_bg_color not found in process.php!\n";
}

