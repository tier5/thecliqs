<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: IndexController.php 2011-11-17 11:18:13 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Checkin
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Checkin_IndexController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
  }

  public function suggestAction()
  {
    $keyword = $this->_getParam('keyword', '');
    $latitude = $this->_getParam('latitude', 0);
    $longitude = $this->_getParam('longitude', 0);

    $pageResults = Engine_Api::_()->checkin()->getPageResults($keyword);
    $googleResults = Engine_Api::_()->checkin()->getGoogleResults($keyword, $latitude, $longitude);

    $suggest_list = array();
    $key = 1;
    foreach ($pageResults as $pageResult) {
      $pageResult['id'] = 'checkin_' . $key++;
      $pageResult['checkins'] = 0;
      $suggest_list[] = $pageResult;

      if ($key > 10) {
        break;
      }
    }

    foreach ($googleResults as $googleResult) {
      $googleResult['id'] = 'checkin_' . $key++;
      $googleResult['checkins'] = 0;
      $suggest_list[] = $googleResult;
    }

/*  //todo implement in the future
     if (count($suggest_list) == 0) {
      $suggest_list[] = array(
        'id' => 'checkin_1',
        'no_content' => true,
      );
    }*/

    echo Zend_Json::encode($suggest_list);
    exit();
  }

  public function viewMapAction()
  {
    $place_id = $this->_getParam('place_id', 0);
    $noPhoto = 'application/modules/Checkin/externals/images/nophoto.png';
    if (!$place_id) {
      $this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Undefined");
      return ;
    }

    $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
    $checksTbl = Engine_Api::_()->getDbTable('checks', 'checkin');

    $this->view->checkin = $checkin = $placesTbl->findRow($place_id);

    if (!$checkin) {
      $this->view->result = 0;
      $this->view->message = Zend_Registry::get('Zend_Translate')->_("Undefined");
      return ;
    }

    if ($checkin->object_type == 'page' || $checkin->object_type == 'event') {
      $viewer = Engine_Api::_()->user()->getViewer();
      $subject = Engine_Api::_()->getItem($checkin->object_type, $checkin->object_id);

      if (!$subject || !$viewer ||  !$this->_helper->requireAuth()->setAuthParams($subject, $viewer, 'view')->isValid()) {
        $this->view->result = 0;
        $this->view->message = Zend_Registry::get('Zend_Translate')->_("Undefined");
        return ;
      }
    }

    $markers = array();
    $markers[0] = array(
      'lat' => $checkin->latitude,
      'lng' => $checkin->longitude,
      'checkin_icon' => ($checkin->icon) ? $checkin->icon : $noPhoto,
      'title' => $checkin->name,
    );

    $this->view->users = $checksTbl->getPlaceVisitors($place_id, 9);
    $this->view->markers = Zend_Json_Encoder::encode($markers);
    $this->view->bounds = Zend_Json_Encoder::encode(Engine_Api::_()->checkin()->getMapBounds($markers));
  }

  public function getEventLocationAction()
  {
    $event_id = $this->_getParam('event_id', 0);
    $keyword = $this->_getParam('keyword', '');

    if (!$event_id || !$keyword) {
      return;
    }

    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event || !$viewer || $viewer->getIdentity() != $event->user_id) {
      return;
    }

    $this->view->places = Engine_Api::_()->checkin()->getGoogleResults($keyword, 0, 0);
  }

  public function setEventLocationAction()
  {
    $event_id = $this->_getParam('event_id', 0);
    $reference = $this->_getParam('reference', '');

    if (!$event_id || !$reference) {
      return;
    }

    $event = Engine_Api::_()->getItem('event', $event_id);
    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$event || !$viewer || $viewer->getIdentity() != $event->user_id) {
      return;
    }

    $this->view->place = $place_info = Engine_Api::_()->checkin()->getGooglePlaceDetails($reference);

    if ($place_info && isset($place_info['name']) && $place_info['name']) {
      $event->location = $place_info['name'];
      $event->save();

      $placesTbl = Engine_Api::_()->getDbTable('places', 'checkin');
      $event_place = $placesTbl->findByObject('event', $event_id);

      if (!$event_place) {
        $event_place = $placesTbl->createRow();
      }

      $event_place->setFromArray(array(
        'object_id' => $event_id,
        'object_type' => 'event',
        'google_id' => isset($place_info['google_id']) ? $place_info['google_id'] : '',
        'name' => isset($place_info['name']) ? $place_info['name'] : '',
        'types' => isset($place_info['types']) ? $place_info['types'] : '',
        'vicinity' => isset($place_info['vicinity']) ? $place_info['vicinity'] : '',
        'latitude' => $place_info['latitude'],
        'longitude' => $place_info['longitude'],
        'creation_date' => new Zend_Db_Expr('NOW()')
      ));

      $event_place->save();
    }
  }

  public function widgetAction()
  {
    $content_id = $this->_getParam('content_id');
    $show_container = $this->_getParam('container', true);

    // Render by content row

    if( null !== $content_id ) {
      $contentTable = Engine_Api::_()->getDbtable('content', 'core');
      $row = $contentTable->find($content_id)->current();
      if( null !== $row ) {
        // Build full structure from children
        $page_id = (int)$row->page_id;
        $pageTable = Engine_Api::_()->getDbtable('pages', 'core');
        $content = $contentTable->fetchAll($contentTable->select()->where('page_id = ?', $page_id));
        $structure = $pageTable->createElementParams($row);
        $children = $pageTable->prepareContentArea($content, $row);
        if( !empty($children) ) {
          $structure['elements'] = $children;
        }
        $structure['request'] = $this->getRequest();
        $structure['action'] = false;

        // Create element (with structure)
        $element = new Engine_Content_Element_Container(array(
          'elements' => array($structure),
          'decorators' => array(
            'Children'
          )
        ));

        // Strip decorators
        if( !$show_container ) {
          foreach( $element->getElements() as $cel ) {
            $cel->clearDecorators();
          }
        }

        $content = $element->render();
        $this->getResponse()->setBody($content);
      }

      $this->_helper->viewRenderer->setNoRender(true);
      return;
    }

    $this->_helper->viewRenderer->setNoRender(true);
    return;
  }
}
