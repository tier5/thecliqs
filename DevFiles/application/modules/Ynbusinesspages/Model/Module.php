<?php
class Ynbusinesspages_Model_Module extends Core_Model_Item_Abstract {
    protected $_searchTriggers = false;
    
    public function getOptions($business_id) {
        $options = array();
        $business = Engine_Api::_()->getItem('ynbusinesspages_business', $business_id);
        if (!$business) return array();
        $view = Zend_Registry::get('Zend_View');
        switch ($this->item_type) {
            case 'user':
                $option = array(
                    'title' => $view -> translate('View members'),
                    'url' => $view->url(array('controller'=>'member', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option);
                break;
            
            case 'ynbusinesspages_album':
                if ($business->isAllowed('album_create')) {
                    $option = array(
                        'title' => $view -> translate('Upload Photos'),
                        'url' => $view->url(array('controller'=>'photo', 'action'=>'upload', 'business_id'=> $business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynbusinesspages_extended', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Photos'),
                    'url' => $view->url(array('controller'=>'photo', 'action'=>'list', 'business_id'=> $business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'event':
                if ($business->isAllowed('event_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Event'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'event_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Events'),
                    'url' => $view->url(array('controller'=>'event', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'video':
                if ($business->isAllowed('video_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Video'),
                        'url' => $view->url(array('action'=>'create', 'parent_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'video_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Videos'),
                    'url' => $view->url(array('controller'=>'video', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
           case 'poll':
                if ($business->isAllowed('poll_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Poll'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'poll_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Polls'),
                    'url' => $view->url(array('controller'=>'poll', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'music_playlist':
                if ($business->isAllowed('music_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Music'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'music_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Music'),
                    'url' => $view->url(array('controller'=>'music', 'type' => 'music', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
            
            case 'mp3music_album':
                if ($business->isAllowed('music_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Mp3 Music'),
                        'url' => $view->url(array('business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'mp3music_create_playlist', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Mp3 Music'),
                    'url' => $view->url(array('controller'=>'music', 'type' => 'mp3music', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'ynbusinesspages_topic':
                if ($business->isAllowed('discussion_create')) {                
                    $option = array(
                        'title' => $view -> translate('Add Discussion'),
                        'url' => $view->url(array('action'=>'create', 'controller' => 'topic', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Discussions'),
                    'url' => $view->url(array('controller'=>'topic', 'action'=>'index', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'ynfilesharing_folder':
                if ($business->isAllowed('file_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Folder'),
                        'url' => $view->url(array('controller' => 'folder', 'action'=>'create', 'business_id'=>$business_id, 'parent_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynfilesharing_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Folders'),
                    'url' => $view->url(array('controller'=>'file', 'action'=>'list', 'subject'=>$business->getGuid()), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'ynwiki_page':
                if ($business->isAllowed('wiki_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Wiki'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynwiki_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Wikis'),
                    'url' => $view->url(array('controller'=>'wiki', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;  
                
            case 'classified':
                if ($business->isAllowed('classified_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Classified'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'classified_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Classified'),
                    'url' => $view->url(array('controller'=>'wiki', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'groupbuy_deal':
                if ($business->isAllowed('deal_create')) {
                    $option = array(
                        'title' => $view -> translate('Add GroupBuy'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'groupbuy_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View GroupBuy'),
                    'url' => $view->url(array('controller'=>'groupbuy', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'yncontest_contest':
                if ($business->isAllowed('contest_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Contest'),
                        'url' => $view->url(array('action'=>'create-contest', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'yncontest_mycontest', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Contest'),
                    'url' => $view->url(array('controller'=>'contest', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'blog':
                if ($business->isAllowed('blog_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Blog'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'blog_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Blogs'),
                    'url' => $view->url(array('controller'=>'blog', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'ynlistings_listing':
                if ($business->isAllowed('listing_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Listing'),
                        'url' => $view->url(array('action'=>'create', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynlistings_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Listings'),
                    'url' => $view->url(array('controller'=>'listings', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
                
            case 'ynjobposting_job':
                if ($business->isAllowed('job_import')) {
                    $option = array(
                        'title' => $view -> translate('Get Jobs'),
                        'url' => $view->url(array('controller'=>'job','action'=>'import', 'business_id'=>$business_id, 'reload'=>false), 'ynbusinesspages_extended', true),
                        'class' => 'smoothbox'
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Jobs'),
                    'url' => $view->url(array('controller'=>'job', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
				
			case 'ynmusic_song':
                if ($business->isAllowed('music_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Social Music'),
                        'url' => $view->url(array('action'=>'upload', 'business_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynmusic_song', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Albums'),
                    'url' => $view->url(array('controller'=>'social-music', 'action'=>'list', 'business_id'=>$business_id, 'type' => 'album'), 'ynbusinesspages_extended', true)
                );
				$option2 = array(
                    'title' => $view -> translate('View Songs'),
                    'url' => $view->url(array('controller'=>'social-music', 'action'=>'list', 'business_id'=>$business_id, 'type' => 'song'), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option2);
                break;                 
			case 'ynultimatevideo_video':
                if ($business->isAllowed('video_create')) {
                    $option = array(
                        'title' => $view -> translate('Add Video'),
                        'url' => $view->url(array('action'=>'create', 'subject_id'=>$business_id, 'parent_type'=>'ynbusinesspages_business'), 'ynultimatevideo_general', true)
                    );
                    array_push($options, $option);
                }
                $option1 = array(
                    'title' => $view -> translate('View Videos'),
                    'url' => $view->url(array('controller'=>'ultimate-video', 'action'=>'list', 'business_id'=>$business_id), 'ynbusinesspages_extended', true)
                );
                array_push($options, $option1);
                break;
        }
        return $options;
    }
}