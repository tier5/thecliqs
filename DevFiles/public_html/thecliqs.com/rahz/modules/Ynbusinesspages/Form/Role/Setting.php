<?php
class Ynbusinesspages_Form_Role_Setting extends Engine_Form
{
	protected $_businessId;
	protected $_roleId;
	
	public function getBusinessId()
	{
		return $this -> _businessId;
	}
	
	public function setBusinessId($businessId)
	{
		$this -> _businessId = $businessId;
	} 
	
	public function getRoleId()
	{
		return $this -> _roleId;
	}
	
	public function setRoleId($roleId)
	{
		$this -> _roleId = $roleId;
	} 
	
	public function init()
  	{
	    //Clone Role Element
	    $listTbl = Engine_Api::_()->getItemTable('ynbusinesspages_list');
		$options = $listTbl -> getListAssocByBusiness($this->_businessId);
		unset($options[0]);
		$currentRoleId = $this->_roleId;
		$currentRole = Engine_Api::_()->getItem('ynbusinesspages_list', $currentRoleId);
	    if ($this->_roleId == 0)
	    {
	    	foreach ($options as $k => $v)
	    	{
	    		$currentRoleId = $k;
	    		break;
	    	}
	    }
	    $this->addElement('Select', 'clone_list_id', array(
	      	'label' => 'Select an existing role',
		 	'multiOptions' => $options,
	    	'value' => $currentRoleId,
	    	'onchange' => "selectRole(this);"
	    ));
	    
	    /**
	     * 
	     * @todo get setting belong enabled module.
	     */
		$role = Engine_Api::_()->getItem('ynbusinesspages_list', $currentRoleId);
		$rolePrivacy = $role->privacy;
		
		if (is_null($rolePrivacy))
		{
			$rolePrivacy = array();
		}
	    $moduleSettingTbl = Engine_Api::_()->getDbTable('modulesettings', 'ynbusinesspages');
	    $settings = $moduleSettingTbl -> getEnabledModuleSettings();
	    $translate = Zend_Registry::get("Zend_Translate");
	    if (count($settings))
	    {
	    	foreach ($settings as $setting)
	    	{
	    		//INIT OPTIONS
    			$options = array();
    			if ($setting -> edit_or_delete == '1')
    			{
    				$options['2'] = 'Yes, allow member to ' . $setting->getActionText(1);
    			}
    			$options['1'] = $translate->_('Yes, allow member to ') . $setting->getActionText();
    			$options['0'] = 'No, do not allow';
    				
    			//INIT VALUE
    			$value = 0;
    			if ( in_array($setting->key, array_keys($rolePrivacy)) && $rolePrivacy[$setting->key] == '1' )
    			{
    				$value = 1;
    			}
    			else if ( in_array($setting->key, array_keys($rolePrivacy)) && $rolePrivacy[$setting->key] == '2' ){
    				$value = 2;
    			}
	    		
	    		if ($currentRole -> type == 'non-registered')
	    		{
	    			if ($setting->key == 'view')
	    			{
	    				$this->addElement('radio', $setting->key, array(
					      	'label' => $setting->title,
	    					'multiOptions' => $options,
						 	'value' => $value,
					    ));
	    			}
	    			break;
	    		}
	    		else 
	    		{
    				$this->addElement('radio', $setting->key, array(
				      	'label' => $setting->title,
    					'multiOptions' => $options,
					 	'value' => $value,
				    ));
	    		}
	    		
	    	}
	    }
	    
	    // Buttons
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Save Changes',
	      'type' => 'submit',
	      'ignore' => true,
	      'decorators' => array('ViewHelper')
	    ));
  	}
}
