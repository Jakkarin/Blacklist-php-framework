<?php namespace systems;

class Route
{
    public static function setDomain($_domain)
    {
        return ($GLOBALS['routeDomain'] = APP_PATH . 'caches/routes/' . $_domain);
    }

    public static function pattern($_pn, $_pattern)
    {
        return ($GLOBALS['routePattern'][$_pn] = '(' . $_pattern . ')');
    }

    public static function middleware($_middleware, $_callback)
    {

    }

    public static function group($_groupName, $_callback, $_middleware = null)
    {

    }

    public static function controller($_uri, $_controller)
    {
        if ($_uri === '')
            return self::write('[Controller]/\[index\]/:' . $_controller);
        $_uri = str_replace(' ', '', $_uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $_uri, $_match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($_match as $_v)
                $_uri = str_replace($_v[0], $patterns[$_v[1]], $_uri);
            $_uri = str_replace('/', '\/', $_uri);
        } return self::write('[Controller]/^' . $_uri . '\/?(.+)?/:' . $_controller);
    }

    public static function get($_uri, $_controller)
    {
        $_controller = str_replace(' ', '', $_controller);
        if ($_uri === '')
            return self::write('[GET]/\[index\]/:' . $_controller);
        $_uri = str_replace(' ', '', $_uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $_uri, $_match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($_match as $_v)
                $_uri = str_replace($_v[0], $patterns[$_v[1]], $_uri);
            $_uri = str_replace('/', '\/', $_uri);
        } return self::write('[GET]/^' . $_uri . '$/:' . $_controller);
    }

    public static function post($_uri, $_controller)
    {
        $_controller = str_replace(' ', '', $_controller);
        if ($_uri === '')
            return self::write('[POST]/\[index\]/:' . $_controller);
        $_uri = str_replace(' ', '', $_uri);
        if (preg_match_all('/\{([A-z0-9]+)\}/', $_uri, $_match, PREG_SET_ORDER)) {
            $patterns = $GLOBALS['routePattern'];
            foreach ($_match as $_v)
                $_uri = str_replace($_v[0], $patterns[$_v[1]], $_uri);
            $_uri = str_replace('/', '\/', $_uri);
        } return self::write('[POST]/^' . $_uri . '$/:' . $_controller);
    }

    public static function domain($_domain, $_callback, $_newPatterns = false)
    {
        self::setModTime();
        $GLOBALS['routeDomain'] = APP_PATH . 'caches/routes/' . $_domain;
        if ($_newPatterns)
            $GLOBALS['routePattern'] = array();
        return call_user_func($_callback);
    }

    private static function write($_data)
    {
        return file_put_contents($GLOBALS['routeDomain'], $_data . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function setModTime()
    {
        return touch($GLOBALS['routeDomain'], $GLOBALS['routeModTime']);
    }
}
