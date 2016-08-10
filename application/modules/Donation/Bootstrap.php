<?php

class Donation_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $headScript = new Zend_View_Helper_HeadScript();
    $headScript->appendFile(Zend_Registry::get('StaticBaseUrl')
      . 'application/modules/Donation/externals/scripts/core.js');
  }

  public  function _bootstrap($resource=null)
  {
    parent::_bootstrap($resource);
    $front = Zend_Controller_Front::getInstance();
//    $front->registerPlugin(new Donation_Plugin_Core(), 999);
  }

  function str_getcsv_donation($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
    $fp = fopen("php://memory", 'r+');
    fputs($fp, $input);
    rewind($fp);
    $data = fgetcsv($fp, null, $delimiter, $enclosure); // $escape добавлена в php 5.3.0
    fclose($fp);
    return $data;
  }
}