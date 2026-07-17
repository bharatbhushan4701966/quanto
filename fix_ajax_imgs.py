import re

IMG_STYLE = 'style="width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important; margin: 0 !important; padding: 0 !important;"'
A_STYLE = 'style="display: block; width: 100%; height: 100%;"'

with open('inc/cmr-ajax-handlers.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Fix anchor tags inside img-wrap that are missing height:100%
# Pattern: <a href="..."> without style, inside a card-img-wrap context
# Replace <a href="..."> with <a href="..." style="display:block;width:100%;height:100%;">
# But only where there is NO style already on the anchor
content = re.sub(
    r'(<div class="cmr-[a-z]+-card-img-wrap">)\s*\n(\s*<a href="[^"]*")>',
    lambda m: m.group(1) + '\n' + m.group(2) + ' ' + A_STYLE + '>',
    content
)

# Fix <img> tags inside AJAX card markup - add inline styles
# Match <img src="..." alt="..."> WITHOUT any style attribute
content = re.sub(
    r'(<img src="[^"]*" alt="[^"]*")(?! style=)>',
    r'\1 ' + IMG_STYLE + '>',
    content
)

with open('inc/cmr-ajax-handlers.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Done - AJAX handler images fixed")
