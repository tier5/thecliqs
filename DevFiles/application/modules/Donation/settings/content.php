<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 19.07.12
 * Time: 17:26
 * To change this template use File | Settings | File Templates.
 */

return array(
  array(
    'title' => 'Donation Browse Menu',
    'description' => 'Displays a menu in the donation browse page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.browse-menu',
    'requirements' => array(
      'no-subject',
    ),
  ),
  array(
    'title' => 'Donation Title',
    'description' => 'Displays a donation\'s title. Please put this widget on Donation Profile page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.donation-title',
  ),
  array(
    'title' => 'Donation Options',
    'description' => 'Displays a donation\'s options. Please put this widget on Donation Profile page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-options'
  ),

  array(
    'title' => 'Donation Map',
    'description' => 'Displays a donation\'s contact address on a map. Please put this widget on Donation Profile page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-map'
  ),

  array(
    'title' => 'Donation Status',
    'description' => 'Displays a donation\'s raised status.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-status'
  ),

  array(
    'title' => 'Donation Photo',
    'description' => 'Displays a donation\'s profile photo.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-photo'
  ),

  array(
    'title' => 'Donation Description',
    'description' => 'Displays a donation\'s description.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-description'
  ),

  array(
    'title' => 'Donation Supporters',
    'description' => 'Displays a donation\'s supportDonationers.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-supporters'
  ),

  array(
    'title' => 'Donation Recent',
    'description' => 'Displays a list of donations according to selected filters and search terms. Please put this widget on Browse page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.donation-recent',
    'defaultParams' => array(
      'title' => 'Recent',
      'titleCount' => true
    ),
  ),
  array(
    'title' => 'Donation Search',
    'description' => 'Displays search form which allows members to search donations by keywords and amount. Please put this widget on Donation Browse Page, Browse Donations and any other wished page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.donation-search'
  ),

  array(
    'title' => 'Parent Donation',
    'description' => 'Display Parent Donation of Fundraising .',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.parent-donation'
  ),

  array(
    'title' => 'Donation Photos',
    'description' => 'Displays donation\'s profile photos. Please put this widget on Donation Profile Page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-photos',
    'defaultParams' => array(
      'title' => 'Photos',
      'titleCount' => true
    ),
  ),

  array(
    'title' => 'Donations',
    'description' => 'Displays donation\'s. Please put this widget on Donation Profile Page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-donations',
    'defaultParams' => array(
      'titleCount' => true
    ),
  ),
  array(
    'title' => 'Top Fundraisers',
    'description' => 'Displays donation\'s fundraisers. Please put this widget on Donation Profile Page.',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.profile-fundraisers',
    'defaultParams' => array(
      'titleCount' => true
    ),
  ),

  array(
    'title' => 'Create New Charity',
    'description' => 'Create Charity',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.browse-menu-charity-quick',
  ),

  array(
    'title' => 'Create New Project',
    'description' => 'Create Project',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.browse-menu-project-quick',
  ),

  array(
    'title' => 'Top Donors',
    'description' => 'Top Donors List',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.top-donors',
    'isPaginated' => true,
    'defaultParams' => array(
      'title' => 'Top Donors',
    ),
    'requirements' => array(
      'no-subject',
    ),
  ),

  array(
    'title' => 'Donors List',
    'description' => 'All Donors List',
    'category' => 'Donations',
    'type' => 'widget',
    'name' => 'donation.donors',
  ),
) ?>