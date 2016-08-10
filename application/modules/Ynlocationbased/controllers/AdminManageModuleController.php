<?php

class Ynlocationbased_AdminManageModuleController extends Core_Controller_Action_Admin
{
    public function init()
    {
        $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')->getNavigation('ynlocationbased_admin_main', array(), 'ynlocationbased_admin_main_manage_module');
    }

    public function indexAction()
    {
        $page = $this->_getParam('page', 1);
        $this->view->paginator = Engine_Api::_()->getDbTable('modules', 'ynlocationbased')->getModulePaginator();
        $this->view->paginator->setItemCountPerPage(20);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function disableModuleAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $module_id = $this->_getParam('module_id');
        $status = $this->_getParam('status');
        $table = Engine_Api::_()->getDbTable('modules', 'ynlocationbased');
        $table -> update(array('enabled' => $status), "module_id = $module_id");
    }
}
