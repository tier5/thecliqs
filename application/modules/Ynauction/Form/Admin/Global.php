<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Global.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    
    $this
      ->setTitle('Global Settings')
      ->setDescription('These settings affect all members in your community.');
      $this->addElement('Radio', 'ynauction_mode', array(
        'label' => '*Enable test mode',
        'description' => 'Allow admin to test auction by using development mode? ',
        'multiOptions' => array(
          1 => 'Yes',
          0 => 'No'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.mode', 1),
      ));
      
       // Element: currency
        $this->addElement('Select', 'ynauction_currency', array(
          'label' => 'Currency',
          'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.currency', 'USD'),
          'description' => '',
        ));
        $this->getElement('ynauction_currency')->getDecorator('Description')->setOption('placement', 'APPEND');
     $this->addElement('Radio', 'ynauction_block', array(
        'label' => 'The seller can bid for their items?',
        'description' => 'IP blocking to prevent the seller from self-bidding their items ',
        'multiOptions' => array(
          1 => 'Yes, allow seller bid for their items.',
          0 => 'No, do not allow seller bid for their items.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.block', 1),
      ));
   $this->addElement('Text', 'ynauction_timeupdate', array(
      'label' => 'Time Update (s)',
      'description' => 'Set time to update page.',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.timeupdate', 5),
    ));
    $this->addElement('Radio', 'ynauction_notify', array(
        'label' => 'Will notify to all relevant users?',
        'description' => 'Will notify to all relevant users when a new bid is placed?',
        'multiOptions' => array(
          2 => 'Yes, notify to all relevant users.',
          1 => 'Yes, just notify to product owner and nearest bid price bidders.',
          0 => 'No, do not notify to all relevant users.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.notify', 0),
      ));
	  
  	$this->addElement('Radio', 'ynauction_latestbid', array(
    	'label' => 'The latest bidder can continue bidding?',
    	'description' => 'Setting to allow the latest bidder to continue bidding',
    	'multiOptions' => array(
      		1 => 'Yes, allow the latest bidder to continue bidding.',
          	0 => 'No, do not allow the latest bidder to continue bidding.'
        ),
        'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.latestbid', 0),
  	));
	 
    $this->addElement('Text', 'ynauction_page', array(
      'label' => 'Number of auctions per page',
      'description' => 'How many auctions will be shown per page? (Enter a number between 1 and 999)',
      'value' => Engine_Api::_()->getApi('settings', 'core')->getSetting('ynauction.page', 12),
    ));

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}