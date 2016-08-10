<?php
class Ynlistings_Form_Video_Search extends Engine_Form
{
	public function init()
	{
		$translate = Zend_Registry::get("Zend_Translate");
		//Form Attribute and Method
		$this -> setAttribs(array(
			'id' => 'filter_form',
		)) -> setMethod('GET') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('page' => null)));

		$this -> addElement('Hidden', 'page');
		//Search Text
		$this -> addElement('Text', 'title', array(
			'label' => 'Search Videos',
			'alt' => $translate->translate('Search videos'),
		));

		//Closed
		$this -> addElement('Text', 'owner', array('label' => 'Member', ));

		//Order
		$this -> addElement('Select', 'orderby', array(
			'label' => 'Browse By',
			'multiOptions' => array(
				'creation_date' => 'Most Recent',
				'view_count' => 'Most Viewed',
				'most_liked' => 'Most Liked',
				'most_commented' => 'Most Commented',
				'rating' => 'Highest Rated',
			),
		));

		// Buttons
		$this -> addElement('Button', 'search', array(
			'label' => 'Search',
			'type' => 'submit',
		));
	}

}
