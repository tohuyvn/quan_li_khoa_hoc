<?php

require __DIR__ . '/../src/bootstrap.php';

$imageDir = BASE_PATH . '/image';
$dirIterator = new DirectoryIterator($imageDir);

echo "Starting image conversion script...\n";

$gdAvailable = extension_loaded('gd') && function_exists('imagewebp');
if (!$gdAvailable) {
    echo "WARNING: GD extension with WebP support is NOT available. Fallback copy method will be used.\n";
}

foreach ($dirIterator as $fileInfo) {
    if ($fileInfo->isDot() || $fileInfo->isDir()) {
        continue;
    }

    $filename = $fileInfo->getFilename();
    $ext = strtolower($fileInfo->getExtension());

    // Skip already converted sizes, logo, or non-image files
    if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'])) {
        if (strpos($filename, '_thumb') !== false || strpos($filename, '_medium') !== false) {
            continue;
        }
        if ($filename === 'logo.webp' || str_starts_with($filename, 'banner_') || str_starts_with($filename, 'banner1') || str_starts_with($filename, 'banner2') || str_starts_with($filename, 'banner3') || str_starts_with($filename, 'promo_')) {
            // Skip banners and logos
            continue;
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $sourcePath = $fileInfo->getPathname();

        echo "Processing: $filename ... ";

        $results = \App\ImageService::processImage($sourcePath, $imageDir, $baseName);

        echo "Done!\n";
        echo "  - Full: " . $results['full'] . "\n";
        echo "  - Medium: " . $results['medium'] . "\n";
        echo "  - Thumb: " . $results['thumb'] . "\n";
    }
}

echo "Image conversion completed!\n";
