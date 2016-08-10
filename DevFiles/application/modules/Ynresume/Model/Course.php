<?php
class Ynresume_Model_Course extends Core_Model_Item_Abstract 
{
    protected $_type = 'ynresume_course';
    protected $_parent_type = 'ynresume_resume';
    protected $_searchTriggers = false;
    
	public function renderText()
    {
        return '';
    }
}
