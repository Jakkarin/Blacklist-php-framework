<?php namespace app\http\controllers;

use systems\Database;
use systems\Helper;
use systems\Config;
use systems\View;

use app\http\models\Main;

class MainController
{
    public function index()
    {
        Database::connect();
        var_dump(Main::getData());
        return View::make('index');
    }

    public function getindex()
    {
        Database::connect();
        var_dump(Main::getData());
        return View::make('index');
    }
}
