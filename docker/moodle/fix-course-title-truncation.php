<?php
$file = '/var/www/html/theme/moove/templates/block_myoverview/view-cards.mustache';
if (!file_exists($file)) {
    die("Error: File not found at $file\n");
}
$content = file_get_contents($file);

$target = '{{#shortentext}}55, {{{fullname}}} {{/shortentext}}';
$replacement = '{{{fullname}}}';

if (strpos($content, $target) !== false) {
    $content = str_replace($target, $replacement, $content);
    file_put_contents($file, $content);
    echo "Successfully updated $file\n";
} else {
    echo "Target string not found in $file. It might already be updated.\n";
}
