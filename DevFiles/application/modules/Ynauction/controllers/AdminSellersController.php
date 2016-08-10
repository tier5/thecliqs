<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminManageController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminSellersController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_sellers');

    $page = $this->_getParam('page',1);
    $this->view->form = $form = new Ynauction_Form_Admin_SearchBecome();   
    $values = array();  
    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
      if(empty($values['order']) ) {
        $values['order'] = 'become_id';
        }
        if( empty($values['direction']) ) {
        $values['direction'] = 'DESC';
        }
        $this->view->filterValues = $values;
        $this->view->order = $values['order'];
        $this->view->direction = $values['direction'];
      $table = Engine_Api::_()->getDbTable('becomes', 'ynauction');
      $becomes = $table->fetchAll(Engine_Api::_()->ynauction()->getBecomeSelect($values))->toArray();
      $this->view->count = count($becomes);
    } 
    $limit = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 10);
    $values['limit'] = $limit;
    $this->view->paginator = Engine_Api::_()->ynauction()->getBecomePaginator($values); 
    $this->view->paginator->setCurrentPageNumber($page);
    $this->view->formValues = $values; 
  }
  public function approveAction()
  {
      if( !$this->_helper->requireUser()->isValid() ) return;
      $become_id = $this->_getParam('become_id'); 
      $become = Engine_Api::_()->getItem('ynauction_become', $become_id);
      $viewer = Engine_Api::_()->user()->getViewer();   
       if($become)
          {
               $become->approved = 1;
               $become->save();  
               $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 1);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $become->user_id);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $become, 'ynauction_approved_become', array());
                } 
          }
          $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Approve successfully.'))
                  ));
  }
  public function denyAction()
  {
        if( !$this->_helper->requireUser()->isValid() ) return;
      $become_id = $this->_getParam('become_id'); 
      $become = Engine_Api::_()->getItem('ynauction_become', $become_id);
      $viewer = Engine_Api::_()->user()->getViewer();   
       if($become)
          {
               $become->approved = -1;
               $become->save();
               $notify =  Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 1);
                if($notify == 1)
                {
                     //send notify 
                     //Send sell
                     $productOwner = Engine_Api::_()->getItem('user', $become->user_id);
                     $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                     @$notifyApi->addNotification($productOwner, $viewer, $become, 'ynauction_denied_become', array(
                        ));
                } 
          }
          $this->_forward('success', 'utility', 'core', array(
                  'smoothboxClose' => true,
                  'parentRefresh' => true,
                  'format'=> 'smoothbox',
                  'messages' => array($this->view->translate('Deny successfully.'))
                  ));
  }
}
