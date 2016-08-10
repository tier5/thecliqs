<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Controller.php 2010-12-17 22:10 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Weather_Widget_WeatherController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    $path = Zend_Controller_Front::getInstance()->getControllerDirectory('weather');
    $path = dirname($path) . '/views/scripts';
    $this->view->addScriptPath($path);

    $subject = (Engine_Api::_()->core()->hasSubject()) ? Engine_Api::_()->core()->getSubject() : null;
    $viewer = Engine_Api::_()->user()->getViewer();

    $locationsTbl = Engine_Api::_()->getDbTable('locations', 'weather');
    $weatherApi = Engine_Api::_()->getApi('core', 'weather');
    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->view->can_edit_location = $weatherApi->checkCanEdit($subject);
    $this->view->unit_system = $settings->getSetting('weather.unit_system', 'us');

    if ($subject && $subject->getType() == 'event') {

      $locationInfo = $locationsTbl->getObjectLocation($subject->getType(), $subject->getIdentity());
      $location = ($locationInfo && $locationInfo->location) ? $locationInfo->location : $subject->location;

      $this->view->object_type = $subject->getType();
      $this->view->object_id = $subject->getIdentity();

      $this->view->weather = $weatherApi->getLocationData($location);

    } elseif ($subject && $subject->getType() == 'page') {

      $locationInfo = $locationsTbl->getObjectLocation($subject->getType(), $subject->getIdentity());
      $location = ($locationInfo && $locationInfo->location)
        ? $locationInfo->location
        : $subject->city . '+' . $subject->state . '+' . $subject->country;

      $location = ($location != '++') ? $location : '';

      $this->view->object_type = $subject->getType();
      $this->view->object_id = $subject->getIdentity();

      $this->view->weather = $weatherApi->getLocationData($location);

    } elseif ($subject && $subject->getType() == 'user') {

      $locationInfo = $locationsTbl->getObjectLocation($subject->getType(), $subject->getIdentity());
      $location = ($locationInfo && $locationInfo->location) ? $locationInfo->location : '';

      $this->view->object_type = $subject->getType();
      $this->view->object_id = $subject->getIdentity();

      $this->view->weather = $weatherApi->getLocationData($location);

    } elseif ($viewer and $viewer->getIdentity() != 0) {

      $locationInfo = $locationsTbl->getObjectLocation('user', $viewer->getIdentity());
      $location = ($locationInfo && $locationInfo->location) ? $locationInfo->location : '';

      $this->view->object_type = 'user';
      $this->view->object_id = $viewer->getIdentity();

      $this->view->can_edit_location = $weatherApi->checkCanEdit($viewer);
      $this->view->weather = $weatherApi->getLocationData($location);

    } else {

      $location = $settings->getSetting('weather.default_location', 'New-York');

      $this->view->object_type = 'default';
      $this->view->object_id = 0;

      $this->view->can_edit_location = false;
      $this->view->weather = $weatherApi->getLocationData($location);

    }

    if (!$this->view->weather) {
      return $this->setNoRender();
    }

    if (!$location && !$this->view->can_edit_location) {
      return $this->setNoRender();
    }
  }
}