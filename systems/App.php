<?php namespace systems;

class App
{
    public static $routePath = (APP_PATH . 'routes.php');
    public static $routeCachePath = (APP_PATH . 'caches/' . HTTP_HOST_TOKEN . '.cache');

    public static function run($app)
    {
        $routePath = self::$routePath;
        $routeCachePath = self::$routeCachePath;
        $routeCacheExists = file_exists($routeCachePath);
        $GLOBALS['routeModTime'] = filemtime($routePath);
        if ($routeCacheExists) {
            $routeCache = filemtime($routeCachePath);
            if ($GLOBALS['routeModTime'] !== $routeCache)
                if (array_map('unlink', glob(APP_PATH . 'caches/*.cache'))) $routeCacheExists = false;
        } if ( ! $routeCacheExists) {
            $GLOBALS['routePattern'] = array();
            $GLOBALS['routeDomain'] = $routeCachePath;
            require $routePath;
            Route::setModTime();
            unset($GLOBALS['routePattern'], $GLOBALS['routeDomain']);
        } unset($GLOBALS['routeModTime']);
        return self::parseRoute(self::applyRoute($app));
    }

    private static function parseRoute($routeParam)
    {
        if (preg_match('/([A-z0-9]+)@?([A-z0-9]+)?\|?(.+)?/', $routeParam[0], $match)) {
            $controllerName = 'app\\http\\controllers\\' . $match[1];
            $controllerClass = new $controllerName;
            if (empty($routeParam[1]))
                $routeParam = array();
            else unset($routeParam[0]);
            if (isset($match[3])) {
                $middlewares = explode(',', $match[3]);
                foreach ($middlewares as $middleware) {
                    $middleware = 'app\\http\\middlewares\\' . $middleware;
                    call_user_func(array(new $middleware, 'run'));
                } unset($middlewares);
            } if (empty($match[2])) {
                if (empty($routeParam))
                    $routeParam[1] = 'index';
                $routeParam = explode('/', $routeParam[1]);
                $method = strtolower($_SERVER['REQUEST_METHOD']) . ucfirst($routeParam[0]);
                unset($routeParam[0]);
                if (method_exists($controllerClass, $method))
                    return call_user_func_array(array($controllerClass, $method), $routeParam);
            } else if (method_exists($controllerClass, $match[2]))
                return call_user_func_array(array($controllerClass, $match[2]), $routeParam);
        } return View::make('errors/404');
    }

    private static function applyRoute($app)
    {
        $uri = ltrim(rtrim($_SERVER['REQUEST_URI'], '/'), '/');
        $uri = ltrim(str_replace($app['SubFolder'], '', $uri), '/');
        if ($uri === '') $uri = '[index]';
        $requestMethod = '/\[(Controller|' . $_SERVER['REQUEST_METHOD'] . ')\](.*)/';
        $fp = fopen(self::$routeCachePath, 'r');
        while ( ! feof($fp)) {
            if (preg_match($requestMethod, fgets($fp), $match)) {
                $_route = explode(':', $match[2]);
                if (preg_match($_route[0], $uri, $match)) {
                    if (fclose($fp)) {
                        $match[0] = $_route[1];
                        return $match;
                    }
                }
            }
        }
    }
}
