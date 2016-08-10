<?php

/**
 * class Ynrestapi_AdminManageController
 */
class Ynrestapi_AdminManageController extends Core_Controller_Action_Admin
{
    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynrestapi_admin_main', array(), 'ynrestapi_admin_main_manage');

        $clientTable = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi');
        $select = $clientTable->select()->order('timestamp ASC');
        $this->view->clients = $clientTable->fetchAll($select);
    }

    /**
     * @return mixed
     */
    public function deleteAction()
    {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->client_id = $id;
        $clientTable = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi');
        $client = $clientTable->find($id)->current();

        if (!$client) {
            throw new Engine_Exception('No client found');
        }

        // Check post
        if (!$this->getRequest()->isPost()) {
            $this->renderScript('admin-manage/delete.tpl');
            return;
        }

        // Process
        $db = $clientTable->getAdapter();
        $db->beginTransaction();

        try {
            $client->delete();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_forward('success', 'utility', 'core', array(
            'smoothboxClose' => 10,
            'parentRefresh' => 10,
            'messages' => array(''),
        ));
    }

    /**
     * @return mixed
     */
    public function createAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynrestapi_admin_main', array(), 'ynrestapi_admin_main_manage');

        // Generate and assign form
        $form = $this->view->form = new Ynrestapi_Form_Admin_Manage_Create();

        // Populate scopes
        $scopeTable = Engine_Api::_()->getDbtable('oauthScopes', 'ynrestapi');
        $select = $scopeTable->select()->order('is_default DESC');
        $scopes = $scopeTable->fetchAll($select);
        $scopeOptions = array();
        foreach ($scopes as $key => $value) {
            $scopeOptions[$value['scope']] = ucfirst($value['scope']);
        }
        $form->scope->setMultiOptions($scopeOptions);

        // Check post
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();
        $insert = array(
            'client_id' => Ynrestapi_Helper_Utils::generateRandomString(15),
            'client_secret' => Ynrestapi_Helper_Utils::generateRandomString(32),
            'redirect_uri' => $values['redirect_uri'],
            'grant_types' => !empty($values['grant_types']) ? implode(' ', $values['grant_types']) : null,
            'scope' => !empty($values['scope']) ? implode(' ', $values['scope']) : null,
            'title' => $values['title'],
            'timestamp' => time(),
        );

        $clientTable = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi');
        $db = $clientTable->getAdapter();
        $db->beginTransaction();

        try {
            $clientTable->insert($insert);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        return $this->_helper->redirector->gotoRoute(array('action' => 'index'));
    }

    /**
     * @return mixed
     */
    public function editAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynrestapi_admin_main', array(), 'ynrestapi_admin_main_manage');

        if (null === ($clientId = $this->_getParam('client_id')) ||
            !($client = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi')->find($clientId)->current())) {
            throw new Engine_Exception('No client found');
        }

        // Make form
        $this->view->form = $form = new Ynrestapi_Form_Admin_Manage_Edit();

        // Populate scopes
        $scopeTable = Engine_Api::_()->getDbtable('oauthScopes', 'ynrestapi');
        $select = $scopeTable->select()->order('is_default DESC');
        $scopes = $scopeTable->fetchAll($select);
        $scopeOptions = array();
        foreach ($scopes as $key => $value) {
            $scopeOptions[$value['scope']] = ucfirst($value['scope']);
        }
        $form->scope->setMultiOptions($scopeOptions);

        // Populate form
        $values = $client->toArray();

        if (!empty($client['grant_types'])) {
            $values['grant_types'] = explode(' ', $client['grant_types']);
        }

        if (!empty($client['scope'])) {
            $values['scope'] = explode(' ', $client['scope']);
        }

        $form->populate($values);

        // Check method/data
        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            return;
        }

        // Process
        $values = $form->getValues();
        $update = array(
            'redirect_uri' => $values['redirect_uri'],
            'grant_types' => !empty($values['grant_types']) ? implode(' ', $values['grant_types']) : null,
            'scope' => !empty($values['scope']) ? implode(' ', $values['scope']) : null,
            'title' => $values['title'],
        );

        $clientTable = Engine_Api::_()->getDbtable('oauthClients', 'ynrestapi');
        $db = $clientTable->getAdapter();
        $db->beginTransaction();

        try {
            // Update client
            $client->setFromArray($update);
            $client->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }

        $form->addNotice('Your changes have been saved.');
    }
}
