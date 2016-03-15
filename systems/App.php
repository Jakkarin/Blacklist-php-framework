<?php namespace systems;

class App
{
    public static $routePath = (APP_PATH . 'routes.php');
    public static $routeCachePath = (APP_PATH . 'caches/' . HTTP_HOST_TOKEN . '.cache');

    public static function run($app)
    {
        $_routePath = self::$routePath;
        $_routeCachePath = self::$routeCachePath;
        $_routeCahceExist = file_exists($_routeCachePath);
        $GLOBALS['routeModTime'] = filemtime($_routePath);
        if ($_routeCahceExist) {
            $_routeCahce = filemtime($_routeCachePath);
            if ($GLOBALS['routeModTime'] !== $_routeCahce)
                if (array_map('unlink', glob(APP_PATH . 'caches/*.cache'))) $_routeCahceExist = false;
        } if ( ! $_routeCahceExist) {
            $GLOBALS['routePattern'] = array();
            $GLOBALS['routeDomain'] = $_routeCachePath;
            require $_routePath;
            Route::setModTime();
            unset($GLOBALS['routePattern'], $GLOBALS['routeDomain']);
        } unset($GLOBALS['routeModTime']);
        return self::parseRoute(self::applyRoute($app));
    }

    private static function parseRoute($_routeParam)
    {
        if (preg_match('/([A-z0-9]+)@?([A-z0-9]+)?\|?(.+)?/', $_routeParam[0], $_match)) {
            $_controllerName = 'app\\http\\controllers\\' . $_match[1];
            $_controller = new $_controllerName;
            if (empty($_routeParam[1]))
                $_routeParam = array();
            else unset($_routeParam[0]);
            if (isset($_match[3])) {
                $_middlewares = explode(',', $_match[3]);
                foreach ($_middlewares as $_middleware) {
                    $_middleware = 'app\\http\\middlewares\\' . $_middleware;
                    call_user_func(array(new $_middleware, 'run'));
                } unset($_middlewares);
            } if (empty($_match[2])) {
                if (empty($_routeParam))
                    $_routeParam[1] = 'index';
                $_routeParam = explode('/', $_routeParam[1]);
                $_method = strtolower($_SERVER['REQUEST_METHOD']) . ucfirst($_routeParam[0]);
                unset($_routeParam[0]);
                if (method_exists($_controller, $_method))
                    return call_user_func_array(array($_controller, $_method), $_routeParam);
            } else if (method_exists($_controller, $_match[2]))
                return call_user_func_array(array($_controller, $_match[2]), $_routeParam);
        } return View::make('errors/404');
    }

    private static function applyRoute($app)
    {
        $_uri = ltrim(rtrim($_SERVER['REQUEST_URI'], '/'), '/');
        $_uri = ltrim(str_replace($app['sub_folder'], '', $_uri), '/');
        if ($_uri === '') $_uri = '[index]';
        $_requestMethod = '/\[(Controller|' . $_SERVER['REQUEST_METHOD'] . ')\](.*)/';
        $_fp = fopen(self::$routeCachePath, 'r');
        while ( ! feof($_fp)) {
            if (preg_match($_requestMethod, fgets($_fp), $_match)) {
                $_route = explode(':', $_match[2]);
                if (preg_match($_route[0], $_uri, $_match)) {
                    if (fclose($_fp)) {
                        $_match[0] = $_route[1];
                        return $_match;
                    }
                }
            }
        }
    }
}
