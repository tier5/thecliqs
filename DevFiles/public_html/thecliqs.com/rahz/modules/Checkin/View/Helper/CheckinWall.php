<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CheckinWall.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_View_Helper_CheckinWall extends Zend_View_Helper_Abstract
{

  public function checkinWall(array $data = array())
  {
    $viewer = Engine_Api::_()->user()->getViewer();

    return $this->view->partial(
      '_checkinWall.tpl',
      'checkin',
      $data
    );

  }
}
