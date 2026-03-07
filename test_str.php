<?php
$html = file_get_contents('blade-debug.html');
$pos = strpos($html, '<div id="info-point-container"');
if ($pos !== false) {
    echo "Found at $pos\n";
    echo substr($html, $pos - 200, 300);
} else {
    echo "Not found\n";
}
