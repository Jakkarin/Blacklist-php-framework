<?php namespace app\http\models;

use systems\dirvers\PDOMysql\builder as DB;

class main
{
    public static function getData()
    {
        return DB::select('test','*')->get();
    }
}
