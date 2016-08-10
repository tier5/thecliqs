<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Bootstrap.php 10070 2013-07-24 20:14:47Z andres $
 * @author     Charlotte
 */

/**
 * @category   Application_Extensions
 * @package    Mobi
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Mobi_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function _bootstrap($resource = null)
  {
    $front = Zend_Controller_Front::getInstance();
    $front->registerPlugin(new Mobi_Plugin_Core);
  }
}
