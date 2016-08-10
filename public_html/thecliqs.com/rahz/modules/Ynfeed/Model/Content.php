<?php
class Ynfeed_Model_Content extends Core_Model_Item_Abstract {

	protected $_searchTriggers = false;
	protected $_modifiedTriggers = false;
	public function setPhoto($photo) {
		if ($photo instanceof Zend_Form_Element_File) {
			$file = $photo -> getFileName();
		} else if (is_array($photo) && !empty($photo['tmp_name'])) {
			$file = $photo['tmp_name'];
		} else if (is_string($photo) && file_exists($photo)) {
			$file = $photo;
		} else {
			throw new Event_Model_Exception('invalid argument passed to setPhoto');
		}

		$name = basename($file);
		$path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
		$params = array('parent_id' => $this -> getIdentity(), 'parent_type' => 'ynfeed');

		// Save
		$storage = Engine_Api::_() -> storage();

		// Resize image (icon)
		$image = Engine_Image::factory();
		$image -> open($file) -> resize(16, 16) -> write($path . '/ic_' . $name) -> destroy();

		// Store
		$iMain = $storage -> create($path . '/ic_' . $name, $params);

		// Remove temp files
		@unlink($path . '/ic_' . $name);

		// Update row
		$this -> photo_id = $iMain -> file_id;
		$this -> save();
		return $this;
	}

}
