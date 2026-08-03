<?php
/**
 * Simple CSS minifier script for Shop Hades.
 */

function minifyCSS($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/*][^*]*\*+)*/!', '', $css);
    // Remove space after colons, commas, semicolons, brackets
    $css = str_replace(': ', ':', $css);
    $css = str_replace(', ', ',', $css);
    $css = str_replace('; ', ';', $css);
    $css = str_replace('{ ', '{', $css);
    $css = str_replace(' }', '}', $css);
    $css = str_replace(';}', '}', $css);
    // Replace multiple spaces/newlines with a single space
    $css = preg_replace('/\s+/', ' ', $css);
    return trim($css);
}

$cssDir = dirname(__DIR__) . '/public/css';
$files = ['shop.css', 'animation.css'];

foreach ($files as $file) {
    $filePath = "$cssDir/$file";
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        $minified = minifyCSS($content);
        $minFile = str_replace('.css', '.min.css', $file);
        file_put_contents("$cssDir/$minFile", $minified);
        echo "Minified $file to $minFile (" . strlen($content) . " bytes -> " . strlen($minified) . " bytes)\n";
    } else {
        echo "File not found: $filePath\n";
    }
}
