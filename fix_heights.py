import os
import glob
files = glob.glob('inc/cmr-*-grid.php')
for f in files:
    with open(f, 'r') as file:
        content = file.read()
    
    # Force wrapper height
    content = content.replace('height: 240px;\n            overflow: hidden;', 'height: 240px !important;\n            min-height: 240px !important;\n            flex-shrink: 0;\n            overflow: hidden;')
    
    # Also add inline styles to the <img> tags just to be bulletproof
    content = content.replace('<img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>">', '<img src="<?php echo esc_url($bg_image); ?>" alt="<?php echo esc_attr($title); ?>" style="width: 100% !important; height: 100% !important; object-fit: cover !important; margin: 0 !important; padding: 0 !important; display: block !important;">')
    
    with open(f, 'w') as file:
        file.write(content)
print("Updated all grids")
