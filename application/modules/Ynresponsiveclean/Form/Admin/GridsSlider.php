<?php
class Ynresponsiveclean_Form_Admin_GridsSlider extends Engine_Form
{
	function init()
	{
		$this -> addElement('select', 'content_type', array(
			'label' => 'Content Type',
			'required' => true,
			'multiOptions' => Engine_Api::_() -> ynresponsive1() -> getAllSupportedSliderContent()
		));

		$this -> addElement('select', 'col_style', array(
			'label' => 'Grid Style',
			'required' => true,
			'multiOptions' => array(
				1 => 'Poster photo at left side',
				2 => 'Large item\'s at top',
			)
		));

		$this -> addElement('select', 'num_cols', array(
			'label' => 'Number Of Columns',
			'value' => 1,
			'multiOptions' => array(
				'1' => '1 Column',
				'2' => '2 Columns',
				'3' => '3 Columns',
				'4' => '4 Columns'
			),
		));
	}

}
