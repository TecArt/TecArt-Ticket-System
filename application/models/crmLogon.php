<?php

class crmLogon
{
    private $session_id;
    private $client;

    public function __construct($session_id = null, $config)
    {
        if ($session_id != null) {
            $this->session_id = $session_id;
        }

        if (!isset($this->client)) {
            try {
                $this->client  = new SOAPClient($config['webservice_url']."soap/index.php?op=logon&wsdl");
            } catch (Exception $e) {
                log_error('SOAP Connection to CRM-logon error : '. $e->getMessage());
            }
        }
    }

    /**
     * get all users from crm system
     *
     * @return array
     */
    public function get_users()
    {
        try {
            $return = $this->client->crmgetUsers($this->session_id);
        } catch (Exception $e) {
            log_error('crmgetUsers error : '. $e->getMessage());
        }

        return $return;
    }

    public function get_changed_users($timestamp)
    {
        try {
            $return = $this->client->crmgetChangedUsers($this->session_id, $timestamp);
        } catch (Exception $e) {
            log_error('crmgetChangedUsers error : '. $e->getMessage());
        }

        return $return;
    }

    public function get_crm_version()
    {
        try {
            $return = $this->client->crmgetVersion($this->session_id);
        } catch (Exception $e) {
            log_error('crmgetVersion error : '. $e->getMessage());
        }

        return $return;
    }

    /**
     * get defined fields from crm
     *
     * @param string $name
     * @return array
     */
    public function get_ticket_lists($name)
    {
        try {
            $return = $this->client->crmgetList($this->session_id, $name);
        } catch (Exception $e) {
            log_error('crmgetList error : '. $e->getMessage());
        }
        
        return $return;
    }

    public function get_changed_lists($timestamp)
    {
        try {
            $return = $this->client->crmgetChangedLists($this->session_id, $timestamp);
        } catch (Exception $e) {
            log_error('crmgetChangedLists error : '. $e->getMessage());
        }

        return $return;
    }

    /**
     * create a connection to crm system
     *
     * @param array $config
     * @return string
     */
    public function connect($username, $password)
    {
        try {
            $session_id = $this->client->crmLogin($username, $password);
        } catch (Exception $e) {
            log_error('crmLogin error : '. $e->getMessage());
        }

        if (!$session_id) {
            return false;
        }

        $this->session_id = $session_id;

        return $session_id;
    }

    /**
     * session logoff from crm system
     * @return int
     */
    public function logout()
    {
        try {
            $result = $this->client->crmLogoff($this->session_id);
        } catch (Exception $e) {
            log_error('crmLogoff error : '. $e->getMessage());
        }

        return $result;
    }
}
