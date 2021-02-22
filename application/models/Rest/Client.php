<?php
require_once __SITE_PATH . '/application/models/Rest/Exception/CouldNotAuthenticate.php';
require_once __SITE_PATH . '/application/models/Rest/Response.php';

class Rest_Client
{
    private static $config;
    private static $session_id;

    public static function init(array $config)
    {
        self::$config = $config;
        if (isset($_SESSION['rest_session_id'])) {
            self::$session_id = $_SESSION['rest_session_id'];
        }
    }

    private static function request(string $http_method, string $path = '', array $get_params = array(), array $post_params = array()) : Rest_Response
    {
        if (!isset(self::$config)) {
            throw new Exception('RestClient not inited');
        }
        if (!isset(self::$session_id)) {
            self::login();
        }
        if (!isset(self::$session_id)) {
            throw new Rest_Exception_CouldNotAuthenticate();
        }

        $webservice_url = self::$config['webservice_url'];

        if (!in_array($http_method, array('GET', 'POST', 'PUT', 'DELETE'))) {
           throw new Exception("invalid request method: $http_method");
        }

        $curl_options = array(
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_TIMEOUT         => 120,
            CURLOPT_RETURNTRANSFER  => true
        );

        $curl_options[CURLOPT_HTTPHEADER] = array(
            'Session-Id: '. self::$session_id
        );

        if ('GET' !== $http_method) {
            $curl_options[CURLOPT_CUSTOMREQUEST] = $http_method;
        }

        if ('GET' === $http_method) {
            $get_params['response_type'] = 'JSON';
        }
        else {

            if (isset($post_params['File'])){
                $curl_options[CURLOPT_POSTFIELDS] = base64_decode($post_params['File']);
            }
            else {

                if(!empty($post_params)) {
                    $post_body = json_encode($post_params);
                }

                $curl_options[CURLOPT_POSTFIELDS] = $post_body;
            }
        }

        $url = "{$webservice_url}rest_v2/index.php/5.0/$path";
        if (!empty($get_params)) {
            $url .= '?' . http_build_query($get_params);
        }

        $curl = curl_init($url);

        curl_setopt_array($curl, $curl_options);

        self::init_proxy($curl);

        $curl_response = curl_exec($curl);

        $response = new Rest_Response($curl, $curl_response);

        if ($response->is401()) {
            throw new Rest_Exception_CouldNotAuthenticate();
        }

        curl_close($curl);

        return $response;
    }

    public static function requestGet(string $path, array $get_params = array()) : Rest_Response
    {
        return self::request('GET', $path, $get_params);
    }

    public static function requestPost(string $path,array $post_params = array(), array $get_params = array()) : Rest_Response
    {
        return self::request('POST', $path, $get_params, $post_params);
    }

    public static function requestPut(string $path,array $post_params = array(), array $get_params = array()) : Rest_Response
    {
        return self::request('PUT', $path, $get_params, $post_params);
    }

    public static function login()
    {
        $config = self::$config;
        $webservice_url = $config['webservice_url'];
        $username = $config['username'];
        $password = $config['password'];

        $url = "$webservice_url/rest/index.php/logon?method=crmLogin";

        $params = array(
            'username' => $username,
            'password' => $password
        );

        $args = array(
            'method' => 'crmLogin',
            'request_type' => 'JSON',
            'response_type' => 'serialize',
            'params' => json_encode($params)
        );

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);


        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($args));

//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYSTATUS, false);
        self::init_proxy($curl);

        $response = curl_exec($curl);

        $http_code = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if (200 !== $http_code) {
            throw new Rest_Exception_CouldNotAuthenticate();
        }

        self::$session_id = unserialize($response);
        $_SESSION['rest_session_id'] = self::$session_id;
    }

    private static function init_proxy($curl_handle)
    {
        $config = self::$config;

        if (isset($config['proxy'])) {
//            curl_setopt($curl, CURLOPT_PROXY_SSL_VERIFYPEER, false);
//            curl_setopt($curl, CURLOPT_PROXY_SSL_VERIFYHOST, false);

            curl_setopt($curl_handle, CURLOPT_PROXY, $config['proxy']['ip']);
            curl_setopt($curl_handle, CURLOPT_PROXYPORT, $config['proxy']['port']);
            curl_setopt($curl_handle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);


            if (isset($config['proxy']['username']) && ! empty($config['proxy']['username'])) {

                $proxy_auth = "{$config['proxy']['username']}:{$config['proxy']['password']}";
                curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $proxy_auth);
            }
        }
    }

    public static function destroy()
    {
        self::$session_id = null;
        $_SESSION['rest_session_id'] = null;
    }
}