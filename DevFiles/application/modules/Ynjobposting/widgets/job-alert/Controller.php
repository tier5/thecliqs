<?php
class Ynjobposting_Widget_JobAlertController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynjobposting_Form_Jobs_Alert();
		
		//populate industry
        $industries = Engine_Api::_() -> getItemTable('ynjobposting_industry')->getIndustries();
        unset($industries[0]);
        foreach ($industries as $industry) {
            $form->industry_id_alert->addMultiOption($industry['industry_id'], str_repeat("-- ", $industry['level'] - 1).$industry['title']);
        }
		//populate currency
        $supportedCurrencies = array();
        $gateways = array();
        $gatewaysTable = Engine_Api::_() -> getDbtable('gateways', 'payment');
        foreach ($gatewaysTable->fetchAll() as $gateway) {
            $gateways[$gateway -> gateway_id] = $gateway -> title;
            $gatewayObject = $gateway -> getGateway();
            $currencies = $gatewayObject -> getSupportedCurrencies();
			
            if (empty($currencies)) {
                continue;
            }
            $supportedCurrencyIndex[$gateway -> title] = $currencies;
            if (empty($fullySupportedCurrencies)) {
                $fullySupportedCurrencies = $currencies;
            }
            else {
                $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
            }
            $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
        }
        $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
        
        $form -> getElement('salary_currency_alert') -> setMultiOptions(array(
            'Currency' => array_merge(array_combine($fullySupportedCurrencies,$fullySupportedCurrencies), array_combine($supportedCurrencies,$supportedCurrencies))
        ));
		
	}
}
