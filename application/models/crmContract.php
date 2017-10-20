<?php
class crmContract
{
    private $session_id;
    private $client;
    private $baseUrl;
    private $partner_contract_type_field;
    private $partner_contract_type;
    
    public function __construct($session_id, $config, $baseUrl)
    {
        $this->session_id = $session_id;
        $this->baseUrl    = $baseUrl;
    
        if (!isset($this->client)) {
            try {
                $this->client  = new SOAPClient($config['webservice_url']."soap/index.php?op=contracts&wsdl");
            } catch (Exception $e) {
                log_error('SOAP Connection to CRM-contracts error !'. $e->getMessage());
            }
        }
        
        $this->partner_contract_type_field    = isset($config["partners"]["contract_type_field"]) ? $config["partners"]["contract_type_field"] : false;
        $this->partner_contract_type        = isset($config['partners']['contract_type']) ? $config['partners']['contract_type'] : false;
    }
    
    /**
     * Check if contact has a valid partner contract or not
     * @param int $cid contact id
     * @return boolean
     */
    public function has_partner_contract($cid)
    {
        $search_params          = array();
        $search_params['cid'] = array($cid, "$cid:%");
        $search_params[$this->partner_contract_type_field] = $this->partner_contract_type;
        
        $contracts = $this->get_contracts($search_params);

        $now = time();
        foreach ($contracts as $contract) {
            if ($contract->start <= $now && ((!$contract->cancellation && (!$contract->stop || $contract->stop > $now)) || ($contract->cancellation && $contract->stop > $now))) {
                return true;
            }
        }

        return false;
    }
    
    /**
     * Fetches contracts for given params
     * @param array $params search params, use field => value
     * @return array
     */
    public function get_contracts(array $params)
    {
        $search = array('crmsearchContractsItems' => array());
        
        foreach ($params as $field => $value) {
            $fparams = array(
                    'field' => $field,
                    'crmsearchContractsItemValues' => array()
            );

            // check for array params for OR condition
            $fparams['crmsearchContractsItemValues'] = (is_array($value))
                                                     ? array_map(array($this, 'set_search_values'), $value)
                                                     : array($this->set_search_values($value));
            
            $search['crmsearchContractsItems'][] = $fparams;
        }
        
        $return = false;
        
        try {
            $return = $this->client->crmsearchContracts($this->session_id, $search, false, false, 0);
        } catch (Exception $e) {
            log_error('Error: crmsearchContracts : '.$e->getMessage());
        }
    
        return $return;
    }
    
    private function set_search_values($value)
    {
        return array('value' => $value);
    }
}
