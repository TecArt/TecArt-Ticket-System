<?php

class crmTickets
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
                $this->client  = new SOAPClient($config['webservice_url']."soap/index.php?op=tickets&wsdl");
            } catch (Exception $e) {
                log_error('SOAP Connection to CRM-tickets error !');
            }
        }
    }


    /**
     * get Ticket from CRM System by condition.
     *
     * @param array $params
     * @return array or false when fehler
     */
    public function get_by_condition($params)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketByCondition($this->session_id, $params);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketByCondition : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    /**
     * get Ticket from CRM System by ticket id
     *
     * @param unknown_type $id
     * @return array or false when fehler
     */
    public function get_ticket_by_id($id)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicket($this->session_id, $id);
        } catch (Exception $e) {
            log_error('Error: crmgetTicket : '.$e->getMessage());
             
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    /**
     * add one ticket in CRM System
     *
     * @param array $ticket
     * @return ticket_id or false when fehler
     */
    public function add_ticket($ticket)
    {
        $return = false;

        try {
            $return = $this->client->crmaddTicket($this->session_id, $ticket);
        } catch (Exception $e) {
            log_error('Error: crmaddTicket : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    /**
     * get ticket action by ticket id
     *
     * @param array $param
     * @return array or false when fehler
     */
    public function get_ticket_action_by_ticket_id($param)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketActionByCondition($this->session_id, $param);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketActionByCondition : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }
    
    /**
     * get ticket action from more ids
     *
     * @param array $ids
     * @return array or false when fehler
     */
    public function get_ticket_action_by_ids($ids)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketActionByCondition($this->session_id, $ids);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketActionByCondition : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    /**
     * add one ticket action
     *
     * @param array $ticket_action
     * @return ticket action id or false when fehler
     */
    public function add_ticket_action($ticket_action)
    {
        $return = false;

        try {
            $return = $this->client->crmaddTicketAction($this->session_id, $ticket_action);
        } catch (Exception $e) {
            log_error('Error: crmaddTicketAction : '.$e->getMessage());

            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }
        
        return $return;
    }

    /**
     * get crm responsible user for web service
     *
     * @return array
     */
    public function get_crm_responsible_user_for_ws($section = 0)
    {
        $return = false;

        try {
            $return = $this->client->crmgetResponsibleUserForSection($this->session_id, $section);
        } catch (Exception $e) {
            log_error('Error: crmgetResponsibleUserForSection : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    public function get_min_max_createtime($cid)
    {
        $return = false;

        try {
            $return = $this->client->crmgetMinMaxCreateTime($this->session_id, $cid);
        } catch (Exception $e) {
            log_error('Error: crmgetMinMaxCreateTime : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }


    public function get_ticket_activities_by_ticket_id($tid)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketActivities($this->session_id, $tid);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketActivities : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }

    public function get_tickets_information($ticket_ids, $activities)
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketsInfo($this->session_id, $ticket_ids, $activities);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketsInfo : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }
    
    public function get_ticket_sections()
    {
        $return = false;

        try {
            $return = $this->client->crmgetTicketSections($this->session_id);
        } catch (Exception $e) {
            log_error('Error: crmgetTicketSections : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }

        return $return;
    }
    
    public function close_ticket($id)
    {
        $return = false;
        
        try {
            $return = $this->client->crmCloseTicket($this->session_id, $id);
        } catch (Exception $e) {
            log_error('Error: crmCloseTicket : '.$e->getMessage());
        
            if (strpos($e->getMessage(), 'Invalid session or session expired') !== false) {
                header("Location: ".$this->baseUrl."?co=auth/logout");
                exit;
            }
        }
        
        return $return;
    }
}
