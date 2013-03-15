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
    
    protected function xImageUpload($inputname,$location,$fname=false,$maxsize=false,$container_width=false,$container_height=false,$force_to_fit=false,$msg=false){
        
        if($msg)
            $messages = $msg;
        else
            $messages = array(
                'xiu_format' => 'Invalid file format!',
                'xiu_size'   => 'The file size is over the limit!',
                'xiu_file'   => 'No file was selected!',
                'xiu_error'  => 'Something went wrong!',
                'xiu_done'   => 'File uploaded!'
            );
        
        if(!$_SERVER["REQUEST_METHOD"] == "POST") 
            return array(
                'message' => $messages['xiu_error'],
                'sent'    => 0
            );
        
        if(!isset($_FILES[$inputname]) or empty($_FILES[$inputname]))
            return array(
                'message' => $messages['xiu_file'],
                'sent'    => 0
            );
        
        $image          = $_FILES[$inputname]["name"];
        $uploadedfile   = $_FILES[$inputname]['tmp_name'];

        if (!$image) 
            return array(
                'message' => $messages['xiu_file'],
                'sent'    => 0
            );

        $filename       = stripslashes($image);
        $extension      = $this -> xGetExtension($filename);
        $extension      = strtolower($extension);
        if (($extension != "jpg") and ($extension != "jpeg") and ($extension != "png") and ($extension != "gif"))
            return array(
                'message' => $messages['xiu_format'],
                'sent'    => 0
            );
        
        $size=filesize($uploadedfile);
        if(!$size)
            return array(
                'message' => $messages['xiu_error'],
                'sent'    => 0
            );
        
        
        
        if ($maxsize and $size > $maxsize*1024)
            return array(
                'message' => $messages['xiu_size'],
                'sent'    => 0
            );

        if($extension=="jpg" || $extension=="jpeg" )
            $src = imagecreatefromjpeg($uploadedfile);
        else if($extension=="png")
            $src = imagecreatefrompng($uploadedfile);
        else 
            $src = imagecreatefromgif($uploadedfile);

        list($width,$height)=getimagesize($uploadedfile);
        
        $newheight = $height;
        $newwidth  = $width;
        
        if($container_width and !$container_height and $container_width < $width and !$force_to_fit){
            $newwidth=$container_width;
            $newheight=$newwidth*($height/$width);
        }
        elseif(!$container_width and $container_height and $container_height < $height and !$force_to_fit){
            $newheight=$container_height;
            $newwidth=$newheight/($height/$width);
        }
        elseif($container_width and $container_height and !$force_to_fit){
            if($container_width < $width or $container_height < $height){
                $img_aspect_ratio = $width/$height;
                $ctr_aspect_ratio = $container_width/$container_height;
                
                if($ctr_aspect_ratio > 1){
                    if($img_aspect_ratio <= $ctr_aspect_ratio){
                        $newheight=$container_height;
                        $newwidth=$newheight/($height/$width);
                    }           
                    else{
                        $newwidth=$container_width;
                        $newheight=$newwidth*($height/$width);
                    }
                }
                
                if($ctr_aspect_ratio < 1){
                    if($img_aspect_ratio <= $ctr_aspect_ratio){
                        $newheight=$container_height;
                        $newwidth=$newheight/($height/$width);
                    }           
                    else{                       
                        $newwidth=$container_width;
                        $newheight=$newwidth*($height/$width);
                    }
                }
                
                if($ctr_aspect_ratio == 1){
                   if($width > $height){
                        $newwidth=$container_width;
                        $newheight=$newwidth*($height/$width);
                    }           
                    else{
                        $newheight=$container_height;
                        $newwidth=$newheight/($height/$width);
                    } 
                }
            }
        }
        
        if($force_to_fit == 1){
            if($container_width and !$container_height){
                $newwidth=$container_width;
                $newheight=$newwidth*($height/$width);
            }           
            elseif(!$container_width and $container_height){
                $newheight=$container_height;
                $newwidth=$newheight/($height/$width);
            }
            elseif($container_width and $container_height){
                $newwidth  = $container_width;
                $newheight = $container_height;
            }
        }
        
        
        $tmp=imagecreatetruecolor($newwidth,$newheight);
        imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
        $width,$height);
        
        if($fname)
            $filename = $fname.".".$extension;
        else
            $filename = $image;
        
        imagejpeg($tmp,$location.$filename,100);

        imagedestroy($src);
        imagedestroy($tmp);
        
        return array(
            'message' => $messages['xiu_done'],
            'sent'    => 1
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
