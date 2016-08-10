<?php
/**

 */
class Yncontest_Model_List extends Core_Model_List
{
  protected $_owner_type = 'contest';

  protected $_child_type = 'user';
  
  protected $_type = 'yncontest_list';

  public $ignorePermCheck = true;

  public function getListItemTable()
  {
    return Engine_Api::_()->getItemTable('yncontest_list_item');
  }
}