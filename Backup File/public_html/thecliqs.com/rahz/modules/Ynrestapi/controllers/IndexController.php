<?php

define('YNRESTAPI_DEBUG', false);

if (YNRESTAPI_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    ini_set('error_reporting', E_ALL);
}

/**
 *
 * @author An Nguyen <annt@younetco.com>
 */
class Ynrestapi_IndexController extends Core_Controller_Action_Standard
{
    /**
     * @var mixed
     */
    private $_requestMethod;

    /**
     * @var mixed
     */
    private $_params;

    /**
     * @var object $_apiClass
     */
    private $_apiClass;

    /**
     * @var string $_apiMethod
     */
    private $_apiMethod;

    /**
     * @var array
     */
    private $_apiMaps = array(
        'activities' => 'activity',
        'albums' => 'album',
        'blogs' => 'blog',
        'classifieds' => 'classified',
        'core' => 'core',
        // 'forums' => 'forum',
        'groups' => 'group',
        'messages' => 'message',
        'notifications' => 'notification',
        'users' => 'user',
        'videos' => 'video',
        'music' => 'music',
        'events' => 'event',
    );

    /**
     * indexAction
     */
    public function indexAction()
    {
        $this->_disableLayout();

        $this->_setApiClass();
        $this->_setApiMethod();
        $this->_processApi();
    }

    /**
     * methodAction
     */
    public function methodAction()
    {
        $this->_disableLayout();

        $method = $this->_getParam('method');
        $this->_setApiClass();
        $this->_setApiMethod($method);
        $this->_processApi();
    }

    /**
     * meAction
     */
    public function meAction()
    {
        $this->_disableLayout();

        $method = $this->_getParam('method');
        $this->_setApiClass('users');
        $this->_setApiMethod('me_' . $method);
        $this->_processApi();
    }

    /**
     * defaultAction
     */
    public function defaultAction()
    {
        $this->_disableLayout();

        Ynrestapi_Api_Base::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Bad Request'));
        Ynrestapi_Api_Base::sendResponse();
    }

    private function _disableLayout()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }

    /**
     * Process api
     */
    private function _processApi()
    {
        $this->_params = $this->_getAllParams();

        if ($this->_requestMethod == 'put') {
            $putParams = array();
            parse_str(file_get_contents('php://input'), $putParams);
            $this->_params = array_merge($this->_params, $putParams);
        }

        $this->_setViewer($this->_params);

        try {
            $this->_apiClass->{$this->_apiMethod}($this->_params);
        } catch (Exception $e) {
            Ynrestapi_Api_Base::setExceptionError($e);
        }

        Ynrestapi_Api_Base::sendResponse();
    }

    /**
     * Set viewer by token
     *
     * @param $params
     */
    private function _setViewer($params)
    {
        // clear
        $user = Engine_Api::_()->user()->getViewer();
        if ($user->getIdentity()) {
            Engine_Api::_()->user()->getAuth()->clearIdentity();
            Engine_Api::_()->user()->setViewer(null);
        }

        if (null != ($token = Ynrestapi_Service_Oauth_Server::getAccessTokenData())) {
            if (!empty($token['user_id'])) {
                $userId = (int) $token['user_id'];
                $user = Engine_Api::_()->user()->getUser($userId);

                // Get ip address
                $db = Engine_Db_Table::getDefaultAdapter();
                $ipObj = new Engine_IP();
                $ipExpr = new Zend_Db_Expr($db->quoteInto('UNHEX(?)', bin2hex($ipObj->toBinary())));

                $oStorage = new Zend_Auth_Storage_Session();
                $oStorage->write($user->getIdentity());
                Engine_Api::_()->user()->setViewer();

                // Register login
                $loginTable = Engine_Api::_()->getDbtable('logins', 'user');
                $loginTable->insert(array(
                    'user_id' => $user->getIdentity(),
                    'email' => $user->email,
                    'ip' => $ipExpr,
                    'timestamp' => new Zend_Db_Expr('NOW()'),
                    'state' => 'success',
                    'active' => true,
                ));
                $_SESSION['login_id'] = $login_id = $loginTable->getAdapter()->lastInsertId();

                // Increment sign-in count
                Engine_Api::_()->getDbtable('statistics', 'core')
                    ->increment('user.logins');
            }
        }
    }

    /**
     * Set api method
     */
    private function _setApiMethod($method = '')
    {
        $validMethods = array(
            'get',
            'post',
            'delete',
        );

        $this->_requestMethod = isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : '';

        if (!isset($this->_requestMethod) || !in_array($this->_requestMethod, $validMethods)) {
            Ynrestapi_Api_Base::setError(400, 'bad_request', Zend_Registry::get('Zend_Translate')->_('Bad Request'));
            Ynrestapi_Api_Base::sendResponse();
        }

        $apiMethod = $this->_requestMethod . str_replace(' ', '', ucwords(strtolower(str_replace('_', ' ', $method))));

        if (!method_exists($this->_apiClass, $apiMethod)) {
            Ynrestapi_Api_Base::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Service Not Found'));
            Ynrestapi_Api_Base::sendResponse();
        }

        $this->_apiMethod = $apiMethod;
    }

    /**
     * Set api class
     */
    private function _setApiClass($name = null)
    {
        if (empty($name)) {
            $name = $this->_getParam('name');
        }

        $apiName = $this->_getApiName($name);
        $className = 'Ynrestapi_Api_' . Engine_Api::_()->inflect($apiName);

        if (!class_exists($className)) {
            Ynrestapi_Api_Base::setError(404, 'not_found', Zend_Registry::get('Zend_Translate')->_('Service Not Found'));
            Ynrestapi_Api_Base::sendResponse();
        }

        $this->_apiClass = new $className();
    }

    /**
     * Get mapped api name
     *
     * @param  string   $apiName
     * @return string
     */
    private function _getApiName($apiName)
    {
        if (array_key_exists($apiName, $this->_apiMaps)) {
            return $this->_apiMaps[$apiName];
        }

        return $apiName;
    }
}
