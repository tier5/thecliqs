<?php

/**
 * SocialEngine
 *
 * @category   Engine
 * @package    Engine_Server_Php
 * @copyright  Copyright 2006-2016 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Ali Mousavi <ali@socialengine.com>
 */

class Engine_Server_Php
{

  //PHP 5 Constants
  const PHP_VERSION_5_3 = '5.3.0';
  const PHP_VERSION_5_6 = '5.6.0';
  
  
  static public function isMinimum($version)
  {
    if ( version_compare(PHP_VERSION, $version ) >= 0 ) {
      return true;
    }
  }
  
}
