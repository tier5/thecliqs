<?php
class Yncontest_Model_Photo extends Core_Model_Item_Collectible
{
	protected $_parent_type = 'contest';
	
	protected $_owner_type = 'user';
	
	protected $_collection_type = 'contest';
	
	protected $_searchTriggers = false;
}