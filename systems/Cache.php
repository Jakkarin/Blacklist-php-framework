<?php namespace systems;

/**
 * systems/Cache.php
 * @author Jakkarin Yotapakdee (jakkarinwebmaster@gmail.com)
 */
class Cache
{
    /**
     * Get cache data form file cache.
     * @param String $_cacheName
     * @return Array or Object
     */
    public static function get($_cacheName)
    {
        $_cahcePath = APP_PATH . 'app/caches/data/' . md5($_cacheName);
        return unserialize(file_get_contents($_cahcePath));
    }

    /**
     * Set data to cahce file.
     * @param String $_cacheName
     * @param Array or Object $_data
     * @return Boolean
     */
    public static function set($_cacheName, $_data)
    {
        $_cahcePath = APP_PATH . 'app/caches/data/' . md5($_cacheName);
        return file_put_contents($_cahcePath, serialize($_data), LOCK_EX);
    }

    /**
     * Set and Get cache file with timeout.
     * @param String $_cacheName
     * @param function $_callback
     * @param Integer $_timeOut
     * @return Array or Object
     */
    public static function remember($_cacheName, $_callback, $_timeOut = 0)
    {
        $_cahcePath = APP_PATH . 'app/caches/data/' . md5($_cacheName);
        if ( ! file_exists($_cahcePath)) {
            if ($_timeOut > 0)
                $_timeOut = time() + $_timeOut;
            $_cacheData = call_user_func($_callback);
            if (file_put_contents($_cahcePath, serialize($_cacheData), LOCK_EX))
                touch($_cahcePath, $_timeOut);
            return $_cacheData;
        } $_cacheTime = filemtime($_cahcePath);
        if ($_cacheTime != 0 && time() > $_cacheTime) {
            $_cacheData = call_user_func($_callback);
            if (file_put_contents($_cahcePath, serialize($_cacheData), , LOCK_EX))
                touch($_cahcePath, time() + $_timeOut);
            return $_cacheData;
        } $_cacheData = file_get_contents($_cahcePath);
        return unserialize($_cacheData);
    }

    /**
     * Delete cache file.
     * @param String $_cacheName
     * @return Boolean
     */
    public static function forget($_cacheName)
    {
        return unlink(APP_PATH . 'app/caches/data/' . md5($_cacheName));
    }

    /**
     * Flush cache.
     * @return Boolean
     */
    public static function clear()
    {
        $_cachePath = APP_PATH . 'app/caches/data/*';
        $_cacheFiles = glob($_cachePath);
        foreach ($_cacheFiles as $_file)
            unlink($_file);
        return (count($_cachePath) === 0);
    }
}
