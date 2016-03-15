<?php namespace systems;

/**
 * systems/Cache.php
 * @author Jakkarin Yotapakdee (jakkarinwebmaster@gmail.com)
 */
class Cache
{
    private static $cacheDir = APP_PATH . 'app/caches/data/';
    /**
     * Get cache data form file cache.
     * @param String $cacheName
     * @return Array or Object
     */
    public static function get($cacheName)
    {
        return unserialize(file_get_contents(self::$cacheDir . md5($cacheName) . '.cache'));
    }

    /**
     * Set data to cahce file.
     * @param String $cacheName
     * @param Array or Object $_data
     * @return Boolean
     */
    public static function set($cacheName, $_data)
    {
        return file_put_contents(self::$cacheDir . md5($cacheName) . '.cache', serialize($_data), LOCK_EX);
    }

    /**
     * Set and Get cache file with timeout.
     * @param String $cacheName
     * @param function $callBack
     * @param Integer $timeOut
     * @return Array or Object
     */
    public static function remember($cacheName, $callBack, $timeOut = 0)
    {
        $cachePath = self::$cacheDir . md5($cacheName) . '.cache';
        if ( ! file_exists($cachePath)) {
            if ($timeOut > 0)
                $timeOut = time() + $timeOut;
            $cacheData = call_user_func($callBack);
            if (file_put_contents($cachePath, serialize($cacheData), LOCK_EX))
                touch($cachePath, $timeOut);
            return $cacheData;
        } $cacheTime = filemtime($cachePath);
        if ($cacheTime != 0 && time() > $cacheTime) {
            $cacheData = call_user_func($callBack);
            if (file_put_contents($cachePath, serialize($cacheData), , LOCK_EX))
                touch($cachePath, time() + $timeOut);
            return $cacheData;
        } $cacheData = file_get_contents($cachePath);
        return unserialize($cacheData);
    }

    /**
     * Delete cache file.
     * @param String $cacheName
     * @return Boolean
     */
    public static function forget($cacheName)
    {
        return unlink(self::$cacheDir . md5($cacheName) . '.cache');
    }

    /**
     * Flush cache.
     * @return Boolean
     */
    public static function clear()
    {
        $cachePath = self::$cacheDir . '*';
        $cacheFiles = glob($cachePath);
        foreach ($cacheFiles as $file)
            unlink($file);
        return (count($cachePath) === 0);
    }
}
