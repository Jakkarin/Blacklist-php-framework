<?php namespace systems;

class Route
{
    public static function pattern($patternName, $pattern)
    {
        return ($GLOBALS['routePattern'][$patternName] = '(' . $pattern . ')');
    }

    public static function middleware($middleware, $callback)
    {

    }

    public static function group($groupName, $callback, $middleware = null)
    {

    }

    public static function controller($uri, $controller)
    {
        if ($uri === '')
            return self::write('[Controller]/\[index\]/:' . $controller);
        $uri = str_replace(' ', '', $uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $uri, $match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($match as $v)
                $uri = str_replace($v[0], $patterns[$v[1]], $uri);
            $uri = str_replace('/', '\/', $uri);
        } return self::write('[Controller]/^' . $uri . '\/?(.+)?/:' . $controller);
    }

    public static function get($uri, $controller)
    {
        $controller = str_replace(' ', '', $controller);
        if ($uri === '')
            return self::write('[GET]/\[index\]/:' . $controller);
        $uri = str_replace(' ', '', $uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $uri, $match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($match as $v)
                $uri = str_replace($v[0], $patterns[$v[1]], $uri);
            $uri = str_replace('/', '\/', $uri);
        } return self::write('[GET]/^' . $uri . '$/:' . $controller);
    }

    public static function post($uri, $controller)
    {
        $controller = str_replace(' ', '', $controller);
        if ($uri === '')
            return self::write('[POST]/\[index\]/:' . $controller);
        $uri = str_replace(' ', '', $uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $uri, $match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($match as $v)
                $uri = str_replace($v[0], $patterns[$v[1]], $uri);
            $uri = str_replace('/', '\/', $uri);
        } return self::write('[POST]/^' . $uri . '$/:' . $controller);
    }

    public static function domain($domain, $callback, $newPatterns = false)
    {
        self::setModTime();
        $GLOBALS['routeDomain'] = APP_PATH . 'caches/' . md5($domain) . '.cache';
        if ($newPatterns)
            $GLOBALS['routePattern'] = array();
        return call_user_func($callback);
    }

    private static function write($data)
    {
        return file_put_contents($GLOBALS['routeDomain'], $data . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function setModTime()
    {
        return touch($GLOBALS['routeDomain'], $GLOBALS['routeModTime']);
    }
}
