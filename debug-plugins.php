<?php
require_once( __DIR__ . '/../../../wp-load.php' );
echo "ACTIVE PLUGINS:\n";
print_r(get_option('active_plugins'));
echo "\nFOOTER OPTION:\n";
print_r(get_option('quanto_footer_choice'));
echo "\nGLOBAL FOOTER STYLE:\n";
print_r(get_option('quanto_footer_style'));
