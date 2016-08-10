<?php
class Ynultimatevideo_Form_Custom_FieldsParent extends Fields_Form_Standard
{
	protected $_fieldType = 'ynultimatevideo_video';

	public $_error = array();

	protected $_name = 'fieldsParent';

	protected $_elementsBelongTo = 'fieldsParent';

	public function init()
	{
		// custom classified fields
		if( !$this->_item ) {
			$video = new Ynultimatevideo_Model_Video(array());
			$this->setItem($video);
		}
		parent::init();
		$this->removeElement('submit');
	}

	public function loadDefaultDecorators()
	{
		if( $this->loadDefaultDecoratorsIsDisabled() )
		{
			return;
		}

		$decorators = $this->getDecorators();
		if( empty($decorators) )
		{
			$this
			->addDecorator('FormElements')
			;
		}
	}
}