<?php
$html = file_get_contents('blade-debug.html');
$dom = new DOMDocument();
@$dom->loadHTML($html);
$body = $dom->getElementsByTagName('body')->item(0);
foreach ($body->childNodes as $child) {
    if ($child->nodeType == XML_ELEMENT_NODE && $child->tagName === 'div') {
        echo "=== FIRST ROOT DIV CONTENTS ===\n";
        $inner = $dom->saveHTML($child);
        echo substr($inner, -500); // See the the last 500 characters
        break;
    }
}
