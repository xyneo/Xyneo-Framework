<?php
if (! defined('XYNEO')) {
    throw new XyneoError("Direct access denied!");
}

/**
 * This is the Xyneo Framework inbuilt router class
 */
class XyneoRoute
{

    private static $routes = array();

    private static $routes_num = array();

    private static $url_vals = array();

    /**
     * Add route
     *
     * @param string $route            
     * @param string $original            
     * @return void
     */
    public static function add($route, $original)
    {
        self::$routes[$route] = $original;
        self::$routes_num[] = array(
            'route' => $route,
            'original' => $original
        );
    }

    /**
     * Dump routes
     */
    public static function dumpRoutes()
    {
        var_dump(self::$routes);
    }

    /**
     * Parse route
     *
     * @param string $url            
     * @return string
     */
    public static function parseRoute($url)
    {
        if (empty($url)) {
            return $url;
        }
        
        $url = trim($url, '/');
        if (isset(self::$routes[$url])) {
            return self::$routes[$url];
        }
        
        if (preg_match("/\./", $url)) {
            $url = current(explode(".", $url));
        }
        $url_segments = explode('/', $url);
        $match = 0;
        $i = 0;
        $routes = sizeof(self::$routes_num);
        $goto = "";
        $vals = array();
        
        while ($match == 0 and $i < $routes) {
            self::$url_vals = array();
            $item = self::$routes_num[$i];
            if (preg_match("/\./", $item['route'])) {
                $item['route'] = current(explode(".", $item['route']));
            }
            $route_segments = explode('/', $item['route']);
            if (sizeof($url_segments) == sizeof($route_segments)) {
                $match = 1;
                $goto = $item['original'];
                foreach ($url_segments as $key => $value) {
                    if (! self::compareSegments($url_segments[$key], $route_segments[$key])) {
                        $match = 0;
                    }
                }
            }
            $i ++;
        }
        if ($match) {
            foreach (self::$url_vals as $key => $val) {
                $goto = str_replace($val['short'], $val['value'], $goto);
            }
            return $goto;
        }
        
        return $url;
    }

    /**
     * Compare route segments
     *
     * @param string $seg1            
     * @param string $seg2            
     * @return boolean
     */
    private static function compareSegments($seg1, $seg2)
    {
        if ($seg1 == $seg2) {
            return true;
        }
        
        $seg2_len = mb_strlen($seg2, LAYOUT_CHARSET) - 2;
        $seg2_trimlen = mb_strlen(ltrim(rtrim($seg2, '}'), '{'), LAYOUT_CHARSET);
        if ($seg2_len == $seg2_trimlen) {
            self::$url_vals[] = array(
                'value' => $seg1,
                'short' => $seg2
            );
            return true;
        } else {
            return false;
        }
    }
}