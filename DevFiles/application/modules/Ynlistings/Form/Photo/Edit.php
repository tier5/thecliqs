<?php
class Ynlistings_Form_Photo_Edit extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Edit Photo')
      ->clearDecorators()
      ->addDecorator('FormElements');
      ;

    $this->addElement('Text', 'image_title', array(
      'label' => 'Title',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
	
    $this->addElement('Textarea', 'image_description', array(
      'label' => 'Description',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
	
	$this->addElement('Checkbox', 'delete', array(
		'label' => 'Delete',
		'type' => 'checkbox',
		'checked_value' => '1',
        'unchecked_value' => '0'
	));
  }
}