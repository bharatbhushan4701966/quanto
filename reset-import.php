<?php
$state_file = __DIR__ . '/import-state.json';
$log_file = __DIR__ . '/import-log.txt';
if (file_exists($state_file)) unlink($state_file);
if (file_exists($log_file)) unlink($log_file);
echo "Reset successful.";
?>
