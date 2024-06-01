<?php

readonly class ImageService
{
    public function __construct(

    )
    {
    }



    private function resizeImage(GdImage $image, $newWidth): GdImage|false{
        // Load the source image
        // Get the original dimensions
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);


        // Calculate the new height while maintaining the aspect ratio
        $newHeight = (int)(($newWidth / $originalWidth) * $originalHeight);

        // Create a new image with the desired width and height
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        if ($newImage === false) {
              return false;
        }

        // Preserve transparency for PNG and GIF images
        $color = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        if($color === false){
            return false;
        }

        imagecolortransparent($newImage, $color);
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);

        // Resize the image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);


        return $newImage;
    }

    public function loadImage($imagePath): GdImage|false{
        $image = imagecreatefromwebp($imagePath);
        if($image === false){
            return false;
        }
        return $image;
    }

    public function splitImage(GdImage $image, $threads, $offX,$offY, &$return = []):void{
        $oX = imagesx($image);
        $oY = imagesy($image);


        $currentThread = 0;

        for($x = 0; $x < $oX; $x++){
            for($y = 0; $y < $oY; $y++){
                $color = imagecolorat($image, $x, $y);
                $color_rgb = imagecolorsforindex($image, $color);

                if($color_rgb['alpha'] == 127){
                    continue;
                }
                $rgb_hex = sprintf("%02x%02x%02x", $color_rgb['red'], $color_rgb['green'], $color_rgb['blue']);

                $command = "PX ".($x+$offX)." ".($y+$offY)." ".$rgb_hex."\n";
                $return[$currentThread%$threads][] = [$command, strlen($command)];
                $currentThread++;
            }
        }
    }




}