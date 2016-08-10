<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminLevelController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_AdminPermissionController extends Core_Controller_Action_Admin
{
	/**
	 * @var $_settings Core_Model_DbTable_Settings
	 */
	protected $_settings;

	public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('page_admin_main', array(), 'page_admin_main_permission');

		$this->_settings = Engine_Api::_()->getDbTable('settings', 'core');
    $this->view->hide = _ENGINE_ADMIN_NEUTER;
  }
  
	public function indexAction()
  {
		/**
		 * @var $mode String
		 */
		$package_enabled = $this->_settings->getSetting('page.package.enabled', 0);

    if ($this->getRequest()->isPost()) {
			$package_enabled = ($this->_getParam('permission_mode') == 'package')? 1:0;

      $this->_settings->setSetting('page.package.enabled', $package_enabled);

			if ($this->getRequest()->getPost('format') == 'json'){
				$this->view->success = true;
				return;
			}
		}

		$mode = ( $package_enabled )? 'package':'level';
		$this->_forward($mode, null, null, $this->_getAllParams());
	}

	public function levelAction()
	{
    if ($this->_settings->getSetting('page.package.enabled', 0)) {
			return $this->_helper->redirector->gotoRoute(array(), 'page_admin_packages', true);
		}

    if( null !== ($id = $this->_getParam('level_id')) ) {
      $level = Engine_Api::_()->getItem('authorization_level', $id);
    } else {
      $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
    }
    
  	if( !$level instanceof Authorization_Model_Level ) {
      throw new Engine_Exception('missing level');
    }

    $id = $level->level_id;
    
    $this->view->form = $form = new Page_Form_Admin_Level();
    $form->level_id->setValue($id);
    
    $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
    
    if( !$this->getRequest()->isPost() )
    {
      $form->populate($permissionsTable->getAllowed('page', $id, array_keys($form->getValues())));
      $form->getElement('allowed_pages')->setValue($form->getElement('allowed_pages')->getValue() - 6);
      return;
    }
    
  	if( !$form->isValid($this->getRequest()->getPost()) ) {
      return;
    }
    
    $values = $form->getValues();
    $values['allowed_pages'] += 6;
   	// Work around bug :(
   	unset($values['level_id']);

   	$prefix = $permissionsTable->getTablePrefix();
    $db = $permissionsTable->getAdapter();
    $db->beginTransaction();

    try
    {
	  $values['view'] = 1;
	  $values['comment'] = 1;
      $values['posting'] = 1;

      $featured = ($values['featured'])? 1:0;
      $sponsored = ($values['sponsored'])? 1:0;

      $sql = 'UPDATE ' . $prefix . 'page_pages AS p ' .
             'LEFT JOIN ' . $prefix . 'users AS u ON (u.user_id = p.user_id)' .
            'SET ' .
            'p.featured = IF  ((p.auto_set = 0 || p.auto_set = 2), ' . $featured . ' ,p.featured ), ' .
            'p.sponsored = IF  ((p.auto_set = 0 || p.auto_set = 1), ' . $sponsored . ' ,p.sponsored ) ' .
            'WHERE u.level_id = ' . $id
      ;

      $db->query($sql);

      $permissionsTable->setAllowed('page', $id, $values);
      $db->commit();
    }
    catch( Exception $e )
    {
      $db->rollBack();
      throw $e;
    }
  }

	public function packageAction()
	{
		return $this->_helper->redirector->gotoRoute(array(), 'page_admin_packages', true);
	}
}