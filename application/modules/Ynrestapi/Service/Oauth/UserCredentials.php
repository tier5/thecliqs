<?php

use OAuth2\GrantType\GrantTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\ResponseType\AccessTokenInterface;
use OAuth2\Storage\UserCredentialsInterface;

/**
 *
 * @author An Nguyen <annt@younetco.com>
 *
 * @see http://bshaffer.github.io/oauth2-server-php-docs/
 */
class Ynrestapi_Service_Oauth_UserCredentials implements GrantTypeInterface
{
    /**
     * @var mixed
     */
    private $userInfo;

    /**
     * @var mixed
     */
    protected $storage;

    /**
     * @param OAuth2\Storage\UserCredentialsInterface $storage REQUIRED Storage class for retrieving user credentials information
     */
    public function __construct(UserCredentialsInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getQuerystringIdentifier()
    {
        return 'password';
    }

    /**
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     */
    public function validateRequest(RequestInterface $request, ResponseInterface $response)
    {
        // support login by social provider
        if (null != $request->request('provider')) {
            $provider = strtolower($request->request('provider'));
            $validProviders = array(
                'facebook',
                'twitter',
            );

            if (!in_array($provider, $validProviders)) {
                $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid parameter') . ': provider');
                return null;
            }

            $methodName = '_checkUser' . ucfirst($provider) . 'Credentials';

            if (!$this->{$methodName}($request, $response)) {
                return null;
            }
        } elseif (!$this->checkUserCredentials($request->request('email'), $request->request('password'), $response)) {
            return null;
        }

        $this->userInfo = array(
            'user_id' => $response->getParameter('user_id'),
        );

        return true;
    }

    public function getClientId()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userInfo['user_id'];
    }

    public function getScope()
    {
        return isset($this->userInfo['scope']) ? $this->userInfo['scope'] : null;
    }

    /**
     * @param  AccessTokenInterface $accessToken
     * @param  $client_id
     * @param  $user_id
     * @param  $scope
     * @return mixed
     */
    public function createAccessToken(AccessTokenInterface $accessToken, $client_id, $user_id, $scope)
    {
        return $accessToken->createAccessToken($client_id, $user_id, $scope);
    }

    /**
     * @param $email
     * @param $password
     * @param $response
     */
    public function checkUserCredentials($email, $password, $response)
    {
        // check for email and password existence
        if (empty($email)) {
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter') . ': email');
            return false;
        }

        if (empty($password)) {
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter') . ': password');
            return false;
        }

        // Check login creds
        $user_table = Engine_Api::_()->getDbtable('users', 'user');
        $user_select = $user_table->select()
            ->where('email = ?', $email); // If post exists
        $user = $user_table->fetchRow($user_select);

        // Get ip address
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        // Check if user exists
        if (empty($user)) {
            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'no-member',
            ));

