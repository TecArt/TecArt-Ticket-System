<?php

class sqLite extends sqLiteAbstract
{
    public function __construct()
    {
        parent::__construct();
        if (!function_exists('sqlite_open')) {
            log_error('SQLITE does not exists!');
            return false;
        }

        try {
            $this->db = sqlite_open($this->dbname, 0666, $sqliteerror);
        } catch (Exception $e) {
            log_error('SQLITE Open Error : '. $e->getMessage());
        }

        if (!$this->db) {
            log_error('SQLITE Error: sqlite_open '.sqlite_error_string(sqlite_last_error($this->db)));
        }
    }

    public function check_tables()
    {
        $table = 'timestamp';
        if (!is_resource(sqlite_query($this->db, "SELECT * FROM $table"))) {
            $this->create_table($table);
        }

        $table = 'users';
        if (!is_resource(sqlite_query($this->db, "SELECT * FROM $table"))) {
            $this->create_table($table);
        }

        $table = 'ticket_lists';
        if (!is_resource(sqlite_query($this->db, "SELECT * FROM $table"))) {
            $this->create_table($table);
        }

        $table = 'contacts';
        if (!is_resource(sqlite_query($this->db, "SELECT * FROM $table"))) {
            $this->create_table($table);
        }
        
        if (!is_resource(sqlite_query($this->db, "SELECT partner_access FROM $table"))) {
            $this->drop_table($table);
            $this->create_table($table);
        }

        return true;
    }


    public function get_data($table)
    {
        $resource = sqlite_query($this->db, "SELECT * FROM $table");

        if (!$resource) {
            log_error('SQLITE Error: get_data '.sqlite_error_string(sqlite_last_error($this->db)));
            return array();
        }
        
        $result = sqlite_fetch_all($resource, SQLITE_ASSOC);

        if (!$result || !is_array($result)) {
            log_error('SQLITE Error: get_data fetch all '.sqlite_error_string(sqlite_last_error($this->db)));
            return array();
        }

        return $result;
    }

    public function insert_users($values)
    {
        if (!is_array($values) || count($values) == 0) {
            return false;
        }

        $all_users = $this->get_data('users');

        $users = array();
        foreach ($all_users as $value) {
            $users[$value['userid']] = $value['id'];
        }

        foreach ($values as $user) {
            if (isset($users[$user->id])) {
                $id = $users[$user->id];
                $query = "UPDATE users SET name = '$user->name', login = '$user->login' WHERE (id=$id)";
            } else {
                $query = "INSERT INTO users (id, userid, name, login) VALUES (null, '".$user->id."', '".$user->name."', '".$user->login."')";
            }

            if (!sqlite_query($this->db, $query)) {
                log_error('SQLITE Error: insert_users '.sqlite_error_string(sqlite_last_error($this->db)));
            }
        }

        return true;
    }

    public function update_timestamp($insert = false)
    {
        if ($insert == true) {
            $query = "INSERT INTO timestamp (timestamp) VALUES ('".time()."')";
        } else {
            $query = "UPDATE timestamp SET timestamp = '".time()."'";
        }

        if (!sqlite_query($this->db, $query)) {
            log_error('SQLITE Error: update_timestamp '.sqlite_error_string(sqlite_last_error($this->db)));
        }

        return true;
    }

    public function insert_list($name, $crm_lists)
    {
        if (!sqlite_query($this->db, "DELETE FROM ticket_lists WHERE (list='$name')")) {
            return false;
        }

        foreach ($crm_lists as $crm_list) {
            $query = "INSERT INTO ticket_lists (id, list, value, name) VALUES (null, '$name', '$crm_list->id', '$crm_list->name')";

            if (!sqlite_query($this->db, $query)) {
                log_error('SQLITE Error: insert_list '.sqlite_error_string(sqlite_last_error($this->db)));
            }
        }

        return true;
    }


