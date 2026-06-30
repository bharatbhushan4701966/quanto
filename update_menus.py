import os, re, glob
for file in glob.glob('inc/cmr-mega-menu-*.php'):
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # 1. Hide labels on mobile
    if '.cmr-mm-label' not in content or 'display: none' not in content.split('.cmr-mm-label')[1][:50]:
        content = re.sub(r'(@media \(max-width: 1024px\) \{)', r'\1\n            .cmr-mm-label, .cmr-mmt-label { display: none !important; }', content)
    
    # 2. Extract specific variables for this menu
    menu_id_match = re.search(r"document\.getElementById\('([^']+)'\)", content)
    if not menu_id_match: continue
    menu_id = menu_id_match.group(1)
    
    text_match = re.search(r"if \(text === '([^']+)'\)", content)
    if not text_match: continue
    menu_text = text_match.group(1)
    
    class_match = re.search(r"classList\.add\('([^']+)'\)", content)
    if not class_match: continue
    menu_class = class_match.group(1)
    
    # Determine if it uses a wrapperOuter (what-we-think, etc)
    uses_wrapper = "createElement('div')" in content or 'wrapperOuter' in content
    wrapper_class = ''
    if uses_wrapper:
        w_match = re.search(r"className = '([^']+)'", content)
        if w_match: wrapper_class = w_match.group(1)
    
    # Generate new JS block
    new_js = f'''<script>
        document.addEventListener('DOMContentLoaded', function() {{
            var megaMenuTemplate = document.getElementById('{menu_id}');
            if (!megaMenuTemplate) return;

            function injectMegaMenu() {{
                var navLinks = document.querySelectorAll('.menu-item > a, .elementor-item');
                navLinks.forEach(function(link) {{
                    var text = link.innerText.trim().toLowerCase();
                    if (text === '{menu_text}') {{
                        var parentLi = link.closest('li, .menu-item');
                        if (parentLi && !parentLi.classList.contains('{menu_class}')) {{
                            parentLi.classList.add('{menu_class}');
'''
    if uses_wrapper:
        new_js += f'''                            var wrapperOuter = document.createElement('div');
                            wrapperOuter.className = '{wrapper_class}';
                            Array.from(megaMenuTemplate.childNodes).forEach(function(node) {{ wrapperOuter.appendChild(node.cloneNode(true)); }});
                            parentLi.appendChild(wrapperOuter);
'''
    else:
        new_js += f'''                            Array.from(megaMenuTemplate.childNodes).forEach(function(node) {{ parentLi.appendChild(node.cloneNode(true)); }});
'''
    
    new_js += f'''                        }}
                    }}
                }});
            }}

            injectMegaMenu();
            setInterval(injectMegaMenu, 1000);

            document.addEventListener('click', function(e) {{
                if (window.innerWidth <= 1024) {{
                    var link = e.target.closest('a');
                    if (link) {{
                        var text = link.innerText.trim().toLowerCase();
                        if (text === '{menu_text}') {{
                            var parentLi = link.closest('.{menu_class}');
                            if (parentLi) {{
                                e.preventDefault();
                                e.stopPropagation();
                                parentLi.classList.toggle('cmr-mobile-open');
                            }}
                        }}
                    }}
                }}
            }}, true);
        }});
    </script>'''
    
    # Replace the old script block
    content = re.sub(r'<script>.*?</script>', new_js, content, flags=re.DOTALL)
    
    with open(file, 'w', encoding='utf-8') as f:
        f.write(content)
    print('Updated', file)
