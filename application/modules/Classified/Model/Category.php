<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Category.php 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Classified_Model_Category extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;
  
  public function getTitle()
  {
    return $this->category_name;
  }
  
  public function getUsedCount()
  {
    $classifiedTable = Engine_Api::_()->getItemTable('classified');
    return $classifiedTable->select()
        ->from($classifiedTable, new Zend_Db_Expr('COUNT(classified_id)'))
        ->where('category_id = ?', $this->category_id)
        ->query()
        ->fetchColumn();
  }

  public function isOwner($owner)
  {
    return false;
  }

  public function getOwner()
  {
    return $this;
  }
}
