import urllib.request
import re

url = 'http://qai8358l95-staging.onrocket.site/cmr-news-item/indias-electric-two-wheeler-market-grows-17-yoy-in-q4-2025-digital-cluster-adoption/'
req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
html = urllib.request.urlopen(req, timeout=10).read().decode('utf-8', errors='ignore')

# Find all elementor-post-* CSS links
css_links = re.findall(r'<link[^>]*id=[\'"].*?elementor-post.*?[\'"][^>]*>', html)
print("SINGLE PAGE CSS LINKS:")
for link in css_links:
    print(link)
