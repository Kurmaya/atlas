import datetime
import os

# Directory containing the crawled website
crawled_dir = '/var/www/website'

# Function to recursively find all HTML files
def find_html_files(directory):
    html_files = []
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.html'):
                html_files.append(os.path.join(root, file))
    return html_files

# Find all HTML files in the crawled directory
html_files = find_html_files(crawled_dir)

# Generate the sitemap XML
sitemap_xml = '''<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
'''

# Directories to exclude
excluded_dirs = ['httrack_output', 'atlas-demo1', 'js', 'backpain/Atlas-Cyrotheraphy-website']

for html_file in html_files:
    # Convert the file path to a URL
    url = html_file.replace(crawled_dir, 'https://www.atlaschiroindia.com')
    url = url.replace(os.sep, '/')
    #url = url.replace('/index.html', '')  # Remove 'index.html' from the URL
    #url = url.replace('.html', '')  # Remove '.html' from the URL

    # Check if the URL should be excluded
    if not any(excluded in url for excluded in excluded_dirs):
        sitemap_xml += f'''
    <url>
        <loc>{url}</loc>
        <lastmod>{datetime.datetime.now().strftime('%Y-%m-%d')}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
'''

sitemap_xml += '</urlset>'

# Write the sitemap XML to a file
with open('/var/www/website/sitemap.xml', 'w') as file:
    file.write(sitemap_xml)
