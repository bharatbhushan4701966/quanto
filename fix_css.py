import sys
import re

def update_css(filepath, class_prefix):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Replace the pill style with flat style
    pattern_pill = r'/\* Top Navigation Pill Style \*/.*?backdrop-filter: blur\(10px\);\s+justify-content: space-between;\s+\}'
    replacement_pill = f"""/* Top Navigation Style */
        .{class_prefix}-top-nav {{
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 0;
            margin-bottom: 40px;
            background: #fff;
            position: relative;
            z-index: 99999;
        }}"""
    
    content = re.sub(pattern_pill, replacement_pill, content, flags=re.MULTILINE|re.DOTALL)
    
    # 2. Update nav title size
    pattern_title = rf'\.{class_prefix}-nav-title \{{.*?font-size: 22px;.*?\}}'
    replacement_title = f""".{class_prefix}-nav-title {{
            font-size: 16px;
            font-weight: 600;
            color: #111;
        }}"""
    content = re.sub(pattern_title, replacement_title, content, flags=re.MULTILINE|re.DOTALL)
    
    # 3. Update intel-nav-links
    pattern_links = r'\.intel-nav-links \{.*?\}\s+\.intel-nav-links a \{.*?\}\s+\.intel-nav-links a:hover \{.*?\}\s+\.intel-nav-links a\.cmr-nav-btn-subscribe:hover \{.*?\}'
    replacement_links = """.intel-nav-links {
            display: flex;
            gap: 35px;
            align-items: center;
        }
        .intel-nav-links a {
            color: #111;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .intel-nav-links a:hover {
            color: #6A35FF;
        }
        .intel-nav-links a.cmr-nav-btn-subscribe:hover {
            background: #111 !important;
            color: #fff !important;
        }"""
    content = re.sub(pattern_links, replacement_links, content, flags=re.MULTILINE|re.DOTALL)
    
    # 4. Remove intel-nav-fixed-js
    pattern_fixed = r'\s*\.intel-nav-fixed-js \{.*?\border-top: none;\s*\}'
    content = re.sub(pattern_fixed, '', content, flags=re.MULTILINE|re.DOTALL)

    # 5. Remove any leftover Top Navigation Pill Style text just in case
    content = content.replace("/* Top Navigation Pill Style */", "/* Top Navigation Style */")

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Updated CSS in {filepath}")

update_css('inc/cmr-enterprise-connect-grid.php', 'cmr-enterprisecgd')
update_css('inc/cmr-channel-connect-grid.php', 'cmr-channelcgd')
update_css('inc/cmr-smb-connect-grid.php', 'cmr-smbcgd')
update_css('inc/cmr-media-releases-grid.php', 'cmr-mrg')
