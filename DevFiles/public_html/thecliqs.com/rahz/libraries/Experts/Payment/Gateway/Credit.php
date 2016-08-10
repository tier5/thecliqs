<?php
/**
 * SocialEngine
 *
 * @category   Experts
 * @package    Experts_Payment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Credit.php 04.01.12 13:44 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Experts
 * @package    Experts_Payment
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Experts_Payment_Gateway_Credit extends Experts_Payment_Gateway
{
  // Support

  protected $_supportedCurrencies = array(
    // 'ARS', // Supported by 2Checkout, but not by PayPal
    'AUD',
    'BRL', // This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
    'CAD',
    'CHF',
    'CZK', // Not supported by 2Checkout
    'DKK',
    'EUR',
    'GBP',
    'HKD',
    'HUF', // Not supported by 2Checkout
    'ILS', // Not supported by 2Checkout
    //'INR', // Supported by 2Checkout
    'JPY',
    'MXN',
    'MYR', // Not supported by 2Checkout - This currency is supported as a payment currency and a currency balance for in-country PayPal accounts only.
    'NOK',
    'NZD',
    'PHP', // Not supported by 2Checkout
    'PLN', // Not supported by 2Checkout
    'SEK',
    'SGD', // Not supported by 2Checkout
    'THB', // Not supported by 2Checkout
    'TWD', // Not supported by 2Checkout
    'USD',
    //'ZAR', // Supported by 2Checkout
  );

  protected $_supportedLanguages = array(
    'es', 'en', 'de', 'fr', 'nl', 'pt', 'zh', 'it', 'ja', 'pl',
    // Full
    //'es_AR', 'en_AU', 'de_AT', 'en_BE', 'fr_BE', 'nl_BE', 'pt_BR', 'en_CA',
    //'fr_CA', 'zh_CN', 'zh_HK', 'fr_FR', 'de_DE', 'it_IT', 'ja_JP', 'es_MX',
    //'nl_NL', 'pl_PL', 'en_SG', 'es_SP', 'fr_CH', 'de_CH', 'en_CH', 'en_GB',
    //'en_US',
    // Not supported
    //'de_BE', 'zh_SG', 'gsw_CH', 'it_CH',
  );

  protected $_supportedRegions = array(
    'AF', 'AX', 'AL', 'DZ', 'AS', 'AD', 'AO', 'AI', 'AQ', 'AG', 'AR', 'AM',
    'AW', 'AU', 'AT', 'AZ', 'BS', 'BH', 'BD', 'BB', 'BY', 'BE', 'BZ', 'BJ',
    'BM', 'BT', 'BO', 'BA', 'BW', 'BV', 'BR', 'IO', 'BN', 'BG', 'BF', 'BI',
    'KH', 'CM', 'CA', 'CV', 'KY', 'CF', 'TD', 'CL', 'CN', 'CX', 'CC', 'CO',
    'KM', 'CG', 'CD', 'CK', 'CR', 'CI', 'HR', 'CU', 'CY', 'CZ', 'DK', 'DJ',
    'DM', 'DO', 'EC', 'EG', 'SV', 'GQ', 'ER', 'EE', 'ET', 'FK', 'FO', 'FJ',
    'FI', 'FR', 'GF', 'PF', 'TF', 'GA', 'GM', 'GE', 'DE', 'GH', 'GI', 'GR',
    'GL', 'GD', 'GP', 'GU', 'GT', 'GG', 'GN', 'GW', 'GY', 'HT', 'HM', 'VA',
    'HN', 'HK', 'HU', 'IS', 'IN', 'ID', 'IR', 'IQ', 'IE', 'IM', 'IL', 'IT',
    'JM', 'JP', 'JE', 'JO', 'KZ', 'KE', 'KI', 'KP', 'KR', 'KW', 'KG', 'LA',
    'LV', 'LB', 'LS', 'LR', 'LY', 'LI', 'LT', 'LU', 'MO', 'MK', 'MG', 'MW',
    'MY', 'MV', 'ML', 'MT', 'MH', 'MQ', 'MR', 'MU', 'YT', 'MX', 'FM', 'MD',
    'MC', 'MN', 'MS', 'MA', 'MZ', 'MM', 'NA', 'NR', 'NP', 'NL', 'AN', 'NC',
    'NZ', 'NI', 'NE', 'NG', 'NU', 'NF', 'MP', 'NO', 'OM', 'PK', 'PW', 'PS',
    'PA', 'PG', 'PY', 'PE', 'PH', 'PN', 'PL', 'PT', 'PR', 'QA', 'RE', 'RO',
    'RU', 'RW', 'SH', 'KN', 'LC', 'PM', 'VC', 'WS', 'SM', 'ST', 'SA', 'SN',
    'CS', 'SC', 'SL', 'SG', 'SK', 'SI', 'SB', 'SO', 'ZA', 'GS', 'ES', 'LK',
    'SD', 'SR', 'SJ', 'SZ', 'SE', 'CH', 'SY', 'TW', 'TJ', 'TZ', 'TH', 'TL',
    'TG', 'TK', 'TO', 'TT', 'TN', 'TR', 'TM', 'TC', 'TV', 'UG', 'UA', 'AE',
    'GB', 'US', 'UM', 'UY', 'UZ', 'VU', 'VE', 'VN', 'VG', 'VI', 'WF', 'EH',
    'YE', 'ZM',
  );

  protected $_supportedBillingCycles = array(
    /* 'Day', */ 'Week', /* 'SemiMonth',*/ 'Month', 'Year',
  );


  // Translation

  protected $_transactionMap = array();

  // General

  /**
   * Constructor
   *
   * @param array $options
   */
  public function  __construct(array $options = null)
  {
    parent::__construct($options);

    if( null === $this->getGatewayMethod() ) {
      $this->setGatewayMethod('POST');
    }
  }

  /**
   * Get the service API
   *
   * @return Experts_Service_PayPal
   */
  public function getService()
  {
    if( null === $this->_service ) {
      $this->_service = new Experts_Service_Credit(array('testMode' => 0));
    }

    return $this->_service;
  }

  public function getGatewayUrl()
  {
    $view = Zend_Registry::get('Zend_View');
    return $view->url(array('module' => 'credit', 'controller' => 'store', 'action' => 'index'), 'default', true);
  }

  // IPN

  public function processIpn(Experts_Payment_Ipn $ipn)
  {
    return true;
  }

  // Transaction

  public function processTransaction(Experts_Payment_Transaction $transaction)
  {
    $rawData = $transaction->getRawData();

    return $rawData;
  }

  // Admin

  public function test()
  {
    return true;
  }
}
