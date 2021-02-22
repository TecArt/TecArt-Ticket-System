<?php

require_once __SITE_PATH . '/application/models/Rest/Client.php';
class crmContacts
{
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    private function authenticate_ad(string $username, string $password, string $dn) : bool
    {
        include_once __SITE_PATH . "/lib/adldap/adLDAP.php";

        $dn_data = explode(',', $dn);

        $base_dn = array();
        $suffix  = array();

        foreach ($dn_data as $dn_datum) {

            $dn_pair = explode('=', $dn_datum);

            if (count($dn_pair) !== 2) {
                continue;
            }

            switch ($dn_pair[0])
            {
                case 'DC':
                    $base_dn[] = $dn_datum;
                    $suffix[] = $dn_pair[1];
                    break;
            }
        }

        $options = array(
            "account_suffix"     => '@' . implode('.', $suffix),
            "base_dn"            => implode(',', $base_dn),
            "domain_controllers" => array($this->config['ad_domain']),
            "ad_port"			 => $this->config['ad_port'],
            "use_ssl"			 => $this->config['ad_use_ssl'],
            "use_tls"			 => $this->config['ad_use_tls']
        );

        try {
            $adldap = new adLDAP($options);
        }
        catch (Exception $e) {
            log_error('connection to ad failed: ' . $e->getMessage());
            return false;
        }

        try {
            if (!$adldap->authenticate($username, $password)) {
                log_error('authentication for user ' . $username . ' failed');
                return false;
            }
        }
        catch (adLDAPException $e) {
            log_error('exception: ' . $e->getMessage());
            return false;
        }

        return true;
    }

    public function do_contact_authenticate($username, $pass) : ?int
    {
        $response_field_filter = 'id';
        $ad_username_field = '';
        $ad_dn_field = '';

        if (isset($this->config['use_ad']) && $this->config['use_ad']) {

            $user_field = $this->config['ad_username_field'];
            $tql = "$user_field = '$username'";

            $ad_username_field = $this->config['ad_username_field'];
            $ad_dn_field = $this->config['ad_dn_field'];
            $response_field_filter = "id,$ad_username_field,$ad_dn_field";

        }
        elseif (strpos($username, '@') && strpos($username, '.')) {

            $user_field = 'email';
            $tql = "$user_field = '$username' AND password = '$pass'";
        }
        else {

            $user_field = 'realnumber';
            $tql = "$user_field = '$username' AND password = '$pass'";
        }

        $tql = urlencode($tql);

        $response = Rest_Client::requestGet(
            "contacts/by_tql_condition/$tql",
            array('response_field_filter' => $response_field_filter)
        );

        if (!$response->isSuccessful()) {
            return null;
        }

        $payload = $response->getPayload();

        if (1 !== count($payload->data)) {
            return null;
        }

        $contact = $payload->data[0];

        if (isset($this->config['use_ad']) && $this->config['use_ad']) {

            $dn = $contact->{$ad_dn_field};

            if (empty($dn)) {
                return null;
            }

            if (!$this->authenticate_ad($username, $pass, $dn)) {
                return null;
            }
        }

        return (int)$contact->id;
    }

    /**
     * get one contact by contact id
     *
     * @param int $id
     * @return array or false when error
     */
    public function get_contact_by_id($id)
    {
        $response = Rest_Client::requestGet("contact/$id");

        if ($response->isSuccessful()) {
            $data = $response->getData();

            return $data;
        }
        else {
            return null;
        }
    }

    public function change_password($cid, $new_pass) : bool
    {
        $response = Rest_Client::requestPut("contact/$cid", array('data' => array('password' => $new_pass)));

        return $response->isSuccessful();
    }
}
