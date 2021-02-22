<?php

class Rest_Response
{
    protected $_responseString;
    protected $_curlHandle;

    protected $_curlErrno;
    protected $_curlInfo;

    public function __construct($curlHandle, string $response)
    {
        $this->_curlHandle = $curlHandle;
        $this->_responseString = $response;

        //muss alles hier schon passieren weil der handle später schon geschlossen sein könnte
        $errNo = curl_errno($curlHandle);
        $this->_curlErrno = $errNo;
        $this->_curlInfo = curl_getinfo($curlHandle);
    }

    public function getStatusCode() : int
    {
        return (int)$this->_curlInfo['http_code'];
    }

    public function getCurlErrno() : int
    {
        return $this->_curlErrno;
    }

    /**
     * Problem mit Verbindung
     *
     * @return bool
     */
    public function isConnectionError() : bool
    {
        return (0 !== $this->getCurlErrno());
    }

    /**
     * Problem mit Server
     *
     * @return bool
     */
    public function isServerError() : bool
    {
        return ($this->getStatusCode() >= 500);
    }



    public function isSuccessful() : bool
    {
        return (0 === $this->getCurlErrno()) && ($this->getStatusCode() < 400);
    }

    public function is400()
    {
        return (400 === $this->getStatusCode());
    }

    public function is401()
    {
        return (401 === $this->getStatusCode());
    }

    public function is403()
    {
        return (403 === $this->getStatusCode());
    }
    public function is404()
    {
        return (404 === $this->getStatusCode());
    }

    public function is405()
    {
        return (404 === $this->getStatusCode());
    }

    public function getBodyString() : string
    {
        return $this->_responseString;
    }

    /**
     * liefert die eigentlichen daten als PHP Object/Array
     *
     * @return array | stdClass | null json_decoded string
     */
    public function getPayload()
    {
        $responseString = $this->getBodyString();

        if('' === $responseString){
            return null;
        }

        else{
            $responseObj = json_decode($responseString);

            return $responseObj;
        }
    }

    public function getData()
    {
        $payload = $this->getPayload();
        if (isset($payload->data)) {
            return $payload->data;
        }
        else {
            return null;
        }
    }
}