<?php

class crmTickets
{
   /**
     * get Ticket from CRM System by condition.
     *
     * @param array $params
     * @return array or false when error
     */
    public function get_by_condition(int $cid, array $params, array $fields = null)
    {
        $tql = '';

        $clauses = array();
        $clauses[] = "contact_id = $cid";


        if (isset($params['status']) && (-1 !== $params['status'])) {
            $clauses[] = "status = {$params['status']}";
        }
        if (isset($params['section'])) {
            $clauses[] = "section_id = {$params['section']}";
        }
        if (isset($params['begin_time'])) {
            $clauses[] = "start >= {$params['begin_time']}";
        }
        if (isset($params['end_time'])) {
            $clauses[] = "stop <= {$params['end_time']}";
        }

        $tql = implode(' AND ', $clauses);
        $tql = urlencode($tql);

        $get_params = array();
        if (null !== $fields) {
            $get_params['response_field_filter'] = implode(',', $fields);
        }

        $response = Rest_Client::requestGet("tickets/by_tql_condition/$tql/", $get_params);

        if ($response->isSuccessful()) {
            return $response->getData();
        }
        else {
            return false;
        }
    }



    /**
     * get Ticket from CRM System by ticket id
     *
     * @param int $id
     * @return array or false when error
     */
    public function get_ticket_by_id(int $id)
    {
        $response = Rest_Client::requestGet("ticket/$id");

        if ($response->isSuccessful()) {
            $data = $response->getData();

            return array($data);
        }
        else {
            return false;
        }
    }

    /**
     * add one ticket in CRM System
     *
     * @param array $ticket
     * @return int ticket_id
     */
    public function add_ticket(array $ticket) : ?int
    {
        $response = Rest_Client::requestPost('ticket', array('data' => $ticket));

        if ($response->isSuccessful()) {
            $data = $response->getData();

            return (int)$data[0];
        }

        return null;
    }

    /**
     * get ticket action by ticket id
     *
     * @param array $param
     * @return array or false when error
     */
    public function get_ticket_action_by_ticket_id(int $ticket_id)
    {
        $get_params = array(
            'response_field_filter' => 'id,ticket_id,subject,duration,type,date,user_id,description,change_user,change_time,create_user,create_time'
        );
        $response = Rest_Client::requestGet("ticket_actions/by_ticket_id/$ticket_id", $get_params);

        if ($response->isSuccessful()) {
            return $response->getData();
        }

        return false;
    }




    /**
     * add one ticket action
     *
     * @param array $ticket_action
     * @return bool
     */
    public function add_ticket_action($ticket_action) : bool
    {
        $post_data = array(
            'data' => $ticket_action
        );

        $response = Rest_Client::requestPost('ticket_action/', $post_data);

        return $response->isSuccessful();
    }

    /**
     * get crm responsible user for web service
     *
     */
    public function get_crm_responsible_user_for_ws($section = 0) :?int
    {

        $response = Rest_Client::requestGet("ticket_section/$section/responsible_user");

        if ($response->isSuccessful()) {
            $data = $response->getData();

            return (int)$data[0];
        }

        return null;
    }

    public function get_min_max_createtime($cid)
    {
        $response = Rest_Client::requestGet("tickets/min_max_createtime/$cid");

        if ($response->isSuccessful()) {
            return $response->getData();
        }

        return false;
    }


    public function get_ticket_activities_by_ticket_id(int $ticket_id)
    {
        $get_params = array(
            'type' => 'email,call,action'
        );
        $response = Rest_Client::requestGet("ticket/$ticket_id/activities/", $get_params);

        if ($response->isSuccessful()) {
            $data = $response->getData();

            if (null === $data) {
                $data = array();
            }

            return $data;
        }
        else {
            return false;
        }
    }

    public function get_tickets_information(array $ticket_ids, $activities)
    {
        $ticket_ids_string = implode(',', $ticket_ids);
        $activities_string = implode(',', $activities);

        $get_params = array(
            'ticket_ids' => $ticket_ids_string,
            'activities' => $activities_string
        );

        $response = Rest_Client::requestGet("tickets/info", $get_params);

        if ($response->isSuccessful()) {
            return $response->getData();
        }
        return null;
    }
    
    public function get_ticket_sections() : ?array
    {
        $response = Rest_Client::requestGet('ticket_sections');

        if ($response->isSuccessful()) {
            return $response->getData();
        }

        return null;
    }
    
    public function close_ticket($id) : bool
    {
        $params = array('data' => array('status' => '3'));

        $response = Rest_Client::requestPut("ticket/$id", $params);

        return $response->isSuccessful();
    }
}
