<?php
class Ynsocialads_AdminPaylaterController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_paylater');
       
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            $confirm  = ($values['submit_type'] == 'confirm') ? true : false;
            foreach ($values as $key => $value) {
                if ($key == 'select_' . $value) {
                    $paylayterrequest = Engine_Api::_()->getItem('ynsocialads_transaction', $value);
                    $ad = Engine_Api::_()->getItem('ynsocialads_ad', $paylayterrequest->ad_id);
                    if ($confirm) {
                        $paylayterrequest->status = 'completed';
						$ad->status = 'approved';
                        $ad->approved = 1;
			            $ad->save();
						Engine_Api::_()->ynsocialads()->checkAndUpdateStatus($ad);
                        
                    }
                    else {
                        $paylayterrequest->status = 'canceled';
                        $ad->status = 'denied';
                        $ad->approved = 0;
                        $ad->save();
                        
                    }
                    $paylayterrequest->save();
                }
            }
        }
        
        $table = Engine_Api::_()->getDbTable('transactions', 'ynsocialads');
        $select = $table->select()->where('gateway_id = ?', -2)->where('status = ?', 'initialized');
        $paylater = $table->fetchAll($select);
        
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($paylater);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }
}