            $response->setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));
            return false;
        }

        // Check if user is verified and enabled
        if (!$user->enabled) {
            if (!$user->verified) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires either email verification.'));
                return false;
            } else if (!$user->approved) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires admin approval.'));
                return false;
            }
            // Should be handled by hooks or payment
            //return;
        }

        // Handle subscriptions
        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            // Check for the user's plan
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if (!$subscriptionsTable->check($user)) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'unpaid',
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires subscription.'));
                return false;
            }
        }

        // Run pre login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginBefore', $user);
        foreach ((array) $event->getResponses() as $response) {
            if (is_array($response)) {
                if (!empty($response['error']) && !empty($response['message'])) {
                    // Register login
                    Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                        'user_id' => $user->getIdentity(),
                        'email' => $email,
                        'ip' => $ipExpr,
                        'timestamp' => new Zend_Db_Expr('NOW()'),
                        'state' => 'third-party',
                    ));

                    $response->setError(500, 'exception_error', $response['message']);
                    return false;
                }

                // Return
                $errorDebug = array(
                    'message' => 'third-party',
                );
                $response->setError(500, 'exception_error', Zend_Registry::get('Zend_Translate')->_('An error has occurred.'), null, $errorDebug);
                return false;
            }
        }

        // Version 3 Import compatibility
        if (empty($user->password)) {
            $compat = Engine_Api::_()->getApi('settings', 'core')->getSetting('core.compatibility.password');
            $migration = null;
            try {
                $migration = Engine_Db_Table::getDefaultAdapter()->select()
                    ->from('engine4_user_migration')
                    ->where('user_id = ?', $user->getIdentity())
                    ->limit(1)
                    ->query()
                    ->fetch();
            } catch (Exception $e) {
                $migration = null;
                $compat = null;
            }
            if (!$migration) {
                $compat = null;
            }

            if ($compat == 'import-version-3') {

                // Version 3 authentication
                $cryptedPassword = self::_version3PasswordCrypt($migration['user_password_method'], $migration['user_code'], $password);
                if ($cryptedPassword === $migration['user_password']) {
                    // Regenerate the user password using the given password
                    $user->salt = (string) rand(1000000, 9999999);
                    $user->password = $password;
                    $user->save();
                    Engine_Api::_()->user()->getAuth()->getStorage()->write($user->getIdentity());
                    // @todo should we delete the old migration row?
                } else {
                    $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
                    return false;
                }
                // End Version 3 authentication

            } else {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'v3-migration',
                ));

                $response->setError(500, 'exception_error', Zend_Registry::get('Zend_Translate')->_('There appears to be a problem logging in. Please reset your password with the Forgot Password link.'));
                return false;
            }
        }

        // Normal authentication
        else {
            $authResult = Engine_Api::_()->user()->authenticate($email, $password);
            $authCode = $authResult->getCode();
            Engine_Api::_()->user()->setViewer();

            if ($authCode != Zend_Auth_Result::SUCCESS) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'bad-password',
                ));

                $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
                return false;
            }
        }

        // -- Success! --

        // Register login
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'active' => true,
        ));
        $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('user.logins');

        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $viewer->lastlogin_date = date('Y-m-d H:i:s');
            if ('cli' !== PHP_SAPI) {
                $viewer->lastlogin_ip = $ipExpr;
            }
            $viewer->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $viewer, 'login');
        }

        // Run post login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);

        $response->setParameter('user_id', $user->getIdentity());
        return true;
    }

    /**
     * @param  $request
     * @param  $response
     * @return mixed
     */
    private function _checkUserFacebookCredentials($request, $response)
    {
        if (!$request->request('uid')) {
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter') . ': uid');
            return false;
        }

        $facebookTable = Engine_Api::_()->getDbtable('facebook', 'user');
        $userId = $facebookTable->select()
            ->from($facebookTable, 'user_id')
            ->where('facebook_uid = ?', $request->request('uid'))
            ->query()
            ->fetchColumn();

        if ($userId) {
            // WHEN USER USING FACEBOOK CONNECTION ALREADY
            $user = Engine_Api::_()->getItem('user', $userId);
            if (!$user->getIdentity()) {
                // SHOULD SIGN UP HERE
                $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
                return false;
            }
        } elseif ($request->request('email')) {
            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userSelect = $userTable->select()->where('email = ?', $request->request('email'));
            $user = $userTable->fetchRow($userSelect);

            if (is_object($user) && $user->getIdentity()) {
                //THIS USER SIGNED UP OUR SYSTEM BEFORE
                if ($request->request('force_connect')) {
                    Ynrestapi_Api_User::updateAgentForFacebook($user, $request->request('uid'));
                } else {
                    $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Email existed'));
                    return false;
                }
            }
        } else {
            // SHOULD SIGN UP HERE
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
            return false;
        }

        return $this->_loginBySource($user, 'facebook', $response);
    }

    /**
     * @param  $request
     * @param  $response
     * @return mixed
     */
    private function _checkUserTwitterCredentials($request, $response)
    {
        if (!$request->request('uid')) {
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Missing parameter') . ': uid');
            return false;
        }

        $twitterTable = Engine_Api::_()->getDbtable('twitter', 'user');
        $userId = $twitterTable->select()
            ->from($twitterTable, 'user_id')
            ->where('twitter_uid = ?', $request->request('uid'))
            ->query()
            ->fetchColumn();

        if ($userId) {
            // WHEN USER USING FACEBOOK CONNECTION ALREADY
            $user = Engine_Api::_()->getItem('user', $userId);
            if (!$user->getIdentity()) {
                // SHOULD SIGN UP HERE
                $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
                return false;
            }
        } elseif ($request->request('email')) {
            $userTable = Engine_Api::_()->getDbtable('users', 'user');
            $userSelect = $userTable->select()->where('email = ?', $request->request('email'));
            $user = $userTable->fetchRow($userSelect);

            if (is_object($user) && $user->getIdentity()) {
                //THIS USER SIGNED UP OUR SYSTEM BEFORE
                if ($request->request('force_connect')) {
                    Ynrestapi_Api_User::updateAgentForTwitter($user, $request->request('uid'));
                } else {
                    $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('Email existed'));
                    return false;
                }
            }
        } else {
            // SHOULD SIGN UP HERE
            $response->setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Invalid credentials'));
            return false;
        }

        return $this->_loginBySource($user, 'twitter', $response);
    }

    /**
     * @param  $user
     * @param  $source
     * @param  $response
     * @return mixed
     */
    private function _loginBySource($user, $source, $response)
    {
        $userId = $user->getIdentity();
        Zend_Auth::getInstance()->getStorage()->write($userId);

        // Get ip address
        $db = Engine_Db_Table::getDefaultAdapter();
        $ipObj = new Engine_IP();
        $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

        // Check if user exists
        if (empty($user)) {
            // Register login
            Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                'email' => $email,
                'ip' => $ipExpr,
                'timestamp' => new Zend_Db_Expr('NOW()'),
                'state' => 'no-member',
                'source' => $source,
            ));

            $response->setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('No record of a member with that email was found.'));
            return false;
        }

        // Check if user is verified and enabled
        if (!$user->enabled) {
            if (!$user->verified) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                    'source' => $source,
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires either email verification.'));
                return false;
            } else if (!$user->approved) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'disabled',
                    'source' => $source,
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires admin approval.'));
                return false;
            }
            // Should be handled by hooks or payment
            //return;
        }

        // Handle subscriptions
        if (Engine_Api::_()->hasModuleBootstrap('payment')) {
            // Check for the user's plan
            $subscriptionsTable = Engine_Api::_()->getDbtable('subscriptions', 'payment');
            if (!$subscriptionsTable->check($user)) {
                // Register login
                Engine_Api::_()->getDbtable('logins', 'user')->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'unpaid',
                    'source' => $source,
                ));

                $response->setError(403, 'forbidden', Zend_Registry::get('Zend_Translate')->_('This account still requires subscription.'));
                return false;
            }
        }

        // -- Success! --

        // Register login
        $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
        $loginTable->insert(array(
            'user_id' => $user->getIdentity(),
            'email' => $email,
            'ip' => $ipExpr,
            'timestamp' => new Zend_Db_Expr('NOW()'),
            'state' => 'success',
            'source' => $source,
            'active' => true,
        ));
        $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

        // Increment sign-in count
        Engine_Api::_()->getDbtable('statistics', 'core')
            ->increment('user.logins');

        // Test activity @todo remove
        $viewer = Engine_Api::_()->user()->getViewer();
        if ($viewer->getIdentity()) {
            $viewer->lastlogin_date = date('Y-m-d H:i:s');
            if ('cli' !== PHP_SAPI) {
                $viewer->lastlogin_ip = $ipExpr;
            }
            $viewer->save();
            Engine_Api::_()->getDbtable('actions', 'activity')
                ->addActivity($viewer, $viewer, 'login');
        }

        // Run post login hook
        $event = Engine_Hooks_Dispatcher::getInstance()->callEvent('onUserLoginAfter', $viewer);

        $response->setParameter('user_id', $user->getIdentity());
        return true;
    }
}
