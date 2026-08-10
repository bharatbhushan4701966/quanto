import os

files = [
    'inc/cmr-enterprise-connect-grid.php',
    'inc/cmr-channel-connect-grid.php',
    'inc/cmr-smb-connect-grid.php',
    'inc/cmr-media-releases-grid.php'
]

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    new_lines = []
    for line in lines:
        if 'link_media_res' in line or 'link_media_con' in line:
            # Skip this line (removes the links from the HTML)
            # Wait, this will also remove them from the shortcode_atts array!
            # That's actually totally fine, because they aren't used anymore.
            continue
        new_lines.append(line)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.writelines(new_lines)

print("Removed Media Resources and Media Contacts from Connect grids.")
