<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * This is the base view class
 */
class XyneoView
{

    /**
     * The private url is set by the constructor.
     * It detects the base URL or uses the
     * URL given in the config.php file
     *
     * @var string
     */
    private $url;

    private $uri = 'public/clientside/';

    /**
     * Detecting the base URL
     *
     * @return void
     */
    public function __construct()
    {
        if (URL == 'http://mydomain.com/') {
            $this->url = XyneoApplication::getRootUrl();
        } else {
            $this->url = URL;
        }
        return;
    }

    /**
     * Rendering the page
     *
     * @param string $name
     * @param string $layout
     * @return void boolean
     */
    public function xRender($name, $layout = false)
    {
        $cont = XyneoApplication::getControllers();
        $cont = $cont['created_controller'];

        if ($cont == "xyneopanel") {
            if (file_exists('xyneo/xyneo_panel/' . $cont . '_view.xyneo')) {
                require_once 'xyneo/xyneo_panel/' . $cont . '_view.xyneo';
            } else {
                die('The ' . $cont . ' view that you are trying to render is
                    missing.');
            }
            return true;
        }

        XyneoApplication::setLayout($layout);

        if ($layout) {
            if (file_exists('myapp/layouts/' . $layout . '/layout_top.xyneo')) {
                require 'myapp/layouts/' . $layout . '/layout_top.xyneo';
            } else {
                die('A top layout element is missing. Check the
                    layout_top.xyneo in ' . $layout . '.');
            }

            if (file_exists('myapp/views/' . $name . '.xyneo')) {
                require_once 'myapp/views/' . $name . '.xyneo';
            } else {
                die('The ' . $name . ' view that you are trying to render is
                    missing.');
            }

            switch (DEVELOPER_MODE) {
                case 'on':
                    $filename = XYNEO_DIR . 'xyneo/xyneo_panel/developer.xyneo';
                    if (file_exists($filename)) {
                        require_once $filename;
                    }
                    break;
                case 'off':
                    break;
                default:
                    die('Bad value for DEVELOPER_MODE. Please check your
                            config file!');
                    break;
            }

            if (file_exists('myapp/layouts/' . $layout . '/layout_bottom.xyneo')) {
                require 'myapp/layouts/' . $layout . '/layout_bottom.xyneo';
            } else {
                die('A bottom layout element is missing. Check the
                    layout_bottom.xyneo in ' . $layout . '.');
            }
        } else {
            if (file_exists('myapp/views/' . $name . '.xyneo')) {
                require_once 'myapp/views/' . $name . '.xyneo';
            } else {
                die('The ' . $name . ' view that you are trying to render is
                    missing.');
            }

            switch (DEVELOPER_MODE) {
                case 'on':
                    $filename = XYNEO_DIR . 'xyneo/xyneo_panel/developer.xyneo';
                    if (file_exists($filename)) {
                        require_once $filename;
                    }
                    break;
                case 'off':
                    break;
                default:
                    die('Bad value for DEVELOPER_MODE. Please check your
                            config file!');
            }
        }
        return;
    }

    /**
     * Show error message
     *
     * @param string $msg_layout
     * @return void
     */
    protected function xShowMessage($msg_layout = false)
    {
        if (isset($_SESSION['xyneomessage'])) {
            if ($msg_layout) {
                $layout = explode('[:showMessage:]', $msg_layout);

                if (sizeof($layout) != 2) {
                    die("The xShowMessage method cannot display this layout!");
                }

                $msg = $layout[0] . $_SESSION['xyneomessage'] . $layout[1];
                echo $msg;
            } else {
                echo $_SESSION['xyneomessage'];
            }

            unset($_SESSION['xyneomessage']);
        }
        return;
    }

    /**
     * Echos the base URL to the view
     *
     * @return void
     */
    protected function xUrl()
    {
        echo $this->url;
        return;
    }

    /**
     * Echos the base javascript path to the view
     *
     * @param boolean $compress
     * @return void
     */
    protected function xJs($compress = true)
    {
        $uri = 'public/javascript/';
        if ($compress)
            $uri = $this->uri . str_replace('/', '-', $uri);
        $uri = '/' . $uri;
        echo $uri;
        return;
    }

    /**
     * Echos the base css path to the view
     *
     * @param boolean $compress
     * @return void
     */
    protected function xCss($compress = true)
    {
        $uri = 'public/stylesheets/';
        if ($compress)
            $uri = $this->uri . str_replace('/', '-', $uri);
        $uri = '/' . $uri;
        echo $uri;
        return;
    }

    /**
     * Echos the base images path to the view
     *
     * @param boolean $output
     * @return void string
     */
    protected function xImages($output = true)
    {
        if ($output)
            echo '/public/images/';
        else
            return '/public/images/';
        return;
    }

    /**
     * Echos the layout javascript path to the view
     *
     * @param boolean $compress
     * @return void
     */
    protected function xLJs($compress = true)
    {
        $uri = 'public/javascript/' . XyneoApplication::getLayout() . '_layout/';
        if ($compress)
            $uri = $this->uri . str_replace('/', '-', $uri);
        $uri = '/' . $uri;
        echo $uri;
        return;
    }

    /**
     * Echos the layout css path to the view
     *
     * @param boolean $compress
     * @return void
     */
    protected function xLCss($compress = true)
    {
        $uri = 'public/stylesheets/' . XyneoApplication::getLayout() . '_layout/';
        if ($compress)
            $uri = $this->uri . str_replace('/', '-', $uri);
        $uri = '/' . $uri;
        echo $uri;
        return;
    }

    /**
     * Echos the layout css path to the view
     *
     * @param boolean $output
     * @return void string
     */
    protected function xLImages($output = true)
    {
        if ($output)
            echo '/public/images/' . XyneoApplication::getLayout() . '_layout/';
        else
            return '/public/images/' . XyneoApplication::getLayout() . '_layout/';
        return;
    }

    /**
     * Returns true if the current page is set to default index page
     *
     * @return boolean
     */
    protected function xIsIndex()
    {
        $controllers = XyneoApplication::getControllers();
        $method = $controllers['called_method'];

        if (class_exists(START_PAGE . "_controller") and $method == 'xyneo') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if the current page is set to default error page
     *
     * @return boolean
     */
    protected function xIsError()
    {
        $controllers = XyneoApplication::getControllers();
        $method = $controllers['called_method'];

        if (class_exists(ERROR_PAGE . "_controller") and $method == 'xyneo') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if the current page is set to default shutdown page
     *
     * @return boolean
     */
    protected function xIsShutdown()
    {
        $controllers = XyneoApplication::getControllers();
        $method = $controllers['called_method'];

        if (class_exists(SHUTDOWN_PAGE . "_controller") and $method == 'xyneo') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Echos the shorten string
     *
     * @param string $str
     * @param integer $length
     * @param string $unit
     * @return void
     */
    protected function xShorten($str, $length, $unit = false)
    {
        if (! $unit) {
            $unit = 'chars';
        }

        switch ($unit) {
            case 'chars':
                if (mb_strlen($str, LAYOUT_CHARSET) > $length) {
                    $str = mb_substr($str, 0, $length, LAYOUT_CHARSET);
                    $str .= '...';
                }
                echo $str;
                break;

            case 'words':
                $words = explode(' ', $str);
                if (sizeof($words) > $length) {
                    $str = implode(' ', array_slice($words, 0, $length));
                    $str .= '...';
                }
                echo $str;
                break;
            default:
                echo 'Invalid value for unit. It must be empty or "chars" or "words"';
                break;
        }
        return;
    }

    /**
     * Echos the full url
     *
     * @return void
     */
    protected function xFullUrl()
    {
        $controllers = XyneoApplication::getControllers();
        $cont = $controllers["created_controller"];
        $method = $controllers["called_method"];
        $values = $controllers["passed_values"];
        echo $this->url . ($this->xIsIndex() ? "" : $cont . ($method == "xyneo" && count($values) == 0 ? "" : "/" . $method . (count($values) == 0 ? "" : "/" . implode("/", $values))));
        return;
    }
}
