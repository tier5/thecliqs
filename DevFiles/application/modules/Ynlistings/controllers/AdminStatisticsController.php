<?php
class Ynlistings_AdminStatisticsController extends Core_Controller_Action_Admin
{
	protected $_periods = array(Zend_Date::DAY, //dd
    Zend_Date::WEEK, //ww
    Zend_Date::MONTH, //MM
    Zend_Date::YEAR, //y
    );
    protected $_allPeriods = array(Zend_Date::SECOND, Zend_Date::MINUTE, Zend_Date::HOUR, Zend_Date::DAY, Zend_Date::WEEK, Zend_Date::MONTH, Zend_Date::YEAR, );
    protected $_periodMap = array(Zend_Date::DAY => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, ), Zend_Date::WEEK => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::WEEKDAY_8601 => 1, ), Zend_Date::MONTH => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, ), Zend_Date::YEAR => array(Zend_Date::SECOND => 0, Zend_Date::MINUTE => 0, Zend_Date::HOUR => 0, Zend_Date::DAY => 1, Zend_Date::MONTH => 1, ), );
	
  public function indexAction()
  {
  	
    $this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('ynlistings_admin_main', array(), 'ynlistings_admin_main_statistics');

	$this -> view -> formChartStatistic = $formChartStatistic = new Ynlistings_Form_Admin_Statistics_ChartStatistics();
    $tableListings = Engine_Api::_()->getItemTable('ynlistings_listing');
	$tablePhotos = Engine_Api::_()->getItemTable('ynlistings_photo');
	$tableMappings = Engine_Api::_()->getItemTable('ynlistings_mapping');
	$tableTopics = Engine_Api::_()->getItemTable('ynlistings_topic');
	$tablePosts = Engine_Api::_()->getItemTable('ynlistings_post');
	$this -> view -> total_listings = $total_listings = $tableListings -> getTotalListings();
	$this -> view -> published_listings = $published_listings = $tableListings -> getPublishedListings();
	$this -> view -> draft_listings = $draft_listings = $tableListings -> getDraftListings();
	$this -> view -> close_listings = $close_listings = $tableListings -> getClosedListings();
	$this -> view -> open_listings = $open_listings = $tableListings -> getOpenListings();
	$this -> view -> approved_listings = $approved_listings = $tableListings -> getApprovedListings();
	$this -> view -> disapproved_listings = $disapproved_listings = $tableListings -> getDisApprovedListings();
	$this -> view -> feature_listings = $feature_listings = $tableListings -> getFeaturedListings();
  	$this -> view -> photo_count = $photo_count = $tablePhotos -> getPhotoCount();
	$this -> view -> video_count = $video_count = $tableMappings -> getVideoCount();
	$this -> view -> topic_count = $topic_count = $tableTopics -> getTopicsCount();
	$this -> view -> post_count = $post_count = $tablePosts -> getPostsCount();
 }

  public function chartDataAction() 
  {
        // Disable layout and viewrenderer
        $this -> _helper -> layout -> disableLayout();
        $this -> _helper -> viewRenderer -> setNoRender(true);

        // Get params
        $start = $this -> _getParam('start');
        $offset = $this -> _getParam('offset', 0);
        $type = $this -> _getParam('type', 'all');
        $mode = $this -> _getParam('mode');
        $chunk = $this -> _getParam('chunk');
        $period = $this -> _getParam('period');
        $periodCount = $this -> _getParam('periodCount', 1);
        

        // Validate chunk/period
        if (!$chunk || !in_array($chunk, $this -> _periods)) {
            $chunk = Zend_Date::DAY;
        }
        if (!$period || !in_array($period, $this -> _periods)) {
            $period = Zend_Date::MONTH;
        }
        if (array_search($chunk, $this -> _periods) >= array_search($period, $this -> _periods)) {
            die('whoops');
            return;
        }

        // Validate start
        if ($start && !is_numeric($start)) {
            $start = strtotime($start);
        }
        if (!$start) {
            $start = time();
        }

        // Fixes issues with month view
        Zend_Date::setOptions(array('extend_month' => true, ));

        // Get timezone
        $timezone = Engine_Api::_() -> getApi('settings', 'core') -> getSetting('core_locale_timezone', 'GMT');
        $viewer = Engine_Api::_() -> user() -> getViewer();
        if ($viewer && $viewer -> getIdentity() && !empty($viewer -> timezone)) {
            $timezone = $viewer -> timezone;
        }

        // Make start fit to period?
        $startObject = new Zend_Date($start);
        $startObject -> setTimezone($timezone);

        $partMaps = $this -> _periodMap[$period];
        foreach ($partMaps as $partType => $partValue) {
            $startObject -> set($partValue, $partType);
        }

        // Do offset
        if ($offset != 0) {
            $startObject -> add($offset, $period);
        }

        // Get end time
        $endObject = new Zend_Date($startObject -> getTimestamp());
        $endObject -> setTimezone($timezone);
        $endObject -> add($periodCount, $period);
        $endObject -> sub(1, Zend_Date::SECOND);
		//Get table according to type
        if ($type == "listings")
        {
	        $staTable = Engine_Api::_() -> getDbtable('listings', 'ynlistings');
		}	
		elseif ($type == "reviews_listings")
        {
	        $staTable = Engine_Api::_() -> getDbtable('reviews', 'ynlistings');
		}
		elseif ($type == "photos")
        {
	        $staTable = Engine_Api::_() -> getDbtable('photos', 'ynlistings');
		}
		elseif ($type == "videos")
        {
	        $staTable = Engine_Api::_() -> getDbtable('mappings', 'ynlistings');
		}
		elseif ($type == "discussions")
        {
	        $staTable = Engine_Api::_() -> getDbtable('topics', 'ynlistings');
		}
		elseif ($type == "discussions_posts")
        {
	        $staTable = Engine_Api::_() -> getDbtable('posts', 'ynlistings');
		}
		$staName = $staTable -> info('name');
        // Get data
        $select = $staTable -> select();
		if($type == "videos")
		{
			$select -> where("type = 'video' OR type ='profile_video'");
		}
        $select -> where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject -> getTimestamp())) -> where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject -> getTimestamp())) -> order('creation_date ASC');
		$rawData = $staTable -> fetchAll($select);
        // Now create data structure
        $currentObject = clone $startObject;
        $nextObject = clone $startObject;
        $data = array();
        $dataLabels = array();
        $cumulative = 0;
        $previous = 0;

        do {
            $nextObject -> add(1, $chunk);
            $currentObjectTimestamp = $currentObject -> getTimestamp();
            $nextObjectTimestamp = $nextObject -> getTimestamp();
            $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;

            // Get everything that matches
            $currentPeriodCount = 0;
            foreach ($rawData as $rawDatum) {
                $rawDatumDate = strtotime($rawDatum -> creation_date);
                if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
                    $currentPeriodCount += 1;
                }
            }
            // Now do stuff with it
            switch( $mode ) {
                default :
                case 'normal' :
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount;
                    break;
                case 'cumulative' :
                    $cumulative += $currentPeriodCount;
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $cumulative;
                    break;
                case 'delta' :
                    $data[$this -> view -> locale() -> toDate($currentObjectTimestamp)] = $currentPeriodCount - $previous;
                    $previous = $currentPeriodCount;
                    break;
            }
            $currentObject -> add(1, $chunk);
        } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );

        // Remove some grid lines if there are too many
        $xsteps = 1;
        if (count($data) > 100) {
            $xsteps = ceil(count($data) / 100);
        }
        $title = $this -> view -> locale() -> toDate($startObject) . ' to ' . $this -> view -> locale() -> toDate($endObject);
        echo Zend_Json::encode(array('json' => $data, 'title' => $title));
        return true;
    }
}