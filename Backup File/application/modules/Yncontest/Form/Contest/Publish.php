<?php
class Yncontest_Form_Contest_Publish extends Engine_Form
{
	
	protected $_contest;
	public function setContest($contest) {
		$this->_contest = $contest;
	}
	
  public function init()
  {
    $request =  Zend_Controller_Front::getInstance()->getRequest();   
  	
  	$view =  Zend_Registry::get('Zend_View');
  	$translate = Zend_Registry::get('Zend_Translate');
  	
  	$user = Engine_Api::_() -> user() -> getUser($this->_contest -> user_id);
  	$publish_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'publishC_fee');
  	$feature_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'featureC_fee');
  	$premium_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'premiumC_fee');
  	$endingsoon_fee = Engine_Api::_()->yncontest()->getFeeContest($user, 'endingsoonC_fee');  	 
  	
    $this
      ->setTitle('Publish Contest')
    ->setDescription('To post contest on our website, you have to pay publishing-contest fee.')
		->setAttrib('name', 'contest_publish');
   
    $this->addElement('Checkbox', 'publishC_fee', array(
    		'label' => $translate->translate('Fee for Publishing').': '. $view->currencycontest($publish_fee),
    		'value' => 1,
    		'disabled' => true,
    		'checked' => true,
    ));
	$this->getElement("publishC_fee")->getDecorator('label')->setOption('escape', false); 
    
    $this->addElement('Checkbox', 'feature_fee', array(
    		'label' => $translate->translate('Fee for Feature Contest').': '. $view->currencycontest($feature_fee),
    		'value' => 1,
    		'checked' => true,
    ));
	$this->getElement("feature_fee")->getDecorator('label')->setOption('escape', false); 
    $this->addElement('Checkbox', 'premium_fee', array(
    		'label' => $translate->translate('Fee for Premium Contest').': '. $view->currencycontest($premium_fee) ,
    		'value' => 1,
    		'checked' => true,
    ));
	$this->getElement("premium_fee")->getDecorator('label')->setOption('escape', false); 
    $this->addElement('Checkbox', 'endingsoon_fee', array(
    		'label' => $translate->translate('Fee for Ending Soon Contest').': '. $view->currencycontest($endingsoon_fee),
    		'value' => 1,
    		'checked' => true,
    ));
	$this->getElement("endingsoon_fee")->getDecorator('label')->setOption('escape', false);     
    
    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Publish',     
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array(
        'ViewHelper',
      ),
    ));
    
    $tmp = $request->getParam('view');	
    if($tmp){    	
    	$this->addElement('Cancel', 'cancel', array(
    			//'prependText' => ' or ',
    			'label' => 'cancel',
    			'link' => true,
    			'href' => '',
    			'onclick' => 'parent.Smoothbox.close();',
    			'decorators' => array(
    					'ViewHelper'
    			),
    	));
    }else{
    $this->addElement('Cancel', 'cancel', array(
    		'label' => 'cancel',
    		'link' => true,
    		//'prependText' => ' or ',
    		'href' => Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'index'), 'yncontest_general', true),
    		'onclick' => '',
    		'decorators' => array(
    				'ViewHelper'
    		)
    ));
	}
  }
}

