<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package     Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminFieldsController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminFieldsController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType = 'ynauction_product';

  protected $_requireProfileType = false;

  public function indexAction()
  {
    // Make navigation
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_fields');
    
    parent::indexAction();
  }

  public function fieldCreateAction(){
    parent::fieldCreateAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Add Auction Question');
      $display = $form->getElement('display');
      $display->setLabel('Show on auction page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on auction page',
          0 => 'Hide on auction page'
        )));
    }
    if(isset($form->search)){
        $form->removeElement('search');
    }
    if(isset($form->show)){
        $form->removeElement('show');
    }
  }

  public function fieldEditAction(){
    parent::fieldEditAction();
    // remove stuff only relavent to profile questions
    $form = $this->view->form;

    if($form){
      $form->setTitle('Edit Auction Question');

      $display = $form->getElement('display');
      $display->setLabel('Show on auction page?');
      $display->setOptions(array('multiOptions' => array(
          1 => 'Show on auction page',
          0 => 'Hide on auction page'
        )));
      $form->removeElement('search');
      $form->removeElement('show');
    }
  }
}