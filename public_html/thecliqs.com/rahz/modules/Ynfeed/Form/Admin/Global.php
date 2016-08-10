<?php
class Ynfeed_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    // Form information
    $this->setTitle('Global Settings')
         ->setDescription('These settings affect all members in your community.');
	$settings = Engine_Api::_()->getDbTable('settings', 'core');
		 
	$this->addElement('Radio', 'ynfeed_autoloadnew', array(
      'label' => 'Auto Loading New Feeds',
      'description' => 'If set to No, user have to click on a link at the top of page Advanced Activity Feeds to load new feeds. If set to Yes, system auto load new feeds in requency.',
      'value' => $settings->getSetting('ynfeed.autoloadnew', 1),
      'multiOptions' => array(
        1 => 'Yes, auto loading new feeds.',
        0 => 'No, user have to load new feeds manually.'
      )
    ));
	
	// Max Send Settings
	$this->addElement('Text', 'ynfeed_liveupdatevalue', array(
	 		'label' => 'Frequency for Auto Loading New Feeds',
			'value' => $settings->getSetting('ynfeed.liveupdatevalue', 2),
			'validators'  => array(
				array('Int', true),
				new Engine_Validate_AtLeast(0),
		  ),
	));
	
	// Period Send Settings
	$this->addElement('Select', 'ynfeed_liveupdateperiod', array(
			'multiOptions' => array(
					'm' => 'Minutes',
					'h' => 'Hours',
			),
			'value' => $settings->getSetting('ynfeed.liveupdateperiod', 'm'),
	));
	
	
	$this->addElement('Radio', 'ynfeed_autoloadfeed', array(
      'label' => 'Auto Loading Activity Feed When Scrolling Down',
      'description' => 'If set to No, user have to click on a link at the bottom of page Activity Feeds to load more feeds. If set to Yes, system auto load more feeds when scrolling down.',
      'value' => $settings->getSetting('ynfeed.autoloadfeed', 1),
      'multiOptions' => array(
        1 => 'Yes, auto loading more feeds when scrolling down.',
        0 => 'No, user have to load more feeds manually.'
      )
    ));
	
	 $this->addElement('Text', 'ynfeed_length', array(
      	'label' => 'Maximum Times for Auto Loading When Scrolling Down',
      	'value' => $settings->getSetting('ynfeed.length', 7),
      	'allowEmpty' => false,
      	'validators' => array(
        	array('Int', true),
        	array('Between', true, array(1, 100, true)),
      	),
    ));
	
    // Advanced Comment / Replies Settings
    $row = Engine_Api::_()->ynfeed()->checkEnabledAdvancedComment();
    if($row) 
    {
        $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
        $URL = $view->url(array('module' => 'yncomment', 'controller' => 'settings', 'action' => 'activity-settings' ), 'admin_default', true);
        $description = sprintf(Zend_Registry::get('Zend_Translate')->_('Please %1svisit here%2s to configure the ‘Advanced Comments / Replies’ setting for activity feeds.'),
        "<a href='" . $URL ."' target='_blank'>", "</a>");
        $this->addElement('Dummy', 'ynfeed_yncomment_setting', array(
            'label' => 'Advanced Comment / Replies Settings',
            'description' => $description,
        ));  
        $this->ynfeed_yncomment_setting ->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
    }
	$this->addElement('Dummy', 'filter_settings', array(
        'label' => 'Feeds Filtering',
    ));
	
	$this->addElement('Select', 'ynfeed_defaultvisible', array(
        'label' => "Default Visible Items",
        'description' => 'Select the number of items that should be visible by default for lists based filtering on member home activity feeds. (You can choose the content types that are important for your website to be visible by default. The items beyond this count will appear in a "More" dropdown. To choose the sequence of items, visit the Content Lists tab.)',
        'multiOptions' => array(
            "0" => "0",
            "1" => "1",
            "2" => "2",
            "3" => "3",
            "4" => "4",
            "5" => "5",
            "6" => "6",
            "7" => "7",
            "8" => "8",
            "9" => "9"
        ),
        'value' => $settings->getSetting('ynfeed.defaultvisible', 7),
    ));
	
	$this->addElement('Radio', 'ynfeed_customlist_filtering', array(
        'label' => 'Custom Lists Filtering',
        'description' => 'Enable Custom Lists for filtering of activity feeds on member home page. (If enabled, users will be able to create their custom lists from various content types & friends, to filter activity feeds on them. This would allow users to easily view updates from entities that are important to them and which they are interested in. Users could create different lists containing different entities according to their choice and interests. To administer content types available for custom lists, visit the Custom Lists tab.)',
        'multiOptions' => array(
            1 => 'Yes',
            0 => 'No'
        ),
        'value' => $settings->getSetting('ynfeed.customlist.filtering', 1),
    ));
	
	if ($settings->getSetting('user.friends.lists')) 
	{
      $this->addElement('Radio', 'ynfeed_friendlist_filtering', array(
          'label' => 'Friend Lists Filtering',
          'description' => 'Enable users to filter activity feeds on member home page over Friend Lists. (Friend Lists are lists in which users organize their friends from the Friends section of their profiles. Users can use this to see updates of their close friends, colleagues, family, class-mates, etc.)',
          'multiOptions' => array(
              '1' => 'Yes',
              '0' => 'No'
          ),
          'value' => $settings->getSetting('ynfeed.friendlist.filtering', 1),
      ));
    }
	else 
    {
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $link = $view->url(array("module" => "user", "controller" => "settings", "action" => "friends"), "admin_default", true);
      $this->addElement('Dummy', 'ynfeed_friendlist_filtering_dummy', array(
          'description' => sprintf("<div class='tip'> <span>You can also enable users to filter activity feeds based on their Friend Lists like family, close friends, co-workers, etc. However, you are not able to see the option for activating that because you have disabled Friend Lists from Friendship Settings. To enable it, %s.</span></div>", "<a href='" . $link . "'>go here</a>"),
      ));
      $this->getElement('ynfeed_friendlist_filtering_dummy')->getDecorator('Description')->setOptions(array('placement', 'APPEND', 'escape' => false));
    }

    $content = $settings->getSetting('activity.content', 'everyone');
    $tip = null;
    if ($content == 'friends') 
    {
      $view = Zend_Registry::isRegistered('Zend_View') ? Zend_Registry::get('Zend_View') : null;
      $link = $view->url(array("module" => "activity", "controller" => "settings"), "admin_default", true);
      $tip = sprintf("<div class='tip'> <span>Note: In Activity Feed Settings, you have chosen ‘My Friends’ for the ‘Feed Content’ field. Please %s, either to choose ‘All Members’ or ‘My Friends & Networks’ for this field.</span></div>", "<a href='" . $link . "'>click here</a>");
    }
    $this->addElement('Radio', 'ynfeed_networklist_filtering', array(
        'label' => 'Networks Based Filtering',
        'description' => 'Enable users to filter activity feeds on member home page over Networks. (Users can use this to see updates from selected Networks.)' . $tip,
        'multiOptions' => array(
            2 => 'Yes, enable users to filter feeds from all networks.',
            1 => 'Yes, enable users to filter feeds only based on networks joined by them.',
            0 => 'No'
        ),
        'value' => $settings->getSetting('ynfeed.networklist.filtering', 0),
    ));
    $this->getElement('ynfeed_networklist_filtering')->getDecorator('Description')->setOptions(array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'escape' => false));
   
	
    // Submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}