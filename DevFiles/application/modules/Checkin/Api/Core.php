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


class Checkin_Api_Core extends Core_Api_Abstract
{
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

    if ($minLat == $maxLat && $minLng == $maxLng){
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

  public function getGoogleLocale($locale = false)
  {
    if (!$locale) {
      $locale = Zend_Registry::get('Zend_Translate')->getLocale();
    }

    $british_english = array('en_AU', 'en_BE', 'en_BW', 'en_BZ', 'en_GB', 'en_GU', 'en_HK', 'en_IE', 'en_IN',
      'en_MT', 'en_NA', 'en_NZ', 'en_PH', 'en_PK', 'en_SG', 'en_ZA', 'en_ZW', 'kw', 'kw_GB');

    $friulian = array('fur', 'fur_IT');

    $swiss_german = array('gsw', 'gsw_CH');

    $norwegian_bokma = array('nb', 'nb_NO');

    $portuguese = array('pt', 'pt_PT');

    $brazilian_portuguese = array('pt_BR');

    $chinese = array('zh', 'zh_CN');

    $sar_china = array('zh_HK', 'zh_MO', 'zh_SG');

    $taiwan = array('zh_TW');

    if (in_array($locale, $british_english)) {
      $locale = 'en-GB';
    } elseif (in_array($locale, $friulian)) {
      $locale = 'it';
    } elseif (in_array($locale, $swiss_german)) {
      $locale = 'de';
    } elseif (in_array($locale, $norwegian_bokma)) {
      $locale = 'no';
    } elseif (in_array($locale, $portuguese)) {
      $locale = 'pt-PT';
    } elseif (in_array($locale, $brazilian_portuguese)) {
      $locale = 'pt-BR';
    } elseif (in_array($locale, $chinese)) {
      $locale = 'zh-CN';
    } elseif (in_array($locale, $sar_china)) {
      $locale = 'zh-HK';
    } elseif (in_array($locale, $taiwan)) {
      $locale = 'zh-TW';
    } elseif ($locale) {
      $locale_arr = explode('_', $locale);
      $locale = ($locale_arr[0]) ? $locale_arr[0] : 'en';
    } else {
      $locale = 'en';
    }

    return $locale;
  }

  public function getPageResults($keyword)
  {
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'hecore');

    if (!$modulesTbl->isModuleEnabled('page')) {
      return array();
    }

    /**
     * @var $pagesTbl Page_Model_DbTable_Pages
     */
    $pagesTbl = Engine_Api::_()->getDbTable('pages', 'page');
    $allowTbl = Engine_Api::_()->getDbtable('allow', 'authorization');
    $listItemsTbl = Engine_Api::_()->getDbtable('listItems', 'page');
    $markersTbl = Engine_Api::_()->getDbtable('markers', 'page');
    $checksTbl = Engine_Api::_()->getDbtable('checks', 'checkin');
    $viewer = Engine_Api::_()->user()->getViewer();

    $pagesSel = $pagesTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $pagesTbl->info('name')), array('p.page_id'))
      ->joinLeft(array('a' => $allowTbl->info('name')), 'a.resource_type = "page" AND p.page_id = a.resource_id AND a.action = "view"')
      ->joinLeft(array('li' => $listItemsTbl->info('name')), 'a.role_id = li.list_id')
      ->where('a.role IN ("everyone", "registered") OR (a.role = "page_list" AND li.child_id = ?)', $viewer->getIdentity())
      ->where('p.title LIKE ? OR p.description LIKE ? OR p.keywords LIKE ?', "%{$keyword}%")
      ->group(array('p.page_id'));

    $pageIds = $pagesTbl->getAdapter()->fetchCol($pagesSel);

    if (!$pageIds){
      return array();
    }

    $options_tbl = $pagesTbl->getTablePrefix() . 'page_fields_options';
    $values_tbl = $pagesTbl->getTablePrefix() . 'page_fields_values';
    $placeChecks = $checksTbl->getObjectVisitorCount('page', $pageIds);

    $select = $pagesTbl->select()
      ->setIntegrityCheck(false)
      ->from(array('p' => $pagesTbl->info('name')), array('page_id', 'name', 'title', 'displayname', 'photo_id', 'country', 'state', 'city', 'street'))
      ->joinLeft(array('m' => $markersTbl->info('name')), 'p.page_id = m.page_id', array('marker_id', 'longitude', 'latitude'))
      ->joinLeft(array('v' => $values_tbl), 'p.page_id = v.item_id AND v.field_id = 1')
      ->joinLeft(array('o' => $options_tbl), 'v.value = o.option_id', array('category' => 'label'))
      ->where('p.page_id IN (?)', $pageIds);

    $pages = $pagesTbl->fetchAll($select);
    $pageList = array();
    foreach ($pages as $page) {
      $pageList[] = array(
        'page_id' => $page->getIdentity(),
        'object_id' => $page->getIdentity(),
        'object_type' => 'page',
        'google_id' => 0,
        'name' => $page->getTitle(),
        'icon' => $page->getPhotoUrl('thumb.icon'),
        'vicinity' => $page->street . ', ' . $page->city,
        'latitude' => $page->latitude,
        'longitude' => $page->longitude,
        'info' => (isset($placeChecks[$page->getIdentity()])) ? $placeChecks[$page->getIdentity()] : $page->category
      );
    }

    return $pageList;
  }

  public function getGoogleResults($keyword, $latitude, $longitude)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->getSetting('checkin.google_map_key', '');

    // todo get from browser or set default place
    $latitude = ($latitude) ? str_replace(',', '.', "$latitude") : 0;
    $longitude = ($longitude) ? str_replace(',', '.', "$longitude") : 0;

    $params = array(
      'key' => $apiKey,
      'sensor' => 'false',
      'input' => $keyword,
      'language' => $this->getGoogleLocale(),
    );

    if ($latitude != '0' && $longitude != '0') {
      $params['location'] = $latitude . ',' . $longitude;
    }

    $params_str = '';
    foreach ($params as $key => $value) {
      $params_str .= '&' . $key . '=' . urlencode($value);
    }

    $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json?' . substr($params_str, 1);
    $curl_handle = curl_init($url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    $content = curl_exec($curl_handle);

    $content_arr = json_decode($content, true);

    if (!isset($content_arr['status']) || $content_arr['status'] != 'OK') {
      return array();
    }

    $results = isset($content_arr['predictions']) ? $content_arr['predictions'] : array();
    $google_ids = array();
    foreach ($results as $result) {
      $google_ids[] = $result['id'];
    }

    $checksTbl = Engine_Api::_()->getDbtable('checks', 'checkin');
    $placeChecks = $checksTbl->getGoogleVisitorCount($google_ids);

    $googlePlaces = array();
    foreach ($results as $place) {
      $googlePlaces[] = array(
        'page_id' => 0,
        'info' => (isset($placeChecks[$place['id']])) ? $placeChecks[$place['id']] : 0,
        'google_id' => $place['id'],
        'name' => $place['description'],
        'reference' => $place['reference'],
      );
    }

    return $googlePlaces;
  }

  public function getGooglePlaceDetails($reference)
  {
    $settings = Engine_Api::_()->getApi('settings', 'core');
    $apiKey = $settings->getSetting('checkin.google_map_key', '');

    $params = array(
      'key' => $apiKey,
      'sensor' => 'false',
      'reference' => $reference,
      'language' => $this->getGoogleLocale(),
    );

    $params_str = '';
    foreach ($params as $key => $value) {
      $params_str .= '&' . $key . '=' . urlencode($value);
    }

    $url = 'https://maps.googleapis.com/maps/api/place/details/json?' . substr($params_str, 1);
    $curl_handle = curl_init($url);
    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
    $content = curl_exec($curl_handle);
    curl_close($curl_handle);

    $content_arr = json_decode($content, true);

    if (!isset($content_arr['status']) || $content_arr['status'] != 'OK' || !isset($content_arr['result'])) {
      return array();
    }

    $details = $content_arr['result'];
    $placeDetails = array(
      'name' => $details['name'],
      'google_id' => $details['id'],
      'latitude' => $details['geometry']['location']['lat'],
      'longitude' => $details['geometry']['location']['lng'],
      'vicinity' => ($details['vicinity']) ? $details['vicinity'] : $details['formatted_address'],
      'icon' => $details['icon'],
      'types' => implode(',', $details['types'])
    );

    return $placeDetails;
  }

  public function getCheckinsUsersByPage(array $params)
  {
    $page_id = $params['page_id'];
    $user_id = $params['user_id'];

    return Engine_Api::_()->getDbTable('checks', 'checkin')->getMatchedChekinsCount(0, $user_id, $page_id, false, false);
  }

  public function convertCheckinData()
  {
    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');
    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');

    $checksSel = $checksTbl->select()
      ->where('place_id = ?', 0)
      ->limit(100);

    $checks = $checksTbl->fetchAll($checksSel);

    $page_ids = array();
    $google_ids = array();

    foreach ($checks as $check) {
      if ($check->page_id) {
        $page_ids[] = $check->page_id;
      }

      if ($check->google_id) {
        $google_ids[] = $check->google_id;
      }
    }

    $placesByPage = $placesTbl->findByPageIds($page_ids, true);
    $placesByGoogle = $placesTbl->findByGoogleIds($google_ids, true);

    foreach ($checks as $check) {

      if ($check->page_id && isset($placesByPage[$check->page_id])) {
        $place = $placesByPage[$check->page_id];

        if ($place) {
          $check->place_id = $place->place_id;
          $check->save();

          continue;
        }
      }

      if ($check->google_id && isset($placesByGoogle[$check->google_id])) {
        $place = $placesByGoogle[$check->google_id];

        if ($place) {
          $check->place_id = $place->place_id;
          $check->save();

          continue;
        }
      }

      $place = $placesTbl->createRow(array(
        'google_id' => $check->google_id,
        'object_type' => (isset($check->page_id) && $check->page_id) ? 'page' : 'checkin',
        'object_id' => $check->page_id,
        'name' => $check->name,
        'vicinity' => $check->vicinity,
        'types' => $check->types,
        'icon' => $check->icon,
        'latitude' => $check->latitude,
        'longitude' => $check->longitude,
        'creation_date' => $check->creation_date
      ));

      $place->save();

      if ($place->object_type == 'page') {
        $placesByPage[$place->object_id] = $place;
      } else {
        $placesByGoogle[$place->google_id] = $place;
      }

      $check->place_id = $place->place_id;
      $check->save();
    }
  }
}