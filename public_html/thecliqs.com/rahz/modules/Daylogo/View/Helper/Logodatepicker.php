<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 * @version    $Id: Logodatepicker.php 2012-08-16 16:38 nurmat $
 * @author     Nurmat
 */

/**
 * @category   Application_Extensions
 * @package    Daylogo
 * @copyright  Copyright Hire-Experts
 * @license    http://www.hire-experts.com
 */

class Daylogo_View_Helper_Logodatepicker extends Zend_View_Helper_FormElement
{

  public function logodatepicker($name, $value = null, $attibs = null)
  {
    $localeObject = Zend_Registry::get('Locale');

    $months = Zend_Locale::getTranslationList('months', $localeObject);
    $months = $months['format'][$months['default']];

    $days = Zend_Locale::getTranslationList('days', $localeObject);
    $days = $days['format'][$days['default']];

    $js_str = "
      window.addEvent('domready', function (){
        new DatePicker('input[name={$name}]', {
          pickerClass: 'datepicker_vista',
          timePicker: true,
          format: 'Y-m-d H:i',
          inputOutputFormat: 'Y-m-d H:i',
          months : " . Zend_Json::encode(array_values($months)) . ",
          days : " . Zend_Json::encode(array_values($days)) . ",
          allowEmpty: true
        });
      });
    ";

    $this->view->headScript()
        ->appendFile( $this->view->baseUrl() . '/application/modules/Daylogo/externals/scripts/datepicker.js')
        ->appendScript($js_str);

    return '<div class="datepicker_container '.$name.'-container">'.$this->view->formText($name, $value, $attibs).'</div>';

  }

}