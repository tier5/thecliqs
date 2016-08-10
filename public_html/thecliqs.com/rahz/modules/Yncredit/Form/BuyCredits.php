<?php
class Yncredit_Form_BuyCredits extends Engine_Form
{
	public function init()
	{
		parent::init();
		$this->setAttribs(array(
			'class' => 'global_form',
			));
		$view = Zend_Registry::get('Zend_View');
		$action = $view -> url(array(), 'yncredit_package', true);
		$this -> setAction($action) -> setMethod('GET');
		$packages = Engine_Api::_() -> getDbTable("packages", 'yncredit') -> getPackages(true);
		$settings = Engine_Api::_()->getDbTable('settings', 'core');
		$credits_for_one_unit = $settings->getSetting('yncredit.credit_price', 100);
    	$currency = $settings->getSetting('payment.currency', 'USD');
		$options = array();
		if(count($packages))
		{
			foreach($packages as $package)
			{
				$bonus = '';
				//$bonus = ((100*$package->credit)/($package->price*$credits_for_one_unit) - 100); 
	            $bonus = ($bonus) ? round($bonus, 2) . $view->translate('% bonus') : '';
				$options[$package -> getIdentity()] = $view->locale()->toCurrency($package->price, $currency). $view -> translate(array(" for %s credit", " for %s credits", $package -> credit), $package -> credit).' '.$bonus;
			}
			$this->addElement('Radio', 'package', array(
					'multiOptions' => $options,
					'value' => $packages[0] -> getIdentity(),
			));
		}
		$this->addElement('Button', 'buy_credit', array(
	      'label' => 'Buy Credits',
	      'value' => 1,
	      'type' => 'submit',
	    ));
	}
}