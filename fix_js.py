import sys

def fix_file(filepath, js_class):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Replace the fixed class name
    content = content.replace(f'.{js_class}-fixed-js', '.intel-nav-fixed-js')
    
    # 2. Replace the JS at the end
    start_str = '// AJAX Pagination'
    end_str = '</script>'
    
    start_idx = content.find(start_str)
    end_idx = content.find(end_str, start_idx)
    
    if start_idx != -1 and end_idx != -1:
        prefix = content[:start_idx]
        suffix = content[end_idx:]
        
        replacement = """// Fetch posts on click
        const navLinks = document.querySelectorAll('.intel-nav-links a:not(.cmr-nav-btn-subscribe)');
        if (navLinks.length > 0) {
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Check if it's an anchor link
                    const href = link.getAttribute('href');
                    if (href && href.startsWith('#') && href !== '#') {
                        // Let cmr-sticky-nav-script handle the smooth scroll
                        return;
                    }
                    
                    if (href === '#' || !href) {
                        e.preventDefault();
                    } else {
                        e.preventDefault();
                        const match = href.match(/paged=(\\d+)/);
                        if (match) {
                            currentPage = parseInt(match[1], 10);
                        } else {
                            const pathMatch = href.match(/\\/page\\/(\\d+)/);
                            if (pathMatch) {
                                currentPage = parseInt(pathMatch[1], 10);
                            } else if (href.indexOf('?') === -1 && href.indexOf('page') === -1) {
                                currentPage = 1;
                            }
                        }
                        fetchPosts(true);
                    }
                });
            });
        }
    });
    """
        
        new_content = prefix + replacement + suffix
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
            
        print(f"Fixed {filepath}")
    else:
        print(f"Could not find block in {filepath}")

fix_file('inc/cmr-channel-connect-grid.php', 'cmr-channelcgd')
fix_file('inc/cmr-smb-connect-grid.php', 'cmr-smbcgd')
fix_file('inc/cmr-media-releases-grid.php', 'cmr-mrg')
