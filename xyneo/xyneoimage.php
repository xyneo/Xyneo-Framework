<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * Képkezelő osztály
 */
class XyneoImage extends XyneoFile
{

    /**
     * Image's file name
     *
     * @var string
     */
    private $filename;

    /**
     * Image's mime type
     *
     * @var string
     */
    private $mime;

    /**
     * Image resourse
     *
     * @var image resource
     */
    private $image = false;

    /**
     * Image has been changed
     *
     * @var boolean
     */
    private $changed = false;

    /**
     * Image is transparent
     *
     * @var boolean
     */
    private $isTransparent = false;

    /**
     * With the constructor is possible direcly open the given image file.
     *
     * @param string $filename            
     * @return XyneoImage
     */
    public function __construct($filename = null)
    {
        if (! is_null($filename)) {
            return $this->open($filename);
        }
        return $this;
    }

    /**
     * This method opens
     *
     * @param string $path            
     * @return XyneoImage
     */
    public function open($path)
    {
        if (! file_exists($path)) {
            return $this;
        }
        if ($this->isCodeInjectionAttack($path)) {
            return $this;
        }
        
        $this->mime = mime_content_type($path);
        switch ($this->mime) {
            case "image/gif":
                $this->image = imagecreatefromgif($path);
                break;
            case "image/jpeg":
                $this->image = imagecreatefromjpeg($path);
                break;
            case "image/png":
                if ($this->detectPngTransparency($path)) {
                    $this->isTransparent = true;
                }
                $this->image = imagecreatefrompng($path);
                break;
            default:
                return $this;
                break;
        }
        $this->filename = current(explode(".", end(explode("/", $path))));
        return $this;
    }

    /**
     * This method detects if the image file is fake
     *
     * @param string $path            
     * @return boolean
     */
    private function isCodeInjectionAttack($path)
    {
        return ! ((boolean) @getimagesize($path));
    }

    /**
     * This method detects if the image has any transparency
     *
     * @param string $path            
     * @return boolean
     */
    private function detectPngTransparency($path)
    {
        if (strlen($path) == 0 || ! file_exists($path)) {
            return false;
        }
        
        if (ord(file_get_contents($path, false, null, 25, 1)) & 4) {
            return true;
        }
        
        $contents = file_get_contents($path);
        if (stripos($contents, "PLTE") !== false && stripos($contents, "tRNS") !== false) {
            return true;
        }
        
        return false;
    }

    /**
     * This method checks if there is an image in the process
     *
     * @return boolean
     */
    public function exists()
    {
        return (boolean) $this->image;
    }

    /**
     * This method returns if the image has been changed
     *
     * @return boolean
     */
    public function isChanged()
    {
        return $this->changed;
    }

    /**
     * This methods returns the image filename
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * This method returns the image's size
     *
     * @return boolean array
     */
    private function getSize()
    {
        if (! $this->exists()) {
            return false;
        }
        return array(
            "width" => imagesx($this->image),
            "height" => imagesy($this->image)
        );
    }

