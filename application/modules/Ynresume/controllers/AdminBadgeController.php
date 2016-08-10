<?php
class Ynresume_AdminBadgeController extends Core_Controller_Action_Admin {
    public function init() {
        //get admin menu
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynresume_admin_main', array(), 'ynresume_admin_main_badge');
    }
        
    public function indexAction() {
        $page = $this->_getParam('page',1);
        $table = Engine_Api::_()->getDbTable('badges', 'ynresume');
        $this->view->badges = $badges = $table->fetchAll($table->select()->order('order ASC'));
    }
    
    public function createAction() {
        $this->view->form = $form = new Ynresume_Form_Admin_Badge_Create();
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }
        
        $values = $form->getValues();
        
        if ($values['condition'] == 'completeness') {
            if (empty($values['completeness_value'])) {
                $form->addError('"Sections this member has to add" cannot be empty!');
                return;
            }
            else {
                $values['value'] = serialize($values['completeness_value']);
            }
        }
        else {
            if (empty($values['count_value'])) {
                $form->addError('"Value" cannot be empty!');
                return;
            }
            else {
                $values['value'] = $values['count_value'];
            } 
        }
        $table = Engine_Api::_()->getDbtable('badges', 'ynresume');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $badge = $table->createRow();
            $values['photo_id'] = $this->setPhoto($form->photo, array(
                'parent_type' => 'ynresume_badge',
                'parent_id' => $badge->getIdentity(),
            ));
            $badge->setFromArray($values);
            $badge->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynresume','controller'=>'badge', 'action'=>'index'), 'admin_default', true);
        }
        catch( Exception $e ) {
            $db->rollBack();
            throw $e;
        }       
    }

    public function deleteAction() {
        // In smoothbox
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->badge_id = $id;
        // Check post
        if( $this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                $faq = Engine_Api::_()->getItem('ynresume_badge', $id);
                $faq->delete();
                $db->commit();
            }

            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRefresh'=> true,
                'messages' => array('This Badge has been deleted.')
            ));
        }
    }
    
    public function editAction() {
        $id = $this->_getParam('id');
        $this->view->form = $form = new Ynresume_Form_Admin_Badge_Edit();
        $badge = Engine_Api::_()->getItem('ynresume_badge', $id);
        $form->populate($badge->toArray());
        if ($badge->condition == 'completeness') {
            $value = unserialize($badge->value);
            $form->completeness_value->setValue($value);
        }
        else {
            $form->count_value->setValue($badge->value);
        }
        
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        if( !$form->isValid($this->getRequest()->getPost()) ) {
            return;
        }    
        $values = $form->getValues();
        if ($values['condition'] == 'completeness') {
            if (empty($values['completeness_value'])) {
                $form->addError('"Sections this member has to add" cannot be empty!');
                return;
            }
            else {
                $values['value'] = serialize($values['completeness_value']);
            }
        }
        else {
            if (empty($values['count_value'])) {
                $form->addError('"Value" cannot be empty!');
                return;
            }
            else {
                $values['value'] = $values['count_value'];
            } 
        }
        
        $table = Engine_Api::_()->getDbtable('badges', 'ynresume');
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            if (isset($values['photo']) && !empty($values['photo'])) {
                $values['photo_id'] = $this->setPhoto($form->photo, array(
                    'parent_type' => 'ynresume_badge',
                    'parent_id' => $badge->getIdentity(),
                ));
            }
            $badge->setFromArray($values);
            $badge->save();
            $db->commit();
            $this -> _helper -> redirector -> gotoRoute(array('module'=>'ynresume','controller'=>'badge', 'action'=>'index'), 'admin_default', true);
        }
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }  
    }
    
    private function setPhoto($photo, $params) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo -> getFileName();
        }
        else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        }
        else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        }
        else {
            throw new Ynresume_Model_Exception('invalid argument passed to setPhoto');
        }
    
        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
    
        // Save
        $storage = Engine_Api::_() -> storage();
        $angle = 0;
        if(function_exists('exif_read_data')) {
            $exif = exif_read_data($file);
            if (!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 8 :
                        $angle = 90;
                        break;
                    case 3 :
                        $angle = 180;
                        break;
                    case 6 :
                        $angle = -90;
                        break;
                }
            }
        }   
        // Resize image (main)
        $image = Engine_Image::factory();
        $image -> open($file);
        if ($angle != 0)
            $image -> rotate($angle);
        $image -> resize(56, 56) -> write($path . '/m_' . $name) -> destroy();
    
    
        // Store
        $iMain = $storage -> create($path . '/m_' . $name, $params);
        
        // Remove temp files
        @unlink($path . '/m_' . $name);
    
        return $iMain -> file_id;
    }
    
    public function sortAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $table = Engine_Api::_()->getDbTable('badges', 'ynresume');
        $badges = $table->fetchAll();
        $order = explode(',', $this->getRequest()->getParam('order'));
        foreach($order as $i => $item) {
            $badge_id = substr($item, strrpos($item, '_') + 1);
            foreach($badges as $badge) {
                if($badge->getIdentity() == $badge_id) {
                    $badge->order = $i;
                    $badge->save();
                }
            }
        }
    }
}