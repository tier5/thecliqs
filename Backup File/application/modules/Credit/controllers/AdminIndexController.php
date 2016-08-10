<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 03.01.12 12:52 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminIndexController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_index');
  }

  public function indexAction()
  {
    $this->view->formFilter = $formFilter = new Credit_Form_Admin_Filter(array('groupType' => $this->_getParam('group_type', null)));

    $page = $this->_getParam('page', 1);

    // Process form
    $values = array();
    if ($formFilter->isValid($this->_getAllParams())) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'log_id',
      'order_direction' => 'DESC',
      'page' => $page
    ), $values);

    /**
     * @var $logsTbl Credit_Model_DbTable_Logs
     */

    $logsTbl = Engine_Api::_()->getDbTable('logs', 'credit');

    $this->view->assign($values);
    $valuesCopy = array_filter($values);
    $this->view->paginator = $logsTbl->getTransaction($values);
    $this->view->formValues = $valuesCopy;
  }

  public function typesAction()
  {
    /**
     * @var $actionTypes Credit_Model_DbTable_ActionTypes
     */
    $translate = Zend_Registry::get('Zend_Translate');
    $type = $this->_getParam('type', '');
    $actionTypes = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $types = $actionTypes->getActionTypesByGroupType($type);
    $select_box = '';
    foreach ($types as $key => $value) {
      $select_box .= '<option label="'. $translate->_($value) .'" value="'. $key .'">'. $translate->_($value) .'</option>';
    }
    $this->view->html = $select_box;
    return;
  }

  public function deleteAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Logs
     */
    $this->_helper->layout->setLayout('default-simple');
    $table = Engine_Api::_()->getDbTable('logs', 'credit');
    $this->view->form = new Credit_Form_Admin_LogDelete();
    $log_id = $this->_getParam('log_id', 0);
    $log = $table->findRow($log_id);
    if (!$log) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_("Log doesn't exists or not authorized to delete");
      return 0;
    }

    if (!$this->getRequest()->isPost()) {
      $this->view->status = false;
      $this->view->error = Zend_Registry::get('Zend_Translate')->_('Invalid request method');
      return 0;
    }

    $db = $table->getAdapter();
    $db->beginTransaction();
    try {
      $log->delete();
      $db->commit();
    }
    catch (Exception $e) {
      $db->rollBack();
      throw $e;
    }

    $this->_forward('success', 'utility', 'core', array(
      'messages' => Array(Zend_Registry::get('Zend_Translate')->_('Log has been deleted.')),
      'layout' => 'default-simple',
      'parentRefresh' => true,
    ));
  }
}
