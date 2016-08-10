<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2012-02-01 16:58:20 mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title'        => 'Timeline Header',
    'description'  => 'Displays following widgets with a special timeline design: Profile Cover, Profile Photo, Profile Status, Profile Info, Profile Options. ' .
      'This widget can be used ONLY on TIMELINE profile page',
    'category'     => 'Timeline',
    'type'         => 'widget',
    'name'         => 'timeline.header',
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title'        => 'Timeline Feed',
    'description'  => 'Displays Timeline\'s main content (feed actions). ' .
      'This widget can be used ONLY on TIMELINE profile page',
    'category'     => 'Timeline',
    'type'         => 'widget',
    'name'         => 'timeline.content',
    'requirements' => array(
      'subject' => 'user',
    ),
  )
) ?>