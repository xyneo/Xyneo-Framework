<?php
if (! defined("XYNEO")) {
    die("Direct access denied!");
}

class Index_Model extends XyneoModel
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Minify css and js files
     *
     * @param string $layout
     * @param string $param
     * @return void
     */
    public function clientSide($layout, $param)
    {
        $host = URL != "http://mydomain.com/" ? URL : "http" . (isset($_SERVER["HTTPS"]) ? "s" : "") . "://" . $_SERVER["HTTP_HOST"] . "/";
        if (! $layout) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
        if (! $param) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
        if (! preg_match("/\-/", $param)) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
        $type = end(explode("-", $param));
        $file = str_replace("-", "/", $param);
        $file = str_replace("/" . $type, "." . $type, $file);
        
        $minify = false;
        if (preg_match("/(min\." . $type . ")/", $file)) {
            $minify = true;
        }
        $cache = "temp/" . $param . "." . $type;
        if (! file_exists($file) && ! $minify) {
            header("HTTP/1.0 404 Not Found");
            exit();
        }
        if (! $minify) {
            $header = @get_headers($host . (! preg_match("/(^public)/", $file) ? "public/" . $file : $file), 1);
            $output = file_get_contents($file);
            $skip = false;
            if (file_exists($cache)) {
                if (filemtime($file) <= filemtime($cache)) {
                    $output = file_get_contents($cache);
                    $skip = true;
                }
            }
        } else {
            $auto = unserialize(AUTO);
            $modified = false;
            $output = "";
            $header = false;
            foreach ($auto[$layout][$type] as $key => $file) {
                if (! $header) {
                    $header = @get_headers($host . $file, 1);
                }
                $output .= file_get_contents($file) . "\n";
                if (file_exists($cache)) {
                    if (filemtime($file) > filemtime($cache)) {
                        $modified = true;
                    }
                } else {
                    $modified = true;
                }
            }
            $skip = false;
            if (! $modified && file_exists($cache)) {
                $output = file_get_contents($cache);
                $skip = true;
            }
        }
        
        switch ($type) {
            case "js":
                if ($header["Content-Type"] != "application/javascript" && $header["Content-Type"] != "text/javascript") {
                    header("HTTP/1.0 404 Not Found");
                    exit();
                }
                
                if (! $skip) {
                    require_once "myapp/third_party/Compressor/JSMin.php";
                    $output = JSMin::minify($output);
                    $output = trim(str_replace(array(
                        "\r\n",
                        "\n"
                    ), " ", $output), " ");
                    file_put_contents($cache, $output);
                }
                break;
            case "css":
                if ($header["Content-Type"] != "text/css") {
                    header("HTTP/1.0 404 Not Found");
                    exit();
                }
                
                if (! $skip) {
                    require_once "myapp/third_party/Compressor/CssMin.php";
                    $output = str_ireplace("\"", "", CssMin::minify($output));
                    file_put_contents($cache, $output);
                }
                break;
        }
        
        ob_start("ob_gzhandler");
        header("Content-Type: " . $header["Content-Type"] . "; charset=utf-8");
        header("Expires: " . date("r", strtotime("+1 week", filemtime($cache))));
        header("Last-Modified: " . date("r", strtotime("+1 week", filemtime($cache))));
        header("Cache-Control: store, cache");
        header("Pragma: cache");
        echo $output;
        ob_end_flush();
        exit();
    }
}
