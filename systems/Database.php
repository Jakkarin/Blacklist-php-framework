<?php namespace systems;

class Database
{
    /**
     * เชื่อมต่อฐานข้อมูล โดยค้นหา ชนิดฐานข้อมูลก่อน
     * Connect database with dirver name.
     * @param Array $_dbConfig
     * @param String $_dirvers
     */
    public static function connect($_dbConfig = null, $_dirvers = null)
    {
        if (empty($_dbConfig))
            $_dbConfig = Config::get('database');
        $_providers = array(
            'mysql'     => 'systems\\dirvers\\PDOMysql\\builder'
        ); if (empty($_dirvers))
            $_dirvers = $_providers[$_dbConfig['dirvers']];
        $_dirvers::initialize($_dbConfig);
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
