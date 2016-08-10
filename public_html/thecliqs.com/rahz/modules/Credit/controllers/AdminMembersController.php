<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminMembersController.php 05.01.12 15:43 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminMembersController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    /**
     * @var $table Credit_Model_DbTable_Balances
     **/

    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_members');

    $table = Engine_Api::_()->getDbTable('balances', 'credit');

    $this->view->formFilter = $formFilter = new Credit_Form_Admin_Members_Filter();

    $page = $this->_getParam('page', 1);

    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
      $values = $formFilter->getValues();
    }

    foreach( $values as $key => $value ) {
      if( null === $value ) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'balance_id',
      'order_direction' => 'DESC',
      'page' => $page
    ), $values);

    $this->view->assign($values);
    $valuesCopy = array_filter($values);

    $this->view->paginator = $paginator = $table->getMembers($values);
    $this->view->formValues = $valuesCopy;
  }

  public function editAction()
  {
    /**
     * @var $balance Credit_Model_Balance
     */

    $user_id = $this->_getParam('user_id');
    $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
    $this->view->custom_nav = $this->getNavigation($user_id);
    $balance = Engine_Api::_()->getItem('credit_balance', $user->getIdentity());

    $this->view->form = $form = new Credit_Form_Admin_Members_Edit();
    if (!$this->getRequest()->isPost()) {
      $values = array(
        'current_credit' => $balance->current_credit,
        'earned_credit' => $balance->earned_credit,
        'spent_credit' => $balance->spent_credit
      );
      $form->populate($values);
      return ;
    }

    $params = $this->getRequest()->getParams();
    $values['current_credit'] = $params['current_credit'];
    $values['earned_credit'] = $params['earned_credit'];
    $values['spent_credit'] = $params['spent_credit'];

    $balance->setFromArray($values);
    $balance->save();

    $form->populate($balance->toArray());
    $form->addNotice(Zend_Registry::get('Zend_Translate')->_('Your changes successfully saved!'));
  }

  public function transactionAction()
  {
    $user_id = $this->_getParam('user_id');
    $this->view->user = $user = Engine_Api::_()->getItem('user', $user_id);
    $this->view->custom_nav = $this->getNavigation($user_id);

    $this->view->formFilter = $formFilter = new Credit_Form_Admin_Filter(array('groupType' => $this->_getParam('group_type', null)));
    $formFilter->removeElement('displayname');
    $page = $this->_getParam('page', 1);
    // Process form
    $values = array();
    if( $formFilter->isValid($this->_getAllParams()) ) {
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
      'page' => $page,
      'user_id' => $user_id
    ), $values);

    /**
     * @var $logTbl Credit_Model_DbTable_Logs
     */

    $logTbl = Engine_Api::_()->getDbTable('logs', 'credit');

    $this->view->assign($values);
    $valuesCopy = array_filter($values);
    $this->view->paginator = $logTbl->getTransaction($values);
    $this->view->formValues = $valuesCopy;
  }

  public function getNavigation($user_id)
  {
 		$navigation = new Zend_Navigation();
 		$navigation->addPages(array(
       array(
         'label' => "Edit Member's Credit",
         'route' => 'admin_members_credit',
         'action' => 'edit',
         'params' => array('user_id' => $user_id)
       ),
       array(
         'label' => "Credits Transactions",
         'route' => 'admin_members_credit',
         'action' => 'transaction',
         'params' => array('user_id' => $user_id)
       )
    ));

 		return $navigation;
 	}
}
