<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: CheckinDefault.php 2012-03-22 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_View_Helper_CheckinDefaultLocation extends Zend_View_Helper_Abstract
{

  public function checkinDefaultLocation($subject = null)
  {
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    $defaultLocation = array();

    if ($subject && $subject->getType() == 'event') {
      $place = $placesTbl->findByObject($subject->getType(), $subject->getIdentity());
    }
    else if ($subject && $subject->getType() == 'page') {
      $place = $placesTbl->findByObject($subject->getType(), $subject->getIdentity());
      if (!$place) {
        $markersTbl = Engine_Api::_()->getDbTable('markers', 'page');
        $select = $markersTbl->select();
        $select->where('page_id = ?', $subject->getIdentity());

        $marker = $markersTbl->fetchRow($select);

        $defaultLocation = array(
          'object_type' => 'page',
          'object_id' => $subject->getIdentity(),
          'google_id' => 0,
          'name' => $subject->getTitle(),
          'icon' => $subject->getPhotoUrl('thumb.icon'),
          'vicinity' => $subject->street . ', ' . $subject->city,
          'latitude' => ($marker && $marker->latitude) ? $marker->latitude : '',
          'longitude' => ($marker && $marker->longitude) ? $marker->longitude : '',
        );
      }
    } else {
      $viewer = Engine_Api::_()->user()->getViewer();
      $place = $placesTbl->getUserLastPlace($viewer->getIdentity());
    }

    if (!$defaultLocation && $place) {
      $defaultLocation = array(
        'object_type' => $place->object_type,
        'object_id' => $place->object_id,
        'google_id' => $place->google_id,
        'name' => ($subject && $subject->getType() == 'event') ? $subject->getTitle() : $place->name,
        'icon' => ($subject && $subject->getType() == 'event') ? $subject->getPhotoUrl('thumb.icon') : $place->icon,
        'vicinity' => $place->vicinity,
        'latitude' => $place->latitude,
        'longitude' => $place->longitude
      );
    }

    return $this->view->jsonInline($defaultLocation);
  }
}
