<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Core.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_Plugin_Core
{
  public function onItemDeleteBefore($event)
  {
    $payload = $event->getPayload();

    if ($payload instanceof Wall_Model_Action) {
      $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
      $checksSel  = $checksTbl->select()
        ->where('action_id = ?', $payload->getIdentity())
        ->limit(1);

      $checkin = $checksTbl->fetchRow($checksSel);
      if ($checkin) {
        $checkin->delete();
      }
    } elseif ($payload instanceof Core_Model_Item_Abstract && ($payload->getType() === 'page' || $payload->getType() === 'event')) {
      $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
      $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');

      $place = $placesTbl->findByObject($payload->getType(), $payload->getIdentity());
      if (!$place) {
        return;
      }

      $checksSel  = $checksTbl->select()
        ->where('place_id = ?', $place->getIdentity());

      foreach ($checksTbl->fetchAll($checksSel) as $checkin) {
        $checkin->delete();
      }
    }
  }
}