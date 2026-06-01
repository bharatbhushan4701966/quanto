<?php
require_once('wp-load.php');
$ids = [10, 14, 6436, 7758, 7637, 7832, 7849, 9397, 8920];
foreach($ids as $id) {
    echo "ID " . $id . " (" . get_post_type($id) . "): " . get_the_title($id) . "\n";
}
