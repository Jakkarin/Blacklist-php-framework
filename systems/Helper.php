<?php namespace systems;

class Helper
{
    public static function core()
    {
        require APP_SYSTEM . 'helpers/core.php';
    }

    public static function load($helperName)
    {
        require APP_PATH . 'helpers/' . $helperName . '.php';
    }
}
