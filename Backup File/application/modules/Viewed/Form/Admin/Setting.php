<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: General.php 10249 2014-05-30 22:38:38Z andres $
 * @author     John
 */

/**
 * @category   Application_Core
 * @package    Activity
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Viewed_Form_Admin_Setting extends Engine_Form
{
  public function init()
  {
  	// get member levels
  	$table = Engine_Api::_()->getDbTable('levels','authorization');
  	$select = $table->select();
  	$rows = $table->fetchAll($select);
  	$member_levels = array();
  	foreach ($rows as $member_level)
  	{
  		$member_levels[$member_level->level_id] = $member_level->title;
  	}
  	// get packages
  	$log =Zend_Registry::get('Zend_Log');
  	$packageTable = Engine_Api::_()->getDbTable('packages','payment');
  	$packageselect = $packageTable->select()
  									->where('enabled = 1');
  	$packagerows = $table->fetchAll($packageselect);
  	$packages = array();
        $packages[] = " "; 
	foreach ($packagerows as $package)
  	{
  		$packages[$package->package_id] = $package->title;
  	}
//  	array_unshift($packages, ' ');
  	$this->addElement('Select', 'level_id', array(
  			'label' => 'Member Level',
  			'multiOptions' => $member_levels,
  			'onchange' => 'javascript:fetchLevelSettings(this.value);'
  	));
    
    $this->addElement('Text','view_count',array(
    		'label' => 'Profile count',
    		'description' => 'Enter the profile count which can be seen by member',
    		'required' => true,
    		'allowEmpty'=>false,
    		'value'=>5	
    		));
    
    $this->addElement('Select', 'package_id', array(
    		'label' => 'Select Package',
    		'description' => 'If this field left blank for any member level, then that member level can view all "who viewed me" profiles. If you want any member level should subscribed to any particular package, to view all "who viewed me" profiles, then select a package.',
    		'required' => true,
    		'multiOptions' => $packages
    ));
    
    $this->addElement('Checkbox', 'test_mode', array(
    		'label' => 'If test mode is enabled, only super-admin can view this widget.',
    		'description' => 'Enable Test Mode',
    		'Options' => array(
    				1 => 'test_mode'
    				)
    ));
    
    $this->addElement('MultiCheckbox', 'exclude', array(
    		'label' => 'Exclude Member Levels',
    		'description' => 'Select the anonymity for the member levels, eg: Super-Admins, Admins',
    		'multiOptions' => $member_levels
    
    ));
    
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}
