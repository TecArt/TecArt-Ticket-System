<?php

class crmDocuments
{
    private $session_id;
    private $client;
    private $module;

    public function __construct($session_id, $config, $module = 'tickets')
    {
        $this->session_id    = $session_id;
        $this->module        = $module;

        if (!isset($this->client)) {
            try {
                $this->client  = new SOAPClient($config['webservice_url']."soap/index.php?op=docs&wsdl");
            } catch (Exception $e) {
                log_error('SOAP Connection to CRM-docs error : '.$e->getMessage());
            }
        }
    }

    /**
     * get the document tree by ticket id
     *
     * @param string $path
     * @param int $objectid
     * @return array or false when error
     */
    public function get_documents_tree($path, $objectid)
    {
        if (!$this->is_utf8($path)) {
            $path = utf8_encode($path);
        }
        
        try {
            $return = $this->client->crmgetTree($this->session_id, $this->module, $path, -1, -1, -1, $objectid, -1);
        } catch (Exception $e) {
            log_error('crmgetTree error : '. $e->getMessage());
        }
        
        return $return;
    }

    /**
     * get one document
     *
     * @param string $docpath
     * @param int $objectid
     * @return array or false when error
     */
    public function get_document($docpath, $objectid)
    {
        // the document should be compressed
        $compress       = -1;

        $return = false;
        
        try {
            $return =  $this->client->crmgetDocument($this->session_id, $this->module, $docpath, -1, $objectid, $compress);
        } catch (Exception $e) {
            log_error('crmgetDocument error : '. $e->getMessage());
        }

        return $return;
    }

    /**
     * create folder for upload
     *
     * @param string $folder_name
     * @param int $ticket_id
     * @return unknown
     */
    public function create_folder($folder_name, $ticket_id)
    {
        $return = false;
        
        try {
            $return = $this->client->crmcreateDirectory($this->session_id, $this->module, $folder_name, -1, $ticket_id);
        } catch (Exception $e) {
            log_error('crmcreateDirectory error : '. $e->getMessage());
        }

        return $return;
    }

    /**
     * upload document
     *
     * @param string $doc_name
     * @param string $base64_content
     * @param int $ticket_id
     * @return unknown
     */
    public function upload_document($doc_name, $base64_content, $ticket_id)
    {
        $return = false;
        
        try {
            $return = $this->client->crmuploadDocument($this->session_id, $this->module, $doc_name, $base64_content, -1, $ticket_id, -1);
        } catch (Exception $e) {
            log_error('crmuploadDocument error : '. $e->getMessage());
        }

        return $return;
    }
    
    /**
     * utf-8 check
     *
     * @param string $string
     * @return bool
     */
    public function is_utf8($string)
    {
        return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )+%xs', substr($string, 0, 5000));
    }
}
