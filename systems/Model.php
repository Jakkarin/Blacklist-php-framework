<?php namespace systems;

class Model
{
    public $db;

    public function __construct()
    {
        $this->db = Database::getQueryBuilder();
    }
}
