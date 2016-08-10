<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Edit.php
 * @author     Minh Nguyen
 */
class Ynauction_Form_Edit extends Ynauction_Form_Create
{
  public $_error = array();
  protected $_item;

  public function getItem()
  {
    return $this->_item;
  }

  public function setItem(Core_Model_Item_Abstract $item)
  {
    $this->_item = $item;
    return $this;
  }  
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Auction')
         ->setDescription("Edit your auction below, then click 'Save Changes' to save your auction.");
     $this->addElement('Radio', 'cover', array(
      'label' => 'Album Cover',
    ));
    $this->submit->setLabel('Save Changes');
  }
}