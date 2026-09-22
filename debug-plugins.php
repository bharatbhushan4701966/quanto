<?php
require_once( __DIR__ . '/../../../wp-load.php' );
header('Content-Type: text/plain');

$file = WP_PLUGIN_DIR . '/quanto-core/addons/widgets/process.php';
if (file_exists($file)) {
    echo file_get_contents($file);
} else {
    echo "File not found!";
}

