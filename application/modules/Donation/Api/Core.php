<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 20.07.12
 * Time: 12:08
 * To change this template use File | Settings | File Templates.
 */
class Donation_Api_Core extends Core_Api_Abstract
{
  public function onRenderLayoutDefault($event)
  {
    // Arg should be an instance of Zend_View
    $view = $event->getPayload();

    if ($view instanceof Zend_View) {
      $view->headScript()
        ->appendFile('application/modules/Donation/externals/scripts/core.js');
    }
  }

  public function getInitJs($content_info)
  {
    if(empty($content_info)){
      return false;
    }

    $content = $content_info['content'];
    $content_id = $content_info['content_id'];

    if($content == 'charity_donations'){
      return "donation.init_donation();";
    }
    elseif($content == 'project_donations'){
      return "donation.init_donation();";
    }

    return false;
  }

  public function canCreateCharity()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }
    // Must be able to create charities
    if( !Engine_Api::_()->authorization()->isAllowed('donation', $viewer, 'create_charity') ) {
      return false;
    }

    if(!$this->getSetting('donation.enable.charities')){
      return false;
    }

    return true;
  }

  public function canCreateProject()
  {
    // Must be logged in
    $viewer = Engine_Api::_()->user()->getViewer();
    if( !$viewer || !$viewer->getIdentity() ) {
      return false;
    }

    // Must be able to create projects
    if( !Engine_Api::_()->authorization()->isAllowed('donation', $viewer, 'create_project') ) {
      return false;
    }

    if(!$this->getSetting('donation.enable.projects')){
      return false;
    }

    return true;
  }

  public function getSetting($setting)
  {
    return Engine_Api::_()->getApi('settings', 'core')->getSetting($setting,1);
  }

  function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
    $fp = fopen("php://memory", 'r+');
    fputs($fp, $input);
    rewind($fp);
    $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape добавлена в php 5.3.0
    fclose($fp);
    return $data;
  }

  function datediff(DateTime $date1, DateTime $date2) {
    $diff = new MyDateInterval();
    if($date1 > $date2) {
      $tmp = $date1;
      $date1 = $date2;
      $date2 = $tmp;
      $diff->invert = true;
    }

    $diff->y = ((int) $date2->format('Y')) - ((int) $date1->format('Y'));
    $diff->m = ((int) $date2->format('n')) - ((int) $date1->format('n'));
    if($diff->m < 0) {
      $diff->y -= 1;
      $diff->m = $diff->m + 12;
    }
    $diff->d = ((int) $date2->format('j')) - ((int) $date1->format('j'));
    if($diff->d < 0) {
      $diff->m -= 1;
      $diff->d = $diff->d + ((int) $date1->format('t'));
    }
    $diff->h = ((int) $date2->format('G')) - ((int) $date1->format('G'));
    if($diff->h < 0) {
      $diff->d -= 1;
      $diff->h = $diff->h + 24;
    }
    $diff->i = ((int) $date2->format('i')) - ((int) $date1->format('i'));
    if($diff->i < 0) {
      $diff->h -= 1;
      $diff->i = $diff->i + 60;
    }
    $diff->s = ((int) $date2->format('s')) - ((int) $date1->format('s'));
    if($diff->s < 0) {
      $diff->i -= 1;
      $diff->s = $diff->s + 60;
    }

    return $diff;
  }
}

class MyDateInterval {
  public $y;
  public $m;
  public $d;
  public $h;
  public $i;
  public $s;
  public $invert;

  public function format($format) {
    $format = str_replace('%R%y', ($this->invert ? '-' : '+') . $this->y, $format);
    $format = str_replace('%R%m', ($this->invert ? '-' : '+') . $this->m, $format);
    $format = str_replace('%R%d', ($this->invert ? '-' : '+') . $this->d, $format);
    $format = str_replace('%R%h', ($this->invert ? '-' : '+') . $this->h, $format);
    $format = str_replace('%R%i', ($this->invert ? '-' : '+') . $this->i, $format);
    $format = str_replace('%R%s', ($this->invert ? '-' : '+') . $this->s, $format);

    $format = str_replace('%y', $this->y, $format);
    $format = str_replace('%m', $this->m, $format);
    $format = str_replace('%d', $this->d, $format);
    $format = str_replace('%h', $this->h, $format);
    $format = str_replace('%i', $this->i, $format);
    $format = str_replace('%s', $this->s, $format);

    return $format;
  }
}
