with open('inc/quanto-functions.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

new_lines = []
skip = False
for i, line in enumerate(lines):
    if i >= 488 and i <= 574:
        continue
    new_lines.append(line)

with open('inc/quanto-functions.php', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)
print("Removed duplicate old comment callback code.")
