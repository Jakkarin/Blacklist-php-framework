<?php namespace systems;

class Helper
{
    public static function core()
    {
        require APP_SYSTEM . 'helpers/core.php';
    }

    public static function load($_hName)
    {
        require APP_PATH . 'helpers/' . $_hName . '.php';
    }
}
