<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Standard.php 5/23/12 2:51 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

abstract class Store_Controller_Action_Standard extends Core_Controller_Action_Standard
{
  public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
  {
    parent::__construct($request, $response, $invokeArgs);
    $this->_helper->requireAuth()->setAuthParams('store', null, 'use')->isValid();
  }
}
