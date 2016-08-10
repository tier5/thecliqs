<?php
class Ynbusinesspages_Form_Admin_Claims_Update extends Engine_Form {
	
	protected $_action;
	public function getAction()
	{
		return $this ->_action;
	}
	public function setAction($action)
	{
		$this ->_action = $action;
	}
		
    public function init() {
    	$desc = 'Are you sure you want to '. $this->_action .' this request?';
		$title = ucfirst($this->_action). ' Claim Request';
		$valueSumit = ucfirst($this->_action);
        $this->setTitle($title)
		    ->setMethod('post')
            ->setDescription($desc)
            ->setAttrib('class', 'global_form_popup')
            ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()));
        ;

        // Buttons
        $this->addElement('Button', 'submit', array(
        	'label' => $valueSumit,
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array('ViewHelper')
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'href' => '',
            'onclick' => 'parent.Smoothbox.close();',
            'decorators' => array(
                'ViewHelper'
            )
        ));
        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
    }
}