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

class Checkin_Plugin_Composer_Core extends Core_Plugin_Abstract
{
  public function onComposerCheckin($data, $params)
  {
    $action = (empty($params)) ? null : $params['action'];
    if (!$action) {
      return;
    }

    $viewer = Engine_Api::_()->_()->user()->getViewer();
    if (!$viewer || $viewer->getIdentity() == 0 || !isset($data['checkin']) || !$data['checkin']) {
      return;
    }

    $checkin_params = array();
    parse_str($data['checkin'], $checkin_params);

    if (!isset($checkin_params['name']) || !$checkin_params['name']) {
      return;
    }

    if (isset($checkin_params['reference']) && !isset($checkin_params['latitude'])) {
      $checkin_params = Engine_Api::_()->checkin()->getGooglePlaceDetails($checkin_params['reference']);
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    if (isset($checkin_params['object_id']) && $checkin_params['object_id']) {
      $place = $placesTbl->findByObject($checkin_params['object_type'], $checkin_params['object_id']);
    } else if (isset($checkin_params['google_id']) && $checkin_params['google_id']) {
      $place = $placesTbl->findByGoogleId($checkin_params['google_id']);
    }

    if (!$place) {
      $place = $placesTbl->createRow(array(
        'object_id' => isset($checkin_params['object_id']) ? $checkin_params['object_id'] : 0,
        'object_type' => isset($checkin_params['object_type']) ? $checkin_params['object_type'] : 'checkin',
        'google_id' => isset($checkin_params['google_id']) ? $checkin_params['google_id'] : '',
        'name' => isset($checkin_params['name']) ? $checkin_params['name'] : '',
        'types' => isset($checkin_params['types']) ? $checkin_params['types'] : '',
        'vicinity' => isset($checkin_params['vicinity']) ? $checkin_params['vicinity'] : '',
        'icon' => (isset($checkin_params['page_id']) && $checkin_params['page_id'] != 0) ? '' : $checkin_params['icon'],
        'latitude' => $checkin_params['latitude'],
        'longitude' => $checkin_params['longitude'],
        'creation_date' => new Zend_Db_Expr('NOW()')
      ));

      $place->save();
    }

    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $checkin = $checksTbl->createRow(array(
      'action_id' => $action->getIdentity(),
      'user_id' => $viewer->getIdentity(),
      'place_id' => $place->place_id,
      'creation_date' => new Zend_Db_Expr('NOW()')
    ));

    $checkin->save();
  }
}