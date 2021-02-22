<?php
class crmContract
{
    private $baseUrl;
    private $partner_contract_type_field;
    private $partner_contract_type;
    
    public function __construct($config, $baseUrl)
    {
        $this->baseUrl    = $baseUrl;

        $this->partner_contract_type_field    = isset($config["partners"]["contract_type_field"]) ? $config["partners"]["contract_type_field"] : false;
        $this->partner_contract_type        = isset($config['partners']['contract_type']) ? $config['partners']['contract_type'] : false;
    }
    
    /**
     * Check if contact has a valid partner contract or not
     * @param int $cid contact id
     * @return boolean
     */
    public function has_partner_contract(int $cid) : bool
    {
        $now = time();
        $clauses = array(
            "(cid = $cid OR cid LIKE '$cid:%')",
            "{$this->partner_contract_type_field} = '{$this->partner_contract_type}'"
        );

        $get_params = array(
            'response_field_filter' => 'start,cancellation,stop'
        );


        $tql = implode(' AND ', $clauses);
        $tql = urlencode($tql);

        $response = Rest_Client::requestGet("contracts/by_tql_condition/$tql", $get_params);

        if ($response->isSuccessful()) {
            $contracts = $response->getData();
            foreach ($contracts as $contract) {

                if ($contract->start <= $now && ((!$contract->cancellation && (!$contract->stop || $contract->stop > $now)) || ($contract->cancellation && $contract->stop > $now))) {
                    return true;
                }
            }
        }
        return false;
    }
}
