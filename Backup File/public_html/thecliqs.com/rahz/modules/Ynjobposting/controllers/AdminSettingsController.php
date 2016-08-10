<?php
class Ynjobposting_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_settings_global');
         $settings = Engine_Api::_()->getApi('settings', 'core');
         $this->view->form = $form = new Ynjobposting_Form_Admin_Settings_Global();
        
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
        ->getNavigation('ynjobposting_admin_main', array(), 'ynjobposting_admin_settings_level');
    
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
        $this->view->form = $form = new Ynjobposting_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        //populate data to form
        $form->level_id->setValue($id);
        $form->populate($permissionsTable->getAllowed('ynjobposting', $id, array_keys($form->getValues())));
        if ($level->type != 'public') {
            $form->create_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'create'));
            $form->edit_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'edit'));
            $form->delete_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'delete'));
            $form->view_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'view'));
            $form->comment_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'comment'));
            $form->close_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'close'));
            $form->sponsor_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'sponsor'));
			$form->auth_view_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'auth_view'));			
            $form->create_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'create'));
            $form->edit_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'edit'));
            $form->delete_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'delete'));
            $form->view_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'view'));
            $form->comment_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'comment'));
            $form->end_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'end'));
            $form->apply_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'apply'));
            $form->autoapprove_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'autoapprove'));
			$form->auth_view_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'auth_view'));
			$form->auth_comment_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'auth_comment'));
        }   
        else {
            $form->view_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'view'));
            $form->view_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'view'));
        }
 
        if ($level->type != 'public') {
            $numberFieldArr = Array('max_company', 'max_job');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynjobposting', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynjobposting')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
                $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit"); 
                
				//credit for buy&feature job
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'buyfeature_job'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'buyfeature_job';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to buy & feature %s job';
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
				//end credit for buy&feature job
				
				//credit for buy job
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'buy_job'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'buy_job';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to buy %s job';
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
				//end credit for buy job
				
				//credit for  job
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'feature_job'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'feature_job';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to feature %s job';
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
				//end credit for feature job
				
				//credit for feature company
				$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'sponsor_company'")->limit(1);
                $type_spend = $typeTbl -> fetchRow($select);
    			
    			if(!$type_spend) 
                {
                    $type_spend = $typeTbl->createRow();
                    $type_spend->module = 'yncredit';
                    $type_spend->action_type = 'sponsor_company';
                    $type_spend->group = 'spend';
                    $type_spend->content = 'Use credit to sponsor %s company';
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
				//end credit for sponsor company
				
                $select = $typeTbl->select()->where('module = ?', 'ynjobposting')->where('action_type = ?', 'ynjobposting_company')->limit(1);
                $company_type = $typeTbl -> fetchRow($select);
                
                if(!$company_type) {
                    $company_type = $typeTbl->createRow();
                    $company_type->module = 'ynjobposting';
                    $company_type->action_type = 'ynjobposting_company';
                    $company_type->group = 'earn';
                    $company_type->content = 'Creation %s company';
                    $company_type->credit_default = 5;
                    $company_type->save();
                }
                         
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $company_type->type_id)
                    ->limit(1);
                $company_credit = $creditTbl->fetchRow($select);
                if(!$company_credit) {
                    $company_credit = $creditTbl->createRow();
                }
                else {
                    $form->company_first_amount->setValue($company_credit->first_amount);
                    $form->company_first_credit->setValue($company_credit->first_credit);
                    $form->company_credit->setValue($company_credit->credit);
                    $form->company_max_credit->setValue($company_credit->max_credit);
                    $form->company_period->setValue($company_credit->period);
                }
                
                $select = $typeTbl->select()->where('module = ?', 'ynjobposting')->where('action_type = ?', 'ynjobposting_job')->limit(1);
                $job_type = $typeTbl -> fetchRow($select);
                
                if(!$job_type) {
                    $job_type = $typeTbl->createRow();
                    $job_type->module = 'ynjobposting';
                    $job_type->action_type = 'ynjobposting_job';
                    $job_type->group = 'earn';
                    $job_type->content = 'Creation %s job';
                    $job_type->credit_default = 5;
                    $job_type->save();
                }
                         
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $job_type->type_id)
                    ->limit(1);
                $job_credit = $creditTbl->fetchRow($select);
                if(!$job_credit) {
                    $job_credit = $creditTbl->createRow();
                }
                else {
                    $form->job_first_amount->setValue($job_credit->first_amount);
                    $form->job_first_credit->setValue($job_credit->first_credit);
                    $form->job_credit->setValue($job_credit->credit);
                    $form->job_max_credit->setValue($job_credit->max_credit);
                    $form->job_period->setValue($job_credit->period);
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
        	
			if(empty($values['auth_view_company'])) {
                unset($values['auth_view_company']);
                $form->auth_view_company->setValue($permissionsTable->getAllowed('ynjobposting_company', $id, 'auth_view'));
            }
			
			if(empty($values['auth_view_job'])) {
                unset($values['auth_view_job']);
                $form->auth_view_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'auth_view'));
            }
			
			if(empty($values['auth_comment_job'])) {
                unset($values['auth_comment_job']);
                $form->auth_comment_job->setValue($permissionsTable->getAllowed('ynjobposting_job', $id, 'auth_comment'));
            }
			
            try {
                //credit
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $company_creditValues = array_slice($values, 0, 5);
                    $job_creditValues = array_slice($values, 5, 5);
                    $permissionValues = array_slice($values, 10);
                    
                    $company_credit->level_id = $id;
                    $company_credit->type_id = $company_type->type_id;
                    $auth_arr = array('first_amount', 'first_credit', 'credit', 'max_credit', 'period');
                    $i = 0;
                    foreach ($company_creditValues as $value) {
                        $company_credit->$auth_arr[$i] = $value;
                        $i++;
                    }
                    $company_credit->save();
                    
                    $job_credit->level_id = $id;
                    $job_credit->type_id = $job_type->type_id;
                    $i = 0;
                    foreach ($job_creditValues as $value) {
                        $job_credit->$auth_arr[$i] = $value;
                        $i++;
                    }
                    $job_credit->save();
                }
                else {
                    $permissionValues = $values;
                }
                
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $permissionsTable->setAllowed('ynjobposting', $id, 'use_credit', $permissionValues['use_credit']);
                }
                $permissionsTable->setAllowed('ynjobposting', $id, 'max_company', $permissionValues['max_company']);
                $permissionsTable->setAllowed('ynjobposting', $id, 'max_job', $permissionValues['max_job']);
                
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'create', $permissionValues['create_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'edit', $permissionValues['edit_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'view', $permissionValues['view_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'comment', $permissionValues['comment_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'delete', $permissionValues['delete_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'close', $permissionValues['close_company']);
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'sponsor', $permissionValues['sponsor_company']);
				$permissionsTable->setAllowed('ynjobposting_company', $id, 'auth_view', $permissionValues['auth_view_company']);
                
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'create', $permissionValues['create_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'edit', $permissionValues['edit_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'view', $permissionValues['view_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'comment', $permissionValues['comment_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'delete', $permissionValues['delete_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'end', $permissionValues['end_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'apply', $permissionValues['apply_job']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'autoapprove', $permissionValues['autoapprove_job']);
				$permissionsTable->setAllowed('ynjobposting_job', $id, 'auth_view', $permissionValues['auth_view_job']);
				$permissionsTable->setAllowed('ynjobposting_job', $id, 'auth_comment', $permissionValues['auth_comment_job']);
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
                $permissionsTable->setAllowed('ynjobposting_company', $id, 'view', $permissionValues['view_company']);
                $permissionsTable->setAllowed('ynjobposting_job', $id, 'view', $permissionValues['view_job']);
                 // Commit
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