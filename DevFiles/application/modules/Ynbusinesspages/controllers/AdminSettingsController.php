<?php
class Ynbusinesspages_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_settings_global');
         $settings = Engine_Api::_()->getApi('settings', 'core');
         $this->view->form = $form = new Ynbusinesspages_Form_Admin_Settings_Global();
        
         if ($this->getRequest()->isPost()&& $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            foreach ($values as $key => $value) {
                $settings->setSetting($key, $value);
            }
            $form->addNotice('Your changes have been saved.'); 
        }
    }
    
    public function levelAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynbusinesspages_admin_main', array(), 'ynbusinesspages_admin_settings_level');

        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } 
        else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
    
        // Make form
        $this->view->form = $form = new Ynbusinesspages_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        $form->populate($permissionsTable->getAllowed('ynbusinesspages_business', $id, array_keys($form->getValues())));
        
        if ($level->type != 'public') {
            $numberFieldArr = Array('max');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynbusinesspages_business', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynbusinesspages_business')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
            	$creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
				
				//credit for publish business
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_businesses'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'publish_businesses';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to publish %s businesses';
                    $type_spend->credit_default = 0;
                    $type_spend->link_params = '';
                    $type_spend->save();
                }
				
				$select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type_spend -> type_id)
                    ->limit(1);
                $spend_credit = $creditTbl->fetchRow($select);
    			if(!$spend_credit)
    			{
    				$spend_credit = $creditTbl->createRow();
    				$spend_credit -> level_id = $id;
    				$spend_credit -> type_id = $type_spend -> type_id;
    				$spend_credit -> first_amount = 0;
    				$spend_credit -> first_credit = 0;
    				$spend_credit -> credit = 0;
    				$spend_credit -> max_credit = 0;
    				$spend_credit -> period = 1;
    				$spend_credit->save();
    			}
				//end credit for publish business
				
				//credit for feature business
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'feature_businesses'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'feature_businesses';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to feature %s businesses';
                    $type_spend->credit_default = 0;
                    $type_spend->link_params = '';
                    $type_spend->save();
                }
				
				$select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type_spend -> type_id)
                    ->limit(1);
                $spend_credit = $creditTbl->fetchRow($select);
    			if(!$spend_credit)
    			{
    				$spend_credit = $creditTbl->createRow();
    				$spend_credit -> level_id = $id;
    				$spend_credit -> type_id = $type_spend -> type_id;
    				$spend_credit -> first_amount = 0;
    				$spend_credit -> first_credit = 0;
    				$spend_credit -> credit = 0;
    				$spend_credit -> max_credit = 0;
    				$spend_credit -> period = 1;
    				$spend_credit->save();
    			}
				//end credit for feature business
				
                $select = $typeTbl->select()->where('module = ?', 'ynbusinesspages')->where('action_type = ?', 'ynbusinesspages_new')->limit(1);
                $type = $typeTbl -> fetchRow($select);
                
                if(!$type) {
                    $type = $typeTbl->createRow();
                    $type->module = 'ynbusinesspages';
                    $type->action_type = 'ynbusinesspages_new';
                    $type->group = 'earn';
                    $type->content = 'Creating %s business';
                    $type->credit_default = 5;
                    $type->link_params = '{"route":"ynbusinesspages_general","action":"create"}';
                    $type->save();
                }
				
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $type->type_id)
                    ->limit(1);
                $credit = $creditTbl->fetchRow($select);
                if(!$credit) {
                    $credit = $creditTbl->createRow();
                }
                else 
                {
                    $form->first_amount->setValue($credit->first_amount);
                    $form->first_credit->setValue($credit->first_credit);
                    $form->credit->setValue($credit->credit);
                    $form->max_credit->setValue($credit->max_credit);
                    $form->period->setValue($credit->period);
                } 
            }
        }

        // Check post
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        // Check validitiy
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
        
        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        // Process
        if ($level->type != 'public') {
            try {
                //credit
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $creditValues = array_slice($values, 0, 5);
                    $permissionValues = array_slice($values, 5);
                    $credit->level_id = $id;
                    $credit->type_id = $type->type_id;
                    $credit->setFromArray($creditValues);
                    $credit->save();
                }
                else {
                    $permissionValues = $values;
                }
                $permissionsTable->setAllowed('ynbusinesspages_business', $id, $permissionValues);
                 // Commit
                $db->commit();
            }
        
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        else {
            try {
                
                $permissionsTable->setAllowed('ynbusinesspages_business', $id, $values);
                $db->commit();
            }
            
            catch(Exception $e) {
                $db->rollBack();
                throw $e;
            }
        }
        
        $form->addNotice('Your changes have been saved.'); 
    }
}