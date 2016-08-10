<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Audio.php 09.09.11 17:01 taalay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Audio extends Core_Model_Item_Abstract
{
  protected $_parent_type = 'store_product';

  protected $_owner_type = 'user';

  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'store_profile',
      'product_id' => $this->product_id,
    ), $params);

    $route = @$params['route'];
    unset($params['route']);
    return Zend_Controller_Front::getInstance()->getRouter()->assemble($params, $route, true);
  }
}