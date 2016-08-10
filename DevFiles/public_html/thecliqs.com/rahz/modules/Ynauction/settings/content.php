<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    YnAuction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: content.php
 * @author     Minh Nguyen
 */
return array(
   array(
    'title' => 'Menu Auctions',
    'description' => 'Displays menu auctions on browse auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.menu-ynauctions',
  ),
  array(
    'title' => 'Detail Auctions',
    'description' => 'Displays detail auctions on detail auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.detail-ynauctions',
  ),
  array(
    'title' => 'Listing Auctions',
    'description' => 'Displays listing auctions on browse auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.listing-ynauctions',
    'defaultParams' => array(
      'title' => 'Listing Auctions',
    ),
  ),
  array(
    'title' => 'Search Listing Auctions',
    'description' => 'Displays search listing auctions on browse auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.search-listing-ynauctions',
    'defaultParams' => array(
      'title' => 'Listing Auctions',
    ),
  ),
  array(
    'title' => 'Featured Auctions',
    'description' => 'Displays featured auctions on browse auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.featured-ynauctions',
    'defaultParams' => array(
      'title' => 'Featured Auctions',
    ),
  ),
   array(
    'title' => 'Description Auctions',
    'description' => 'Displays description auctions on detail auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.description-ynauctions',
    'defaultParams' => array(
      'title' => 'Description',
    ),
  ),
   array(
    'title' => 'Bid History Auctions',
    'description' => 'Displays bid history auctions on detail auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.bid-history-ynauctions',
    'defaultParams' => array(
      'title' => 'Bid History',
    ),
  ),
   array(
    'title' => 'Proposal History Auctions',
    'description' => 'Displays proposal history auctions on detail auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.proposal-history-ynauctions',
    'defaultParams' => array(
      'title' => 'Proposal History',
    ),
  ),
   array(
    'title' => 'Shipping and Payment Auctions',
    'description' => 'Displays shipping and payment auctions on detail auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.shipping-payment-ynauctions',
    'defaultParams' => array(
      'title' => 'Shipping & Payment',
    ),
  ),
    array(
    'title' => 'Search Auctions',
    'description' => 'Displays search auctions on browse auction page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.search-ynauctions',
  	'defaultParams' => array(
      'title' => 'Search Auctions',
    ),
  ),
  array(
    'title' => 'Profile Created Auctions',
    'description' => 'Displays created auctions on user profile page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.profile-created-ynauctions',
    'defaultParams' => array(
      'title' => 'Auctions',
      'titleCount' => true,
    ),
  ),
  array(
    'title' => 'Profile Participated Auctions',
    'description' => 'Displays participated auctions on user profile page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.profile-participated-ynauctions',
     'defaultParams' => array(
      'title' => 'Participated Auctions',
      'titleCount' => true,
    ),
  ),
   array(
    'title' => 'Latest Auctions',
    'description' => 'Displays latest auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.latest-ynauctions',
    'defaultParams' => array(
      'title' => 'Latest Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of latest auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
   array(
    'title' => 'Most Rated Auctions',
    'description' => 'Displays most rated auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.most-rated-ynauctions',
    'defaultParams' => array(
      'title' => 'Most Rated Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of most rated auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
   array(
    'title' => 'Running Auctions',
    'description' => 'Displays running auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.running-ynauctions',
    'defaultParams' => array(
      'title' => 'Running Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of running auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Ending Soon',
    'description' => 'Displays ending soon auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.ending-soon-ynauctions',
    'defaultParams' => array(
      'title' => 'Ending Soon',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of ending soon auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Most Liked Auctions',
    'description' => 'Displays most liked auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.most-liked-ynauctions',
    'defaultParams' => array(
      'title' => 'Most Liked Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of most like auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Related Auctions',
    'description' => 'Displays related auctions on detail page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.related-ynauctions',
    'defaultParams' => array(
      'title' => 'Related Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of related auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Other User Auctions',
    'description' => 'Displays other user auctions on detail page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.other-user-ynauctions',
    'defaultParams' => array(
      'title' => 'Other User Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of other user auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
  array(
    'title' => 'Active Bidders',
    'description' => 'Displays active bidders on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.active-bidders-ynauctions',
    'defaultParams' => array(
      'title' => 'Active Bidders',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of active bidders show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
   array(
    'title' => 'User Auctions',
    'description' => 'Displays user auctions on home page.',
    'category' => ' Auction',
    'type' => 'widget',
    'name' => 'ynauction.user-ynauctions',
    'defaultParams' => array(
      'title' => 'User Auctions',
    ),
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
          'Text',
          'max',
           array(
            'label' => 'Number of User auctions show on page.',
            'value' => 5,
            
          )
        ),
        array(
          'Select',
          'nomobile',
          array(
            'style' => 'display:none',
          )
        ),
      )
    ),
  ),
) ?>