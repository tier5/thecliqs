<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Subscription.php 10154 2014-04-08 18:47:01Z lucas $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Viewed_Form_Signup_Subscription extends Engine_Form
{
  protected $_isSignup = true;
  
  protected $_packages;
  
  public function setIsSignup($flag)
  {
    $this->_isSignup = (bool) $flag;
  }
  
  public function init()
  {
  	$log = Zend_Registry::get('Zend_Log');
    $this
      ->setTitle('Subscription Plan')
      ->setDescription('Please select a subscription plan from the list below.')
      ;
      //get viewer level_id
      $viewer = Engine_Api::_()->user()->getViewer();
      $level_id = $viewer->level_id;

      //get member_level packages
      
      $memberpackage_table = Engine_Api::_()->getDbTable('membercounts','viewed');
      $memberpackage_select = $memberpackage_table->select()
      												->where('level_id = ?',$level_id);
      $memberpackage_result = $memberpackage_table->fetchRow($memberpackage_select);
      if(isset($memberpackage_result) && count($memberpackage_result)>0)
      {
      	$package_id = $memberpackage_result->package_id;
      	$log->log('package id'.$package_id,Zend_Log::DEBUG);
      }
      
    // Get available subscriptions
    $packagesTable = Engine_Api::_()->getDbtable('packages', 'payment');
    $packagesSelect = $packagesTable
      ->select()
      ->from($packagesTable)
      ->where('enabled = ?', true);
    
    if(isset($package_id) && !($package_id == 0))
    {
       $packagesSelect ->where('package_id = ?',$package_id);
    }
    elseif( $this->_isSignup ) {
      $packagesSelect->where('signup = ?', true);
    }
    else{
      $packagesSelect->where('after_signup = ?', true);
    }

    $multiOptions = array();
    $this->_packages = $packagesTable->fetchAll($packagesSelect);
    foreach( $this->_packages as $package ) {
      $multiOptions[$package->package_id] = $package->title
        . ' (' . $package->getPackageDescription() . ')'
        ;
    }
    
    // Element: package_id
    //if( count($multiOptions) > 1 ) {
      $this->addElement('Radio', 'package_id', array(
        'label' => 'Choose Plan:',
        'required' => true,
        'allowEmpty' => false,
        'multiOptions' => $multiOptions,
      ));
    //}

    
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Continue',
      'type' => 'submit',
      'ignore' => true,
    ));
  }
  
  public function getPackages()
  {
    return $this->_packages;
  }
  
  public function setPackages($packages)
  {
    $this->_packages = $packages;
  }
}