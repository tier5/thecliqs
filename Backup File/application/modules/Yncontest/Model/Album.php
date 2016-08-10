<?php
class Yncontest_Model_Album extends Core_Model_Item_Collection
{
  protected $_parent_type = 'contest';

  protected $_owner_type = 'contest';

  protected $_children_types = array('yncontest_photo');

  protected $_collectible_type = 'yncontest_photo';

  protected $_searchTriggers = false;
  
  
}