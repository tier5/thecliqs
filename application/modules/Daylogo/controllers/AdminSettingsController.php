<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2012-08-16 16:20 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Daylogo_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function indexAction()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('daylogo_admin_main', array(), 'daylogo_admin_main_settings');

    $this->view->form = $form = new Daylogo_Form_Admin_Settings();

    if ($this->getRequest()->isPost() && $form->isValid($this->_getAllParams())) {
      $values = $form->getValues();

      foreach ($values as $key => $value) {
        if (!is_numeric($value) and $key != 'submit') {
          $form->addNotice($this->view->translate('DAYLOGO_SETTINGS_NUMERIC'));
          return;
        }
      }
      foreach ($values as $key => $value) {
        if ($key == 'maxwidth' or $key == 'maxheight') {
          Engine_Api::_()->getApi('settings', 'core')->setSetting('daylogo.' . $key, $value);
        }
      }
      $form->addNotice('DAYLOGO_SETTINGS_SAVED');
    }
  }
}