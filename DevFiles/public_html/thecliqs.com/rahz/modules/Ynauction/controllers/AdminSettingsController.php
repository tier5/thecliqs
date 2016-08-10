<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: AdminSettingsController.php
 * @author     Minh Nguyen
 */
class Ynauction_AdminSettingsController extends Core_Controller_Action_Admin
{
  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('ynauction_admin_main', array(), 'ynauction_admin_main_settings');
  }
  public function indexAction()
  {
    $this->view->form = $form = new Ynauction_Form_Admin_Global();
    $supportedCurrencyIndex = array();
    $fullySupportedCurrencies = array();
    $supportedCurrencies = array();
    $gateways = array();
    $gatewaysTable = Engine_Api::_()->getDbtable('gateways', 'payment');
    foreach( $gatewaysTable->fetchAll() as $gateway ) {
      $gateways[$gateway->gateway_id] = $gateway->title;
      $gatewayObject = $gateway->getGateway();
      $currencies = $gatewayObject->getSupportedCurrencies();
      if( empty($currencies) ) {
        continue;
      }
      $supportedCurrencyIndex[$gateway->title] = $currencies;
      if( empty($fullySupportedCurrencies) ) {
        $fullySupportedCurrencies = $currencies;
      } else {
        $fullySupportedCurrencies = array_intersect($fullySupportedCurrencies, $currencies);
      }
      $supportedCurrencies = array_merge($supportedCurrencies, $currencies);
    }
    $supportedCurrencies = array_diff($supportedCurrencies, $fullySupportedCurrencies);
    
    $translationList = Zend_Locale::getTranslationList('nametocurrency', Zend_Registry::get('Locale'));
    $fullySupportedCurrencies = array_intersect_key($translationList, array_flip($fullySupportedCurrencies));
    $supportedCurrencies = array_intersect_key($translationList, array_flip($supportedCurrencies));
    $form->ynauction_currency->setMultiOptions(array(
      'Fully Supported' => $fullySupportedCurrencies,
      'Partially Supported' => $supportedCurrencies,
    ));     
   if( $this->getRequest()->isPost() && $form->isValid($this->_getAllParams()) )
    {
      $values = $form->getValues();

      foreach ($values as $key => $value){
        if($key != 'ynauction_currency')
        {
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, round($value,2));
        }
        Engine_Api::_()->getApi('settings', 'core')->setSetting($key, $value);   
      }
       $form->addNotice('Your changes have been saved.');
    }
  }
}