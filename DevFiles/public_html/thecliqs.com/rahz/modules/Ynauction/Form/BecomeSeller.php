<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: CreateAccount.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_BecomeSeller extends Engine_Form
{
  protected $_account;
  public function init()
  {
    // Init form
    $this
      ->setTitle('Request to become Auction Seller')
      ->setAttrib('id',      'form-become-seller')
      ->setAttrib('name',    'become_seller')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ->setDescription('Enter your Seller infomation.')
      ;
    $viewer = Engine_Api::_()->user()->getViewer();
    // Init conact name
    $this->addElement('Text', 'displayname', array(
      'label' => 'Contact Name',
      'maxlength' => '63',
      //'required' => true,
	  'value' =>$viewer->displayname,
	  'readonly'=>true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
	// Init conact address
    $this->addElement('Text', 'address', array(
      'label' => 'Contact Address',
      'maxlength' => '255',
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '255')),
      )
    ));
	// Init conact address
    $this->addElement('Text', 'phone', array(
      'label' => 'Contact Phone',
      'maxlength' => '63',
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '63')),
      )
    ));
	// Init conact email
    $this->addElement('Text', 'email', array(
      'label' => 'Contact Email',
      'maxlength' => '128',
	  'value' =>$viewer->email,
	  'readonly'=>true,
      'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '128')),
      )
    ));
	// Init conact name
    $this->addElement('Text', 'account_username', array(
      'label' => 'Paypal Email',
      'maxlength' => '128',
      //'required' => true,
      'filters' => array(
        new Engine_Filter_StringLength(array('max' => '128')),
      )
    ));
	$this->addElement('Checkbox', 'check', array(
      'label' => 'I have read & agreed to the ',
      'value' => 0,     
      'checked' => false,
	  'required' => false,
    ));  
	$this->addElement('Cancel', 'link', array(
      'label' => 'Term of Service',
      'link' => true,
      'onclick' => 'goto()',   
      'decorators' => array(
        'ViewHelper'
      )
    ));
    $this->addDisplayGroup(array('check', 'link'), 'buttons', array(
      'decorators' => array(
        'FormElements',
        'DivDivDivWrapper'
      )));	
    // Init submit
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type'  => 'submit',
    ));
	
  }

  public function saveValues()
  {
      $values   = $this->getValues(); 
	  $viewer = Engine_Api::_()->user()->getViewer();
      $flag = true;
      if($values['check'] == 0)
      {
           $this->getElement('link')->addError('Please complete this field - it is required. You must agree to the terms of service to continue.'); 
            $flag = false ;
      }
      if(trim($values['account_username']) == "")
      {
           $this->getElement('account_username')->addError('Please complete this field - it is required.'); 
           $flag =  false;
      }
      else if(trim($values['account_username'] != ""))
      {
          $email = trim($values['account_username']);
          $regexp = "/^[A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/";                                                                                                            
        if(!preg_match($regexp, $email))
        {
            $is_validate=1;
            $this->getElement('account_username')->addError('Finance Account is not valid!'); 
            $flag =  false ;
        }
      }
      if($flag ==  false)
        return;
	  if(!Engine_Api::_()->ynauction()->checkBecome($viewer->getIdentity()))
	  {
		$table = Engine_Api::_()->getItemTable('ynauction_become');
		$db = $table->getAdapter();
		$db->beginTransaction();
		$become = $table->createRow();
		$become->user_id = $viewer->getIdentity();
		$become->address = $values['address'];
		$become->phone = $values['phone'];
        $autoApprove = Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynauction_product', $viewer, 'approve_seller');  
        if($autoApprove)
        {
           $become->approved = 1; 
        }
		$become->save();
		$db->commit();
	}
    Ynauction_Api_Account::insertAccount($values);  
    return $become; 
  }
}
