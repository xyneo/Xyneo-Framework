<?php

if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

class XyneoFile
{

    /**
     * Returns the extension from a filename
     * 
     * @param string $fname
     * @return string
     */
    public function xGetExtension($fname)
    {
        if (empty($fname)) {
            die('Could not get file extension because no filename was given.');
        }
        
        $i = strrpos($fname, ".");
        
        if (! $i) {
            return "";
        }
        
        $l = strlen($fname) - $i;
        $ext = substr($fname, $i + 1, $l);
        
        return $ext;
    }
    
    /**
     * Upload, resize, crop an image file.
     * 
     * @param string $inputname
     * @param string $location
     * @param string $fname
     * @param integer $maxsize
     * @param integer $container_width
     * @param integer $container_height
     * @param string $force_or_crop
     * @param array $msg
     * @return array 
     */
    public function xImageUpload($inputname, $location, $fname = false, $maxsize = false, $container_width = false, $container_height = false, $force_or_crop = false, $msg = false)
    {
        if ($msg) {
            $messages = $msg;
        } else {
            $messages = array(
                "xiu_format" => "Invalid file format!",
                "xiu_size" => "The file size is over the limit!",
                "xiu_file" => "No file was selected!",
                "xiu_error" => "Something went wrong!",
                "xiu_done" => "File uploaded!"
            );
        }
        
        if (! $_SERVER["REQUEST_METHOD"] == "POST") {
            return array(
                "message" => $messages["xiu_error"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        if (! isset($_FILES[$inputname]) || empty($_FILES[$inputname])) {
            return array(
                "message" => $messages["xiu_file"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        $image = $_FILES[$inputname]["name"];
        $uploaded_file = $_FILES[$inputname]["tmp_name"];
        
        if (! $image) {
            return array(
                "message" => $messages["xiu_file"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        $file_name = stripslashes($image);
        $extension = $this->xGetExtension($file_name);
        $extension = strtolower($extension);
        
        if ($extension != "jpg" && $extension != "jpeg" && $extension != "png" && $extension != "gif") {
            return array(
                "message" => $messages["xiu_format"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        $size = filesize($uploaded_file);
        if (! $size) {
            return array(
                "message" => $messages["xiu_error"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        if ($maxsize and $size > $maxsize * 1024) {
            return array(
                "message" => $messages["xiu_size"],
                "uploaded" => 0,
                "file" => ''
            );
        }
        
        if ($extension == "jpg" || $extension == "jpeg") {
            $src = imagecreatefromjpeg($uploaded_file);
        } elseif ($extension == "png") {
            $src = imagecreatefrompng($uploaded_file);
        } else {
            $src = imagecreatefromgif($uploaded_file);
        }
        
        imagealphablending($src, true);
        list ($width, $height) = getimagesize($uploaded_file);
        
        $new_height = $height;
        $new_width = $width;
        
        switch ($force_or_crop) {
            case "crop":
                if ($container_width && ! $container_height) {
                    $new_width = $container_width;
                    $new_height = $new_width * ($height / $width);
                } elseif (! $container_width && $container_height) {
                    $new_height = $container_height;
                    $new_width = $new_height / ($height / $width);
                } elseif ($container_width && $container_height) {
                    $new_width = $container_width;
                    $new_height = $container_height;
                }
                
                if ($width / $height < $new_width / $new_height) {
                    $xtra_new_width = $new_width;
                    $xtra_new_height = $xtra_new_width * ($height / $width);
                } else {
                    $xtra_new_height = $new_height;
                    $xtra_new_width = $xtra_new_height / ($height / $width);
                }
                
                $top = ($new_height - $xtra_new_height) / 2;
                $left = ($new_width - $xtra_new_width) / 2;
                
                $ns = imagecreatetruecolor($xtra_new_width, $xtra_new_height);
                imagecolortransparent($ns, imagecolorallocatealpha($ns, 0, 0, 0, 127));
                imagealphablending($ns, false);
                imagesavealpha($ns, true);
                imagecopyresampled($ns, $src, 0, 0, 0, 0, $xtra_new_width, $xtra_new_height, $width, $height);
                
                $tmp = imagecreatetruecolor($new_width, $new_height);
                imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                imagecopy($tmp, $ns, $left, $top, 0, 0, $xtra_new_width, $xtra_new_height);
                imagedestroy($ns);
                break;
            case "force":
                if ($container_width && ! $container_height) {
                    $new_width = $container_width;
                    $new_height = $new_width * ($height / $width);
                } elseif (! $container_width && $container_height) {
                    $new_height = $container_height;
                    $new_width = $new_height / ($height / $width);
                } elseif ($container_width && $container_height) {
                    $new_width = $container_width;
                    $new_height = $container_height;
                }
                
                $tmp = imagecreatetruecolor($new_width, $new_height);
                imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                break;
            default:
                if ($container_width && ! $container_height && $container_width < $width) {
                    $new_width = $container_width;
                    $new_height = $new_width * ($height / $width);
                } elseif (! $container_width && $container_height && $container_height < $height) {
                    $new_height = $container_height;
                    $new_width = $new_height / ($height / $width);
                } elseif ($container_width && $container_height) {
                    if ($container_width < $width || $container_height < $height) {
                        $img_aspect_ratio = $width / $height;
                        $ctr_aspect_ratio = $container_width / $container_height;
                        
                        if ($ctr_aspect_ratio > 1) {
                            if ($img_aspect_ratio <= $ctr_aspect_ratio) {
                                $new_height = $container_height;
                                $new_width = $new_height / ($height / $width);
                            } else {
                                $new_width = $container_width;
                                $new_height = $new_width * ($height / $width);
                            }
                        }
                        
                        if ($ctr_aspect_ratio < 1) {
                            if ($img_aspect_ratio <= $ctr_aspect_ratio) {
                                $new_height = $container_height;
                                $new_width = $new_height / ($height / $width);
                            } else {
                                $new_width = $container_width;
                                $new_height = $new_width * ($height / $width);
                            }
                        }
                        
                        if ($ctr_aspect_ratio == 1) {
                            if ($width > $height) {
                                $new_width = $container_width;
                                $new_height = $new_width * ($height / $width);
                            } else {
                                $new_height = $container_height;
                                $new_width = $new_height / ($height / $width);
                            }
                        }
                    }
                }
                
                $tmp = imagecreatetruecolor($new_width, $new_height);
                imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
                imagealphablending($tmp, false);
                imagesavealpha($tmp, true);
                imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                break;
        }
        
        if ($fname) {
            $file_name = $fname . "." . $extension;
        } else {
            $file_name = $image;
        }
        if ($extension == "jpg" || $extension == "jpeg") {
            imagejpeg($tmp, $location . $file_name, 100);
        } elseif ($extension == "png") {
            imagepng($tmp, $location . $file_name, 9, PNG_ALL_FILTERS);
        } else {
            imagegif($tmp, $location . $file_name);
        }
        
        imagedestroy($src);
        imagedestroy($tmp);
        
        return array(
            "message" => $messages["xiu_done"],
            "uploaded" => 1,
            "file" => $file_name
        );
    }
}
