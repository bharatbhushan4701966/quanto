<?php
require_once( __DIR__ . '/../../../wp-load.php' );
header('Content-Type: text/plain');

$plugin_dir = WP_PLUGIN_DIR . '/quanto-core';
echo "Plugin dir: " . $plugin_dir . "\n";

if (is_dir($plugin_dir)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($plugin_dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            if (stripos($content, 'quanto_process') !== false || stripos($content, 'process-box') !== false) {
                echo "Found matching file: " . $file->getPathname() . "\n";
            }
        }
    }
}

