<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       07.08.12
 * @time       11:04
 */

class Donation_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  public function getTable()
  {
    if( null === $this->_table ) {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'donation');
    }

    return $this->_table;
  }

  public function getUsedCount()
  {
    $eventTable = Engine_Api::_()->getItemTable('donation');
    return $eventTable->select()
      ->from($eventTable, new Zend_Db_Expr('COUNT(donation_id)'))
      ->where('category_id = ?', $this->category_id)
      ->query()
      ->fetchColumn();
  }
}
