<?php namespace app\http\models;

use systems\dirvers\PDOMysql\Builder as DB;

class Main
{
    public static function getData()
    {
        return DB::table('test')->select('*')->get();
    }
}
