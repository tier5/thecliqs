<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 27.03.12
 * Time: 11:25
 * To change this template use File | Settings | File Templates.
 */

class Page_View_Helper_Distance extends Zend_View_Helper_Abstract
{
  public function distance( $distance )
  {
    $distance *= 10;
    $num = (int) $distance;
    return ($num / 10);
  }
}