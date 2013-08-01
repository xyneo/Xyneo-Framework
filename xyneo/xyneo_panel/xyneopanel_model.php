<?php
// @TODO PSR2 rewrite, cleaning
class XyneoPanel_model extends XyneoModel
{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function saveProjectData()
    {
        if (DEVELOPER_MODE == 'off') {
            die("DEVELOPER MODE IS OFF!");
        } elseif(DEVELOPER_MODE != 'on') {
            die("INVALID VALUE FOR DEVELOPER MODE! CHECK YOUR CONFIG FILE!");
        }
        
        if (isset($_POST['name'])) {
            $name = htmlspecialchars($_POST['name']);
            $company_name = htmlspecialchars($_POST['company']);
            $country = htmlspecialchars($_POST['country']);
            $description = htmlspecialchars($_POST['description']);
            $email = htmlspecialchars($_POST['email']);

            $data = json_encode(array(
                "name"          =>  $name,
                "description"   =>  $description,
                "company_name"  =>  $company_name,
                "country"       =>  $country,
                "email"         =>  $email
            ));

            $data;

            $fp = fopen('xyneo_project.json', 'w');
            fwrite($fp, $data);
            fclose($fp);

            echo "Saved";
        }
        
    }
    
    public function refreshLayouts()
    {
        if (DEVELOPER_MODE == 'off') {
            die("DEVELOPER MODE IS OFF!");
        } elseif(DEVELOPER_MODE != 'on') {
            die("INVALID VALUE FOR DEVELOPER MODE! CHECK YOUR CONFIG FILE!");
        }
        
        $result = '<option value="0.-">-without layout-</option>';
        
        if ($layouts = opendir('myapp/layouts')) {
            while (false !== ($entry = readdir($layouts))) {
                if ($entry != "." && $entry != ".." && is_dir('myapp/layouts/'.$entry)) {
                    $result.='<option value="'.$entry.'">'.$entry.'</option>';
                }
            }
            closedir($layouts);
        }
        echo $result; 
    }
    
    public function createController()
    {
        if (DEVELOPER_MODE == 'off') {
            die("DEVELOPER MODE IS OFF!");
        } elseif(DEVELOPER_MODE != 'on') {
            die("INVALID VALUE FOR DEVELOPER MODE! CHECK YOUR CONFIG FILE!");
        }

        if (isset($_POST['controller_name'])) {
            $name        = $_POST['controller_name'];
            $desc        = $_POST['controller_description'];

            if ($_POST['controller_layout'] == '0.-') {
                $layout='';
            } else {
                $layout =', "'.$_POST['controller_layout'].'"';
            }
            
            if (isset($_POST['controller_view'])) {
                $view=1;
            } else {
                $view=0;
            }
            
            if (isset($_POST['controller_model'])) {
                $model=1; 
            } else {
                $model=0;
            }
            
            if (!preg_match('/^[A-Za-z0-9_]+$/',$name)) {
                die('Invalid controller name!');
            }
                    
            if (file_exists("myapp/controllers/".strtolower($name)."_controller.php")) {
                die("This controller already exists!");
            }
            
            if ($view == 1) {
                if (file_exists("myapp/views/".strtolower($name)."/".strtolower($name).".xyneo")) {
                    die("This view already exists!");
                }        
            }

            if ($model==1) {
                if (file_exists("myapp/models/".strtolower($name)."_model.php")) {
                    die("This model already exists!");
                } 
            }


            $ourFileName = "myapp/controllers/".strtolower($name)."_controller.php";
            $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
            $controller_template = '<?php if ( ! defined("XYNEO") ) die("Direct access denied!");';
if(AUTO_COMMENT_PHP_FILES == 'on') $controller_template.='
/*
* Controller name: '.ucfirst($name).'
* Created: '.date('Y-m-d h:i:s',time()).'
* Description: '.$desc.'
*/';
$controller_template.='
class '.ucfirst($name).'_Controller extends XyneoController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function xyneo()
    {';
        if($view == 1){ $controller_template.='
        $this->view->page_title = "'.$name.'";
        $this->view->xRender("'.strtolower($name).'/'.strtolower($name).'"'.$layout.');'; }
        $controller_template.='
    }
}
';

            fwrite($ourFileHandle,$controller_template);
            fclose($ourFileHandle);

            if ($view == 1) {
                if (!is_dir("myapp/views/".strtolower($name))) {
                    mkdir ("myapp/views/".strtolower($name));
                }
                $ourFileName = "myapp/views/".strtolower($name)."/".strtolower($name).".xyneo";
                $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
                $view_template='';
                if (AUTO_COMMENT_XYNEO_FILES == 'on')
                            $view_template='<!--
View name: '.ucfirst($name).'
Created: '.date('Y-m-d h:i:s',time()).'
Description: '.$desc.'
-->
';

        $view_template.='<br /><br /><br /><br />
    <h1 style="color:#b6b6b7;">This is <a href="#" style="color:#2d82b8;text-decoration:none;">'.ucfirst($name).'</a> view.</h1>';
                    fwrite($ourFileHandle,$view_template);
                    fclose($ourFileHandle);
            }

            if($model==1){
                $ourFileName = "myapp/models/".strtolower($name)."_model.php";
                $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                $model_template='<?php if ( ! defined("XYNEO") ) die("Direct access denied!");
';
if(AUTO_COMMENT_PHP_FILES == 'on')$model_template.='/*
* Model name: '.ucfirst($name).'
* Created: '.date('Y-m-d h:i:s',time()).'
* Description: '.$desc.'
*/';
$model_template.='
class '.ucfirst($name).'_Model extends XyneoModel
{
    public function __construct()
    {
        parent::__construct();
    }
}
';

                fwrite($ourFileHandle,$model_template);

                fclose($ourFileHandle);
            }

            echo "Success! Your elements have been created!";
        }
        
    }
    
