<?php
class Ynbusinesspages_Form_Business_ChartStatistics extends Engine_Form {
	protected $_business;
	
	public function getBusiness()
	{
		return $this ->_business;
	}
	
	public function setBusiness($business)
	{
		$this ->_business = $business;
	}
	
	public function init(){
		$this
      ->setAttrib('class', 'global_form_box')
	  ->setAttrib('id', 'statistic_form')
      ->addDecorator('FormElements')
      ->addDecorator('Form')
      ->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
      ;  
	  
		// Init mode
	    $this->addElement('Select', 'mode', array(
	      'label' => 'See',
	      'multiOptions' => array(
	        'normal' => 'All',
	        'cumulative' => 'Cumulative',
	        'delta' => 'Change in',
	      ),
	      'value' => 'normal',
	      'class' => 'filter_elem',
	    ));
		
		$arr_types = array(
			'reviews' => 'Reviews',
			'members' => 'Members',
	      	'followers' => 'Followers',
	      	'comments' => 'Comments',
	      	'shares' => 'Shares',
	      	'events' => 'Events',
	      	'photos' => 'Photos',
	      	'videos' => 'Videos',
	      	'files' => 'File Sharing',
	      	'mp3musics' => 'Mp3Music Albums',
	      	'musics' => 'Music Albums',
	      	'blogs' => 'Blogs',
	      	'polls' => 'Poll',
	       	'discussions' => 'Discussions',
	       	'wikis' => 'Wikis',
	       	'classified' => 'Classified',
	       	'groupbuy' => 'Groupbuy',
	       	'contests' => 'Contests',
	       	'listings' => 'Listings',
	       	'jobs' => 'Jobs',
	       	'ynmusic_songs' => 'Social Music Songs',
	       	'ynmusic_albums' => 'Social Music Albums',
	       	'ynultimatevideo_videos' => 'Ultimate Videos',
		);
		
		 $business = $this ->_business;
		   
		 if (!$business -> getPackage() -> checkAvailableModule('ynwiki_page') || !Engine_Api::_() -> hasModuleBootstrap('ynwiki'))
		 {
		 	unset($arr_types['wikis']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('event') || !Engine_Api::_() -> hasModuleBootstrap('event'))
		 {
		 	unset($arr_types['events']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('ynbusinesspages_album')) 
		 {
		 	unset($arr_types['photos']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('video') || !Engine_Api::_()->hasItemType('video'))
		 {
		 	unset($arr_types['videos']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('ynfilesharing_folder') || !Engine_Api::_() -> hasModuleBootstrap('ynfilesharing')) 
		 {
		 	unset($arr_types['files']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('mp3music_album') || !Engine_Api::_() -> hasModuleBootstrap('mp3music'))
		 {
		 	unset($arr_types['mp3musics']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('music_playlist') || !Engine_Api::_() -> hasModuleBootstrap('music'))
		 {
		 	unset($arr_types['musics']);
		 }
		 if (!$business -> getPackage() -> checkAvailableModule('blog') || !Engine_Api::_() -> hasModuleBootstrap('blog'))
		 {
		 	unset($arr_types['blogs']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('poll') || !Engine_Api::_()->hasModuleBootstrap('poll'))
		 {
		 	unset($arr_types['polls']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('classified') || !Engine_Api::_() -> hasModuleBootstrap('classified'))
		 {
		 	unset($arr_types['classified']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('groupbuy_deal') || !Engine_Api::_() -> hasModuleBootstrap('groupbuy'))
		 {
		 	unset($arr_types['groupbuy']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('yncontest_contest') || !Engine_Api::_() -> hasModuleBootstrap('yncontest'))
		 {
		 	unset($arr_types['contests']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('ynlistings_listing') || !Engine_Api::_()->hasModuleBootstrap('ynlistings'))
		 {
		 	unset($arr_types['listings']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('ynjobposting_job') || !Engine_Api::_()->hasModuleBootstrap('ynjobposting'))
		 {
		 	unset($arr_types['jobs']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('ynmusic_song') || !Engine_Api::_()->hasModuleBootstrap('ynmusic'))
		 {
		 	unset($arr_types['ynmusic_songs']);
			unset($arr_types['ynmusic_albums']);
		 }
		 if(!$business -> getPackage() -> checkAvailableModule('ynultimatevideo_video') || !Engine_Api::_()->hasModuleBootstrap('ynultimatevideo'))
		 {
		 	unset($arr_types['ynultimatevideo_videos']);
		 }
	    // Init type
	    $this->addElement('Select', 'type', array(
	      'label' => 'Type',
	      'multiOptions' => $arr_types,
	      'value' => 'earn',
	      'class' => 'filter_elem',
	    ));
		
	    // Init period
	    $this->addElement('Select', 'period', array(
	      'label' => 'Duration',
	      'multiOptions' => array(
	        Zend_Date::WEEK => 'This week',
	        Zend_Date::MONTH => 'This month',
	        Zend_Date::YEAR => 'This year',
	      ),
	      'value' => 'week',
	      'class' => 'filter_elem',
	    ));
	
	    // Init chunk
	    $this->addElement('Select', 'chunk', array(
	      'label' => 'Time Summary',
	      'multiOptions' => array(
	        Zend_Date::DAY => 'By Day',
	        Zend_Date::WEEK => 'By Week',
	        Zend_Date::MONTH => 'By Month',
	        Zend_Date::YEAR => 'By Year',
	      ),
	      'value' => 'day',
	      'class' => 'filter_elem',
	    ));
	
	    // Init submit
	    $this->addElement('Button', 'submit', array(
	      'label' => 'Filter',
	      'type' => 'submit',
	      'onclick' => 'return processStatisticsFilter($(this).getParent("form"))',
	      'class' => 'filter_elem',
	    ));
	}
}
?>