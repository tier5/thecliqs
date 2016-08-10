<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       09.08.12
 * @time       15:19
 */


class Donation_Api_Gmap extends Core_Api_Abstract
{
  protected $_ApiKey;

  public function getApiKey()
  {
    if ($this->_ApiKey == null) {
      $this->_ApiKey = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('donation.gmapkey');
    }

    return $this->_ApiKey;
  }

  public function validateAddress($address)
  {
    if (is_array($address)) {
      $address = implode(',', $address);
    }

    return $address;
  }

  public function getMarker($address)
  {
    $address = $this->validateAddress($address);

    if ($address == "") {
      return array();
    }

    $address = rawurlencode($address);
    $url = $this->getGMapUrl($address);
    $marker = Engine_Api::_()->getDbTable('markers', 'donation')->createRow();

    if ( ($result = file_get_contents($url)) != false ){
      $resultParts = explode(',',$result);
      if($resultParts[0] != 200){
        return false;
      }

      $marker->latitude = $resultParts[2];
      $marker->longitude = $resultParts[3];
    }

    return $marker;
  }

  public function deleteMarker($donation)
  {
    if (!($donation instanceof Donation_Model_Donation)){
      throw new Exception('Wrong argument passed.');
    }

    if (!$donation->getIdentity()){
      throw new Exception('Wrong donation does not exists.');
    }

    $markerTable = Engine_Api::_()->getDbTable('markers', 'donation');
    $markerTable->delete("donation_id = {$donation->getIdentity()}");

    return true;
  }

  public function getMapBounds($markers)
  {
    $minLat = 200;
    $maxLat = -200;
    $minLng = 200;
    $maxLng = -200;

    if (count($markers) == 0) {
      return array();
    } elseif (count($markers) == 1) {
      $marker = $markers[0];
      $minLat = $maxLat = $marker['lat'];
      $minLng = $maxLng = $marker['lng'];
    } else {
      foreach($markers as $marker) {
        if (empty($marker['lng']) || empty($marker['lat'])) continue;

        if ($marker['lng'] <= $minLng) {$minLng = $marker['lng'];}
        if ($marker['lng'] >= $maxLng) {$maxLng = $marker['lng'];}
        if ($marker['lat'] <= $minLat) {$minLat = $marker['lat'];}
        if ($marker['lat'] >= $maxLat) {$maxLat = $marker['lat'];}
      }
    }

    if ($minLat == $maxLat && $minLng == $maxLng) {
      $minLat -= 0.0009;
      $maxLat += 0.0009;
      $minLng -= 0.0009;
      $maxLng += 0.0009;
    }

    $mapCenterLat = (float)($minLat + $maxLat) / 2;
    $mapCenterLng = (float)($minLng + $maxLng) / 2;

    if ( $minLat == 200 || $maxLat == -200 || $minLng == 200 || $maxLng == -200 ) {
      $minLat = '';
      $maxLat = '';
      $minLng = '';
      $maxLng = '';
      $mapCenterLat = '';
      $mapCenterLng = '';
    }

    return array(
      'min_lat' => $minLat,
      'max_lat' => $maxLat,
      'min_lng' => $minLng,
      'max_lng' => $maxLng,
      'map_center_lat' => $mapCenterLat,
      'map_center_lng' => $mapCenterLng
    );
  }

  public function getGMapUrl($address)
  {
    if ($address == ""){
      return "";
    }

    return 'http://maps.google.com/maps/geo?&q='.$address.'&output=csv';
  }

  public function getMapJS()
  {
    return "<script src='http://maps.google.com/maps/api/js?sensor=false' type='text/javascript'></script>";
  }

  public function getDonationMarker($donation)
  {
    $donationMarker = $donation->getMarker();
    if (!$donationMarker) {
      return array(
        'marker_id' => 0,
        'lat' => 0,
        'lng' => 0,
        'donations_id' => $donation->donation_id,
        'donations_photo' => $donation->getPhotoUrl('thumb.normal'),
        'title' => $donation->getTitle(),
        'desc' => Engine_String::substr($donation->getDescription(false,false),0,200),
        'url' => $donation->getHref()
      );
    }

    return array(
      'marker_id' => $donationMarker->marker_id,
      'lat' => $donationMarker->latitude,
      'lng' => $donationMarker->longitude,
      'donations_id'=>$donation->donation_id,
      'donations_photo'=>$donation->getPhotoUrl('thumb.normal'),
      'title'=>$donation->getTitle(),
      'desc'=>Engine_String::substr($donation->getDescription(false,false),0,200),
      'url' => $donation->getHref()
    );
  }

  public function getMarkers($paginator, $get_coordinates = true)
  {
    $markers = array();

    if ($get_coordinates) {
      $markersTbl = Engine_Api::_()->getDbTable('markers', 'donation');
      $donation_ids = array();
      foreach( $paginator as $donation ) {
        $donation_ids[] = $donation->donation_id;
      }

      $marker_list = $markersTbl->getByDonationIds($donation_ids);
      foreach ($marker_list as $marker) {
        $donation = $paginator->getRowMatching(array('donation_id' => $marker->donation_id));

        if( !$donation ) continue;

        $markers[] = array(
          'marker_id' => $marker->marker_id,
          'lat' => $marker->latitude,
          'lng' => $marker->longitude,
          'donations_id' => $donation->donation_id,
          'donations_photo' => $donation->getPhotoUrl('thumb.normal'),
          'title' => $donation->getTitle(),
          'desc' => Engine_String::substr($donation->getDescription(),0,200),
          'url' => $donation->getHref()
        );
      }

      return $markers;
    }

    return $markers;
  }
}