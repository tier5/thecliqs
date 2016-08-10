<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2010-07-02 19:25 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Quiz
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Quiz_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('quiz_admin_main', array(), 'quiz_admin_main_settings');
    
    $this->view->form = $form = new Quiz_Form_Admin_Global();

    if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost()))
    {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);
      }
    }
  }
}