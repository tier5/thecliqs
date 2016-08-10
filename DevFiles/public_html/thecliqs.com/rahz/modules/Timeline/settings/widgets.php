<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-08-31 16:05  $
 * @author
 */

/**
 * @category   Application_Extensions
 * @package    Timeline
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
    array(
        'title' => 'Page Timeline Header',
        'description' => 'Displays following widgets with a special timeline design: Profile Cover, Profile Photo, Profile Status, Profile Info, Profile Options. ' .
            'This widget can be used ONLY on TIMELINE profile page',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.page-header',
        'requirements' => array(
            'subject' => 'user',
        ),
    ),

    array(
        'title' => 'Page Timeline Feed',
        'description' => 'Displays Timeline\'s main content (feed actions). ' .
            'This widget can be used ONLY on TIMELINE profile page',
        'category' => 'Timeline',
        'type' => 'widget',
        'name' => 'timeline.page-content',
        'requirements' => array(
            'subject' => 'user',
        ),
    )
);