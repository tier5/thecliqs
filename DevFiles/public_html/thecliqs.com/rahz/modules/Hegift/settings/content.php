<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hegift
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 03.02.12 12:50 TeaJay $
 * @author     Taalay
 */

return array(
  array(
    'title' => 'Browse Gifts',
    'description' => 'Displays list of the gifts with tabs: Actual, Recent, Photo, Audio, Video. Put this widget on Browse Gifts page.',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.browse-gifts',
  ),
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation menu: Browse Gifts, My Gifts, Sent/Received gifts. Put this widget on Browse Gifts page.',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.navigation-tabs',
  ),
  array(
    'title' => 'Profile Photo',
    'description' => 'Displays a member\'s photo on their profile with a received gift. Replace Profile Photo widget to this widget on Member Profile page.',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.profile-photo',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Gift Categories',
    'description' => 'Displays gift categories. Put this widget on Browse Gifts page',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.gift-categories',
    'defaultParams' => array(
      'title' => 'Categories',
    )
  ),
  array(
    'title' => 'Birthdays',
    'description' => 'Displays friends birthdays for today and suggest to send a gift. You can put this widget on any page, it is recommended to put it on Member Home page so member wont miss any birthday.',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.birthdays',
    'defaultParams' => array(
      'title' => 'Birthdays',
    )
  ),
  array(
    'title' => 'Profile Gifts',
    'description' => 'Displays a member\'s received gifts on their profile.',
    'category' => 'Gifts',
    'type' => 'widget',
    'name' => 'hegift.profile-gifts',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Gifts',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
);