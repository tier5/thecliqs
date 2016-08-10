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
 * @time       11:01
 */
class Donation_Model_DbTable_Categories extends Engine_Db_Table
{
  protected $_rowClass = 'Donation_Model_Category';

  public function getCategoriesAssoc()
  {
    $stmt = $this->select()
      ->from($this, array('category_id', 'title'))
      ->order('title ASC')
      ->query();

    $categories = array();
    foreach( $stmt->fetchAll() as $category ) {
      $categories[$category['category_id']] = $category['title'];
    }

    return $categories;
  }
}
