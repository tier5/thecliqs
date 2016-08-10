<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Donation
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @author     adik
 * @date       15.08.12
 * @time       13:01
 */

return array(
  array(
    'title' => 'Donations',
    'description' => 'Displays the page donation.',
    'category' => 'Tabs',
    'type' => 'widget',
    'name' => 'donation.page-profile-donations',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Donations',
      'titleCount' => true
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),
);

