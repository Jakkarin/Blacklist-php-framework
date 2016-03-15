<?php namespace systems;

class Database
{
    /**
     * เชื่อมต่อฐานข้อมูล โดยค้นหา ชนิดฐานข้อมูลก่อน
     * Connect database with dirver name.
     * @param Array $dbConfig
     * @param String $dirver
     */
    public static function connect($dbConfig = null, $dirver = null)
    {
        if (empty($dbConfig))
            $dbConfig = Config::get('database');
        $drivers = array(
            'mysql'     => 'systems\\dirvers\\PDOMysql\\Builder'
        ); if (empty($dirver))
            $dirver = $drivers[$dbConfig['Dirver']];
        $dirver::initialize($dbConfig);
    }

    /**
     * ปิดการเชื่อมต่อฐานข้อมูล
     * Close database connection.
     */
    public static function close()
    {
        $GLOBALS['PDOApp'] = null;
    }
}
