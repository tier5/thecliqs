<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Classified
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: content.php 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
return array(
  array(
    'title' => 'Profile Classifieds',
    'description' => 'Displays a member\'s classifieds on their profile.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.profile-classifieds',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Classifieds',
      'titleCount' => true,
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
  array(
    'title' => 'Popular Classifieds',
    'description' => 'Displays a list of most viewed classifieds.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.list-popular-classifieds',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Classifieds',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'popularType',
          array(
            'label' => 'Popular Type',
            'multiOptions' => array(
              'view' => 'Views',
              'comment' => 'Comments',
            ),
            'value' => 'view',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Recent Classifieds',
    'description' => 'Displays a list of recently posted classifieds.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.list-recent-classifieds',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Recent Classifieds',
    ),
    'requirements' => array(
      'no-subject',
    ),
    'adminForm' => array(
      'elements' => array(
        array(
          'Radio',
          'recentType',
          array(
            'label' => 'Recent Type',
            'multiOptions' => array(
              'creation' => 'Creation Date',
              'modified' => 'Modified Date',
            ),
            'value' => 'creation',
          )
        ),
      )
    ),
  ),
  
  array(
    'title' => 'Classified Browse Search',
    'description' => 'Displays a search form in the poll browse page.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.browse-search',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Classified Browse Menu',
    'description' => 'Displays a menu in the poll browse page.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Classified Browse Quick Menu',
    'description' => 'Displays a small menu in the poll browse page.',
    'category' => 'Classifieds',
    'type' => 'widget',
    'name' => 'classified.browse-menu-quick',
    'requirements' => array(
      'no-subject',
    ),
  ),
) ?>