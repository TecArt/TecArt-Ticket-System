<?php

abstract class sqLiteAbstract
{
    public $dbname;
        
    protected $db;

    public function __construct()
    {
        $this->dbname = dirname(dirname(dirname(__FILE__))).'/data/sqlite/db.sqlite';
    }

    public function check_tables()
    {
    }

    public function get_data($table)
    {
    }

    public function insert_users($values)
    {
    }

    public function update_timestamp($insert = false)
    {
    }

    public function insert_list($name, $crm_lists)
    {
    }

    public function get_contact($cid)
    {
    }

    public function insert_contact($values)
    {
    }

    public function update_contact($values)
    {
    }
        
    private function create_table($table)
    {
    }
}
