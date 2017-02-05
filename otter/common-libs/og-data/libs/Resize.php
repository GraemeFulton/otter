<?php

/*

Original version:
    written by Jarrod Oberto
    taken from http://net.tutsplus.com/tutorials/php/image-resizing-made-easy-with-php/

Example usage:
    include("classes/Resize.php");
    $resizer = new Resize('images/cars/large/input.jpg');
    $resizer->resizeImage(150, 100, 0);
    $resizer->saveImage('images/cars/large/output.jpg', 100);

*/

Class Resize {
    private $image;
    private $width;
    private $height;
    private $imageResized;

    function __construct($fileName){
        $this->image    = $this->openImage($fileName);
        $this->width    = imagesx($this->image);
        $this->height   = imagesy($this->image);
    }

    private function openImage($file){
        if (!is_file($file)){
            throw new Exception("File {$file} doesn't exists");
        }

        switch(pathinfo($file, PATHINFO_EXTENSION)){
            case 'jpg':
            case 'jpeg': return imagecreatefromjpeg($file);
            case 'gif':	 return imagecreatefromgif($file);
            case 'png':	 return imagecreatefrompng($file);
        }

        throw new Exception("Invalid image extension for {$file}. Acceptable image types are jpg,jpeg,gif,png");
    }

    public function resizeImage($newWidth, $newHeight, $option='auto'){
        list($width, $height) = $this->getDimensions($newWidth, $newHeight, $option);

        $this->imageResized = imagecreatetruecolor($width, $height);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $width, $height, $this->width, $this->height);

        if ($option == 'crop'){
            $this->crop($width, $height, $newWidth, $newHeight);
        }
    }

    private function getDimensions($width, $height, $option){
        switch ($option){
            case 'portrait':    return array($this->getSizeByFixedHeight($height),  $height);
            case 'landscape':   return array($width, $this->getSizeByFixedWidth($width));
            case 'auto':        return $this->getSizeByAuto($width, $height);
            case 'crop':        return $this->getOptimalCrop($width, $height);
            case 'exact':		
            default:            return array($width, $height);
        }
    }

    private function getSizeByFixedHeight($height){
        return ($this->width / $this->height) * $height;
    }

    private function getSizeByFixedWidth($width){
        return ($this->height / $this->width) * $width;
    }

    private function getSizeByAuto($width, $height){
        if ($this->height < $this->width){
            return array($width, $this->getSizeByFixedWidth($width));
        }

        if ($this->height > $this->width){
            return array($this->getSizeByFixedHeight($height), $height);
        }

        if ($height < $width){
            return array($width, $this->getSizeByFixedWidth($width));
        }

        if ($height > $width){
            return array($this->getSizeByFixedHeight($height), $height);
        }

            return array($width, $height);
        }

    private function getOptimalCrop($width, $height){
        $ratio = min($this->height / $height, $this->width /  $width);
        return array(
            $this->width  / $ratio, 
            $this->height / $ratio
        );
    }

    private function crop($optimalWidth, $optimalHeight, $width, $height){
        $x = ( $optimalWidth  / 2) - ( $width  /2 );
        $y = ( $optimalHeight / 2) - ( $height /2 );

        $crop = $this->imageResized;

        $this->imageResized = imagecreatetruecolor($width , $height);
        imagecopyresampled($this->imageResized, $crop, 0, 0, $x, $y, $width, $height , $width, $height);
    }

    public function saveImage($savePath, $imageQuality="100"){
        switch(pathinfo($savePath, PATHINFO_EXTENSION)){
            case 'jpg':
            case 'jpeg':
                if (imagetypes() & IMG_JPG){
                    imagejpeg($this->imageResized, $savePath, $imageQuality);
                }
            break;

            case 'gif':
                if (imagetypes() & IMG_GIF){
                    imagegif($this->imageResized, $savePath);
                }
            break;
            
            case 'png':
                if (imagetypes() & IMG_PNG){
                    // Scale quality from 0-100 to 0-9
                    // Invert quality setting as 0 is best, not 9
                    $invertScaleQuality = 9 - round(($imageQuality/100) * 9);
                    imagepng($this->imageResized, $savePath, $invertScaleQuality);
                }
            break;
        }

        imagedestroy($this->imageResized);
    }
}
