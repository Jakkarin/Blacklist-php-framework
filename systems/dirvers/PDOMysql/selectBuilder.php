<?php namespace systems\dirvers\PDOMysql;

class selectBuilder
{
    private $connection;
    private $queryString;

    public function __construct($_queryString)
    {
        $this->connection = &$GLOBALS['PDOApp'];
        $this->queryString = $_queryString;
    }

    public function raw($_queryString)
    {
        $this->queryString = $_queryString . ' ';
        return $this;
    }

    public function distinct()
    {
        $this->queryString = str_replace('SELECT', 'SELECT DISTINCT', $this->queryString);
        return $this;
    }

    public function where($_field, $_operator, $_value)
    {
        $_queryString = "WHERE `$_field`$_operator'$_value' ";
        $this->queryString .= $_queryString;
        return $this;
    }

    public function orWhere($_field, $_operator, $_value)
    {
        $_queryString = "OR `$_field`$_operator'$_value' ";
        $this->queryString .= $_queryString;
        return $this;
    }

    public function andWhere($_field, $_operator, $_value)
    {
        $_queryString = "AND `$_field`$_operator'$_value' ";
        $this->queryString .= $_queryString;
        return $this;
    }

    public function limit($_numeral)
    {
        $this->queryString .= 'LIMIT ' . $_numeral . ' ';
        return $this;
    }

    public function orderBy($_column, $_keyword = 'DESC')
    {
        $_columnType = getType($_column);
        if ($_columnType === 'array') {
            $_queryString = 'ORDER BY ';
            foreach ($_column as $_key => $_value)
                $_queryString .= "$_key $_value,";
            $_queryString = rtrim($_queryString, ',');
            $this->queryString .= $_queryString;
        } else $this->queryString .= 'ORDER BY ' . $_queryString . ' ' . $_keyword . ' ';
        return $this;
    }

    public function get($_returnObject = false)
    {
        $_query = $this->connection->prepare($this->queryString);
        $_query->execute();
        if ($_returnObject)
            return $_query->fetchAll(\PDO::FETCH_CLASS, 'pdoData');
        else return $_query->fetchAll(\PDO::FETCH_ASSOC);
    }
}
