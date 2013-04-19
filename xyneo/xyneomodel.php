<?php if ( ! defined('XYNEO') ) die("Direct access denied!");

class XyneoModel
{

// Instantiate the XyneoDatabaseClass

    public function __construct()
    {
        
        if(DB_ALLOW == 'on')
            
            $this -> db = new XyneoDataBase();
        
    }
    
//  Validating email address
    
    protected function xValidEmail($email)
    {
        
        if(empty($email))
            
            die('No email given to validate.');
       
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            
            return false;
            
        }
        
        else{
            
            return true;
        
        }
        
    }
 
// Basic built-in data filter 
    
    protected function xFilter($str)
    {
        
        return trim(strip_tags($str));
        
    }
    
// Password strength checker
    
    protected function xStrongPassword($password,$minlength,$maxlength,$strength)
    {
        
        if(empty($strength))
            
            die('No strength given to password checker.');
        
        if(empty($maxlength))
            
            die('No maxlength given to password checker.');
        
        if(empty($minlength))
            
            die('No minlength given to password checker.');
        
        
        $password=trim($password);
        
        $actual_strength = 0;
        
        if(strlen($password)<$minlength)
        {
            
            return false;
            
        }
        
        if(strlen($password)>$maxlength)
        {
            
            return false;
            
        }
        
        if (preg_match("/[a-z]/", $password) && preg_match("/[A-Z]/", $password))
                
            $actual_strength++;
        
        if (preg_match("/[0-9]/", $password))
                
            $actual_strength++;
        
        if (preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,Ã‚Â£,(,)]/", $password))
                
            $actual_strength++;
        
        if($actual_strength<$strength)
            
            return false;
        
        return true;
    }

// Checks if the given value an age of a human
    
    protected function xIsAge($age)
    {
        
        
        if(!is_numeric($age))
            
            return false;
        
        if($age<1 or $age>120)
            
            return false;
        
        else
            
            return true;
        
    }
    
// Validate URL 
    
    protected function xIsUrl($url)
    {
       
        return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
    }
    
// Validate full name fields
    
    protected function xIsFullname($fullname)
    {
        
        $result = explode(' ',$fullname);
        
        if(!isset($result[0]) or !isset($result[1]))
            
            return false;
        
        $f_name = trim($result[0]);
        
        $l_name = trim($result[1]);
        
        if(strlen($f_name)>= 2 && strlen($l_name)>= 2)
            
            return true;
        
        else
            
            return false;
    }
    
