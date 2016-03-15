<?php namespace systems\dirvers\PDOMysql;

class builder
{
    /**
     * การเชื่อมต่อ ฐานข้อมูล mysql ด้วย class PDO
     * Connect to mysql database with PDO
     * @param Array $config
     */
    public static function initialize($config)
    {
        $_pdoString = 'mysql:host=' . $config['dbhost'] . ';dbname=' . $config['dbname'];
        if ($config['charset'] !== '') $_pdoString .= ';charset=' . $config['charset'];
        if ($config['port'] !== '') $_pdoString .= ';port=' . $config['port'];
        try {
            $_pdoConnection = new \PDO($_pdoString, $config['username'], $config['password'], array(
                \PDO::ATTR_ERRMODE => true, \PDO::ATTR_PERSISTENT => true
            ));
        } catch (\PDOException $e) { die('Error!: ' . $e->getMessage()); }
        $GLOBALS['PDOApp'] = $_pdoConnection;
    }

    /**
     * Sql ที่กำหนดเอง
     * Custom Sql
     * @param String $_queryString
     * @param Boolean $_return
     * @return Array or Boolean
     */
    public static function query($_queryString, $_return = false)
    {
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        $_status = $_query->execute();
        if ($_return)
            return $_query->fetchAll(\PDO::FETCH_ASSOC);
        return $_status;
    }

    /**
     * Query Builder สำหรับดึงข้อมูล
     * @param String $_table
     * @param String $_field
     * @return Class SelectBuilder
     */
    public static function select($_table, $_field)
    {
        if (is_array($_table))
            $_table = implode('`,`', $_table);
        if (is_array($_field))
            $_field = '`' . implode('`,`', $_field) . '`';
        $_queryString = "SELECT $_field FROM `$_table` ";
        return new selectBuilder($_queryString);
    }

    /**
     * ค้นหาตารางและดึงข้อมูลจากฐานข้อมูล
     * @param String $_table
     * @param String $_field
     * @return Class SelectBuilder
     */
    public static function getWhere($_table, $_field, $_where, $_orderBy = null, $_conj = 'AND')
    {
        if (is_array($_table))
            $_table = implode('`,`', $_table);
        if (is_array($_field))
            $_field = '`' . implode('`,`', $_field) . '`';
        if (is_array($_where[0])) {
            $_whereQuery = '';
            foreach ($_where as $_values) {
                foreach ($_values as $_key => $_value) {
                    if ($_key === 0) $_whereQuery .= '`' . $_value . '`';
                    elseif ($_key === 1) $_whereQuery .= $_value;
                    else $_whereQuery .= '\'' . $value . '\'' . $_conj;
                }
            } $_whereQuery = rtrim($_whereQuery, $_conj);
        } else $_whereQuery = "`$_where[0]`$_where[1]'$_where[2]'";
        $_queryString = "SELECT $_field FROM `$_table` WHERE $_whereQuery";
        if ( ! empty($_orderBy))
            $_queryString .= " ORDER BY $_orderBy";
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        $_query->execute();
        return $_query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function insert($_table, $_data, $_lastInsertId = false)
    {
        $_keys = implode('`,`', array_keys($_data));
        $_values = implode('\',\'', $_data);
        $_queryString = "INSERT INTO `$_table` (`$_keys`) VALUES ('$_values')";
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        $_status = $_query->execute();
        if ($_lastInsertId)
            return $_query->lastInsertId();
        return $_status;
    }

    public static function inserts($_table, $_keys, $_datas, $_lastInsertId = false)
    {
        $_values = '';
        foreach ($_datas as $key => $value)
            $_values .= '(\'' . implode('\',\'', $_data) . '\'),';
        $_values = rtrim($_values, ',');
        $_keys = implode('`,`', array_keys($_keys));
        $_queryString = "INSERT INTO `$_table` (`$_keys`) VALUES $_values";
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        $_status = $_query->execute();
        if ($_lastInsertId)
            return $_query->lastInsertId();
        return $_status;
    }

    public static function update($_table, $_data, $_where, $_conj = 'AND')
    {
        $_setQuery = '';
        foreach ($_data as $key => $value)
            $_setQuery .= "`$key`='$value',";
        $_setQuery = rtrim($_setQuery, ',');
        if (is_array($_where[0])) {
            $_whereQuery = '';
            foreach ($_where as $_values) {
                foreach ($_values as $_key => $_value) {
                    if ($_key === 0) $_whereQuery .= '`' . $_value . '`';
                    elseif ($_key === 1) $_whereQuery .= $_value;
                    else $_whereQuery .= '\'' . $value . '\'' . $_conj;
                }
            } $_whereQuery = rtrim($_whereQuery, $_conj);
        } else $_whereQuery = "`$_where[0]`$_where[1]'$_where[2]'";
        $_queryString = "UPDATE `$_table` SET $_set WHERE $_whereQuery";
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        return $_query->execute();
    }

    public static function delete($_table, $_where, $_conj = 'AND')
    {
        $_whereType = getType($_where[0]);
        if ($_whereType === 'array') {
            $_whereQuery = '';
            foreach ($_where as $_values) {
                foreach ($_values as $_key => $_value) {
                    if ($_key === 0) $_whereQuery .= '`' . $_value . '`';
                    elseif ($_key === 1) $_whereQuery .= $_value;
                    else $_whereQuery .= '\'' . $value . '\'' . $_conj;
                }
            } $_whereQuery = rtrim($_whereQuery, $_conj);
        } else $_whereQuery = "`$_where[0]`$_where[1]'$_where[2]'";
        $_queryString = "DELETE `$_table` WHERE $_whereQuery";
        $_query = $GLOBALS['PDOApp']->prepare($_queryString);
        return $_query->execute();
    }
}
