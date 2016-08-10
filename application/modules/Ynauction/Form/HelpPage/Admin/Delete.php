<?php

class Ynauction_Form_HelpPage_Admin_Delete extends Engine_Form {

	public function init() {
		
	$this -> setTitle('Delete Help Page')
    ->setDescription('Are you sure that you want to delete this Help Page?')   ;

	/**
	 * add button groups
	 */
	$this->addElement('Button', 'submit', array(
      'label' => 'Submit',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
	}

}
