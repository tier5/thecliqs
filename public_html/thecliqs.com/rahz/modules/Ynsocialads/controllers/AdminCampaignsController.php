<?php
class Ynsocialads_AdminCampaignsController extends Core_Controller_Action_Admin {
    public function indexAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_campaigns');
        
        $table = Engine_Api::_()->getItemTable('ynsocialads_campaign');
        $select = $table->select();
        
        $this->view->form = $form = new Ynsocialads_Form_Admin_Campaigns_Filter();
        
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $this->view->formValues = $values;
        if ($values['status'] == 'All') {
            $statusArr = array('active', 'deleted');
        }
        else {
            $statusArr = array($values['status']);
        }
        if ($values['title'] != null) {
            $select = $select->where('title LIKE ?', '%'.$values['title'].'%');
        }
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            foreach ($values as $key => $value) {
                if ($key == 'delete_' . $value) {
                    $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $value);
                    $campaign->deleteAllAds();
                    $campaign->status = 'deleted';
                    $campaign->save();
                    
                }
            }
        }
        $select = $select->where('status IN (?)', $statusArr);              
        
        $campaigns = $table->fetchAll($select);
        
        $page = $this->_getParam('page',1);
        $this->view->paginator = $paginator = Zend_Paginator::factory($campaigns);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->campaign_id=$id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $id);
                $campaign->deleteAllAds();
                $campaign->status = "deleted";
                $campaign->save();
                
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-campaigns/delete.tpl');
    }
    
    public function editAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->campaign_id=$id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $newTitle = strip_tags($this->getRequest()->getPost('newTitle'));
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $id);
                $campaign->title = $newTitle;
                $campaign->save();
                
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh'=> 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-campaigns/edit.tpl');
    }

    public function multideleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $this -> view -> ids = $ids = $this -> _getParam('ids', null);
        $confirm = $this -> _getParam('confirm', false);
        $this -> view -> count = count(explode(",", $ids));

        // Check post
        if ($this -> getRequest() -> isPost() && $confirm == true)
        {
            //Process delete
            $ids_array = explode(",", $ids);
            foreach ($ids_array as $id)
            {
                $campaign = Engine_Api::_()->getItem('ynsocialads_campaign', $id);
                if ($campaign) {
                    $campaign->deleteAllAds();
                    $campaign->status = 'deleted';
                    $campaign->save();
                }
            }

            $this -> _helper -> redirector -> gotoRoute(array('action' => ''));
        }
    }
}