    public function get_contact($cid)
    {
        $resource = sqlite_query($this->db, "SELECT * FROM contacts WHERE cid = '$cid'");

        if (!$resource) {
            log_error('SQLITE Error: get_contact '.sqlite_error_string(sqlite_last_error($this->db)));
            return array();
        }

        $result = sqlite_fetch_array($resource, SQLITE_ASSOC);

        if (!$result || !is_array($result)) {
            // log_error('SQLITE Error: get contact fetch all '.sqlite_error_string(sqlite_last_error($this->db)));
            return array();
        }

        return $result;
    }

    public function insert_contact($values)
    {
        $cid             = $values['cid'];
        $name             = $values['name'];
        $min_ticket_year = $values['min_ticket_year'];
        $max_ticket_year = $values['max_ticket_year'];
        $crm_version     = $values['crm_version'];
        $last_update     = time();
        $partner_access = isset($values['partner_access']) ? $values['partner_access'] : 0;
        $partner_contract = isset($values['partner_contract']) ? $values['partner_contract'] : 0;

        $query = "INSERT INTO contacts (id, cid, name, min_ticket_year, max_ticket_year, crm_version, last_update, partner_access, partner_contract) VALUES 
		                               (null, '$cid', '$name', '$min_ticket_year', '$max_ticket_year', '$crm_version', '$last_update', '$partner_access', '$partner_contract')";

        if (!sqlite_query($this->db, $query)) {
            log_error('SQLITE Error: update_contacts '.sqlite_error_string(sqlite_last_error($this->db)));
        }

        return true;
    }

    public function update_contact($values)
    {
        $cid             = $values['cid'];
        $name             = $values['name'];
        $crm_version     = $values['crm_version'];
        $last_update     = time();
        $partner_access = isset($values['partner_access']) ? $values['partner_access'] : 0;
        $partner_contract = isset($values['partner_contract']) ? $values['partner_contract'] : 0;

        $query = "UPDATE contacts SET name = '$name', crm_version = '$crm_version', last_update = '$last_update', partner_access = '$partner_access', partner_contract = '$partner_contract' 
                  WHERE (cid=$cid)";

        if (!sqlite_query($this->db, $query)) {
            log_error('SQLITE Error: update_contacts '.sqlite_error_string(sqlite_last_error($this->db)));
        }

        return true;
    }
    
    
    private function create_table($table)
    {
        if ($table == 'users') {
            $query = "CREATE TABLE $table(
		            id INTEGER PRIMARY KEY,
		            userid bigint(20) NOT NULL,            
		            name VARCHAR(255),
		            login VARCHAR(255)       
		            )";
        } elseif ($table == 'ticket_lists') {
            $query = "CREATE TABLE $table(
		            id INTEGER PRIMARY KEY,
		            list VARCHAR(255),            
		            value INTEGER(4),
		            name VARCHAR(255)       
		            )";
        } elseif ($table == 'timestamp') {
            $query = "CREATE TABLE $table(
		            timestamp bigint(20) NOT NULL 
		            )";
        } elseif ($table == 'contacts') {
            $query = "CREATE TABLE $table(
		            id INTEGER PRIMARY KEY,
		            cid bigint(20) NOT NULL,            
		            name VARCHAR(255),
		            min_ticket_year INTEGER(4),
		            max_ticket_year INTEGER(4),
		            crm_version VARCHAR(255),
		            last_update bigint(20),
                    partner_access INTEGER(4),
                    partner_contract INTEGER(4)
		            )";
        }

        if (!sqlite_query($this->db, $query)) {
            log_error('SQLITE Error: create_table '.sqlite_error_string(sqlite_last_error($this->db)));
        }

        return true;
    }

    private function drop_table($table)
    {
        $query = "DROP TABLE $table";
            
        if (!sqlite_query($this->db, $query)) {
            log_error('SQLITE Error: drop_table '.sqlite_error_string(sqlite_last_error($this->db)));
        }

        return true;
    }
}
