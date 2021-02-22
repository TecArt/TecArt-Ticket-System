<?php

class crmLogon
{
    public function get_changed_users($timestamp)
    {
        $get_params = array(
            'response_field_filter' => 'name,login'
        );

        $response = Rest_Client::requestGet("users/$timestamp", $get_params);

        if ($response->isSuccessful()) {
            return $response->getData();
        }

        return array();
    }

    public function get_crm_version()
    {
        //Todo:use api
        return '5.0';
    }

    /**
     * get defined fields from crm
     *
     * @param string $name
     * @return array
     */
    public function get_ticket_lists($name)
    {
        $response = Rest_Client::requestGet("crmlist/$name");

        if ($response->isSuccessful()) {
            return $response->getData();
        }
        else {
            return null;
        }

        return $return;
    }

    public function get_changed_lists($timestamp) : array
    {
        $response = Rest_Client::requestGet("crmlists/$timestamp");

        if ($response->isSuccessful()) {
            return $response->getData();
        }
        else {
            return array();
        }

        return $return;
    }
}
