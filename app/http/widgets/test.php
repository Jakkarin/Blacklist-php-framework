<?php namespace app\http\widgets;

// use systems\Database;
use systems\Helper;
use systems\Config;
use systems\View;

use app\http\models\main;

class test
{
    public function run()
    {
        foreach (main::getData() as $key => $value) {
            $a = $value;
        }
        return View::make('adm', $a);
    }
}
