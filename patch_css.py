with open('comments.php', 'r', encoding='utf-8') as f:
    content = f.read()

target = """<div id="cmr-review-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(243, 244, 246, 0.95); z-index: 9999; align-items: center; justify-content: center;">"""
replacement = """<div id="cmr-review-modal" style="display: none; position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background: rgba(243, 244, 246, 0.95); z-index: 999999 !important; align-items: center; justify-content: center; margin: 0; padding: 0; box-sizing: border-box;">"""

if target in content:
    content = content.replace(target, replacement)
    with open('comments.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Fixed modal CSS!")
else:
    print("Target not found. Looking for substring...")
    if 'id="cmr-review-modal"' in content:
        print("Found ID, manual replace needed.")
