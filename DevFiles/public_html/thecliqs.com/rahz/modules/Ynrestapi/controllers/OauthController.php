<?php

/**
 *
 * @author An Nguyen <annt@younetco.com>
 *
 * @see http://bshaffer.github.io/oauth2-server-php-docs/
 */
class Ynrestapi_OauthController extends Core_Controller_Action_Standard
{
    /**
     * Authorize Controller
     *
     * For the Authorize Endpoint, which requires the user to authenticate and
     * redirects back to the client with an authorization code
     * (Authorization Code grant type) or access token (Implicit grant type).
     */
    public function authorizeAction()
    {
        if (!Engine_Api::_()->user()->getViewer()->getIdentity()) {
            $session = new Zend_Session_Namespace('Redirect');
            $session->uri = $this->view->serverUrl() . $_SERVER['REQUEST_URI'];
            $session->options = array();
            return $this->_helper->redirector->gotoRoute(array(), 'user_login', true);
        }

        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        $server = Ynrestapi_Service_Oauth_Server::getServer();

        // validate the authorize request
        if (!$server->validateAuthorizeRequest($request, $response)) {
            $response->send();
            exit();
        }

        // display an authorization form
        if (empty($_POST)) {
            return $this->prepareAuthorizeForm($request, $server);
        }

        // print the authorization code if the user has authorized your client
        $is_authorized = ($_POST['authorized'] === 'yes');
        $userid = Engine_Api::_()->user()->getViewer()->getIdentity();
        $server->handleAuthorizeRequest($request, $response, $is_authorized, $userid);
        $response->send();
    }

    /**
     * @param $request
     * @param $server
     */
    public function prepareAuthorizeForm($request, $server)
    {
        $clientId = $request->query('client_id');
        $clientTable = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi');
        $client = $clientTable->find($clientId)->current();

        $authorizeScope = $server->getAuthorizeController()->getScope();
        $authorizeScopes = explode(' ', $authorizeScope);

        $this->view->assign(array(
            'client' => $client,
            'authorizeScopes' => $authorizeScopes,
        ));
    }

    /**
     * Token Controller
     *
     * For the Token Endpoint, which uses the configured Grant Types to return
     * an access token to the client.
     */
    public function tokenAction()
    {
        $request = OAuth2\Request::createFromGlobals();
        $response = new OAuth2\Response();
        $server = Ynrestapi_Service_Oauth_Server::getServer();

        $server->handleTokenRequest($request, $response)->send();
        exit();
    }
}
