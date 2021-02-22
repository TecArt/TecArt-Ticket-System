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
    }

    /**
     * get the document tree by ticket id
     *
     * @param string $path
     * @param int $objectid
     * @return array or false when error
     */
    public function get_documents_tree($path, $objectid) : ?array
    {
        if (!$this->is_utf8($path)) {
            $path = utf8_encode($path);
        }

        $params = array(
            'module' => $this->module,
            'path' => $path,
            'object_id' => $objectid,
            'recursive' => false,
            'change_time' => 0
        );

        $response = Rest_Client::requestGet("document/tree", $params);
        if ($response->isSuccessful()) {
            $data = $response->getData();

            if (null === $data) {//Dokumente api gibt keine data zurück wenn Verzeichnis noch nicht erstellt
                return array();
            }

            return $data;
        }

        return false;
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

        $params = array(
            'path' => $docpath
        );

        $meta_response = Rest_Client::requestGet("document/{$this->module}/$objectid", $params);

        if ($meta_response->isSuccessful()) {
            $meta_data = $meta_response->getData();

            $data_response = Rest_Client::requestGet("document/download/{$this->module}/$objectid", $params);

            if ($data_response->isSuccessful()) {
                $content = $data_response->getBodyString();

                $meta_data->content= $content;

                return $meta_data;
            }
        }

        return false;
    }

    /**
     * create folder for upload
     *
     * @param string $folder_name
     * @param int $ticket_id
     * @return bool
     */
    public function create_folder($folder_name, $ticket_id) : bool
    {
        $params = array(
            'module' => $this->module,
            'path' => $folder_name,
            'object_id' => $ticket_id
        );

        $response = Rest_Client::requestGet('document/directory', $params);

        return $response->isSuccessful();
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
        $get_params = array(
            'path' => $doc_name,
            'subscription_type' => 0,
            'sync_doc' => 0
        );
        $post_params = array(
            'File' => $base64_content
        );
        $response = Rest_Client::requestPut("document/upload/{$this->module}/$ticket_id", $post_params, $get_params);

        return $response->isSuccessful();
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
