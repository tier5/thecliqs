<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2012-02-13 17:46 ulan T $
 * @author     Ulan T
 */

class Page_Widget_MyLocationController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $params = $request->getParams();
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $address = null;
    if( !empty($params['my_address']) ) {
      $address = urlencode($params['my_address']);
      $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
      $url = $prefix.'maps.google.com/maps/geo?&q=' . $address . '&output=csv';
      if (($result = file_get_contents($url)) != false) {
        $resultParts = $this->str_getcsv($result);
        if ($resultParts[0] == 200) {
          $params['my_latitude'] = $resultParts[2];
          $params['my_longitude'] = $resultParts[3];
        }
      }
    }

    $this->getElement()->setTitle('My Location');

    if( !empty( $params['my_latitude']) && !empty( $params['my_longitude']) ) {
      $pageTbl = Engine_Api::_()->getItemTable('page');
      $markerTbl = Engine_Api::_()->getDbTable('markers', 'page');

      if( empty($params['my_address']) ) {
        $prefix = (constant('_ENGINE_SSL') ? 'https://' : 'http://');
        $url = $prefix.'maps.google.com/maps/geo?&q=' . $params['my_latitude'].','.$params['my_longitude'] . '&output=csv';
        if (($result = file_get_contents($url)) != false) {
          $resultParts = $this->str_getcsv($result);

          if ($resultParts[0] == 200) {
            $params['my_address'] = $resultParts[2];

          }
        }
      }

      $latitude = $params['my_latitude'];
      $longitude = $params['my_longitude'];

      $unit = 1;
      $this->view->unit = $settings->getSetting('page.advsearch.unit', 'Miles');
      if( $this->view->unit == 'Km' )
        $unit = 1.609344;

      $select = $pageTbl->select();
      $select->setIntegrityCheck(false);
      $select
        ->from($pageTbl->info('name'))
        ->joinLeft(
          array('m' => $markerTbl->info('name')),
                "m.page_id = " . $pageTbl->info('name') . ".page_id",
              array(
                'marker_id',
                'longitude',
                'latitude',
                'distance' => new Zend_Db_Expr("(((acos(sin((" . $latitude . "*pi()/180)) * sin((m.`latitude`*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((m.`latitude`*pi()/180)) * cos(((" . $longitude . "- m.`longitude`)*pi()/180))))*180/pi())*60*1.1515*{$unit})")
              )
        )
        ->where("(((acos(sin((" . $latitude . "*pi()/180)) * sin((m.`latitude`*pi()/180))+cos((" . $latitude . "*pi()/180)) * cos((m.`latitude`*pi()/180)) * cos(((" . $longitude . "- m.`longitude`)*pi()/180))))*180/pi())*60*1.1515*{$unit}) >= 0")
        ->where($pageTbl->info('name').'.approved = 1')
        ->order('distance')
      ;

      if (Engine_Api::_()->getDbTable('settings', 'core')->getSetting('page.package.enabled', 0)) {
        $select->where($pageTbl->info('name').'.enabled = ?', 1);
      }

      $paginator = Zend_Paginator::factory($select);
      $paginator->setItemCountPerPage($this->_getParam('itemCountPerPage', 4));

      if( empty($params['my_page_num']) )
        $paginator->setCurrentPageNumber(1);
      else
        $paginator->setCurrentPageNumber($params['my_page_num']);

      $markers = Engine_Api::_()->getApi('gmap', 'page')->getMarkers($paginator->getCurrentItems());
      $my_markers = $markers;
      $markers[] = array('lat' => $params['my_latitude'], 'lng' => $params['my_longitude']);
      $bounds = Engine_Api::_()->getApi('gmap', 'page')->getMapBounds($markers);

      $this->view->markers = (!empty($markers)) ? Zend_Json_Encoder::encode($my_markers) : '';
      $this->view->bounds  = Zend_Json_Encoder::encode($bounds);

      $this->view->paginator = $paginator;
      $this->getElement()->setTitle('');

      $this->view->my_latitude = $params['my_latitude'];
      $this->view->my_longitude = $params['my_longitude'];
      $this->view->my_address = $params['my_address'];

    }
  }

  protected function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\")
  {
    $fp = fopen("php://memory", 'r+');
    fputs($fp, $input);
    rewind($fp);
    $data = fgetcsv($fp, null, $delimiter, $enclosure);
    fclose($fp);
    return $data;
  }
}