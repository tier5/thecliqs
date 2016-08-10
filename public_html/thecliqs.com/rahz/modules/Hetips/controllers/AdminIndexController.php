<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminIndexController.php 2012-03-04 17:01 alexander $
 * @author     Alexander
 */

/**
 * @category   Application_Extensions
 * @package    Hetips
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hetips_AdminIndexController extends Fields_Controller_AdminAbstract
{
  protected $_fieldType;

  protected $_requireProfileType = true;

  public function init()
  {
    $type = $this->_getParam('type', 'user');
    $this->_fieldType = $type;

    parent::init();
  }

  public function indexAction()
  {
   /* $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('hetips_admin_main', array(), 'hetips_admin_main_settings');*/

    $this->menu =  $this->_getParam('type', 'user');

    if($this->_fieldType != 'group'){
      parent::indexAction();
    }

    if ($this->_fieldType == 'group') {
      $this->view->topLevelOptions = Engine_Api::_()->getDbtable('categories', 'group')->getCategoriesAssoc();
    }

    $this->view->type = $type = $this->_fieldType;
    $this->view->option_id = $option_id = (int) $this->_getParam('option_id', 1);
    $this->view->tipsMaps = Engine_Api::_()->hetips()->getTipsMap($type, $option_id);
    $this->view->tipsTypes = Engine_Api::_()->hetips()->getTipsTypes();
    $this->view->tipsMeta = Engine_Api::_()->hetips()->getTipsMeta($this->_fieldType, $option_id);
    $this->view->form = $form = new Hetips_Form_Admin_Settings($this->_fieldType);

    if (!$this->getRequest()->isPost()) {
      return 0;
    }

    if (!$form->isValid($this->getRequest()->getPost())) {
      return 0;
    }

    $values = $form->getValues();
    Engine_Api::_()->getDbTable('settings', 'hetips')->setSettings($values);

  }

  public function addTipAction()
  {
    $tipsData = array(
      'tip_id' => (int) $this->_getParam('tip_id'),
      'option_id' => (int) $this->_getParam('option_id'),
      'tip_type' => (string) $this->_getParam('type', 'user'),
    );

    $newTip = Engine_Api::_()->getDbTable('maps', 'hetips')->addTip($tipsData);
    $this->view->html= $this->view->adminTipsMeta($newTip);
    $this->view->hehe = array('sadasd');
  }

  public function deleteTipAction()
  {
    $tip_id = (int) $this->_getParam('tip_id');
    Engine_Api::_()->getDbTable('maps', 'hetips')->deleteTip($tip_id);
  }

  public function orderTipsAction()
  {
    $tips_ids = $this->_getParam('tips_ids');
    Engine_Api::_()->getDbTable('maps', 'hetips')->orderTips($tips_ids);
  }
}