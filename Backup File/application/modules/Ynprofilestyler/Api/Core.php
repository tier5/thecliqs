<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynprofilestyler
 * @author     YouNet Company
 */

class Ynprofilestyler_Api_Core extends Core_Api_Abstract
{
	public function getTempLayout()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$layout = $this->getLayout(0);
		if (!is_object($layout))
		{
			throw new Engine_Exception('There is no temporary layouts. Please contact the administrator');
		}
		return $layout;
	}

	public function getLayout($layoutId)
	{
		$layoutTable = new Ynprofilestyler_Model_DbTable_Layouts;
		return $layoutTable->fetchRow(array('layout_id = ?' => $layoutId));
	}

	public function getLayoutFromUser($userId)
	{
		$layoutTable = new Ynprofilestyler_Model_DbTable_Layouts;
		$layoutTableName = $layoutTable->info('name');
		$userTable = new Ynprofilestyler_Model_DbTable_Users;
		$userTableName = $userTable->info('name');

		$select = $layoutTable->select()->from($layoutTableName)->setIntegrityCheck(false)->join($userTableName, "$layoutTableName.layout_id = $userTableName.layout_id", "$layoutTableName.*")->where("$userTableName.user_id = ?", $userId);

		return $layoutTable->fetchRow($select);
	}

	public function getViewerLayout()
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		$layouts = new Ynprofilestyler_Model_DbTable_Layouts;
		$layout = $this->getLayoutFromUser($viewer->getIdentity());
		if (!$layout)
		{
			$layout = $layouts->createRow(array(
				'user_id'       => $viewer->getIdentity(),
				'creation_date' => date('Y-m-d H:i:s')
			));
			$layout->save();
			$users = new Ynprofilestyler_Model_DbTable_Users;
			$user = $users->createRow(array(
				'user_id'   => $viewer->getIdentity(),
				'layout_id' => $layout->getIdentity(),
			));
			$user->save();
		}
		return $layout;
	}

	public function getAllRuleSettings()
	{
		$cacheKey = 'ynps_all_rules';
			
		$cache =  NULL;
		$data = FALSE;
			
		if(Zend_Registry::isRegistered('Zend_Cache')){
			$cache = Zend_Registry::get('Zend_Cache');
		}
			
		if($cache && ($data = $cache->load($cacheKey)) != false){
			return $data;
		}
		
		$model = new Ynprofilestyler_Model_DbTable_Rules;
		$select = $model->select();
		$result = array();
		foreach ($model->fetchAll() as $rule)
		{
		    $value = ($rule->default && $rule->preview == 1)?$rule->default:'';
			array_push($result, array(
				'rule_id'      => $rule->getIdentity(),
				'name'         => $rule->name,
				'dompath'      => $rule->dompath,
				'value'        => $value,
				'type'         => $rule->rule_type,
				'rulegroup_id' => $rule->rulegroup_id
			));
		}
		
		if($cache && !empty($result))
		{
			$cache->save($result,$cacheKey);	
		}
			
		return $result;
	}

	public function bindRuleIds($form, $groupName = null, $ruleGroupId = null)
	{
		if ($groupName == null)
		{
			$groupTitle = str_replace('Ynprofilestyler_Form_Custom_', '', get_class($form));
			$results = preg_split("/(?<=[a-z])(?![a-z])/", $groupTitle, -1, PREG_SPLIT_NO_EMPTY);
			$groupName = '';
			foreach ($results as $str)
			{
				$groupName .= '-' . strtolower($str);
			}
			if (is_string($groupName) && strlen($groupName) > 0)
			{
				$groupName = substr($groupName, 1);
			}
		}

		$model = new Ynprofilestyler_Model_DbTable_Rulegroups;
		$select = $model->select()->where('group_name = ?', strtolower($groupName));
		$group = $model->fetchRow($select);
		if (!is_object($group))
		{
			return;
		}

		$model = new Ynprofilestyler_Model_DbTable_Rules;
		$select = $model->select()->where('rulegroup_id = ?', $group->rulegroup_id);

		foreach ($model->fetchAll($select) as $rule)
		{
			$name = str_replace('-', '_', $rule->name);
			$ele = $form->getElement($name);
			if (is_object($ele) && method_exists($ele, 'setAttrib'))
			{
				$ele->setAttrib('rule_id', $rule->rule_id);
				$ele->setAttrib('class', 'rule-element ' . $rule->rule_type);
    			if ($rule->control_type == 'hidden' && $rule->preview == 0) {
    			    $ele->setAttrib('preview', 0);
    			    if ($rule->default) {
    			        $ele->setValue($rule->default);
    			    }
    			}
			}
			if ($rule->control_type == 'select')
			{
				$ruleOptionTable = new Ynprofilestyler_Model_DbTable_Ruleoptions;
				$options = $ruleOptionTable->getOptionsForRule($rule->rule_id);
			}
			elseif ($rule->control_type == 'image')
			{
				$options = Ynprofilestyler_Plugin_Constants::getExistingImagesMultiOptions();
			} 
			
			if (isset($options) && is_array($options) && method_exists($ele, 'addMultiOptions'))
			{
				$ele->addMultiOptions($options);
			}
		}
	}

	/**
	 *
	 * Get CSS style inline string from an array of rules
	 * @param Array $rules
	 * @return String
	 */
	public function getStyleStringFromRules($rules)
	{
		$style = '';
		if (is_array($rules) && count($rules) > 0)
		{
			$models = new Ynprofilestyler_Model_DbTable_Rules;

			$ruleIds = array();
			foreach ($rules as $rule)
			{
				array_push($ruleIds, $rule['rule_id']);
			}
			$select = $models->select()->where('rule_id in (?)', $ruleIds);

			$arrRules = array();

			foreach ($models->fetchAll($select) as $rule)
			{
				$arrRules[$rule->rule_id] = $rule;
			}

			foreach ($rules as $rule)
			{
				$r = $arrRules["{$rule['rule_id']}"];

				$style .= $r->dompath . ' { ' . $r->name . ' : ' . $this->_formatValyeByType($rule['value'], $r->rule_type) . ';}';
			}
		}

		return $style;
	}

	public function isUserLayoutAllowedApply($userId)
	{
		$viewer = Engine_Api::_()->user()->getViewer();
		if ($viewer->getIdentity() != $userId)
		{
			$subject = Engine_Api::_()->user()->getUser($userId);
			$style_perm = Engine_Api::_()->getDbtable('permissions', 'authorization')->getAllowed('user', $subject->level_id, 'style');

			$users = new Ynprofilestyler_Model_DbTable_Users;
			$select = $users->select()->where('user_id = ?', $subject->getIdentity());
			$user = $users->fetchRow($select);

			if (is_object($user))
			{
				return $user->is_allowed;
			}
		}
		return false;
	}
	
	public function getViewerSlideshow() {
		$layout = $this->getViewerLayout();
		
		$slideshowTable = new Ynprofilestyler_Model_DbTable_Slideshows();
		$select = $slideshowTable->select()->where('layout_id = ?', $layout->getIdentity());
		
		$slideshow = $slideshowTable->fetchRow($select);
		if ($slideshow == NULL) {
			$slideshow = $slideshowTable->createRow(array(
					'layout_id' => $layout->getIdentity()
			));
			$slideshow->save();
		}
		
		return $slideshow;
	}
	
	public function addSlide($url) {
		$slideshow = $this->getViewerSlideshow();		
		$slideshow->addSlide($url);		
	}
	
	public function getUserSlideshow($userId) {
		$layoutTable = new Ynprofilestyler_Model_DbTable_Layouts();
		$layoutTableName = $layoutTable->info('name');
		
		$slideshowTable = new Ynprofilestyler_Model_DbTable_Slideshows();
		$slideshowTableName = $slideshowTable->info('name');
		
		$select = $slideshowTable->select()->from($slideshowTableName)->setIntegrityCheck(false);
		$select->join($layoutTableName, "$layoutTableName.layout_id = $slideshowTableName.layout_id");
		$select->where("$layoutTableName.user_id = ?", $userId);
		
		return $slideshowTable->fetchRow($select);
	}
	
	public function getUserSlides($userId) {
		$slideTable = new Ynprofilestyler_Model_DbTable_Slides();
		$slideTableName = $slideTable->info('name');
		
		$layoutTable = new Ynprofilestyler_Model_DbTable_Layouts();
		$layoutTableName = $layoutTable->info('name');
		
		$slideshowTable = new Ynprofilestyler_Model_DbTable_Slideshows();
		$slideshowTableName = $slideshowTable->info('name');

		$select = $slideTable->select()->from($slideTableName)->setIntegrityCheck(false);
		$select->join($slideshowTableName, "$slideshowTableName.slideshow_id = $slideTableName.slideshow_id");
		$select->join($layoutTableName, "$layoutTableName.layout_id = $slideshowTableName.layout_id");
		
		$select->where("$layoutTableName.user_id = ?", $userId);
		$select->where("$slideTableName.published = 1");

		return $slideTable->fetchAll($select);
	} 

	public function getProfileStyle($user) {
		$style = '';
		if(Engine_Api::_()->getDbTable('permissions', 'authorization')->getAllowed('user', $user->level_id, 'style')){
			// Get styles
			$table = Engine_Api::_()->getDbTable('styles', 'core');
			$select = $table->select()
			->where('type = ?', $user->getType())
			->where('id = ?', $user->getIdentity())->limit();
		
			$row = $table->fetchRow($select);
			if(null !== $row && !empty($row->style))
			{
				$style = $row->style;
			}
		}
		return $style;	
	}
	
	private function _formatValyeByType($value, $type)
	{
		switch ($type)
		{
			case 'color':
				return '#' . $value;
			case 'image':
				return 'url(' . $value . ')';
			case 'size':
				if (is_numeric($value))
				{
					return $value . 'pt';
				}
			default:
				return $value;
		}
	}
}
