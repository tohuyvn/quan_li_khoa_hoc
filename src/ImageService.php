<?php

namespace App;

class ImageService
{
    public const MAX_UPLOAD_BYTES = 5 * 1024 * 1024;

    /** @return array{extension: string, mime: string, width: int, height: int} */
    public static function inspectUploadedImage(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK
            || empty($file['tmp_name'])
            || !is_uploaded_file((string) $file['tmp_name'])
        ) {
            throw new \InvalidArgumentException('File upload không hợp lệ.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_UPLOAD_BYTES) {
            throw new \InvalidArgumentException('Ảnh phải có dung lượng không quá 5 MB.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file((string) $file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($extensions[$mime])) {
            throw new \InvalidArgumentException('Định dạng ảnh không được hỗ trợ.');
        }

        $dimensions = @getimagesize((string) $file['tmp_name']);
        if ($dimensions === false || $dimensions[0] <= 0 || $dimensions[1] <= 0
            || $dimensions[0] > 10000 || $dimensions[1] > 10000
        ) {
            throw new \InvalidArgumentException('Nội dung hoặc kích thước ảnh không hợp lệ.');
        }

        return [
            'extension' => $extensions[$mime],
            'mime' => $mime,
            'width' => (int) $dimensions[0],
            'height' => (int) $dimensions[1],
        ];
    }

    /**
     * Process an uploaded image: convert to WebP and generate thumb and medium sizes.
     * Returns an array with paths to ['full', 'medium', 'thumb'].
     * If GD is not available, falls back to copying the original file.
     */
    public static function processImage(string $sourcePath, string $destDir, string $baseName): array
    {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0775, true);
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $outputFull = $baseName . '.webp';
        $outputMedium = $baseName . '_medium.webp';
        $outputThumb = $baseName . '_thumb.webp';

        $fullPath = $destDir . '/' . $outputFull;
        $mediumPath = $destDir . '/' . $outputMedium;
        $thumbPath = $destDir . '/' . $outputThumb;

        if (extension_loaded('gd') && function_exists('imagewebp')) {
            // Load source image
            $srcImg = null;
            switch ($ext) {
                case 'jpeg':
                case 'jpg':
                    $srcImg = @imagecreatefromjpeg($sourcePath);
                    break;
                case 'png':
                    $srcImg = @imagecreatefrompng($sourcePath);
                    break;
                case 'gif':
                    $srcImg = @imagecreatefromgif($sourcePath);
                    break;
                case 'webp':
                    $srcImg = @imagecreatefromwebp($sourcePath);
                    break;
            }

            if ($srcImg) {
                // Ensure proper transparency handler for png/gif to webp
                imagepalettetotruecolor($srcImg);
                imagealphablending($srcImg, true);
                imagesavealpha($srcImg, true);

                // Save full version as webp
                imagewebp($srcImg, $fullPath, 85);

                // Get original dimensions
                $width = imagesx($srcImg);
                $height = imagesy($srcImg);

                // Generate medium version (max width 600px)
                $mediumWidth = 600;
                if ($width > $mediumWidth) {
                    $mediumHeight = (int) (($height / $width) * $mediumWidth);
                    $mediumImg = imagecreatetruecolor($mediumWidth, $mediumHeight);
                    imagealphablending($mediumImg, false);
                    imagesavealpha($mediumImg, true);
                    imagecopyresampled($mediumImg, $srcImg, 0, 0, 0, 0, $mediumWidth, $mediumHeight, $width, $height);
                    imagewebp($mediumImg, $mediumPath, 80);
                    imagedestroy($mediumImg);
                } else {
                    imagewebp($srcImg, $mediumPath, 80);
                }

                // Generate thumb version (max width 200px)
                $thumbWidth = 200;
                if ($width > $thumbWidth) {
                    $thumbHeight = (int) (($height / $width) * $thumbWidth);
                    $thumbImg = imagecreatetruecolor($thumbWidth, $thumbHeight);
                    imagealphablending($thumbImg, false);
                    imagesavealpha($thumbImg, true);
                    imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $width, $height);
                    imagewebp($thumbImg, $thumbPath, 80);
                    imagedestroy($thumbImg);
                } else {
                    imagewebp($srcImg, $thumbPath, 80);
                }

                imagedestroy($srcImg);

                return [
                    'full' => '/image/' . $outputFull,
                    'medium' => '/image/' . $outputMedium,
                    'thumb' => '/image/' . $outputThumb
                ];
            }
        }

        // Fallback if GD is not available or image loading fails:
        // We copy the original uploaded image file to the target paths.
        // Even if they are not real webp, they will have these filenames for structural consistency.
        $outputFullFallback = $baseName . '.' . $ext;
        $outputMediumFallback = $baseName . '_medium.' . $ext;
        $outputThumbFallback = $baseName . '_thumb.' . $ext;

        $fullPathFallback = $destDir . '/' . $outputFullFallback;
        $mediumPathFallback = $destDir . '/' . $outputMediumFallback;
        $thumbPathFallback = $destDir . '/' . $outputThumbFallback;

        copy($sourcePath, $fullPathFallback);
        copy($sourcePath, $mediumPathFallback);
        copy($sourcePath, $thumbPathFallback);

        return [
            'full' => '/image/' . $outputFullFallback,
            'medium' => '/image/' . $outputMediumFallback,
            'thumb' => '/image/' . $outputThumbFallback
        ];
    }
}
