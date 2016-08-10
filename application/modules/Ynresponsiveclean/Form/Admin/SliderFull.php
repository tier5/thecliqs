<?php
class Ynresponsiveclean_Form_Admin_SliderFull extends Engine_Form
{
	function init()
	{
		$this -> addElement('select', 'content_type', array(
			'label' => "Content Type",
			'required' => true,
			'multiOptions' => Engine_Api::_() -> ynresponsive1() -> getAllSupportedSliderContent()
		));
		$this->addElement('text','background_image', array('label'=>'Background Image', 'description'=>'Set url of background image for slider.'));
		$this->addElement('select','show_title', array('label'=>'Show Title', 'value'=>'1', 'multiOptions'=>array('1'=>'Yes','0'=>'No')));
		$this->addElement('select','show_description', array('label'=>'Show Description', 'value'=>'1', 'multiOptions'=>array('1'=>'Yes','0'=>'No')));
		$this->addElement('text', 'height', array('label'=>'Height (px)'));
		$this->addElement('text', 'image_height', array('label'=>'Image Height (100%)'));
	}
}
