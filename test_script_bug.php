<?php
$html = '<div><script>var a = "</div><span></span>";</script></div><div>second</div>';
$dom = new DOMDocument();
@$dom->loadHTML($html);
$body = $dom->getElementsByTagName('body')->item(0);
echo 'ROOTS: ' . $body->childNodes->length . PHP_EOL;
foreach ($body->childNodes as $child) { echo "-> " . $child->tagName . "\n"; }
