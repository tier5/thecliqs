<?php 
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation tabs for pages having links of pages Browse Pages, My Pages, Create New Page. This widget should be placed only at the top of Browse Pages',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.navigation-tabs',
  ),

  array(
    'title' => 'Recent Pages',
    'description' => 'Displays recently created pages.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.recent-pages',
    'defaultParams' => array(
      'title' => 'Recent Pages',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Member Pages',
    'description' => 'Displays member pages. Please drag it on Member Profile page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.profile-pages',
    'defaultParams' => array(
      'title' => 'Pages',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Most Popular Pages',
    'description' => 'Displays most popular pages.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.popular-pages',
    'defaultParams' => array(
      'title' => 'Most Popular Pages',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Featured Pages',
    'description' => 'Displays featured pages.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.featured-pages',
    'defaultParams' => array(
      'title' => 'Featured Pages',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Sponsored Pages',
    'description' => 'Displays featured pages.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.sponsored-pages',
    'defaultParams' => array(
      'title' => 'Sponsored Pages',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Featured Pages Carousel',
    'description' => 'Displays Featured Pages in a nice carousel.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.featured-carousel',
    'defaultParams' => array(
      'title' => 'Featured Pages Carousel',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Sponsored Pages Carousel',
    'description' => 'Displays Sponsored Pages in a nice carousel.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.sponsored-carousel',
    'defaultParams' => array(
      'title' => 'Sponsored Pages Carousel',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Browse Pages',
    'description' => 'Displays Pages.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.browse-pages',
  ),
  array(
    'title' => 'Page Categories',
    'description' => 'Displays Page Categories',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-categories',
    'defaultParams' => array(
      'title' => 'Page Categories',
    )
  ),
  array(
    'title' => 'Page Locations',
    'description' => 'Displays Page Locations',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-locations',
    'defaultParams' => array(
      'title' => 'Page Locations',
    )
  ),
  array(
    'title' => 'Page Tags',
    'description' => 'Displays Page Tags',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-tags',
    'defaultParams' => array(
      'title' => 'Page Tags',
    )
  ),
  array(
    'title' => 'Page Search',
    'description' => 'Displays search form which allows members to search page by keywords and categories. Please put this widget on Browse Page',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-search',
    'defaultParams' => array(
      'title' => 'Search Page',
    )
  ),
  array(
    'title' => 'Page Abc',
    'description' => 'Displays search form which allows members to search page by keywords and categories. Please put this widget on Browse Page',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-abc',
    'defaultParams' => array(
//      'title' => 'Page Abc',
    )
  ),
  array(
    'title' => 'Quick Menu',
    'description' =>'Displays a small menu option - link to create a new page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.browse-menu-quick',
  ),
  array(
    'title' => 'Advanced Search',
    'description' => 'Displays advanced search form which allows to search pages by keyword, location and within radius, category, etc. Please put this widget on Browse Page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-advanced-search',
    'defaultParams' => array(
      'title' => 'Advanced Search',
    )
  ),

  array(
    'title' => 'Search Pages by Location',
    'description' => 'Automatically detects a member location and displays pages nearest to the member. Also it allows to search pages by locations. Please put this widget on any wished page.',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.my-location',
    'isPaginated' => true,
  ),
  array(
    'title' => 'Page Categories Tree',
    'description' => 'Displays Page Categories',
    'category' => 'Page',
    'type' => 'widget',
    'name' => 'page.page-categories-tree',
    'defaultParams' => array(
      'title' => 'Page Categories',
    )
  ),
);