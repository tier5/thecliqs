<?php
class Ynsocialads_AdminSettingsController extends Core_Controller_Action_Admin
{
	public function globalAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_settings_global');
		$settings = Engine_Api::_() -> getApi('settings', 'core');
		$this -> view -> form = $form = new Ynsocialads_Form_Admin_Settings_Global();
		if ($this -> getRequest() -> isPost() && $form -> isValid($this -> getRequest() -> getPost()))
		{
			$values = $form -> getValues();
			$settings -> setSetting('ynsocialads_noadsshown', $values['no_ads_shown']);
			$settings -> setSetting('ynsocialads_posfeedads', $values['pos_feed_ads']);
			$settings -> setSetting('ynsocialads_paylaterexpiretime', $values['pay_later_expire_time']);
			$form -> addNotice('Your changes have been saved.');
		}
	}

	public function levelAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
        ->getNavigation('ynsocialads_admin_main', array(), 'ynsocialads_admin_main_settings_level');

        // Get level id
        if (null !== ($id = $this->_getParam('level_id'))) {
            $level = Engine_Api::_()->getItem('authorization_level', $id);
        } else {
            $level = Engine_Api::_()->getItemTable('authorization_level')->getDefaultLevel();
        }

        if(!$level instanceof Authorization_Model_Level) {
            throw new Engine_Exception('missing level');
        }

        $id = $level->level_id;
    
        // Make form
        $this->view->form = $form = new Ynsocialads_Form_Admin_Settings_Level(array(
            'public' => ( in_array($level->type, array('public')) ),
            'moderator' => ( in_array($level->type, array('admin', 'moderator')) ),
        ));
        $settings = Engine_Api::_()->getApi('settings', 'core');
        $form->level_id->setValue($id);
 
        $permissionsTable = Engine_Api::_()->getDbtable('permissions', 'authorization');
        
        $form->populate($permissionsTable->getAllowed('ynsocialads_money', $id, array_keys($form->getValues())));
        $form->populate($permissionsTable->getAllowed('ynsocialads_ad', $id, array_keys($form->getValues())));
        
        if ($permissionsTable->getAllowed('ynsocialads_money', $id, 'min_amount') == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'min_amount'));
            if ($row) {
                $form->min_amount->setValue($row->value);
            }
        } 
        if ($permissionsTable->getAllowed('ynsocialads_money', $id, 'max_amount') == null) {
            $row = $permissionsTable->fetchRow($permissionsTable->select()
            ->where('level_id = ?', $id)
            ->where('type = ?', 'ynsocialads_money')
            ->where('name = ?', 'max_amount'));
            if ($row) {
                $form->max_amount->setValue($row->value);
            }
        }
        
        $credit = array();
        
        if (Engine_Api::_()->hasModuleBootstrap("yncredit")) {
            $typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
            $select = $typeTbl->select()->where("module = 'ynsocialads'")->where("action_type = 'ynsocialads_new'")->limit(1);
            $type = $typeTbl -> fetchRow($select);
			
			$select = $typeTbl->select()->where("module = 'yncredit'")->where("action_type = 'publish_ads'")->limit(1);
            $type_spend = $typeTbl -> fetchRow($select);
			
			if(!$type_spend) 
            {
                $type_spend = $typeTbl->createRow();
                $type_spend->module = 'yncredit';
                $type_spend->action_type = 'publish_ads';
                $type_spend->group = 'spend';
                $type_spend->content = 'Use credit to publish %s ads';
                $type_spend->credit_default = 0;
                $type_spend->link_params = '';
                $type_spend->save();
            }
			
            if(!$type) 
            {
                $type = $typeTbl->createRow();
                $type->module = 'ynsocialads';
                $type->action_type = 'ynsocialads_new';
                $type->group = 'earn';
                $type->content = 'Creating %s ads';
                $type->credit_default = 5;
                $type->link_params = '{"route":"ynsocialads_ads","action":"create-choose-package"}';
                $type->save();
            }
            $creditTbl = Engine_Api::_()->getDbTable("credits", "yncredit");
			
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
			
            $select = $creditTbl->select()
                ->where("level_id = ? ", $id)
                ->where("type_id = ?", $type->type_id)
                ->limit(1);
            $credit = $creditTbl->fetchRow($select);
            if(!$credit) 
            {
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
    
        // Check post
        if(!$this->getRequest()->isPost()) {
            return;
        }
    
        // Check validitiy
        if(!$form->isValid($this->getRequest()->getPost())) {
            return;
        }
    
        // Process
        $values = $form->getValues();
        $db = $permissionsTable->getAdapter();
        $db->beginTransaction();
        
        try {
            //credit
            if (Engine_Api::_() -> hasModuleBootstrap('yncredit')) {
                $moneyValues = array_slice($values, 0, 2);
                $permissionValues = array_slice($values, 2, 9);
                $creditValues = array_slice($values, 9);
                $credit->level_id = $id;
                $credit->type_id = $type -> type_id;
                $credit->setFromArray($creditValues);
                $credit->save();
            }
            else {
                $moneyValues = array_slice($values, 0, 2);
                $permissionValues = array_slice($values, 2);
            }
            // Set permissions
            //TODO
            //update if add more options
            
            $permissionsTable->setAllowed('ynsocialads_money', $id, $moneyValues);
            $permissionsTable->setAllowed('ynsocialads_ad', $id, $permissionValues);
             // Commit
            $db->commit();
            
            $form -> addNotice('Your changes have been saved.');
        }
    
        catch(Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

}
