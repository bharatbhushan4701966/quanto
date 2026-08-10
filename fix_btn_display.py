import sys

files = [
    'inc/cmr-enterprise-connect-grid.php',
    'inc/cmr-channel-connect-grid.php',
    'inc/cmr-smb-connect-grid.php',
    'inc/cmr-media-releases-grid.php'
]

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # Find the subscribe button inline style and change inline-flex to none
    # "style="display: inline-flex; align-items: center; justify-content: center;"
    content = content.replace(
        'style="display: inline-flex; align-items: center; justify-content: center; background: #fff;',
        'style="display: none; align-items: center; justify-content: center; background: #fff;'
    )

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    
    print(f"Updated {filepath}")
