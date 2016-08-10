<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminAssignCreditsController.php 04.01.12 13:07 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminAssignCreditsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_assignCredits');
  }

  public function indexAction()
  {
    /**
     * @var $table Credit_Model_DbTable_ActionTypes
     */

    $table = Engine_Api::_()->getDbTable('actionTypes', 'credit');
    $this->view->actionTypes = $table->getActionTypes(array('action_module' => 'ASC'));

    if (!$this->getRequest()->isPost()) {
      return ;
    }

    $params = $this->_getAllParams();

    foreach ($params as $key => $param) {
      $row = explode("-", $key);
      $param = (int)$param;
      if ($param < 0) {
        $param *= -1;
      }
      if (isset($row[1])) {
        $table->update(array(
          $row[0] => $param
        ), array(
          'action_id = ?' => $row[1]
        ));
      }
    }

    $this->_redirectCustom(
      $this->view->url(
        array(
          'module' => 'credit',
          'controller' => 'assign-credits',
          'action' => 'index'
        ), 'admin_default', true
      )
    );
  }
}