<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2011-08-19 17:07:11 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

return array(
  array(
    'title' => 'Navigation Tabs',
    'description' => 'Displays the Navigation tabs for stores having links of pages Store Home, Browse Products, Stores and Cart. This widget should be placed at the top of Store Home, Browse Products and Stores pages.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.navigation-tabs',
  ),

	/* Product Widgets */
  array(
    'title' => 'Browse Products',
    'description' => 'Displays a list of products according to selected filters and search terms. Please put this widget on Store Home and Browse Products page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-browse',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Browse Products',
      'titleCount' => true,
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),
  array(
    'title' => 'Product Search',
    'description' => 'Displays search form which allows members to search products by keywords and price. Please put this widget on Store Home, Browse Products and any other wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-search',
		'defaultParams' => array(
      'title' => 'STORE_Search Product',
    )
  ),
  array(
    'title' => 'Product Categories',
    'description' => 'Displays product categories. Please put this widget on Store Home, Browse Products and any other wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-categories',
		'defaultParams' => array(
      'title' => 'STORE_Product Categories',
    )
  ),
  array(
    'title' => 'Product Tags',
    'description' => 'Displays product tags and allows to filter products by tags. Please put this widget on Store Home, Browse Products and any other wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-tags',
		'defaultParams' => array(
      'title' => 'STORE_Product Tags',
    )
  ),

  array(
    'title' => 'Product Status',
    'description' => 'Displays a product\'s name and featured/sponsored status on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-status',
  ),
  array(
    'title' => 'Product Information',
    'description' => 'Displays a product\'s detailed information on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-info',
  ),
  array(
    'title' => 'Product Photos',
    'description' => 'Displays a product\'s photos on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-photos',
    'defaultParams' => array(
      'title' => 'Photos'
    )
  ),

  array(
    'title' => 'Product Audios',
    'description' => 'Displays a product\'s audios on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-audios',
    'defaultParams' => array(
      'title' => 'Audios'
    )
  ),

  array(
    'title' => 'Product Video',
    'description' => 'Displays a product\'s video on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-video',
    'defaultParams' => array(
      'title' => 'Video'
    )
  ),

  array(
    'title' => 'Product Options',
    'description' => 'Displays a product\'s photos on its page. Please put this widget on Product Profile page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-options',
  ),

  array(
    'title' => 'Product Of The Day',
    'description' => 'Displays most viewed product for current day. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-of-the-day',
		'defaultParams' => array(
      'title' => 'STORE_Product Of The Day',
    )
  ),
	array(
    'title' => 'Featured Products Carousel',
    'description' => 'Displays a list of featured products in a nice carousel. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-featured-carousel',
		'defaultParams' => array(
      'title' => 'STORE_Featured Products',
    )
  ),
  array(
    'title' => 'Sponsored Products Carousel',
    'description' => 'Displays a list of sponsored products in a nice carousel. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-sponsored-carousel',
		'defaultParams' => array(
      'title' => 'STORE_Sponsored Products',
    )
  ),

	array(
    'title' => 'Featured Products Slider',
    'description' => 'Displays a list of featured products in a nice slider. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-slider-featured',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Featured Products Slider',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

	array(
    'title' => 'Sponsored Products Slider',
    'description' => 'Displays a list of sponsored products in a nice slider. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-slider-sponsored',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Sponsored Products Slider',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Random Sponsored Products in Site Footer',
    'description' => 'Displays a list of random sponsored products at the bottom of your site. Please put this widget above Footer Menu widget on Site Footer.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-footer-products',
  ),

	array(
    'title' => 'Random Sponsored Products in Sidebar',
    'description' => 'Displays a list of random sponsored products in a widget. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-side-products',
		'defaultParams' => array(
      'title' => 'STORE_Products',
    )
  ),






	/*Store Widgets*/
  array(
    'title' => 'Store Search',
    'description' => 'Displays search form which allows members to search stores by keywords and categories, etc. Please put this widget on Browse Stores page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-search',
  ),
  array(
    'title' => 'Browse Stores',
    'description' => 'Displays a list of stores according to selected filters. Please put this widget on Browse Stores page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-browse',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Browse Stores',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Store Categories',
    'description' => 'Displays store categories(page types). Please put this widget on Browse Stores page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-categories',
		'defaultParams' => array(
      'title' => 'STORE_Store Categories',
    )
  ),

  array(
    'title' => 'Store Locations',
    'description' => 'Displays store locations(page locations). Please put this widget on Browse Stores page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-locations',
		'defaultParams' => array(
      'title' => 'STORE_Store Locations',
    )
  ),

  array(
    'title' => 'Store Of The Day',
    'description' => 'Displays most viewed store for current day. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-of-the-day',
		'defaultParams' => array(
      'title' => 'STORE_Store Of The Day',
    )
  ),

  array(
    'title' => 'Popular Store Tags',
    'description' => 'Displays store tags and allows to filter stores by tags. Please put this widget on Browse Stores page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-tags',
		'defaultParams' => array(
      'title' => 'STORE_Store Tags',
    )
  ),

  array(
    'title' => 'Popular Stores',
    'description' => 'Displays most viewed stores. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-popular-stores',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'STORE_Popular Stores',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Featured Stores Slider',
    'description' => 'Displays featured stores in a nice slider. Please put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-slider-featured',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Featured Stores Slider',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Sponsored Stores Slider',
    'description' => 'Displays sponsored stores in a nice slider. Put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.store-slider-sponsored',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Sponsored Stores Slider',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Random Products',
    'description' => 'Displays random products. Put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-randoms',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Random Products',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Popular Products',
    'description' => 'Displays popular products. Put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-populars',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Popular Products',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),

  array(
    'title' => 'Best Sellers',
    'description' => 'Displays most sold products. Put this widget on any wished page.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.product-best-sellers',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'STORE_Best Sellers',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject'
    ),
  ),
	/* X Store Widgets */

  array(
    'title' => 'Profile Wishlist',
    'description' => 'Displays products that a member has added in wishlist. Put this widget on member profile.',
    'category' => 'Store',
    'type' => 'widget',
    'name' => 'store.profile-wish-list',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Wishlist',
      'titleCount' => true
    ),
    'requirements' => array(
      'subject' => 'user',
    ),
  ),
);