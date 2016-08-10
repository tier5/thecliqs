<?php

/**
 *
 * @author An Nguyen <annt@younetco.com>
 *
 * @see http://bshaffer.github.io/oauth2-server-php-docs/
 */
class Ynrestapi_Service_Oauth_Server
{
    /**
     * @var mixed
     */
    private static $_server;

    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Get server
     */
    public static function getServer()
    {
        if (!self::$_server) {
            self::$_server = self::createServer();
        }

        return self::$_server;
    }

    /**
     * Create server
     */
    public static function createServer()
    {
        $conn = Engine_Db_Table::getDefaultAdapter();
        $storage = new Ynrestapi_Service_Oauth_Storage($conn);

        $config = array(
            'enforce_state' => false,
            'allow_implicit' => true,
        );

        $grantType = array(
            'authorization_code' => new OAuth2\GrantType\AuthorizationCode($storage),
            'password' => new Ynrestapi_Service_Oauth_UserCredentials($storage),
            'client_credentials' => new OAuth2\GrantType\ClientCredentials($storage),
            'refresh_token' => new OAuth2\GrantType\RefreshToken($storage),
        );

        $server = new OAuth2\Server($storage, $config, $grantType);

        return $server;
    }

    /**
     * @param $scopeRequired
     */
    public static function verifyResourceRequest($scopeRequired)
    {
        $request = OAuth2\Request::createFromGlobals();
        $response = new Ynrestapi_Service_Response();
        $server = self::getServer();

        if (!$server->getAccessTokenData($request, $response)) {
            if (!$response->isServerError()) {
                $response->setError(401, 'unauthorized_client', Zend_Registry::get('Zend_Translate')->_('Unauthorized'));
            }
            $response->send();
            exit();
        }

        if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
            // if the scope required is different from what the token allows, this will send a "401 insufficient_scope" error
            $response->send();
            exit();
        }
    }

    /**
     * @return mixed
     */
    public static function getAccessTokenData()
    {
        $request = OAuth2\Request::createFromGlobals();
        $response = new Ynrestapi_Service_Response();
        $server = self::getServer();

        return $server->getAccessTokenData($request, $response);
    }
}
