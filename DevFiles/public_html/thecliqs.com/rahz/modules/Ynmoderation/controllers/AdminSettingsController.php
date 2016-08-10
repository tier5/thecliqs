<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynmoderation
 * @author     YouNet Company
 */

class Ynmoderation_AdminSettingsController extends Core_Controller_Action_Admin {
	public function indexAction() {
		$this->view->navigation = $navigation = Engine_Api::_ ()->getApi('menus', 'core')->getNavigation('ynmoderation_admin_main', array(), 'ynmoderation_admin_main_settings');
		if ($this->getRequest()->isPost()) {
		      $values = $this->getRequest()->getPost();
		      $pluginStatus = $values['enable_or_disable'];
		      if ( $pluginStatus == '1' || $pluginStatus == '0' ) {
			      foreach ($values as $key => $value) {
		        	if ($key == 'm_' . $value) {
		          		$plugin = Engine_Api::_()->getItem('ynmoderation_module', $value);
		          		$plugin->enabled = $pluginStatus;
		          		$plugin->save();
		        	}
			      }
			      $this->view->updatedStatus = true;	
		      }
	    }
		
		$page = $this->_getParam('page',1);
	    $this->view->paginator = Engine_Api::_()->getDbTable('modules', 'ynmoderation')->getModulesPaginator(array(
	      	'orderby' => 'id',
	    	'having_query' => true
	    ));
	    $this->view->paginator->setItemCountPerPage(10);
	    $this->view->paginator->setCurrentPageNumber($page);
	}
}