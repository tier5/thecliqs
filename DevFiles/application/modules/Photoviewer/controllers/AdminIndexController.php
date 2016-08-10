<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 08.02.13 10:28 michael $
 * @author     Michael
 */

/**
 * @category   Application_Extensions
 * @package    Photoviewer
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Photoviewer_AdminIndexController extends Core_Controller_Action_Admin
{

  public function indexAction()
  {

    $this->view->form = $form = new Photoviewer_Form_Admin();

    if (!$this->getRequest()->isPost() || !$form->isValid($this->getRequest()->getPost())){
      return ;
    }

    $values = $form->getValues();

    $settings = Engine_Api::_()->getDbTable('settings', 'core');
    $settings->setSetting('photoviewer.enable', $values['enable']);
    $settings->setSetting('photoviewer.slideshowtime', $values['slideshowtime']);
    $settings->setSetting('photoviewer.downloadable', $values['downloadable']);


    $form->addNotice('Changes has been saved');




  }

}