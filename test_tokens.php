<?php
$html = file_get_contents('blade-debug.html');

$html = trim(str_replace(['<!--[if BLOCK]-->', '<!--[if ENDBLOCK]-->'], '', $html));

$tokens = token_get_all('<?php ' . $html);

$depth = 0;
$nodes = 0;

foreach ($tokens as $token) {
    if (! is_array($token) && $token === '<') {
        if ($depth === 0) {
            $nodes++;
        }

        $depth++;
    } elseif (! is_array($token) && $token === '>') {
        $depth--;
    }
}

echo "Root Nodes via tokens: $nodes\n";
