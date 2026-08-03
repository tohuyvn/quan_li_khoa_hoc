<?php

require __DIR__ . '/../src/bootstrap.php';

$logoPath = BASE_PATH . '/image/logo.webp';
$destDir = BASE_PATH . '/image';

if (!file_exists($logoPath)) {
    echo "Logo file not found at $logoPath\n";
    exit(1);
}

$sizes = [
    192 => 'icon-192.png',
    512 => 'icon-512.png'
];

$gdAvailable = extension_loaded('gd') && function_exists('imagecreatefromwebp');

foreach ($sizes as $size => $filename) {
    $destPath = $destDir . '/' . $filename;
    echo "Generating PWA icon for size $size x $size... ";

    if ($gdAvailable) {
        $srcImg = @imagecreatefromwebp($logoPath);
        if ($srcImg) {
            $destImg = imagecreatetruecolor($size, $size);
            
            // Set alpha channel / transparency
            imagealphablending($destImg, false);
            imagesavealpha($destImg, true);
            
            $width = imagesx($srcImg);
            $height = imagesy($srcImg);
            
            imagecopyresampled($destImg, $srcImg, 0, 0, 0, 0, $size, $size, $width, $height);
            imagepng($destImg, $destPath);
            
            imagedestroy($destImg);
            imagedestroy($srcImg);
            echo "Done (resized via GD).\n";
            continue;
        }
    }

    // Fallback: Copy logo file directly
    copy($logoPath, $destPath);
    echo "Done (fallback copy).\n";
}

echo "PWA icons generation completed.\n";
