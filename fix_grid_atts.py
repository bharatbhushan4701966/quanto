import os
import re

files = [
    {
        'path': 'inc/cmr-channel-connect-grid.php',
        'func': 'cmr_channel_connect_grid_shortcode',
        'tag': 'cmr_channel_connect_grid'
    },
    {
        'path': 'inc/cmr-smb-connect-grid.php',
        'func': 'cmr_smb_connect_grid_shortcode',
        'tag': 'cmr_smb_connect_grid'
    },
    {
        'path': 'inc/cmr-media-releases-grid.php',
        'func': 'cmr_media_releases_grid_shortcode',
        'tag': 'cmr_media_releases_grid'
    }
]

for f in files:
    with open(f['path'], 'r', encoding='utf-8') as file:
        content = file.read()
    
    # 1. Change function signature
    old_sig = f"function {f['func']}() {{"
    new_sig = f"function {f['func']}( $atts = array() ) {{"
    content = content.replace(old_sig, new_sig)
    
    # 2. Add shortcode_atts right after ob_start();
    atts_code = f"""    $atts = shortcode_atts( array(
        'link_featured'     => '#featured',
        'link_latest'       => '#latest',
        'link_media_res'    => '#media-resources',
        'link_media_con'    => '#media-contacts',
        'link_market'       => '#cmr-market-updates',
        'link_reports'      => '#reports',
        'link_cmr_news'     => '#cmr-in-news'
    ), $atts, '{f['tag']}' );
"""
    content = content.replace("    ob_start();\n", "    ob_start();\n\n" + atts_code)
    
    with open(f['path'], 'w', encoding='utf-8') as file:
        file.write(content)
    print(f"Updated {f['path']}")

# Also update enterprise connect grid
enterprise_path = 'inc/cmr-enterprise-connect-grid.php'
with open(enterprise_path, 'r', encoding='utf-8') as file:
    ent_content = file.read()

ent_content = ent_content.replace("'link_market'       => '#market-updates'", "'link_market'       => '#cmr-market-updates'")

with open(enterprise_path, 'w', encoding='utf-8') as file:
    file.write(ent_content)
print(f"Updated {enterprise_path}")
