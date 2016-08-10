<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Categories.php 03.02.12 16:24 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Hegift_Model_DbTable_Categories extends Engine_Db_Table
{
  public function getCategories()
  {
    return Zend_Paginator::factory($this->select());
  }

  public function getCategoriesArray($params = array())
  {
    $Categories = $params;
    $categories = $this->fetchAll();
    foreach ($categories as $category) {
      $Categories[$category->category_id] = $category->title;
    }
    return $Categories;
  }

  public function deleteCategory($category_id)
  {
    /**
     * @var $table Hegift_Model_DbTable_Gifts
     */

    $table = Engine_Api::_()->getDbTable('gifts', 'hegift');
    $table->setDefaultCategory($category_id);

    $row = $this->fetchRow("category_id = {$category_id}");
    $row->delete();
  }

  public function renameCategory($category_id, $title)
  {
    $row = $this->fetchRow("category_id = {$category_id}");
    $row->title = $title;
    $row->save();
  }
}