    public function createLayout()
    {
        
        if(DEVELOPER_MODE == 'off')
            die("DEVELOPER MODE IS OFF!");
        elseif(DEVELOPER_MODE != 'on'){
            die("INVALID VALUE FOR DEVELOPER MODE! CHECK YOUR CONFIG FILE!");
        }
        /*HTML4.01_STRICT, HTML4.01_TRANSITIONAL, HTML4.01_FRAMESET
        * XHTML1.0_STRICT, XHTML1.0_TRANSITIONAL, XHTML1.0_FRAMESET
        * XHTML1.1
        * HTML5
        * FACEBOOK
        */
        $doctypes = array(
            "HTML4.01_STRICT"        => array('type' => 'html', 'dt' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">'),
            "HTML4.01_TRANSITIONAL"  => array('type' => 'html', 'dt' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">'),
            "HTML4.01_FRAMESET"      => array('type' => 'html', 'dt' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">'),

            "XHTML1.0_STRICT"        => array('type' => 'xhtml', 'dt' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'),
            "XHTML1.0_TRANSITIONAL"  => array('type' => 'xhtml', 'dt' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'),
            "XHTML1.0_FRAMESET"      => array('type' => 'xhtml', 'dt' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'),

            "XHTML1.1"               => array('type' => 'xhtml', 'dt' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'),

            "HTML5"                  => array('type' => 'html', 'dt' => '<!DOCTYPE html>')
            );

        if(isset($doctypes[LAYOUT_DOCTYPE])){
            $layout_doctype = $doctypes[LAYOUT_DOCTYPE]['dt'];
            $type = $doctypes[LAYOUT_DOCTYPE]['type'];
            switch($type){
                case 'html' : $layout_language = '<html lang="'.LAYOUT_LANGUAGE.'">'; break;
                case 'xhtml': $layout_language = '<html xmlns="http://www.w3.org/1999/xhtml" lang="'.LAYOUT_LANGUAGE.'" xml:lang="'.LAYOUT_LANGUAGE.'">'; break;
            }
        }
        else{
            $layout_doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            $layout_language = '<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">';
        }

        if(isset($_POST['layout_name'])){
            $name        = $_POST['layout_name'];
            $desc        = $_POST['layout_description'];

            if(isset($_POST['layout_css']))
                $css=1;
            else
                $css=0;

            if(isset($_POST['layout_js']))
                $js=1;
            else
                $js=0; 

            if(!preg_match('/^[A-Za-z0-9_]+$/',$name))
                    die('Invalid layout name!');
            if(file_exists("myapp/layouts/".strtolower($name)."/layout_top.xyneo"))
                    die("This layout already exists!");
            if(file_exists("myapp/layouts/".strtolower($name)."/layout_top.xyneo"))
                    die("This layout already exists!");
            if($css==1 and file_exists("public/stylesheets/".strtolower($name)."_layout/".strtolower($name)."_layout.css"))
                    die("The css file for this layout already exists!");
            if($js==1 and file_exists("public/javascript/".strtolower($name)."_layout/".strtolower($name)."_layout.js"))
                    die("The JavaScript file for this layout already exists!");


            if(!is_dir("myapp/layouts/".strtolower($name)."/layout_top.xyneo"))
                    mkdir ("myapp/layouts/".strtolower($name));
                    $ourFileName = "myapp/layouts/".strtolower($name)."/layout_top.xyneo";
                    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                            $layout_template_top='';
        $layout_template_top=$layout_doctype.'
'.$layout_language;

        if(AUTO_COMMENT_XYNEO_FILES == 'on') $layout_template_top.='
<!--
Layout name: '.ucfirst($name).'
Created: '.date('Y-m-d h:i:s',time()).'
Description: '.$desc.'
-->';

            $layout_template_top.='
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset='.strtolower(LAYOUT_CHARSET).'" />
    <title><?php echo $this->page_title; ?></title>';
                if($css==1)
                    $layout_template_top.= '
    <link rel="stylesheet" type="text/css" href="<?php $this->xLCss() ?>'.strtolower($name).'_layout.css" />';
                if(file_exists("public/javascript/jquery.js") and $js==1)
                    $layout_template_top.= '
    <script type="text/javascript" src="<?php $this->xJs(); ?>jquery.js"></script>';
                if($js==1)
                    $layout_template_top.= '
    <script type="text/javascript" src="<?php $this->xLJs(); ?>'.strtolower($name).'_layout.js"></script>';
        $layout_template_top.='    
  </head>
  <body>
    ';
                    fwrite($ourFileHandle,$layout_template_top);
                    fclose($ourFileHandle);


                    $ourFileName = "myapp/layouts/".strtolower($name)."/layout_bottom.xyneo";
                    $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                            $layout_template_bottom='';
        if(AUTO_COMMENT_XYNEO_FILES == 'on') $layout_template_bottom='<!--
Layout name: '.ucfirst($name).'
Created: '.date('Y-m-d h:i:s',time()).'
Description: '.$desc.'
-->';
        $layout_template_bottom.='

  </body>
</html>';
                    fwrite($ourFileHandle,$layout_template_bottom);
                    fclose($ourFileHandle);

        if($css==1){
            if(!is_dir("public/stylesheets/".strtolower($name)."_layout"))
                        mkdir ("public/stylesheets/".strtolower($name)."_layout");
                        $ourFileName = "public/stylesheets/".strtolower($name)."_layout/".strtolower($name)."_layout.css";
                        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                                $layout_template_css='';
        if(AUTO_COMMENT_CSS_FILES == 'on') $layout_template_css='/*
            * Layout-css name: '.ucfirst($name).'
            * Created: '.date('Y-m-d h:i:s',time()).'
            * Description: '.$desc.'
            */';
        $layout_template_css.='root
{ 
  display: block;
}';
                        fwrite($ourFileHandle,$layout_template_css);
                        fclose($ourFileHandle);
        }

        if($js==1){
            if(!is_dir("public/javascript/".strtolower($name)."_layout"))
                        mkdir ("public/javascript/".strtolower($name)."_layout");
                        $ourFileName = "public/javascript/".strtolower($name)."_layout/".strtolower($name)."_layout.js";
                        $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                                $layout_template_js='';
        if(AUTO_COMMENT_JS_FILES == 'on') $layout_template_js='/*
* Layout-js name: '.ucfirst($name).'
* Created: '.date('Y-m-d h:i:s',time()).'
* Description: '.$desc.'
*/';

            $layout_template_js.='$(document).ready(
  function()
  {
      
  }
);';

                        fwrite($ourFileHandle,$layout_template_js);
                        fclose($ourFileHandle);

        }

        if(!is_dir("public/images/".strtolower($name)."_layout"))
                        mkdir ("public/images/".strtolower($name)."_layout");

        echo "Success! Your elements have been created!";
        }

        
    }
    
    public function createHelper()
    {
    
        if(DEVELOPER_MODE == 'off')
            die("DEVELOPER MODE IS OFF!");
        elseif(DEVELOPER_MODE != 'on'){
            die("INVALID VALUE FOR DEVELOPER MODE! CHECK YOUR CONFIG FILE!");
        }

        if(isset($_POST['helper_name'])){
            $name        = $_POST['helper_name'];
            $desc        = $_POST['helper_description'];

            if(isset($_POST['helper_includes']))
                $includes=1;
            else
                $includes=0;


        if(!preg_match('/^[A-Za-z0-9_]+$/',$name))
                    die('Invalid helper name!');
            if(file_exists("myapp/helpers/".strtolower($name)."_helper.php"))
                    die("This helper already exists!");

        $ourFileName = "myapp/helpers/".strtolower($name)."_helper.php";
                $ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");

                $helper_template='<?php
';
        if(AUTO_COMMENT_PHP_FILES == 'on')$helper_template.='
/*
* Helper name: '.ucfirst($name).'
* Created: '.date('Y-m-d h:i:s',time()).'
* Description: '.$desc.'
*/
';
        $helper_template.='class '.ucfirst($name).'_Helper extends XyneoHelper
{
    public function __construct()
    {
        parent::__construct();
    }
}
';

                fwrite($ourFileHandle,$helper_template);
                fclose($ourFileHandle);

        if($includes == 1){
        $ourFileName = "myapp/config/includes.php";
                $ourFileHandle = fopen($ourFileName, 'a') or die("can't open file");

                $include_template='
        require_once "myapp/helpers/'.strtolower($name).'_helper.php";';

                fwrite($ourFileHandle,$include_template);
                fclose($ourFileHandle);
        }

            echo "Success! Your elements have been created!";


        }
        
    }
    
}
