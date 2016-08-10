<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-12-17 22:10 ermek $
 * @author     Ermek
 */

/**
 * @category   Application_Extensions
 * @package    Weather
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
class Weather_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('weather_Weather Global Settings')
      ->setDescription('WEATHER_FORM_ADMIN_GLOBAL_DESCRIPTION');

    $settings = Engine_Api::_()->getApi('settings', 'core');

    $this->addElement('Text', 'default_location', array(
      'label' => 'weather_Default Location',
      'description' => 'WEATHER_DEFAULT_LOCATION_DESC',
      'value' => $settings->getSetting('weather.default_location', 'New-York')
    ));

    $this->addElement('Select', 'unit_system', array(
      'label' => 'weather_Default temperature units',
      'description' => 'WEATHER_DEFAULT_UNITS_DESC',
      'multiOptions' => array(
        'us' => 'weather_Fahrenheit',
        'si' => 'weather_Celsius',
      ),
      'value' => $settings->getSetting('weather.unit_system', 'us')
    ));

    $this->getElement('unit_system')->getDecorator('Description')->setOption('escape', false);

    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}