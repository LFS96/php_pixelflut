<?php

readonly class ImageService
{
    public function resizeImage(GdImage $image, int $newWidth): GdImage|false
    {
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);

        $newHeight = (int)(($newWidth / $originalWidth) * $originalHeight);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        if (!$newImage) {
            return false;
        }

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        if (!imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
            return false;
        }

        return $newImage;
    }

    public function loadImage(string $imagePath): GdImage|false
    {
        return imagecreatefromwebp($imagePath);
    }

    public function splitImage(GdImage $image, int $threads, int $offX, int $offY, array &$return = []): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        $currentThread = 0;

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = imagecolorat($image, $x, $y);
                $alpha = ($color >> 24) & 0x7F;

                if ($alpha === 127) {
                    continue;
                }

                $red = ($color >> 16) & 0xFF;
                $green = ($color >> 8) & 0xFF;
                $blue = $color & 0xFF;

                $command = "PX " . ($x + $offX) . " " . ($y + $offY) . " " . dechex($red) . dechex($green) . dechex($blue) . "\n";
                $return[$currentThread % $threads][] = [$command, strlen($command)];
                $currentThread++;
            }
        }
    }
}
