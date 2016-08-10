<?php

class Ynbusinesspages_Model_Category extends Ynbusinesspages_Model_Node {
	
	protected $_searchTriggers = false;
	protected $_parent_type = 'user';
	protected $_owner_type = 'user';
	protected $_type = 'ynbusinesspages_category';
    
	public function getParentCategoryLevel1()
	{
		$i = 1;
		$loop_item = $this;
		while($i < 4)
		{
			$item = $loop_item -> getParent($loop_item -> getIdentity());
			if(count($item->themes) > 0)
			{
				return $item;
			}
			$loop_item = $item;
			$i++;
		}
	}
	
	public function getParent($category_id = null)
	{
		$item = Engine_Api::_()->getItem('ynbusinesspages_category', $category_id);
		$parent_item = Engine_Api::_()->getItem('ynbusinesspages_category', $item->parent_id);
		return $parent_item;
	}
	
	public function getHref($params = array()) {
	    $params = array_merge(array(
            'route' => 'ynbusinesspages_general',
            'controller' => 'index',
            'action' => 'listing',
            'category_id' => $this->getIdentity(),
        ), $params);
        $route = $params['route'];
        unset($params['route']);
        return Zend_Controller_Front::getInstance()->getRouter()
        ->assemble($params, $route, true);
	}
    
	public function getTable() {
		if(is_null($this -> _table)) {
			$this -> _table = Engine_Api::_() -> getDbtable('categories', 'ynbusinesspages');
		}
		return $this -> _table;
	}
	
	public function checkHasBusiness()
	{
		$table = Engine_Api::_() -> getDbTable('categorymaps', 'ynbusinesspages');
		$select = $table -> select() -> where('category_id = ?', $this->getIdentity()) -> limit(1);
		$row = $table -> fetchRow($select);
		if($row)
			return true;
		else {
			return false;
		}
	}
	
	public function getMoveCategoriesByLevel($level)
	{
		$table = Engine_Api::_() -> getDbtable('categories', 'ynbusinesspages');
		$select = $table -> select() 
				-> where('category_id <>  ?', 1) // not default
				-> where('category_id <>  ?', $this->getIdentity())// not itseft
				-> where('level = ?', $level);
		$result = $table -> fetchAll($select);
		return $result;
	}
	
	public function setTitle($newTitle) {
		$this -> title = $newTitle;
		$this -> save();
		return $this;
	}

	public function shortTitle() {
		return strlen($this -> title) > 20 ? (substr($this -> title, 0, 17) . '...') : $this -> title;
	}
	
	public function setPhoto($photo)
	{
		if ($photo instanceof Zend_Form_Element_File)
		{
			$file = $photo -> getFileName();
		}
		else
		if (is_array($photo) && !empty($photo['tmp_name']))
		{
			$file = $photo['tmp_name'];
		}
		else
		if (is_string($photo) && file_exists($photo))
		{
			$file = $photo;
		}
		else
		{
			throw new Ynbusinesspages_Model_Exception('Invalid argument passed to setPhoto: ' . print_r($photo, 1));
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array(
			'parent_type' => 'ynbusinesspages',
			'parent_id' => $this -> getIdentity()
		);
		// Save
		$storage = Engine_Api::_ ()->storage ();

		// Resize image (main)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 720, 720 )->write ( $path . '/m_' . $name )->destroy ();

		// Resize image (profile)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 240, 240 )->write ( $path . '/p_' . $name )->destroy ();

		// Resize image (normal)
		$image = Engine_Image::factory ();
		$image->open ( $file )->resize ( 110, 110 )->write ( $path . '/in_' . $name )->destroy ();

		// Resize image (icon)
		$image = Engine_Image::factory ();
		$image->open ( $file );

		$size = min ( $image->height, $image->width );
		$x = ($image->width - $size) / 2;
		$y = ($image->height - $size) / 2;

		$image->resample ( $x, $y, $size, $size, 50, 50 )->write ( $path . '/is_' . $name )->destroy ();

		// Store
		$iMain = $storage->create ( $path . '/m_' . $name, $params );
		$iProfile = $storage->create ( $path . '/p_' . $name, $params );
		$iIconNormal = $storage->create ( $path . '/in_' . $name, $params );
		$iSquare = $storage->create ( $path . '/is_' . $name, $params );

		$iMain->bridge ( $iProfile, 'thumb.profile' );
		$iMain->bridge ( $iIconNormal, 'thumb.normal' );
		$iMain->bridge ( $iSquare, 'thumb.icon' );

		// Remove temp files
		@unlink ( $path . '/p_' . $name );
		@unlink ( $path . '/m_' . $name );
		@unlink ( $path . '/in_' . $name );
		@unlink ( $path . '/is_' . $name );
		// Update row
		$this -> photo_id = $iMain -> getIdentity();
		$this -> save();

		return $this;
	}

    public function getChildList() {
        $table = Engine_Api::_()->getItemTable('ynbusinesspages_category');
        $select = $table->select();
        $select->where('parent_id = ?', $this->getIdentity());
        $childList = $table->fetchAll($select);
        return $childList;
    }
    
    public function getCustomFieldsList() {
        $mapData = Engine_Api::_()->getApi('core', 'fields')->getFieldsMaps('ynbusinesspages_business');
        $option_id = $this->option_id;
        $secondLevelMaps = array();
        $secondLevelFields = array();
        if( !empty($option_id) ) {
            $secondLevelMaps = $mapData->getRowsMatching('option_id', $option_id);
            if( !empty($secondLevelMaps) ) {
                foreach( $secondLevelMaps as $map ) {
                    $secondLevelFields[$map->child_id] = $map->getChild();
                }
            }
        }
        return $secondLevelFields;
    }
    
    public function getNumOfBusinesses() {
        $table = Engine_Api::_()->getDbTable('business', 'ynbusinesspages');
        $select = $table->getBusinessesSelect(array('category_id'=>$this->getIdentity()));
        $rows = $table->fetchAll($select);
        return count($rows);
    }
    
    public function getTitle() {
        $view = Zend_Registry::get('Zend_View');
        return $view->translate($this->title);
    }
}