// Returns the extension from a filename
    
    protected function xGetExtension($fname){
        
        if(empty($fname))
            
            die('Could not get file extension because no filename was given.');
        
         $i = strrpos($fname,".");
         
         if (!$i) { return ""; } 
         
         $l = strlen($fname) - $i;
         
         $ext = substr($fname,$i+1,$l);
         
         return $ext;
    }
    
    protected function xImageUpload($inputname, $location, $fname=false, $maxsize=false, $container_width=false, $container_height=false, $force_or_crop=false, $msg=false)
    {
        if ($msg)
          $messages = $msg;
        else
          $messages = array(
            "xiu_format" => "Invalid file format!",
            "xiu_size"   => "The file size is over the limit!",
            "xiu_file"   => "No file was selected!",
            "xiu_error"  => "Something went wrong!",
            "xiu_done"   => "File uploaded!"
          );

        if (!$_SERVER["REQUEST_METHOD"] == "POST")
          return array(
            "message" => $messages["xiu_error"],
            "sent"    => 0
          );

        if (!isset($_FILES[$inputname]) || empty($_FILES[$inputname]))
          return array(
            "message" => $messages["xiu_file"],
            "sent"    => 0
          );

        $image           = $_FILES[$inputname]["name"];
        $uploaded_file   = $_FILES[$inputname]["tmp_name"];

        if (!$image)
          return array(
            "message" => $messages["xiu_file"],
            "sent"    => 0
          );

        $file_name      = stripslashes($image);
        $extension      = $this->xGetExtension($file_name);
        $extension      = strtolower($extension);
        if ($extension != "jpg" && $extension != "jpeg" && $extension != "png" && $extension != "gif")
          return array(
            "message" => $messages["xiu_format"],
            "sent"    => 0
          );

        $size = filesize($uploaded_file);
        if (!$size)
          return array(
            "message" => $messages["xiu_error"],
            "sent"    => 0
          );

        if ($maxsize and $size > $maxsize * 1024)
          return array(
            "message" => $messages["xiu_size"],
            "sent"    => 0
          );

        if ($extension == "jpg" || $extension == "jpeg")
          $src = imagecreatefromjpeg($uploaded_file);
        elseif ($extension == "png")
          $src = imagecreatefrompng($uploaded_file);
        else
          $src = imagecreatefromgif($uploaded_file);
        imagealphablending($src, true);

        list($width, $height) = getimagesize($uploaded_file);

        $new_height = $height;
        $new_width  = $width;

        switch ($force_or_crop)
        {
          case "crop":
            if ($container_width && !$container_height)
            {
              $new_width  = $container_width;
              $new_height = $new_width * ($height / $width);
            }           
            elseif (!$container_width && $container_height)
            {
              $new_height = $container_height;
              $new_width  = $new_height / ($height / $width);
            }
            elseif ($container_width && $container_height)
            {
              $new_width  = $container_width;
              $new_height = $container_height;
            }

            $top  = ($new_height - $height) / 2;
            $left = ($new_width - $width) / 2;

            $tmp = imagecreatetruecolor($new_width, $new_height);
            imagefill($tmp, 0, 0, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopy($tmp, $src, $left, $top, 0, 0, $width, $height);
          break;
          case "force":
            if ($container_width && !$container_height)
            {
              $new_width  = $container_width;
              $new_height = $new_width * ($height / $width);
            }           
            elseif (!$container_width && $container_height)
            {
              $new_height = $container_height;
              $new_width  = $new_height / ($height / $width);
            }
            elseif ($container_width && $container_height)
            {
              $new_width  = $container_width;
              $new_height = $container_height;
            }

            $tmp = imagecreatetruecolor($new_width, $new_height);
            imagecolortransparent($tmp, imagecolorallocatealpha($tmp, 0, 0, 0, 127));
            imagealphablending($tmp, false);
            imagesavealpha($tmp, true);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
          break;
          default:
            if ($container_width && !$container_height && $container_width < $width)
            {
              $new_width  = $container_width;
              $new_height = $new_width * ($height / $width);
            }
            elseif (!$container_width && $container_height && $container_height < $height)
            {
              $new_height = $container_height;
              $new_width  = $new_height / ($height / $width);
            }
            elseif ($container_width && $container_height)
            {
              if ($container_width < $width || $container_height < $height)
              {
                $img_aspect_ratio = $width / $height;
                $ctr_aspect_ratio = $container_width / $container_height;

                if ($ctr_aspect_ratio > 1)
                {
                  if($img_aspect_ratio <= $ctr_aspect_ratio)
                  {
                    $new_height = $container_height;
                    $new_width  = $new_height / ($height / $width);
                  }           
                  else
                  {
                    $new_width  = $container_width;
                    $new_height = $new_width * ($height / $width);
                  }
                }

                if ($ctr_aspect_ratio < 1)
                {
                  if ($img_aspect_ratio <= $ctr_aspect_ratio)
                  {
                    $new_height = $container_height;
                    $new_width  = $new_height / ($height / $width);
                  }           
                  else
                  {                       
                    $new_width  = $container_width;
                    $new_height = $new_width * ($height / $width);
                  }
                }

                if ($ctr_aspect_ratio == 1)
                {
                  if ($width > $height)
                  {
                    $new_width  = $container_width;
                    $new_height = $new_width * ($height / $width);
                  }           
                  else
                  {
                    $new_height = $container_height;
                    $new_width  = $new_height / ($height / $width);
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

        if ($fname)
          $file_name = $fname .".". $extension;
        else
          $file_name = $image;

        if ($extension == "jpg" || $extension == "jpeg")
          imagejpeg($tmp, $location . $file_name, 100);
        elseif ($extension == "png")
          imagepng($tmp, $location . $file_name, 9, PNG_ALL_FILTERS);
        else
          imagegif($tmp, $location . $file_name);

        imagedestroy($src);
        imagedestroy($tmp);

        return array(
          "message" => $messages["xiu_done"],
          "sent"    => 1
        );
    }
    
    protected function xSetMessage($msg = false)
    {
    
        if ($msg)
        {
            
            $_SESSION['xyneomessage'] = $msg;
            
        }
        
        else
        {
            
            $_SESSION['xyneomessage'] = "ERROR";
            
        }
        
    }

}
