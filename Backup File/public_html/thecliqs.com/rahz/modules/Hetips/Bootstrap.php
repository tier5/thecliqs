<?php

class Hetips_Bootstrap extends Engine_Application_Bootstrap_Abstract
{
  public function __construct($application)
  {
    parent::__construct($application);
    $this->initViewHelperPath();

    $front =  Zend_Controller_Front::getInstance();
    $plugin =  new Hetips_Plugin_Core();
    $front->registerPlugin($plugin);
  }

}