<?php namespace systems;

/**
 * systems/Config.php
 * @author Jakkarin Yotapakdee (jakkarinwebmaster@gmail.com)
 */
class Config
{
    /**
     * Get config data
     * @param String $_fn
     * @return Array
     */
    public static function get($_fn)
    {
        $_configPath = APP_PATH . 'config/' . $_fn . '.php';
        // if (file_exists($_configPath))
        return require $_configPath;
    }

    /**
     * Edit config file
     * @param String $_fn
     * @param Array $_configArray
     * @return Boolean
     */
    public static function set($_fn, $_configArray)
    {
        $_configPath = APP_PATH . 'config/' . $_fn . '.php';
        if (file_exists($_configPath)) {
            $_configData = file_get_contents($_configPath);
            foreach ($_configArray as $_configKey => $_configValue) {
                $_regexKey = '/^(.+)(' . $_configKey . ')(.+)=>\s(.+)$/';
                if (preg_match($_regexKey, $_configData, $_match)) {
                    $_regexKey = '/' . $_match[3] . '/';
                    if (is_string($_configValue))
                        $_configValue = '\'' . $_configData . '\'';
                    $_configData = preg_replace($_regexKey ,'', $_configData);
                    return file_put_contents($_configPath, $_configData, LOCK_EX);
                }
            }
        } return false;
    }
}
