<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Checkins
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 28.11.11 18:04 TeaJay $
 * @author     Taalay
 */

return array(
  array(
    'title' => 'Profile Check-Ins',
    'description' => 'Displays a member\'s check-ins on their profile with location.',
    'category' => 'Checkins',
    'type' => 'widget',
    'name' => 'checkin.profile-checkins',
    'defaultParams' => array(
      'title' => 'Check-Ins',
    ),
  ),
  array(
    'title' => 'Event Profile Check-Ins',
    'description' => 'Displays thumbnails of members who checked-in. Please put this widget on the right side.',
    'category' => 'Checkins',
    'type' => 'widget',
    'name' => 'checkin.event-checkins',
    'defaultParams' => array(
      'title' => 'Event Profile Check-Ins',
    ),
  ),
  array(
    'title' => 'Event Map',
    'description' => 'Displays the event\'s location on Google Map.',
    'category' => 'Checkins',
    'type' => 'widget',
    'name' => 'checkin.event-map',
    'defaultParams' => array(
      'title' => 'Event Map',
    ),
  ),
);