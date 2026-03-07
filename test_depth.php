<?php
$html = file_get_contents('blade-debug.html');

$depth = 0;
$line = 1;
// A very naive string parser to track <div and </div 
$len = strlen($html);
for ($i = 0; $i < $len; $i++) {
    if ($html[$i] === "\n") {
        $line++;
    }
    
    // open div
    if (substr($html, $i, 4) === '<div') {
        // ensure it's a tag (not a component or something else)
        if (preg_match('/^<div[\s>]/i', substr($html, $i, 6))) {
            $depth++;
        }
    }
    
    // close div
    if (substr($html, $i, 5) === '</div') {
        if (preg_match('/^<\/div\s*>/i', substr($html, $i, 7))) {
            $depth--;
            if ($depth === 0) {
                echo "DEPTH REACHED 0 AT LINE: $line\n";
            }
        }
    }
}
echo "FINAL DEPTH: $depth\n";
