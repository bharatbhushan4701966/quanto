import re

with open('inc/cmr-sticky-nav-script.php', 'r', encoding='utf-8') as f:
    content = f.read()

fallback_addition = """                            } else if (targetId.includes('featured')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('featured'));
                            } else if (targetId.includes('latest')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('latest'));
                            } else if (targetId.includes('media-resource')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media resource'));
                            } else if (targetId.includes('media-contact')) {
                                matchingHeading = headings.find(h => h.textContent.toLowerCase().includes('media contact') || h.textContent.toLowerCase().includes('contact us'));
"""

content = content.replace("                            } else if (targetId.includes('newsroom') || targetId.includes('news')) {", fallback_addition + "                            } else if (targetId.includes('newsroom') || targetId.includes('news')) {")

with open('inc/cmr-sticky-nav-script.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated cmr-sticky-nav-script.php")
