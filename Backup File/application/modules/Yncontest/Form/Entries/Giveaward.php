<?php
class Yncontest_Form_Entries_Giveaward extends Engine_Form
{
	
	protected $_award;

	
	
	public function setAward($award) {
		$this->_award = $award;
	}

  public function init()
  {

    $this -> setTitle('Give Award');

		$this->addElement('radio','award_type',array(
			'label' => 'Please choose Award',
			'multiOptions'=>$this->_award,
			'required' => true,
		));
   
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Submit',     
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));

    $this->addElement('Cancel', 'cancel', array(
      'prependText' => ' or ',
      'label' => 'cancel',
      'link' => true,
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      ),
    ));
  }
}

