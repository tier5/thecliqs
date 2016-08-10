<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 06.01.12 16:32 TeaJay $
 * @author     Taalay
 */

return array(
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation tabs(menu) for Credits: Credits Home, My Credits, FAQ. Put this widget on the top of Credits Home and My Credits pages.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.navigation-tabs',
  ),

  array(
    'title' => 'Top Members',
    'description' => 'Displays top members list with highest number of credits. Put this widget on Credits Home or on any other page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.browse-users',
    'defaultParams' => array(
      'title' => 'Top Members'
    )
  ),

  array(
    'title' => 'Transaction List',
    'description' => 'Displays member\'s credits transactions list(earned, spend, purchased, etc). Put this widget My Credits page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.transaction-list',
    'defaultParams' => array(
      'title' => 'Transaction List'
    )
  ),

  array(
    'title' => 'Send Credits',
    'description' => 'Displays a form which allows members to send credits to friends. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.send-credits',
    'defaultParams' => array(
      'title' => 'Send Credits'
    )
  ),

  array(
    'title' => 'My Credits',
    'description' => 'Displays credits balance, position and quick-stats of current user. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.my-credits',
    'defaultParams' => array(
      'title' => 'My Credits'
    )
  ),

  array(
    'title' => 'Buy Credits',
    'description' => 'Displays a widget which allows members to buy credits. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.buy-credits',
    'defaultParams' => array(
      'title' => 'Buy Credits'
    )
  ),

  array(
    'title' => 'Random FAQ',
    'description' => 'Displays a random FAQ about credits with a link to FAQ section. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.faq',
    'defaultParams' => array(
      'title' => 'FAQ'
    )
  ),

  array(
    'title' => 'Quick Links',
    'description' => 'Displays a widgets which displays a list of quick links to create a social media content to earn credits. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.create-items',
    'defaultParams' => array(
      'title' => 'Quick Links'
    )
  ),

  array(
    'title' => 'Buy Level',
    'description' => 'Displays a widget which allows members to buy level. Put this widget on any page.',
    'category' => 'Credits',
    'type' => 'widget',
    'name' => 'credit.buy-level',
    'defaultParams' => array(
      'title' => 'Buy Level'
    )
  )
);