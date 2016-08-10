<?php

use OAuth2\OpenID\Storage\AuthorizationCodeInterface as OpenIDAuthorizationCodeInterface;
use OAuth2\OpenID\Storage\UserClaimsInterface;
use OAuth2\Storage\AccessTokenInterface;
use OAuth2\Storage\AuthorizationCodeInterface;
use OAuth2\Storage\ClientCredentialsInterface;
use OAuth2\Storage\JwtBearerInterface;
use OAuth2\Storage\PublicKeyInterface;
use OAuth2\Storage\RefreshTokenInterface;
use OAuth2\Storage\ScopeInterface;
use OAuth2\Storage\UserCredentialsInterface;

/**
 *
 * @author An Nguyen <annt@younetco.com>
 *
 * @see http://bshaffer.github.io/oauth2-server-php-docs/
 */
class Ynrestapi_Service_Oauth_Storage implements
AuthorizationCodeInterface,
AccessTokenInterface,
ClientCredentialsInterface,
UserCredentialsInterface,
RefreshTokenInterface,
JwtBearerInterface,
ScopeInterface,
PublicKeyInterface,
UserClaimsInterface,
OpenIDAuthorizationCodeInterface
{
    /**
     * @var mixed
     */
    protected $db;
    /**
     * @var mixed
     */
    protected $config;

    /**
     * @param $connection
     * @param array         $config
     */
    public function __construct($connection, $config = array())
    {
        $this->db = $connection;

        $this->config = array_merge(array(
            'client_table' => 'engine4_ynrestapi_oauth_clients',
            'access_token_table' => 'engine4_ynrestapi_oauth_access_tokens',
            'refresh_token_table' => 'engine4_ynrestapi_oauth_refresh_tokens',
            'code_table' => 'engine4_ynrestapi_oauth_authorization_codes',
            'user_table' => 'engine4_users',
            'jwt_table' => 'engine4_ynrestapi_oauth_jwt',
            'jti_table' => 'engine4_ynrestapi_oauth_jti',
            'scope_table' => 'engine4_ynrestapi_oauth_scopes',
            'public_key_table' => 'engine4_ynrestapi_oauth_public_keys',
        ), $config);
    }

    /**
     * @param  $sql
     * @param  array   $bind
     * @return mixed
     */
    public function dbQuery($sql, $bind = array())
    {
        $this->bindNamed($sql, $bind);
        return $this->db->query($sql, $bind);
    }

    /**
     * @param  $sql
     * @param  array        $bind
     * @param  $fetchType
     * @return mixed
     */
    public function dbFetchRow($sql, $bind = array(), $fetchType = null)
    {
        $this->bindNamed($sql, $bind);
        return $this->db->fetchRow($sql, $bind, $fetchType);
    }

    /**
     * @param  $sql
     * @param  array        $bind
     * @param  $fetchType
     * @return mixed
     */
    public function dbFetchAll($sql, $bind = array(), $fetchType = null)
    {
        $this->bindNamed($sql, $bind);
        return $this->db->fetchAll($sql, $bind, $fetchType);
    }

    /**
     * @param  $sql
     * @param  array   $bind
     * @return mixed
     */
    public function dbFetchCol($sql, $bind = array())
    {
        $this->bindNamed($sql, $bind);
        return $this->db->fetchCol($sql, $bind, $fetchType);
    }

    /**
     * @param $sql
     * @param array  $bind
     */
    public function bindNamed(&$sql, $bind = array())
    {
        if (is_array($bind)) {
            foreach ($bind as $key => $value) {
                $value = $this->db->quote($value);
                $sql = str_replace(':' . $key, $value, $sql);
            }
        }
    }

    /* OAuth2\Storage\ClientCredentialsInterface */
    /**
     * @param  $client_id
     * @param  $client_secret
     * @return mixed
     */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $sql = sprintf('SELECT * from %s where client_id = :client_id', $this->config['client_table']);

        $result = $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC);

        // make this extensible
        return $result && $result['client_secret'] == $client_secret;
    }

    /**
     * @param $client_id
     */
    public function isPublicClient($client_id)
    {
        $sql = sprintf('SELECT * from %s where client_id = :client_id', $this->config['client_table']);

        if (!$result = $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC)) {
            return false;
        }

        return empty($result['client_secret']);
    }

    /* OAuth2\Storage\ClientInterface */
    /**
     * @param  $client_id
     * @return mixed
     */
    public function getClientDetails($client_id)
    {
        $sql = sprintf('SELECT * from %s where client_id = :client_id', $this->config['client_table']);

        return $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC);
    }

    /**
     * @param  $client_id
     * @param  $client_secret
     * @param  null             $redirect_uri
     * @param  null             $grant_types
     * @param  null             $scope
     * @param  null             $user_id
     * @return mixed
     */
    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        // if it exists, update it.
        if ($this->getClientDetails($client_id)) {
            $sql = sprintf('UPDATE %s SET client_secret=:client_secret, redirect_uri=:redirect_uri, grant_types=:grant_types, scope=:scope, user_id=:user_id where client_id=:client_id', $this->config['client_table']);
        } else {
            $sql = sprintf('INSERT INTO %s (client_id, client_secret, redirect_uri, grant_types, scope, user_id) VALUES (:client_id, :client_secret, :redirect_uri, :grant_types, :scope, :user_id)', $this->config['client_table']);
        }

        return $this->dbQuery($sql, compact('client_id', 'client_secret', 'redirect_uri', 'grant_types', 'scope', 'user_id'));
    }

    /**
     * @param $client_id
     * @param $grant_type
     */
    public function checkRestrictedGrantType($client_id, $grant_type)
    {
        $details = $this->getClientDetails($client_id);
        if (isset($details['grant_types'])) {
            $grant_types = explode(' ', $details['grant_types']);

            return in_array($grant_type, (array) $grant_types);
        }

        // if grant_types are not defined, then none are restricted
        return true;
    }

    /* OAuth2\Storage\AccessTokenInterface */
    /**
     * @param  $access_token
     * @return mixed
     */
    public function getAccessToken($access_token)
    {
        $sql = sprintf('SELECT * from %s where access_token = :access_token', $this->config['access_token_table']);

        if ($token = $this->dbFetchRow($sql, compact('access_token'), Zend_Db::FETCH_ASSOC)) {
            // convert date string back to timestamp
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    /**
     * @param  $access_token
     * @param  $client_id
     * @param  $user_id
     * @param  $expires
     * @param  $scope
     * @return mixed
     */
    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAccessToken($access_token)) {
            $sql = sprintf('UPDATE %s SET client_id=:client_id, expires=:expires, user_id=:user_id, scope=:scope where access_token=:access_token', $this->config['access_token_table']);
        } else {
            $sql = sprintf('INSERT INTO %s (access_token, client_id, expires, user_id, scope) VALUES (:access_token, :client_id, :expires, :user_id, :scope)', $this->config['access_token_table']);
        }

        return $this->dbQuery($sql, compact('access_token', 'client_id', 'user_id', 'expires', 'scope'));
    }

    /**
     * @param  $access_token
     * @return mixed
     */
    public function unsetAccessToken($access_token)
    {
        $sql = sprintf('DELETE FROM %s WHERE access_token = :access_token', $this->config['access_token_table']);

        return $this->dbQuery($sql, compact('access_token'));
    }

    /* OAuth2\Storage\AuthorizationCodeInterface */
    /**
     * @param  $code
     * @return mixed
     */
    public function getAuthorizationCode($code)
    {
        $sql = sprintf('SELECT * from %s where authorization_code = :code', $this->config['code_table']);

        if ($code = $this->dbFetchRow($sql, compact('code'), Zend_Db::FETCH_ASSOC)) {
            // convert date string back to timestamp
            $code['expires'] = strtotime($code['expires']);
        }

        return $code;
    }

    /**
     * @param  $code
     * @param  $client_id
     * @param  $user_id
     * @param  $redirect_uri
     * @param  $expires
     * @param  $scope
     * @param  null            $id_token
     * @return mixed
     */
    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        if (func_num_args() > 6) {
            // we are calling with an id token
            return call_user_func_array(array($this, 'setAuthorizationCodeWithIdToken'), func_get_args());
        }

        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            $sql = sprintf('UPDATE %s SET client_id=:client_id, user_id=:user_id, redirect_uri=:redirect_uri, expires=:expires, scope=:scope where authorization_code=:code', $this->config['code_table']);
        } else {
            $sql = sprintf('INSERT INTO %s (authorization_code, client_id, user_id, redirect_uri, expires, scope) VALUES (:code, :client_id, :user_id, :redirect_uri, :expires, :scope)', $this->config['code_table']);
        }

        return $this->dbQuery($sql, compact('code', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope'));
    }

    /**
     * @param  $code
     * @param  $client_id
     * @param  $user_id
     * @param  $redirect_uri
     * @param  $expires
     * @param  $scope
     * @param  null            $id_token
     * @return mixed
     */
    private function setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            $sql = sprintf('UPDATE %s SET client_id=:client_id, user_id=:user_id, redirect_uri=:redirect_uri, expires=:expires, scope=:scope, id_token =:id_token where authorization_code=:code', $this->config['code_table']);
        } else {
            $sql = sprintf('INSERT INTO %s (authorization_code, client_id, user_id, redirect_uri, expires, scope, id_token) VALUES (:code, :client_id, :user_id, :redirect_uri, :expires, :scope, :id_token)', $this->config['code_table']);
        }

        return $this->dbQuery($sql, compact('code', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope', 'id_token'));
    }

    /**
     * @param  $code
     * @return mixed
     */
    public function expireAuthorizationCode($code)
    {
        $sql = sprintf('DELETE FROM %s WHERE authorization_code = :code', $this->config['code_table']);

        return $this->dbQuery($sql, compact('code'));
    }

    /* OAuth2\Storage\UserCredentialsInterface */
    /**
     * @param  $email
     * @param  $password
     * @return mixed
     */
    public function checkUserCredentials($email, $password)
    {
        if ($user = $this->getUser($email)) {
            return $this->checkPassword($user, $password);
        }

        return false;
    }

    /**
     * @param  $email
     * @return mixed
     */
    public function getUserDetails($email)
    {
        return $this->getUser($email);
    }

    /* UserClaimsInterface */
    /**
     * @param  $user_id
     * @param  $claims
     * @return mixed
     */
    public function getUserClaims($user_id, $claims)
    {
        if (!$userDetails = $this->getUserDetails($user_id)) {
            return false;
        }

        $claims = explode(' ', trim($claims));
        $userClaims = array();

        // for each requested claim, if the user has the claim, set it in the response
        $validClaims = explode(' ', self::VALID_CLAIMS);
        foreach ($validClaims as $validClaim) {
            if (in_array($validClaim, $claims)) {
                if ($validClaim == 'address') {
                    // address is an object with subfields
                    $userClaims['address'] = $this->getUserClaim($validClaim, $userDetails['address'] ?: $userDetails);
                } else {
                    $userClaims = array_merge($userClaims, $this->getUserClaim($validClaim, $userDetails));
                }
            }
        }

        return $userClaims;
    }

    /**
     * @param  $claim
     * @param  $userDetails
     * @return mixed
     */
    protected function getUserClaim($claim, $userDetails)
    {
        $userClaims = array();
        $claimValuesString = constant(sprintf('self::%s_CLAIM_VALUES', strtoupper($claim)));
        $claimValues = explode(' ', $claimValuesString);

        foreach ($claimValues as $value) {
            $userClaims[$value] = isset($userDetails[$value]) ? $userDetails[$value] : null;
        }

        return $userClaims;
    }

    /* OAuth2\Storage\RefreshTokenInterface */
    /**
     * @param  $refresh_token
     * @return mixed
     */
    public function getRefreshToken($refresh_token)
    {
        $sql = sprintf('SELECT * FROM %s WHERE refresh_token = :refresh_token', $this->config['refresh_token_table']);

        $token = $stmt->execute(compact('refresh_token'));
        if ($token = $this->dbFetchRow($sql, compact('refresh_token'), Zend_Db::FETCH_ASSOC)) {
            // convert expires to epoch time
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    /**
     * @param  $refresh_token
     * @param  $client_id
     * @param  $user_id
     * @param  $expires
     * @param  $scope
     * @return mixed
     */
    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        $sql = sprintf('INSERT INTO %s (refresh_token, client_id, user_id, expires, scope) VALUES (:refresh_token, :client_id, :user_id, :expires, :scope)', $this->config['refresh_token_table']);

        return $this->dbQuery($sql, compact('refresh_token', 'client_id', 'user_id', 'expires', 'scope'));
    }

    /**
     * @param  $refresh_token
     * @return mixed
     */
    public function unsetRefreshToken($refresh_token)
    {
        $sql = sprintf('DELETE FROM %s WHERE refresh_token = :refresh_token', $this->config['refresh_token_table']);

        return $this->dbQuery($sql, compact('refresh_token'));
    }

    /**
     * Not implement. This function for test purpose only
     *
     * @param  $user
     * @param  $password
     * @return mixed
     */
    protected function checkPassword($user, $password)
    {
        return false;
    }

    /**
     * @param $email
     */
    public function getUser($email)
    {
        $sql = sprintf('SELECT * from %s where email=:email', $this->config['user_table']);

        if (!$userInfo = $this->dbFetchRow($sql, compact('email'), Zend_Db::FETCH_ASSOC)) {
            return false;
        }

        return $userInfo;
    }

    /**
     * Not implement. This function for test purpose only
     *
     * @param  $email
     * @param  $password
     * @param  $firstName
     * @param  null         $lastName
     * @return mixed
     */
    public function setUser($email, $password, $firstName = null, $lastName = null)
    {
        return false;
    }

    /* ScopeInterface */
    /**
     * @param  $scope
     * @return mixed
     */
    public function scopeExists($scope)
    {
        $scope = explode(' ', $scope);
        $whereIn = implode(',', array_fill(0, count($scope), '?'));
        $sql = sprintf('SELECT count(scope) as count FROM %s WHERE scope IN (%s)', $this->config['scope_table'], $whereIn);

        if ($result = $this->dbFetchRow($sql, $scope, Zend_Db::FETCH_ASSOC)) {
            return $result['count'] == count($scope);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSupportedScopes()
    {
        $sql = sprintf('SELECT scope FROM %s', $this->config['scope_table']);

        if ($result = $this->dbFetchAll($sql, null, Zend_Db::FETCH_ASSOC)) {
            $scopes = array_map(function ($row) {
                return $row['scope'];
            }, $result);

            return $scopes;
        }

        return null;
    }

    /**
     * @param  $client_id
     * @return mixed
     */
    public function getDefaultScope($client_id = null)
    {
        $sql = sprintf('SELECT scope FROM %s WHERE is_default=:is_default', $this->config['scope_table']);

        if ($result = $this->dbFetchAll($sql, array('is_default' => true), Zend_Db::FETCH_ASSOC)) {
            $defaultScope = array_map(function ($row) {
                return $row['scope'];
            }, $result);

            return implode(' ', $defaultScope);
        }

        return null;
    }

    /* JWTBearerInterface */
    /**
     * @param  $client_id
     * @param  $subject
     * @return mixed
     */
    public function getClientKey($client_id, $subject)
    {
        $sql = sprintf('SELECT public_key from %s where client_id=:client_id AND subject=:subject', $this->config['jwt_table']);

        return $this->dbFetchCol($sql, array('client_id' => $client_id, 'subject' => $subject));
    }

    /**
     * @param  $client_id
     * @return mixed
     */
    public function getClientScope($client_id)
    {
        if (!$clientDetails = $this->getClientDetails($client_id)) {
            return false;
        }

        if (isset($clientDetails['scope'])) {
            return $clientDetails['scope'];
        }

        return null;
    }

    /**
     * @param $client_id
     * @param $subject
     * @param $audience
     * @param $expires
     * @param $jti
     */
    public function getJti($client_id, $subject, $audience, $expires, $jti)
    {
        $sql = sprintf('SELECT * FROM %s WHERE issuer=:client_id AND subject=:subject AND audience=:audience AND expires=:expires AND jti=:jti', $this->config['jti_table']);

        if ($result = $this->dbFetchRow($sql, compact('client_id', 'subject', 'audience', 'expires', 'jti'), Zend_Db::FETCH_ASSOC)) {
            return array(
                'issuer' => $result['issuer'],
                'subject' => $result['subject'],
                'audience' => $result['audience'],
                'expires' => $result['expires'],
                'jti' => $result['jti'],
            );
        }

        return null;
    }

    /**
     * @param  $client_id
     * @param  $subject
     * @param  $audience
     * @param  $expires
     * @param  $jti
     * @return mixed
     */
    public function setJti($client_id, $subject, $audience, $expires, $jti)
    {
        $sql = sprintf('INSERT INTO %s (issuer, subject, audience, expires, jti) VALUES (:client_id, :subject, :audience, :expires, :jti)', $this->config['jti_table']);

        return $this->dbQuery($sql, compact('client_id', 'subject', 'audience', 'expires', 'jti'));
    }

    /* PublicKeyInterface */
    /**
     * @param  $client_id
     * @return mixed
     */
    public function getPublicKey($client_id = null)
    {
        $sql = sprintf('SELECT public_key FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']);

        if ($result = $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC)) {
            return $result['public_key'];
        }
    }

    /**
     * @param  $client_id
     * @return mixed
     */
    public function getPrivateKey($client_id = null)
    {
        $sql = sprintf('SELECT private_key FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']);

        if ($result = $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC)) {
            return $result['private_key'];
        }
    }

    /**
     * @param  $client_id
     * @return mixed
     */
    public function getEncryptionAlgorithm($client_id = null)
    {
        $sql = sprintf('SELECT encryption_algorithm FROM %s WHERE client_id=:client_id OR client_id IS NULL ORDER BY client_id IS NOT NULL DESC', $this->config['public_key_table']);

        if ($result = $this->dbFetchRow($sql, compact('client_id'), Zend_Db::FETCH_ASSOC)) {
            return $result['encryption_algorithm'];
        }

        return 'RS256';
    }
}
