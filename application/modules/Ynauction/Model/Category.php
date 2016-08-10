<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Category.php
 * @author     Minh Nguyen
 */
class Ynauction_Model_Category extends Core_Model_Item_Abstract
{
    protected $_searchTriggers = false;
  // Properties
  public function getTable()
  {
    if( is_null($this->_table) )
    {
      $this->_table = Engine_Api::_()->getDbtable('categories', 'ynauction');
    }

    return $this->_table;
  }
   public function setTitle($newTitle)
  {
    $this->title = $newTitle;
    $this->save();
    return $this;
  } 
  public function getCountYnauction()
  {
    $table = Engine_Api::_()->getDbTable('products', 'ynauction');
    return $table->fetchAll($table->select()->from($table->info('name'),"Count(product_id) as count")->where('cat_id = ?',$this->getIdentity())->where('is_delete = 0'))->toArray(); 
  }
  public function getParentCountYnauction()
  {
    $table = Engine_Api::_()->getDbTable('products', 'ynauction');
    $sudcategory =  Engine_Api::_()->ynauction()->getCategories($this->getIdentity());
    $arr = array();
    $count = 0;
    foreach($sudcategory as $sub)
    {
       $subC =  $sub-> getCountYnauction();
       $count += $subC[0]['count'];
    }
    $PCount = $this-> getCountYnauction();
    return $count+= $PCount[0]['count']; 
  }
}