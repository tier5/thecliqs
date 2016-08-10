<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Gateway.php 8221 2011-01-15 00:24:02Z john $
 * @author     John Boehr <j@webligo.com>
 */

/**
 * @category   Application_Core
 * @package    Payment
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Store_Model_Gateway extends Core_Model_Item_Abstract
{
  protected $_searchTriggers = false;

  protected $_modifiedTriggers = false;

  /**
   * @var Engine_Payment_Plugin_Abstract
   */
  protected $_plugin;

  /**
   * Get the payment plugin
   *
   * @return Engine_Payment_Plugin_Abstract
   */
  public function getPlugin()
  {
    if( null === $this->_plugin ) {
      $class = $this->plugin;
      Engine_Loader::loadClass($class);
      $plugin = new $class($this);
      if( !($plugin instanceof Experts_Payment_Plugin_Abstract) ) {
        throw new Engine_Exception(sprintf('Payment plugin "%1$s" must ' .
            'implement Experts_Payment_Plugin_Abstract', $class));
      }
      $this->_plugin = $plugin;
    }
    return $this->_plugin;
  }

  /**
   * Get the payment gateway
   *
   * @return Experts_Payment_Gateway
   */
  public function getGateway()
  {
    return $this->getPlugin()->getGateway();
  }

  /**
   * Get the payment service api
   *
   * @return Zend_Service_Abstract
   */
  public function getService()
  {
    return $this->getPlugin()->getService();
  }

    // Button

  /**
   * Get the button of the gateway
   *
   * @return String
   */

  public function getButtonUrl()
  {
    if ($this->button_url) {
      return $this->button_url;
    } else {
      $view = Zend_Registry::get('Zend_View');
      return $view->layout()->staticBaseUrl . 'application/modules/Store/externals/images/buttons/' . strtolower($this->title) . '.gif';
    }
  }

  public function getEmail()
  {
    if ($this->email) {
      return $this->email;
    }
    $credential = $this->config;
    $username = $credential['username'];
    $email = str_replace('_api1.', '@', $username);
    return $email;
  }
}