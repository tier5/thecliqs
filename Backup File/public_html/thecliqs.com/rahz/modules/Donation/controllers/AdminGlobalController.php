<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 22.08.12
 * Time: 16:30
 * To change this template use File | Settings | File Templates.
 */
class Donation_AdminGlobalController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_admin_main', array(), 'donation_admin_main_global');
  }

  public function indexAction()
  {
    $this->view->form = $form = new Donation_Form_Admin_Global();

    if (!$this->getRequest()->isPost()){
      return ;
    }

    if (!$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $values = $form->getValues();

    if($values['donation_enable_charities'] == 0 && $values['donation_enable_projects'] == 0){
      return $form->addError('DONATION_Please enable charity or project');
    }

    foreach($values as $key => $value)
    {
      $value = trim($value);
      $settings->setSetting($key,$value);
    }

    $form->addNotice('DONATION_Your changes has been saved.');

  }
}
