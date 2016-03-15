<?php namespace systems\dirvers\PDOMysql;

class SelectBuilder
{
    private $table;
    private $connection;
    private $queryString;

    public function __construct($table)
    {
        $this->table = $table;
        $this->connection = &$GLOBALS['PDOApp'];
    }

    public function select($field)
    {
        $table = $this->table;
        if (is_array($table))
            $table = implode('`,`', $table);
        if (is_array($field))
            $field = '`' . implode('`,`', $field) . '`';
        $this->queryString = "SELECT $field FROM `$table` ";
        return $this;
    }

    public function raw($queryString)
    {
        $this->queryString = $queryString . ' ';
        return $this;
    }

    public function withDistinct()
    {
        $this->queryString = str_replace('SELECT', 'SELECT DISTINCT', $this->queryString);
        return $this;
    }

    public function where($field, $operator, $value)
    {
        $queryString = "WHERE `$field`$operator'$value' ";
        $this->queryString .= $queryString;
        return $this;
    }

    public function orWhere($field, $operator, $value)
    {
        $queryString = "OR `$field`$operator'$value' ";
        $this->queryString .= $queryString;
        return $this;
    }

    public function andWhere($field, $operator, $value)
    {
        $queryString = "AND `$field`$operator'$value' ";
        $this->queryString .= $queryString;
        return $this;
    }

    public function limit($numeral)
    {
        $this->queryString .= 'LIMIT ' . $numeral . ' ';
        return $this;
    }

    public function orderBy($column, $keyword = 'DESC')
    {
        $columnType = getType($column);
        if ($columnType === 'array') {
            $queryString = 'ORDER BY ';
            foreach ($column as $key => $value)
                $queryString .= "$key $value,";
            $queryString = rtrim($queryString, ',');
            $this->queryString .= $queryString;
        } else $this->queryString .= 'ORDER BY ' . $queryString . ' ' . $keyword . ' ';
        return $this;
    }

    public function get($returnObject = false)
    {
        $query = $this->connection->prepare($this->queryString);
        $query->execute();
        if ($returnObject)
            return $query->fetchAll(\PDO::FETCH_CLASS, 'pdoData');
        else return $query->fetchAll(\PDO::FETCH_ASSOC);
    }
}
