<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Menus.php 2013-01-17 15:24:00 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Headvancedalbum
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Headvancedalbum_Plugin_Menus
{

  public function onMenuInitialize_HeadvancedalbumPhotos()
  {
    print_die('onMenuInitialize_HeadvancedalbumPhotos');
    $subject = Engine_Api::_()->core()->getSubject();
    return true;

    /*return array(
      'label' => 'OFFERS_upcoming_offers',
      'href' => $subject->getHref().'/content/offers/',
      'onClick' => 'Offers.list("upcoming"); return false;',
      'route' => 'offers_upcoming',
    );*/
  }

  public function onMenuInitialize_HeadvancedalbumAlbums()
  {
    print_die('onMenuInitialize_HeadvancedalbumAlbums');
    $subject = Engine_Api::_()->core()->getSubject();
    return true;

    /*return array(
      'label' => 'OFFERS_upcoming_offers',
      'href' => $subject->getHref().'/content/offers/',
      'onClick' => 'Offers.list("upcoming"); return false;',
      'route' => 'offers_upcoming',
    );*/
  }
}