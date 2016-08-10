<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

$others = array();
$page = array(
  array(
    'title' => 'HTML Block',
    'description' => 'Inserts any HTML of your choice.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.html-block',
    'special' => 1,
    'autoEdit' => true,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
            'value' => 'HTML Block'
          ),
        ),
        array(
          'Textarea',
          'data',
          array(
            'label' => 'HTML'
        ),
        ),
      ),
    ),
  ),
  array(
    'title' => 'Page Rate',
    'description' => 'Displays the page\'s rate information.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'rate.widget-rate',
		'defaultParams' => array(
      'title' => 'Page Rate',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Activity Feed',
    'description' => 'Displays the page\'s activity feed(wall).',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'page.feed',
  	'defaultParams' => array(
      'title' => 'Updates',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Photo',
    'description' => 'Displays the page\'s photo(logo).',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.profile-photo',
		'defaultParams' => array(
      'title' => 'Page Photo',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Like Status',
    'description' => 'Displays the page\'s `likes` options.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'like.status'
  ),
  array(
    'title' => 'Page Map',
    'description' => 'Displays the page\'s location on Google Map.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.profile-map',
		'defaultParams' => array(
      'title' => 'Page Map',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Note',
    'description' => 'Displays the page\'s note - informative/welcome text, team members are allowed to edit this note.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.profile-note',
		'defaultParams' => array(
      'title' => 'Page Note',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Favorite Pages',
    'description' => 'Displays the page\'s favorite pages',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.favorite-pages',
		'defaultParams' => array(
      'title' => 'Favorite Pages',
  		'titleCount' => true
    ),
  ),
  array(
    'title' => 'Page Team',
    'description' => 'Displays the page\'s team members.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.profile-admins',
    'isPaginated' => true,
  	'defaultParams' => array(
      'title' => 'Page Team',
  		'titleCount' => false
    ),
  ),

  array(
    'title' => 'Members Likes This',
    'description' => 'Displays members who liked the page.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'like.box',
		'defaultParams' => array(
      'title' => 'like_Like Club',
  		'titleCount' => true
    ),
  ),
  array(
    'title' => 'Page Options',
    'description' => 'Displays the page\'s options.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.profile-options',
		'defaultParams' => array(
      'title' => 'Page Options',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Info',
    'description' => 'Displays the page\'s detailed information.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'page.profile-fields',
  	'defaultParams' => array(
      'title' => 'Info',
  		'titleCount' => true
    ),
  ),
	array(
		'title' => 'Search',
		'description' => 'Displays search box.',
		'category' => 'Widgets',
		'type' => 'widget',
		'name' => 'page.search',
		'defaultParams' => array(
      'title' => 'Search',
  		'titleCount' => false
    ),
  ),
	array(
		'title' => 'Tag Cloud',
		'description' => 'Displays tags cloud box.',
		'category' => 'Widgets',
		'type' => 'widget',
		'name' => 'page.tag-cloud',
		'defaultParams' => array(
      'title' => 'Tag Cloud',
  		'titleCount' => false
    ),
  ),
  array(
    'title' => 'Page Links',
    'description' => 'Displays the page\'s links.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'page.page-links',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Links',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject',
    ),
  ),
  array(
    'title' => 'Statistics',
    'description' => 'Displays the page\'s likes, views. Statistics(graphic) - only team members are allowed to view.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.page-statistics',
    'defaultParams' => array(
      'title' => 'Statistics',
      'titleCount' => false
    ),
  ),
  array(
    'title' => 'Staff',
    'description' => 'Displays the page\'s staff list.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'page.profile-staff',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Staff',
      'titleCount' => true
    ),
  ),
  array(
    'title' => 'Page Profile Check-Ins',
    'description' => 'Displays thumbnails of members who checked-in. Please put this widget on the right side.',
    'category' => 'Widgets',
    'type' => 'widget',
    'name' => 'page.page-profile-checkins',
    'defaultParams' => array(
      'title' => 'Check-Ins Profile Thumbnails',
      'titleCount' => false
    ),
  ),

  array(
    'title' => 'Check-Ins Tab',
    'description' => 'Displays the page\'s check-ins in detail in tab.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'checkin.page-checkins',
    'defaultParams' => array(
      'title' => 'Check-Ins',
      'titleCount' => false
    ),
  ),

  array(
    'title' => 'Tab Container',
    'description' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'core.container-tabs',
    'special' => 1,
    'defaultParams' => array(
      'max' => 10
    ),
    'canHaveChildren' => true,
    'childAreaDescription' => 'Adds a container with a tab menu. Any other blocks you drop inside it will become tabs.',
    //'special' => 1,
    'adminForm' => array(
      'elements' => array(
        array(
          'Text',
          'title',
          array(
            'label' => 'Title',
          )
        ),
        array(
          'Select',
          'max',
          array(
            'label' => 'Max Tab Count',
            'description' => 'Show sub menu at x containers.',
            'default' => 10,
            'multiOptions' => array(
              0 => 0,
              1 => 1,
              2 => 2,
              3 => 3,
              4 => 4,
              5 => 5,
              6 => 6,
              7 => 7,
              8 => 8,
              9 => 9,
              10 => 10,
              11 => 11
            )
          )
        ),
      )
    ),
  ),

  array(
    'title' => 'Login or Signup',
    'description' => 'Displays a login form and a signup link for members that are not logged in.',
    'category' => 'User',
    'type' => 'widget',
    'name' => 'user.login-or-signup',
    'requirements' => array(
      'no-subject',
    ),
  )
);

$modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
if ($modulesTbl->isModuleEnabled('offers')) {
  $others = array(
    array(
      'title' => 'Favorite Offer',
      'description' => 'Displays the page\'s favorite offer',
      'category' => 'Widgets',
      'type' => 'widget',
      'name' => 'offers.favorite-offer',
      'defaultParams' => array(
        'title' => 'Favorite Offer',
        'titleCount' => true
      )
    )
  );
}

return array_merge($page, $others);