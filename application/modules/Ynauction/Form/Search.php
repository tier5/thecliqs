<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Search.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Search extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px'
      ))
	  -> setMethod('GET')
      -> setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'listing'), 'ynauction_general'));
    
    $this->addElement('Text', 'search', array(
      'label' => 'Search Auctions',
    ));

    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'featured' => 'Featured',
        'creation_date' => 'Most Recent',
        'start_time' => 'Start Time',
        'currency_symbol' => 'Currency',
        'total_bids' => 'Total Bids',
      ),
    ));

    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
         ' ' => 'All',
        '2' => 'Upcoming',
        '3' => 'Running',  
        '6' => 'Ended',  
      ),
      'value'=> 3,
    ));

    $this->addElement('Select', 'category', array(
      'label' => 'Category',
       'style' => 'width:160px',
      'multiOptions' => array(
        '0' => 'All Categories',
      ),
      'onchange' => 'this.form.submit();',
    ));
    
    $this->addElement('Select', 'subcategory', array(
      'label' => 'SubCategory',
       'style' => 'width:160px',
      'multiOptions' => array(
        '0' => 'All SubCategories',
      ),
    ));
	
	// Buttons
	$this -> addElement('Button', 'Search', array(
		'label' => 'Search',
		'type' => 'submit',
	));
	
    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'start_time', array(
      'order' => 102
    ));

    $this->addElement('Hidden', 'end_time', array(
      'order' => 103
    ));
  }
}
