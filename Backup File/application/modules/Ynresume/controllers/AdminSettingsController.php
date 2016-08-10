<?php
class Ynresume_AdminSettingsController extends Core_Controller_Action_Admin {
    public function globalAction() {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynresume_admin_main', array(), 'ynresume_admin_settings_global');
         $settings = Engine_Api::_()->getApi('settings', 'core');
         $this->view->form = $form = new Ynresume_Form_Admin_Settings_Global();
         
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
        ->getNavigation('ynresume_admin_main', array(), 'ynresume_admin_settings_level');

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
        $this->view->form = $form = new Ynresume_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        $form->populate($permissionsTable->getAllowed('ynresume_resume', $id, array_keys($form->getValues())));
        
        if ($level->type != 'public') {
            $numberFieldArr = Array('max_skill', 'max_friend', 'max_photo');
            foreach ($numberFieldArr as $numberField) {
                if ($permissionsTable->getAllowed('ynresume_resume', $id, $numberField) == null) {
                    $row = $permissionsTable->fetchRow($permissionsTable->select()
                    ->where('level_id = ?', $id)
                    ->where('type = ?', 'ynresume_resume')
                    ->where('name = ?', $numberField));
                    if ($row) {
                        $form->$numberField->setValue($row->value);
                    }
                }
            } 
            $credit = array();
            
            if (Engine_Api::_()->hasModuleBootstrap('yncredit')) {
                $typeTbl = Engine_Api::_()->getDbTable('types', 'yncredit');
                $creditTbl = Engine_Api::_()->getDbTable('credits', 'yncredit');
                
                //spend credit for service and feature resume
                $select = $typeTbl->select()->where('module = ?', 'yncredit')->where('action_type = ?', 'ynresume_service')->limit(1);
                $serviceType = $typeTbl -> fetchRow($select);
                if(!$serviceType) {
                    $serviceType = $typeTbl->createRow();
                    $serviceType->module = 'yncredit';
                    $serviceType->action_type = 'ynresume_service';
                    $serviceType->group = 'spend';
                    $serviceType->content = 'Use credit to purchase "Who Viewed Me" service for %s resume.';
                    $serviceType->credit_default = 0;
                    $serviceType->link_params = '';
                    $serviceType->save();
                }
                
                $select = $typeTbl->select()->where('module = ?', 'yncredit')->where('action_type = ?', 'ynresume_feature')->limit(1);
                $featureType = $typeTbl -> fetchRow($select);
                if(!$featureType) {
                    $featureType = $typeTbl->createRow();
                    $featureType->module = 'yncredit';
                    $featureType->action_type = 'ynresume_feature';
                    $featureType->group = 'spend';
                    $featureType->content = 'Use credit to feature %s resume.';
                    $featureType->credit_default = 0;
                    $featureType->link_params = '';
                    $featureType->save();
                }
                
                $spendType = array($serviceType, $featureType);
                foreach ($spendType as $type) {
                    $select = $creditTbl->select()
                        ->where('level_id = ?', $id)
                        ->where('type_id = ?', $type -> type_id)
                        ->limit(1);
                    $spendCredit = $creditTbl->fetchRow($select);
                    if(!$spendCredit) {
                        $spendCredit = $creditTbl->createRow();
                        $spendCredit -> level_id = $id;
                        $spendCredit -> type_id = $type -> type_id;
                        $spendCredit -> first_amount = 0;
                        $spendCredit -> first_credit = 0;
                        $spendCredit -> credit = 0;
                        $spendCredit -> max_credit = 0;
                        $spendCredit -> period = 1;
                        $spendCredit->save();
                    }
                }
                
                //earn credit
                $select = $typeTbl->select()->where('module = ?', 'ynresume')->where('action_type = ?', 'ynresume_new')->limit(1);
                $resumeType = $typeTbl -> fetchRow($select);
                
                if(!$resumeType) {
                    $resumeType = $typeTbl->createRow();
                    $resumeType->module = 'ynresume';
                    $resumeType->action_type = 'ynresume_new';
                    $resumeType->group = 'earn';
                    $resumeType->content = 'Creating %s resume';
                    $resumeType->credit_default = 5;
                    $resumeType->link_params = '{"route":"ynresume_general","action":"manage"}';
                    $resumeType->save();
                }
                
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $resumeType->type_id)
                    ->limit(1);
                $resumeCredit = $creditTbl->fetchRow($select);
                if(!$resumeCredit) {
                    $resumeCredit = $creditTbl->createRow();
                }
                else {
                    $form->resume_first_amount->setValue($resumeCredit->first_amount);
                    $form->resume_first_credit->setValue($resumeCredit->first_credit);
                    $form->resume_credit->setValue($resumeCredit->credit);
                    $form->resume_max_credit->setValue($resumeCredit->max_credit);
                    $form->resume_period->setValue($resumeCredit->period);
                }
                $select = $typeTbl->select()->where('module = ?', 'ynresume')->where('action_type = ?', 'ynresume_recommend')->limit(1);
                $recommendType = $typeTbl -> fetchRow($select);
                
                if(!$recommendType) {
                    $recommendType = $typeTbl->createRow();
                    $recommendType->module = 'ynresume';
                    $recommendType->action_type = 'ynresume_recommend';
                    $recommendType->group = 'earn';
                    $recommendType->content = 'Writing recommendation for %s resume';
                    $recommendType->credit_default = 5;
                    $recommendType->link_params = '{"route":"ynresume_recommend","action":"create"}';
                    $recommendType->save();
                }
                
                $select = $creditTbl->select()
                    ->where("level_id = ? ", $id)
                    ->where("type_id = ?", $recommendType->type_id)
                    ->limit(1);
                $recommendCredit = $creditTbl->fetchRow($select);
                if(!$recommendCredit) {
                    $recommendCredit = $creditTbl->createRow();
                }
                else {
                    $form->recommendation_first_amount->setValue($recommendCredit->first_amount);
                    $form->recommendation_first_credit->setValue($recommendCredit->first_credit);
                    $form->recommendation_credit->setValue($recommendCredit->credit);
                    $form->recommendation_max_credit->setValue($recommendCredit->max_credit);
                    $form->recommendation_period->setValue($recommendCredit->period);
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
            
            $sections = Engine_Api::_()->ynresume()->getAllSections();
            if (isset($sections['photo'])) unset($sections['photo']);
            $sectionsKeys = array_keys($sections);
            $checkArr = array_map(function($v) {
                return 'auth_'.$v;
            }, $sectionsKeys);
            foreach ($checkArr as $check) {
                if(empty($values[$check])) {
                    unset($values[$check]);
                    print_r($check);
                    $form->$check->setValue($permissionsTable->getAllowed('ynresume_resume', $id, $check));
                }
            }
            try {
                //credit
                if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                    $credits = array('resume' => $resumeCredit, 'recommendation' => $recommendCredit);
                    $types = array('resume' => $resumeType, 'recommendation' => $recommendType);
                    $arr = array('first_amount', 'first_credit', 'credit', 'max_credit', 'period');
                    foreach ($credits as $key => $value) {
                        foreach ($arr as $item) {
                            $value->level_id = $id;
                            $value->type_id = $types[$key]->type_id;
                            $value->$item = $values[$key.'_'.$item];
                            unset($values[$key.'_'.$item]);
                        }
                        $value->save();
                    }
                   
                }
                $permissionsTable->setAllowed('ynresume_resume', $id, $values);
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
                
                $permissionsTable->setAllowed('ynresume_resume', $id, $values);
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