    /**
     * This method resizes the image
     *
     * @param integer $maxX            
     * @param integer $maxY            
     * @return XyneoImage
     */
    public function resize($maxX, $maxY)
    {
        if (! $this->exists()) {
            return $this;
        }
        
        $size = $this->getSize();
        if ($size["width"] <= $maxX && $size["height"] <= $maxY) {
            $this->changed = false;
            return $this;
        }
        $newSize = array(
            "width" => 0,
            "height" => 0
        );
        if ($size["width"] > $size["height"]) { // Landscape
            $newSize["height"] = round($size["height"] / ($size["width"] / $maxX));
            $newSize["width"] = $maxX;
            if ($newSize["height"] > $maxY) {
                $newSize["width"] = round($newSize["width"] / ($newSize["height"] / $maxY));
                $newSize["height"] = $maxY;
            }
        } else { // Portrait
            $newSize["width"] = round($size["width"] / ($size["height"] / $maxY));
            $newSize["height"] = $maxY;
            if ($newSize["width"] > $maxX) {
                $newSize["height"] = round($newSize["height"] / ($newSize["width"] / $maxX));
                $newSize["width"] = $maxX;
            }
        }
        $newImage = imagecreatetruecolor($newSize["width"], $newSize["height"]);
        if ($this->isTransparent) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $newSize["width"], $newSize["height"], $size["width"], $size["height"]);
        $this->setImage($newImage);
        return $this;
    }

    /**
     * This method cuts out a piece from the center of the image
     *
     * @param integer $maxX            
     * @param integer $maxY            
     * @return XyneoImage
     */
    public function crop($maxX, $maxY)
    {
        if (! $this->exists()) {
            return $this;
        }
        
        $newImage = imagecreatetruecolor($maxX, $maxY);
        if ($this->isTransparent) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        $size = $this->getSize();
        $moveX = ceil(($size["width"] - $maxX) / 2);
        $moveY = ceil(($size["height"] - $maxY) / 2);
        imagecopyresampled($newImage, $this->image, 0, 0, $moveX, $moveY, $maxX, $maxY, $maxX, $maxY);
        $this->setImage($newImage);
        return $this;
    }

    /**
     * This method cuts out the biggest square from the center of the image
     *
     * @param integer $width            
     * @param integer $height            
     * @return XyneoImage
     */
    public function cropMax($width = false, $height = false)
    {
        if (! $this->exists()) {
            return $this;
        }
        
        $size = $this->getSize();
        if (! $width && ! $height) {
            extract($size);
            if ($width > $height) {
                $this->crop($height, $height);
            } else {
                $this->crop($width, $width);
            }
            return $this;
        }
        
        $dWidth = $size["width"];
        $dHeight = $size["height"];
        
        $ratioW = $width / $dWidth;
        $ratioH = $height / $dHeight;
        $ratio = $ratioW > $ratioH ? $ratioW : $ratioH;
        
        $newWidth = $ratio * $dWidth;
        $newHeight = $ratio * $dHeight;
        
        $temp = imagecreatetruecolor($newWidth, $newHeight);
        $white = imagecolorallocate($temp, 255, 255, 255);
        imagefill($temp, 0, 0, $white);
        imagecopyresampled($temp, $this->image, 0, 0, 0, 0, $newWidth, $newHeight, $dWidth, $dHeight);
        
        $src = $temp;
        $w = imagesx($src);
        $h = imagesy($src);
        $dest = imagecreatetruecolor($width, $height);
        
        if ($w < $width) {
            $x_coord = floor(($width - $w) / 2);
            $width = $w;
        } else {
            $x_coord = (- 1) * floor(($w - $width) / 2);
            $width = floor(($w - $width) / 2) + $width;
        }
        
        if ($h < $height) {
            $y_coord = floor(($height - $h) / 2);
            $height = $h;
        } else {
            $y_coord = (- 1) * floor(($h - $height) / 2);
            $height = floor(($h - $height) / 2) + $height;
        }
        $white = imagecolorallocate($dest, 255, 255, 255);
        imagefill($dest, 0, 0, $white);
        imagecopyresampled($dest, $src, $x_coord, $y_coord, 0, 0, $width, $height, $width, $height);
        
        $this->setImage($dest);
        return $this;
    }

    /**
     * This method puts the image in an arbitrary size rectangle and the image's ratio remains
     *
     * @param integer $width            
     * @param integer $height            
     * @param array $bgColor
     *            R,G,B
     * @return XyneoImage
     */
    public function box($width, $height, $bgColor = array(0,0,0))
    {
        if (! $this->exists()) {
            return $this;
        }
        
        $size = $this->getSize();
        $dWidth = $size["width"];
        $dHeight = $size["height"];
        
        if ($dWidth <= $width && $dHeight <= $height) {
            $iWidth = $dWidth;
            $iHeight = $dHeight;
            $x = $width / 2 - $iWidth / 2;
            $y = $height / 2 - $iHeight / 2;
        }
        
        if ($dWidth > $width || $dHeight > $height) {
            if ($width / $dWidth < $height / $dHeight) {
                $iWidth = $width;
                $iHeight = ($width / $dWidth) * $dHeight;
                $x = 0;
                $y = $height / 2 - $iHeight / 2;
            } else {
                $iHeight = $height;
                $iWidth = ($height / $dHeight) * $dWidth;
                $x = $width / 2 - $iWidth / 2;
                $y = 0;
            }
        }
        
        $dst = imagecreatetruecolor($width, $height);
        
        $color = imagecolorallocate($dst, $bgColor[0], $bgColor[1], $gradparts[2]);
        imagefill($dst, 0, 0, $color);
        
        imagecopyresampled($dst, $this->image, $x, $y, 0, 0, $iWidth, $iHeight, $dWidth, $dHeight);
        
        $this->setImage($dst);
        return $this;
    }

    /**
     * This method rotates the image.
     * The rotation is in the counterclockwise direction.
     *
     * @param float $angle            
     * @return XyneoImage
     */
    public function rotate($angle)
    {
        if (! $this->exists()) {
            return $this;
        }
        
        $newImage = imagerotate($this->image, $angle, 0);
        if ($this->isTransparent) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        $this->setImage($newImage);
        return $this;
    }

    /**
     * This method flips the image either horizontally or vertically
     *
     * @param string $direction            
     * @return XyneoImage
     */
    public function flip($direction = "horizontal")
    {
        if (! $this->exists()) {
            return $this;
        }
        
        if ($direction != "horizontal" && $direction != "vertical") {
            return $this;
        }
        
        extract($this->getSize());
        
        $newImage = imagecreatetruecolor($width, $height);
        
        if ($this->isTransparent) {
            imagecolortransparent($newImage, imagecolorallocate($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        if ($direction == "horizontal") {
            imagecopyresampled($newImage, $this->image, 0, 0, ($width - 1), 0, $width, $height, 0 - $width, $height);
        } else {
            imagecopyresampled($newImage, $this->image, 0, 0, 0, ($height - 1), $width, $height, $width, 0 - $height);
        }
        
        $this->setImage($newImage);
        return $this;
    }

    /**
     *
     * @param string $mime
     *            Only image/(gif|jpeg|png) mime types are allowed.
     * @param string $save
     *            Save the image the given destination with or without filename (e.g.: path/to/*) or print out the stdOut.
     * @return boolean
     */
    public function save($mime = "", $save = false)
    {
        if (! $this->exists()) {
            return false;
        }
        
        if (empty($mime)) {
            $mime = $this->mime;
        }
        
        if (! in_array($mime, array(
            "image/gif",
            "image/jpeg",
            "image/png"
        ))) {
            $mime = $this->mime;
        }
        $ext = end(explode("/", $mime));
        if ($ext == "jpeg") {
            $ext = "jpg";
        }
        
        if ($this->isTransparent) {
            if ($ext == "png") {
                imagecolortransparent($this->image, imagecolorallocatealpha($this->image, 0, 0, 0, 127));
                imagealphablending($this->image, false);
                imagesavealpha($this->image, true);
            } else {
                $tempSize = $this->getSize();
                $tempImage = imagecreatetruecolor($tempSize["width"], $tempSize["height"]);
                $bg = imagecolorallocate($tempImage, 255, 255, 255);
                imagefilledrectangle($tempImage, 0, 0, $tempSize["width"], $tempSize["height"], $bg);
                imagecopyresampled($tempImage, $this->image, 0, 0, 0, 0, $tempSize["width"], $tempSize["height"], $tempSize["width"], $tempSize["height"]);
                $this->setImage($tempImage);
            }
        }
        if (! $save) {
            if (ob_get_contents() !== false) {
                ob_end_clean();
            }
            header("Content-type: " . $mime);
            switch ($ext) {
                case "gif":
                    imagegif($this->image);
                    break;
                case "jpg":
                    imagejpeg($this->image, null, 100);
                    break;
                case "png":
                    imagepng($this->image, null, 9, PNG_ALL_FILTERS);
                    break;
            }
            imagedestroy($this->image);
            return true;
        }
        
        $filename = explode("/", $save);
        $tempExt = @end(explode(".", end($filename)));
        if ($tempExt && in_array($tempExt, array(
            "gif",
            "jpg",
            "png"
        ))) {
            $ext = $tempExt;
        }
        if (end($filename) == "*") {
            $filename = str_ireplace("*", $this->filename, implode("/", $filename));
        }
        $filename .= "." . $ext;
        if (file_exists($filename)) {
            @unlink($filename);
        }
        switch ($ext) {
            case "gif":
                return imagegif($this->image, $filename);
                break;
            case "jpg":
                return imagejpeg($this->image, $filename, 100);
                break;
            case "png":
                return imagepng($this->image, $filename, 9, PNG_ALL_FILTERS);
                break;
        }
        imagedestroy($this->image);
        return true;
    }

    /**
     * Set the new image resourse and set the changed property to true
     *
     * @param resource $image            
     * @return XyneoImage
     */
    private function setImage($image)
    {
        $this->image = $image;
        $this->changed = true;
        return $this;
    }

    /**
     * This method returns the image resource of false
     *
     * @return resource boolean
     */
    public function getImage()
    {
        return $this->image;
    }
}
