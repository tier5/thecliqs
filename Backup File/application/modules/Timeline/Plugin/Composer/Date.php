<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Date.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Timeline_Plugin_Composer_Date extends Core_Plugin_Abstract
{
  public function onComposerDate($composerData, $params)
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $date = $request->getParam('date');

    if (
      is_array($date) &&
      $params['action'] instanceof Wall_Model_Action &&
      $date['year'] > 0 && $date['month'] > 0 && $date['day'] > 0
    ) {
      $params['action']->date = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
      $params['action']->save();
    }

    return $params['action'];
  }
}