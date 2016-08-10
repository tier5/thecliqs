<?php

class Weather_IndexController extends Core_Controller_Action_Standard
{
  public function init()
  {
    $ajaxContext = $this->_helper->getHelper('AjaxContext');
    $ajaxContext->addActionContext('edit-location', 'json')
      ->initContext();
  }

  public function indexAction()
  {}

  public function editLocationAction()
  {
    $location = $this->_getParam('location');
    $object_type = $this->_getParam('object_type', 'user');
    $object_id = $this->_getParam('object_id', 0);

    $viewer = Engine_Api::_()->user()->getViewer();

    if (!$viewer || $viewer->getIdentity() == 0) {
      $this->view->error = true;
      $this->view->message = $this->view->translate('weather_Please login to continue.');
      return;
    }

    if (!$object_id || !$object_type) {
      $this->view->error = true;
      $this->view->message = $this->view->translate('weather_Please type your location.');
      return;
    }

    $weatherApi = Engine_Api::_()->getApi('core', 'weather');
    $object = Engine_Api::_()->getItem($object_type, $object_id);
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->can_edit_location = $weatherApi->checkCanEdit($object);
    $this->view->unit_system = $settings->getSetting('weather.unit_system', 'us');

    if (!$this->view->can_edit_location) {
      $this->view->error = true;
      $this->view->message = $this->view->translate('weather_You cannot edit this location.');
      return;
    }

    $locationTbl = Engine_Api::_()->getDbTable('locations', 'weather');
    $locationItem = $locationTbl->getObjectLocation($object_type, $object_id);

    if (!$locationItem) {
      $locationItem = $locationTbl->createRow(array(
        'user_id' => $viewer->getIdentity(),
        'object_type' => $object_type,
        'object_id' => $object_id
      ));
    }

    $locationItem->location = $location;
    $locationItem->save();

    $this->view->weather = $weatherApi->getLocationData($location);

    $this->view->error = false;
    $this->view->html = $this->view->render('_weather.tpl');
  }
}
