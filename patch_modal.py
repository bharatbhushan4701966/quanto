with open('comments.php', 'r', encoding='utf-8') as f:
    content = f.read()

target = """        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('cmr-review-modal');
            var btn = document.getElementById('cmr-open-review-modal');
            var span = document.getElementById('cmr-close-review-modal');"""

replacement = """        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('cmr-review-modal');
            if (modal) {
                document.body.appendChild(modal); // Move to body to prevent position:fixed confinement by CSS transforms
            }
            var btn = document.getElementById('cmr-open-review-modal');
            var span = document.getElementById('cmr-close-review-modal');"""

if target in content:
    content = content.replace(target, replacement)
    with open('comments.php', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Successfully added appendChild to modal JS.")
else:
    print("Target not found.")
