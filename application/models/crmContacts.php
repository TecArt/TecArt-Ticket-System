<?php

class crmContacts
{
    private $session_id;
    private $client;
    private $baseUrl;
    
    public function __construct($session_id, $config, $baseUrl)
    {
        $this->session_id = $session_id;
        $this->baseUrl    = $baseUrl;

        if (!isset($this->client)) {
            try {
                $this->client  = new SOAPClient($config['webservice_url']."soap/index.php?op=contacts&wsdl");
            } catch (Exception $e) {
                log_error('SOAP Connection to CRM-contacts error !'. $e->getMessage());
            }
        }
    }
    
    public function do_contact_authenticate($username, $pass)
    {
        $search_term['crmsearchContactItems'] = array();

        if (strpos($username, '@') && strpos($username, '.')) {
            $search_term['crmsearchContactItems'] = array(array('field'=>'email', 'value'=>$username),
                                                           array('field'=>'password', 'value'=>$pass));
        } else {
            $search_term['crmsearchContactItems'] = array(array('field'=>'realnumber', 'value'=>$username),
                                                           array('field'=>'password', 'value'=>$pass));
        }

        try {
            $seach_result = $this->client->crmsearchContact($this->session_id, $search_term, 10, 0);
        } catch (Exception $e) {
            log_error('Error: crmsearchContact : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }
        

        if (empty($seach_result) || count($seach_result) > 1 || !isset($seach_result[0]->cid)) {
            return false;
        }

        return (int)$seach_result[0]->cid;
    }


    /**
     * get one contact by contact id
     *
     * @param int $id
     * @return array or false when error
     */
    public function get_contact_by_id($id)
    {
        $return = false;

        try {
            $return = $this->client->crmgetSingleContact($this->session_id, $id);
        } catch (Exception $e) {
            log_error('Error: crmgetSingleContact : '.$e->getMessage());
  
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    public function change_password($cid, $new_pass)
    {
        $return = false;

        try {
            $return = $this->client->crmChangeContact($this->session_id, $cid, array( 'password' => $new_pass ));
        } catch (Exception $e) {
            log_error('Error: crmChangeContact : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }
}
