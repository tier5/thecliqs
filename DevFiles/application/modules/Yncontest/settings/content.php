<?php

$results = Engine_Api::_()->yncontest()->getPlugins();
$slideshow = array(
'featured_id' => 'Featured Contests',
'premium_id' => 'Premium Contests',
'endingsoon_id' => 'Ending Soon',
);



defined("_ENGINE") or die("Access denied");

return array(
		array(
				'title' => 'Contest Menu',
				'description' => 'Displays contest menu on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.main-menu',
		),
		array(
		    	'title' => 'Comments',
			    'description' => 'Shows the comments about an item.',
			    'category' => 'Contest',
			    'type' => 'widget',
			    'name' => 'yncontest.item-comment',			    
			    'requirements' => array(
			      'subject',
			    ),
		),
		array(
				'title' => 'Contest Categories',
				'description' => 'Displays contest categories.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.contest-categories',
				'autoEdit' => true,
				'adminForm'=> array(
						'elements' => array(								
								array(
										'Text',
										'max',											
										array(
												'label' => 'Number of category on widget',
												'value' => 8,				
										)
								),								
						),
				),
		),
		
		array(
				'title' => 'Listing entries by type',
				'description' => 'Displays contest entries by type',
				'category' => 'Contest',
				'type' => 'widget',				
				'name' => 'yncontest.listing-entries-by-type',
				'autoEdit' => true,
				'adminForm' => array(
						'elements' => array(
								array(
										'Select',
										'typeyncontest',
										array(
												'RegisterInArrayValidator' => false,
												'decorators' => array(array('ViewScript', array(
														'viewScript' => 'application/modules/Yncontest/views/scripts/_numberOfType.tpl',
														'class' => 'form element')))
										)
								),
								array(
										'Text',
										'maxadvalbum',
										array(
												'label' => 'Number of Photo on a page',
												'value' => 12,				
										)
								),
								array(
										'Text',
										'maxynblog',
										array(
												'label' => 'Number of Blog on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxmp3music',
										array(
												'label' => 'Number of Music on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxynvideo',
										array(
												'label' => 'Number of Video on a page',
												'value' => 12,
										)
								),																
								
								array(
										'Text',
										'heightadvalbum',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthadvalbum',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),
								array(
										'Text',
										'heightynvideo',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthynvideo',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),								
								array(
										'Text',
										'heightynblog',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthynblog',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
								array(
										'Text',
										'heightmp3music',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthmp3music',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
				
						)
				),
		),
		
		array(
				'title' => 'Featured Contests',
				'description' => 'Displays featured contests on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.featured-contest',
				'autoEdit' => true,
				'adminForm'=> array(
						'elements' => array(
								array(
										'Text',
										'number',
										array(
												'label' =>  'Number of entries to display',
												'value' => '5',
												'required' => true,
												'validators' => array(
														array('Between',true,array(1,100)),
												),
										),
								),
								array(
										'Radio',
										'slideshowtype',
										array(
												'label' =>  'Type of Slideshow',												
												'required' => true,
												'multiOptions' => array('featured' => 'Featured Contests','premium' => 'Premium Contests','endingsoon' => 'Ending Soon',),
												'value' => 'featured'
										),
								),
								array(
										'Radio',
										'slider_action',
										array(
												'label' =>  'Effect of Slideshow',	
												'multiOptions' => array('overlap'=>'Overlap','noOverlap'=>'NoOverlap','flash'=>'Flash','fold'=>'Fold','kenburns'=>'Kenburns','push'=>'Push'),
												'value' => 'overlap',
										),
								),
								array(
						          'Text',
						          'height',         
						           array(
						             'label' => 'Heigh of image (px).',
						             'required' => true,
						             'value' => 300,                      
						          )
						        ),						        
						        array(
										'Select',
										'nomobile',
										array(
												'style' => 'display:none',
										)
								),
						),
				),
		),		
		array(
				'title' => 'Contest Profile Most Voted, Viewed Entries',
				'description' => 'Displays most voted, viewed entries in a contest',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-most-item-entries',
				'defaultParams' => array('title' => 'Most Voted, Viewed Entries'),
				'autoEdit' => true,
				'adminForm'=> array(
						'elements' => array(
								array(
										'Text',
										'number',
										array(
												'label' =>  'Number of item to display',
												'value' => '5',
												'required' => true,
												'validators' => array(
														array('Between',true,array(1,100)),
												),
										),
								),
								array(
										'Select',
										'type',
										array(	
												'label' => 'Most of',																							
												'required' => true,
												'multiOptions' => array('view_count' => 'Viewed', 'vote_count'=> 'Voted'),
												'value' => 'view_count'
										),
								),
						),
				),
		),
		
		array(
				'title' => 'Profile Tabs',
				'description' => 'Displays Tabs in Profile Contest',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-tab',
				'defaultParams' => array('title' => 'Entries', ),
				'autoEdit' => true,
				'adminForm' => array(
						'elements' => array(
								array(
										'Select',
										'typeyncontest',
										array(
												'RegisterInArrayValidator' => false,
												'decorators' => array(array('ViewScript', array(
														'viewScript' => 'application/modules/Yncontest/views/scripts/_numberOfType.tpl',
														'class' => 'form element')))
										)
								),
								array(
										'Text',
										'maxadvalbum',
										array(
												'label' => 'Number of Photo on a page',
												'value' => 12,				
										)
								),
								array(
										'Text',
										'maxynblog',
										array(
												'label' => 'Number of Blog on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxmp3music',
										array(
												'label' => 'Number of Music on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxynvideo',
										array(
												'label' => 'Number of Video on a page',
												'value' => 12,
										)
								),																
								
								array(
										'Text',
										'heightadvalbum',
										array(
												'label' => 'The height (px) of each item',
												'value' => 200,
										)
								),
								array(
										'Text',
										'widthadvalbum',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),
								array(
										'Text',
										'heightynvideo',
										array(
												'label' => 'The height (px) of each item',
												'value' => 200,
										)
								),
								array(
										'Text',
										'widthynvideo',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),								
								array(
										'Text',
										'heightynblog',
										array(
												'label' => 'The height (px) of each item',
												'value' => 130,
										)
								),
								array(
										'Text',
										'widthynblog',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
								array(
										'Text',
										'heightmp3music',
										array(
												'label' => 'The height (px) of each item',
												'value' => 130,
										)
								),
								array(
										'Text',
										'widthmp3music',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
				
						)
				),
		),

		array(
				'title' => 'Search Contests',
				'description' => 'Seach contests on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.search-contest',
		),

		array(
				'title' => 'Contest Profile Options',
				'description' => 'Displays contest profile options page.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-options',
				'defaultParams' => array('title' => 'Contest Profile Options', ),
		),
		array(
				'title' => 'Contest Profile Information',
				'description' => 'Displays Information Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-information',
				'defaultParams' => array('title' => 'Contest Profile Information', ),
		),
		array(
				'title' => 'Contest Profile Description',
				'description' => 'Displays Description Contest.',
				'category' => 'Contest',
				'autoEdit' => true,
				'type' => 'widget',
				'name' => 'yncontest.profile-description',
				'defaultParams' => array('title' => 'Description', ),
		),
		array(
				'title' => 'Contest Profile Award',
				'description' => 'Displays Award Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'autoEdit' => true,
				'name' => 'yncontest.profile-award',
				'defaultParams' => array('title' => 'Award', ),
		),
		array(
				'title' => 'Contest Profile Participants',
				'description' => 'Displays Participants Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'autoEdit' => true,
				'name' => 'yncontest.profile-participants',
				'defaultParams' => array('title' => 'Participants', ),
				'adminForm' => array(
						'elements' => array(
								array(
										'Text',
										'number',										 
										array(
												'label' => 'Number of items on a page',
												'value' => 9,				
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
				'title' => 'Contest Profile Participants Center',
				'description' => 'Displays Participants Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'autoEdit' => true,
				'name' => 'yncontest.profile-participants-center',
				'defaultParams' => array('title' => 'Participants', ),
				'adminForm' => array(
						'elements' => array(
								array(
										'Text',
										'number',										 
										array(
												'label' => 'Number of items on a page',
												'value' => 9,				
										)
								),
								array(
						          'Text',
						          'height',         
						           array(
						             'label' => 'Heigh this item (px).',
						            'value' => 50,                      
						          )
						        ),
						        array(
						          'Text',
						          'width',         
						           array(
						             'label' => 'Width this item (px).',
						            'value' => 100, 
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
				'title' => 'Contest Profile Entries',
				'description' => 'Displays Entries Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-entries',
				'defaultParams' => array('title' => 'Entries', ),
		),
		array(
				'title' => 'Contest Profile Winning Entries',
				'description' => 'Displays Winning Entries Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-winning-entries',
				'defaultParams' => array('title' => 'Winning Entries', ),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(			          
			           array(
				          'Text',
				          'heightadvalbum',         
				           array(
				             'label' => 'Heigh this item (Photo, Video) (px).',
				            'value' => 160,                      
				          )
				        ),
				        array(
				          'Text',
				          'widthadvalbum',         
				           array(
				             'label' => 'Width this item (Photo, Video) (px).',
				            'value' => 155, 
				          )
				        ),
				        array(
				          'Text',
				          'heightynblog',         
				           array(
				             'label' => 'Heigh this item (Blog, Music) (px).',
				            'value' => 100,                      
				          )
				        ),
				        array(
				          'Text',
				          'widthynblog',         
				           array(
				             'label' => 'Width this item (Blog, Music) (px).',
				            'value' => 250, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		
		array(
				'title' => 'Contest Profile Manage Winning Entries',
				'description' => 'Displays Manage Winning Entries Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-manage-winning-entries',
				'defaultParams' => array('title' => 'Manage Winning Entries', ),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of item to display on page',
			                'value' => '6',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
				          'Text',
				          'heightadvalbum',         
				           array(
				             'label' => 'Heigh this item (Photo, Video) (px).',
				            'value' => 160,                      
				          )
				        ),
				        array(
				          'Text',
				          'widthadvalbum',         
				           array(
				             'label' => 'Width this item (Photo, Video) (px).',
				            'value' => 155, 
				          )
				        ),
				        array(
				          'Text',
				          'heightynblog',         
				           array(
				             'label' => 'Heigh this item (Blog, Music) (px).',
				            'value' => 100,                      
				          )
				        ),
				        array(
				          'Text',
				          'widthynblog',         
				           array(
				             'label' => 'Width this item (Blog, Music) (px).',
				            'value' => 250, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		
		array(
				'title' => 'Contest Profile Add This',
				'description' => 'Displays Add This Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-addthis',
				'defaultParams' => array('title' => 'Add this', ),
		),
		array(
				'title' => 'Promote Contest',
				'description' => 'Displays Promote Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.contest-promote',
				'defaultParams' => array('title' => 'Promote Contest', ),
		),
		array(
				'title' => 'Contest Announcement',
				'description' => 'Displays Announcement Contest.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.contest-announce',
				'defaultParams' => array('title' => 'Announcement Contest', ),
		),
		array(
				'title' => 'Submit Entry',
				'description' => 'Displays Submit Entry.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.submit-entry',
				'defaultParams' => array('title' => 'Submit Entry', ),
				'autoEdit' => true,
				'adminForm' => array(
						'elements' => array(
								array(
										'Select',
										'typeyncontest',
										array(
												'RegisterInArrayValidator' => false,
												'decorators' => array(array('ViewScript', array(
														'viewScript' => 'application/modules/Yncontest/views/scripts/_numberOfType.tpl',
														'class' => 'form element')))
										)
								),
								array(
										'Text',
										'maxadvalbum',
										array(
												'label' => 'Number of Photo on a page',
												'value' => 12,				
										)
								),
								array(
										'Text',
										'maxynblog',
										array(
												'label' => 'Number of Blog on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxmp3music',
										array(
												'label' => 'Number of Music on a page',
												'value' => 12,
										)
								),
								array(
										'Text',
										'maxynvideo',
										array(
												'label' => 'Number of Video on a page',
												'value' => 12,
										)
								),																
								
								array(
										'Text',
										'height',
										array(
												'label' => 'The height (px) of each item',
												'value' => 178,
										)
								),
								array(
										'Text',
										'width',
										array(
												'label' => 'The width (px) of each item',
												'value' => 110,
										)
								),
				
						)
				),
		),
		array(
				'title' => 'My Contests',
				'description' => 'My Contests.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.my-contests',				
		),
		array(
				'title' => 'My Entries',
				'description' => 'My Entries.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.my-entries',
				'defaultParams' => array('title' => 'My Entries', ),
				'autoEdit' => true,
				'adminForm' => array(
						'elements' => array(
								array(
										'Select',
										'typeyncontest',
										array(
												'RegisterInArrayValidator' => false,
												'decorators' => array(array('ViewScript', array(
														'viewScript' => 'application/modules/Yncontest/views/scripts/_numberOfType.tpl',
														'class' => 'form element')))
										)
								),																					
								
								array(
										'Text',
										'heightadvalbum',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthadvalbum',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),
								array(
										'Text',
										'heightynvideo',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthynvideo',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),								
								array(
										'Text',
										'heightynblog',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthynblog',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
								array(
										'Text',
										'heightmp3music',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthmp3music',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
				
						)
				),
		),		
		array(
				'title' => 'Owner Entries Detail',
				'description' => 'Owner Entries Detail.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.entries-detail-owner',
				'defaultParams' => array('title' => 'Owner Entries Detail', ),
		),

		array(
				'title' => 'Favorite Contests',
				'description' => 'Favorite Contests.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.my-favorite-contests',
		),
		array(
				'title' => 'Follow Contests',
				'description' => 'Follow Contests.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.my-follow-contests',
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '16',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
				          'Text',
				          'height',         
				           array(
				             'label' => 'Heigh this item (px).',
				            'value' => 200,                      
				          )
				        ),
				        array(
				          'Text',
				          'width',         
				           array(
				             'label' => 'Width this item (px).',
				            'value' => 200, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
			)
		),		
		array(
				'title' => 'Premium Contest',
				'description' => 'Display premium contest in left at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.premium-contest',
				'autoEdit' => true,
				'defaultParams' => array('title' => 'Premium Contest', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '5',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		array(
				'title' => 'Top Contests',
				'description' => 'Display top contest in left at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.top-contest',
				'defaultParams' => array('title' => 'Top Contests', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '5',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),
		),
		array(
				'title' => 'Hot Contests',
				'description' => 'Display hot contest in left at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.hot-contest',
				'defaultParams' => array('title' => 'Hot Contests', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '5',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),
		),
		array(
				'title' => 'Tag',
				'description' => 'Display tag in left at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.tag',
				'defaultParams' => array('title' => 'Tag', ),
		),
		array(
				'title' => 'New Contest',
				'description' => 'Display new contest  at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.new-contest',
				'defaultParams' => array('title' => 'New Contests'),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '6',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
				          'Text',
				          'height',         
				           array(
				             'label' => 'Heigh this item (px).',
				            'value' => 200,                      
				          )
				        ),
				        array(
				          'Text',
				          'width',         
				           array(
				             'label' => 'Width this item (px).',
				            'value' => 200, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		array(
				'title' => 'Ending Soon Contest',
				'description' => 'Display ending soon contest at front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.ending-soon-contest',
				'defaultParams' => array('title' => 'Ending Soon Contests', ),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '6',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
				          'Text',
				          'height',         
				           array(
				             'label' => 'Heigh this item (px).',
				            'value' => 200,                      
				          )
				        ),
				        array(
				          'Text',
				          'width',         
				           array(
				             'label' => 'Width this item (px).',
				            'value' => 200, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		array(
				'title' => 'New Entries',
				'description' => 'Display new entries front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.new-entry',
				'defaultParams' => array('title' => 'New Entries', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of Entries to display',
			                'value' => '6',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),
		),
		array(
				'title' => 'Latest Winning Entries',
				'description' => 'Display last winner front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.last-winner',
				'defaultParams' => array('title' => 'Latest Winning Entries', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of entries to display',
			                'value' => '6',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),
		),
		array(
				'title' => 'Listing Contests',
				'description' => 'Display listing contest front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-contest',
				'defaultParams' => array('title' => 'Listing Contests', ),
		),

		array(
				'title' => 'Listing Search',
				'description' => 'Display listing contest front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-search',
				'defaultParams' => array('title' => '', ),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests on page',
			                'value' => '16',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			           array(
				          'Text',
				          'height',         
				           array(
				             'label' => 'Heigh this item (px).',
				            'value' => 200,                      
				          )
				        ),
				        array(
				          'Text',
				          'width',         
				           array(
				             'label' => 'Width this item (px).',
				            'value' => 200, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
				   )
		),
		
		array(
				'title' => 'Listing who voted this entry',
				'description' => 'Display listing who voted this entry.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-who-voted-this-entry',
				'defaultParams' => array('title' => 'Who voted this entry'),
		),
		
		array(
				'title' => 'Listing Entries',
				'description' => 'Display listing entries front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-entries',
				'defaultParams' => array('title' => 'Listing Entries', ),
		),
		array(
				'title' => 'Listing Compare',
				'description' => 'Display listing entries front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-compare',
				'defaultParams' => array('title' => 'Listing Compare', ),
		),
		array(
				'title' => 'Search Entries',
				'description' => 'Seach Entries on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.search-entries',
		),
		array(
				'title' => 'Winning Entries',
				'description' => 'Winning Entries on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.winning-entries',
				'defaultParams' => array('title' => 'Winning Entries', ),
				'adminForm'=> array(
			      'elements' => array(
			          array(
			              'Text',
			              'number',
			               array(
			                'label' =>  'Number of contests to display',
			                'value' => '5',
			                'required' => true,
			                'validators' => array(
			                    array('Between',true,array(1,100)),
			                ),
			               ),
			           ),
			       ),
		       ),
		),

		array(
				'title' => 'Compare Entries',
				'description' => 'Compare Entries on front end.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-compare',
		),		
		array(
				'title' => 'Contest Statistics',
				'description' => 'Displays Statistics.',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.statictis',
				'defaultParams' => array('title' => '', ),
		),
		array(
				'title' => 'Contest Menus Mini',
				'description' => 'Contest mini menu',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.contest-menu-mini',
				'defaultParams' => array('title' => '', ),
		),
		array(
				'title' => 'My Contest Statictis',
				'description' => 'My Contest Statictis',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.statictis-contest',
				'defaultParams' => array('title' => '', ),
		),		
		array(
				'title' => 'Listing members',
				'description' => 'Listing members',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.listing-member',
				'defaultParams' => array('title' => '', ),
		),		
		array(
				'title' => 'Manage Rules',
				'description' => 'Manage Rules',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.manage-rule',
				'defaultParams' => array('title' => '', ),
		),
		array(
				'title' => 'Manage Transaction',
				'description' => 'Manage Transaction',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.transaction',
				'defaultParams' => array('title' => '', ),
		),
		array(
				'title' => 'Entry of Contest',
				'description' => 'Entry of Contest',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.contest-entry',
				'defaultParams' => array('title' => '', ),
		),
		array(
				'title' => 'Contests in Profile',
				'description' => 'Contests in Profile',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-my-contests',
				'defaultParams' => array('title' => 'My Contests','titleCount' => true, ),
				'autoEdit' => true,
				'adminForm'=> array(
			      'elements' => array(			          
			           array(
				          'Text',
				          'height',         
				           array(
				             'label' => 'Heigh this item (px).',
				            'value' => 200,                      
				          )
				        ),
				        array(
				          'Text',
				          'width',         
				           array(
				             'label' => 'Width this item (px).',
				            'value' => 200, 
				          )
				        ),
				        array(
								'Select',
								'nomobile',
								array(
										'style' => 'display:none',
								)
						),
			       ),
		       ),
		),
		array(
				'title' => 'Entries in Profile',
				'description' => 'Entries in Profile',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-my-entries',
				'defaultParams' => array('title' => 'My Entries','titleCount' => true, ),
				'autoEdit' => true,
				'adminForm' => array(
						'elements' => array(
								array(
										'Select',
										'typeyncontest',
										array(
												'RegisterInArrayValidator' => false,
												'decorators' => array(array('ViewScript', array(
														'viewScript' => 'application/modules/Yncontest/views/scripts/_numberOfType.tpl',
														'class' => 'form element')))
										)
								),																					
								
								array(
										'Text',
										'heightadvalbum',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthadvalbum',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),
								array(
										'Text',
										'heightynvideo',
										array(
												'label' => 'The height (px) of each item',
												'value' => 160,
										)
								),
								array(
										'Text',
										'widthynvideo',
										array(
												'label' => 'The width (px) of each item',
												'value' => 155,
										)
								),								
								array(
										'Text',
										'heightynblog',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthynblog',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
								array(
										'Text',
										'heightmp3music',
										array(
												'label' => 'The height (px) of each item',
												'value' => 90,
										)
								),
								array(
										'Text',
										'widthmp3music',
										array(
												'label' => 'The width (px) of each item',
												'value' => 250,
										)
								),
				
						)
				),
		),
		array(
				'title' => 'Winning Entries in Profile',
				'description' => 'Winning Entries in Profile',
				'category' => 'Contest',
				'type' => 'widget',
				'name' => 'yncontest.profile-my-winning-entries',
				'defaultParams' => array('title' => 'My Winning Entries','titleCount' => true, ),
		),
);
