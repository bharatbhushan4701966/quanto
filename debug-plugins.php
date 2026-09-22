<?php
require_once( __DIR__ . '/../../../wp-load.php' );
header('Content-Type: text/plain');

$file = WP_PLUGIN_DIR . '/quanto-core/addons/widgets/process.php';
echo "File: " . $file . "\n";
echo "Exists: " . (file_exists($file) ? 'YES' : 'NO') . "\n";
echo "Writable: " . (is_writable($file) ? 'YES' : 'NO') . "\n";
echo "Dir writable: " . (is_writable(dirname($file)) ? 'YES' : 'NO') . "\n";

