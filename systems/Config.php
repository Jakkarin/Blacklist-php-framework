<?php namespace systems;

/**
 * systems/Config.php
 * @author Jakkarin Yotapakdee (jakkarinwebmaster@gmail.com)
 */
class Config
{
    public static $configDir = APP_PATH . 'config/';

    /**
     * Get config data
     * @param String $configName
     * @return Array
     */
    public static function get($configName)
    {
        return require self::$configDir . $configName . '.php';
    }

    /**
     * Edit config file
     * @param String $configName
     * @param Array $configArray
     * @return Boolean
     */
    public static function set($configName, $configArray)
    {
        $configPath = self::$configDir . $configName . '.php';
        if (file_exists($configPath)) {
            $configData = file_get_contents($configPath);
            foreach ($configArray as $configKey => $configValue) {
                $regexKey = '/^(.+)(' . $configKey . ')(.+)=>\s(.+)$/';
                if (preg_match($regexKey, $configData, $_match)) {
                    $regexKey = '/' . $_match[3] . '/';
                    if (is_string($configValue))
                        $configValue = '\'' . $configData . '\'';
                    $configData = preg_replace($regexKey ,'', $configData);
                    return file_put_contents($configPath, $configData, LOCK_EX);
                }
            }
        } return false;
    }
}
