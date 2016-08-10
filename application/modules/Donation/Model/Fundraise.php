<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       08.08.12
 * @time       17:52
 */
class Donation_Model_Fundraise extends Core_Model_Item_Abstract
{

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'donation_fundraise',
      'reset' => true,
      'fundraise_id' => $this->fundraise_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);

    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
}
