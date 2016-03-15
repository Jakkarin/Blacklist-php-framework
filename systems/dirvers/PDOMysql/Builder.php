<?php namespace systems\dirvers\PDOMysql;

class Builder
{
    /**
     * การเชื่อมต่อ ฐานข้อมูล mysql ด้วย class PDO
     * Connect to mysql database with PDO
     * @param Array $config
     */
    public static function initialize($config)
    {
        $pdoString = 'mysql:host=' . $config['Host'] . ';dbname=' . $config['DBName'];
        if ($config['Charset'] !== '') $pdoString .= ';charset=' . $config['Charset'];
        if ($config['Port'] !== '') $pdoString .= ';port=' . $config['Port'];
        try {
            $pdoConnection = new \PDO($pdoString, $config['UserName'], $config['Password'], array(
                \PDO::ATTR_ERRMODE => true, \PDO::ATTR_PERSISTENT => true
            )); $pdoConnection->exec('SET time_zone = "' . $config['TimeZone'] . '"');
        } catch (\PDOException $e) { die('Error!: ' . $e->getMessage()); }
        $GLOBALS['PDOApp'] = $pdoConnection;
    }

    /**
     * Sql ที่กำหนดเอง
     * Custom Sql
     * @param String $queryString
     * @param Boolean $return
     * @return Array or Boolean
     */
    public static function query($queryString, $withReturn = false)
    {
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        $status = $query->execute();
        if ($withReturn)
            return $query->fetchAll(\PDO::FETCH_ASSOC);
        return $status;
    }

    /**
     * Query Builder สำหรับดึงข้อมูล
     * @param String $table
     * @param String $field
     * @return Class SelectBuilder
     */
    public static function table($table)
    {
        return new SelectBuilder($table);
    }

    /**
     * ค้นหาตารางและดึงข้อมูลจากฐานข้อมูล
     * @param String $table
     * @param String $field
     * @return Class SelectBuilder
     */
    public static function getWhere($table, $field, $where, $orderBy = null, $conj = 'AND')
    {
        if (is_array($table))
            $table = implode('`,`', $table);
        if (is_array($field))
            $field = '`' . implode('`,`', $field) . '`';
        if (is_array($where[0])) {
            $whereQuery = '';
            foreach ($where as $values) {
                foreach ($values as $key => $value) {
                    if ($key === 0) $whereQuery .= '`' . $value . '`';
                    elseif ($key === 1) $whereQuery .= $value;
                    else $whereQuery .= '\'' . $value . '\'' . $conj;
                }
            } $whereQuery = rtrim($whereQuery, $conj);
        } else $whereQuery = "`$where[0]`$where[1]'$where[2]'";
        $queryString = "SELECT $field FROM `$table` WHERE $whereQuery";
        if ( ! empty($orderBy))
            $queryString .= " ORDER BY $orderBy";
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        $query->execute();
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function insert($table, $data, $lastInsertId = false)
    {
        $keys = implode('`,`', array_keys($data));
        $values = implode('\',\'', $data);
        $queryString = "INSERT INTO `$table` (`$keys`) VALUES ('$values')";
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        $status = $query->execute();
        if ($lastInsertId)
            return $query->lastInsertId();
        return $status;
    }

    public static function inserts($table, $keys, $datas, $lastInsertId = false)
    {
        $values = '';
        foreach ($datas as $key => $value)
            $values .= '(\'' . implode('\',\'', $data) . '\'),';
        $values = rtrim($values, ',');
        $keys = implode('`,`', array_keys($keys));
        $queryString = "INSERT INTO `$table` (`$keys`) VALUES $values";
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        $status = $query->execute();
        if ($lastInsertId)
            return $query->lastInsertId();
        return $status;
    }

    public static function update($table, $data, $where, $conj = 'AND')
    {
        $setQuery = '';
        foreach ($data as $key => $value)
            $setQuery .= "`$key`='$value',";
        $setQuery = rtrim($setQuery, ',');
        if (is_array($where[0])) {
            $whereQuery = '';
            foreach ($where as $values) {
                foreach ($values as $key => $value) {
                    if ($key === 0) $whereQuery .= '`' . $value . '`';
                    elseif ($key === 1) $whereQuery .= $value;
                    else $whereQuery .= '\'' . $value . '\'' . $conj;
                }
            } $whereQuery = rtrim($whereQuery, $conj);
        } else $whereQuery = "`$where[0]`$where[1]'$where[2]'";
        $queryString = "UPDATE `$table` SET $set WHERE $whereQuery";
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        return $query->execute();
    }

    public static function delete($table, $where, $conj = 'AND')
    {
        $whereType = getType($where[0]);
        if ($whereType === 'array') {
            $whereQuery = '';
            foreach ($where as $values) {
                foreach ($values as $key => $value) {
                    if ($key === 0) $whereQuery .= '`' . $value . '`';
                    elseif ($key === 1) $whereQuery .= $value;
                    else $whereQuery .= '\'' . $value . '\'' . $conj;
                }
            } $whereQuery = rtrim($whereQuery, $conj);
        } else $whereQuery = "`$where[0]`$where[1]'$where[2]'";
        $queryString = "DELETE `$table` WHERE $whereQuery";
        $query = $GLOBALS['PDOApp']->prepare($queryString);
        return $query->execute();
    }
}
