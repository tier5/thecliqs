<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: widgets.php 2011-08-19 17:07:11 mirlan $
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
		'title' => 'Store',
		'description' => 'Displays the page store products.',
		'category' => 'Tabs',
		'type' => 'widget',
		'name' => 'store.page-profile-products',
    'isPaginated' => true,
		'defaultParams' => array(
			'title' => 'Store',
			'titleCount' => true
		),
    'requirements' => array(
      'no-subject',
    ),
	),
);