<?php
class Ynmusic_Form_Admin_Genres_Create extends Engine_Form {

	public function init()
	{
		// Init form
		$this -> setTitle('Add New Genre') -> setAttrib('class', 'global_form_popup');

		// Init name
		$this -> addElement('Text', 'title', array(
			'label' => 'Genre Name',
			'description' => 'Maximum 128 characters',
			'maxlength' => '128',
			'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                'StripTags',
                new Engine_Filter_StringLength( array('max' => '128'))
            )
		));
		$this -> title -> getDecorator("Description") -> setOption("placement", "append");

		$this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Create',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addElement('Cancel', 'cancel', array(
            'link' => true,
            'label' => 'Cancel',
            'onclick' => 'parent.Smoothbox.close()',
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));
        $this->addDisplayGroup(array('submit_btn', 'cancel'), 'buttons', array(
            'order' => '1000',
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
             ),
        ));
	}

}
