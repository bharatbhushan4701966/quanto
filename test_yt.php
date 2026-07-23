<?php
$url = 'https://www.youtube.com/watch?v=QFLsJwN6y2k';
$html = file_get_contents($url);
if (preg_match('/"macroMarkers":\{"runs":(\[.*?\])\}/s', $html, $matches)) {
    print_r(json_decode($matches[1], true));
} else {
    echo 'No macroMarkers found.';
}
?>
