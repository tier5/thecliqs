<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: StatisticsController.php 2010-08-31 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Page
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Page_StatisticsController extends Core_Controller_Action_Standard
{
	protected $_navigation;
	protected $_api;
	
	protected $_periods = array(
    Zend_Date::DAY, //dd
    Zend_Date::WEEK, //ww
    Zend_Date::MONTH, //MM
    Zend_Date::YEAR, //y
  );

  protected $_allPeriods = array(
    Zend_Date::SECOND,
    Zend_Date::MINUTE,
    Zend_Date::HOUR,
    Zend_Date::DAY,
    Zend_Date::WEEK,
    Zend_Date::MONTH,
    Zend_Date::YEAR,
  );

  protected $_periodMap = array(
    Zend_Date::DAY => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
    ),
    Zend_Date::WEEK => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::WEEKDAY_8601 => 1,
    ),
    Zend_Date::MONTH => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
    ),
    Zend_Date::YEAR => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR => 0,
      Zend_Date::DAY => 1,
      Zend_Date::MONTH => 1,
    ),
  );
	
	public function init()
	{
		$page_id = (int)$this->_getParam('page_id');
		$this->view->page = $page = Engine_Api::_()->getItem('page', $page_id);
		
		if ($page == null){
			$this->_redirectCustom(array('route' => 'page_browse'));			
			return ;
		}
		
		if( !$this->_helper->requireUser()->isValid() || !$page->isAdmin() ) {
			$this->_redirectCustom(array('route' => 'page_browse'));
  		return ;
  	}
  	
  	$this->_api = Engine_Api::_()->getApi('statistics', 'page');
    $this->view->action = $this->_getParam('action');
    $this->view->controller = 'statistics';
	}

	public function viewsAction()
	{
		$page = $this->view->page;
		
		$this->view->type = 'views';
		$this->view->filterForm = $filterForm = new Page_Form_Statistics_Filter();
		$this->view->filterForm->getElement('type')->setValue('views'); 
	}

	public function visitorsAction()
	{
		$page = $this->view->page;
		
		$this->view->type = 'unique';
		$this->view->filterForm = $filterForm = new Page_Form_Statistics_Filter();
		$this->view->filterForm->getElement('type')->setValue('unique');
	}
	
	public function chartDataAction()
	{
		$page = $this->view->page;
		
		$this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);
    
    if( !$chunk || !in_array($chunk, $this->_periods) ) {
      $chunk = Zend_Date::DAY;
    }
    
    if( !$period || !in_array($period, $this->_periods) ) {
      $period = Zend_Date::MONTH;
    }
    
    if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {
      die('whoops');
    }
    
    if( $start && !is_numeric($start) ) {
      $start = strtotime($start);
    }
    
    if( !$start ) {
      $start = time();
    }
    
    $startObject = new Zend_Date($start);
    
		$partMaps = $this->_periodMap[$period];
    foreach( $partMaps as $partType => $partValue ) {
      $startObject->set($partValue, $partType);
    }
    
		if( $offset != 0 ) {
      $startObject->add($offset, $period);
    }
    
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->add($periodCount, $period);
    
    $statsTable = Engine_Api::_()->getDbtable('views', 'page');
    $prefix = $statsTable->getTablePrefix();
    
    $statsSelect = $statsTable->select()
    	->setIntegrityCheck(false);

    if ($type == 'unique') {
      $statsSelect
        ->from($prefix.'page_views', array('date' => 'view_date', 'view_id', 'value' => 'COUNT(DISTINCT user_id, ip)'));
    } elseif ($type == 'views') {
      $statsSelect
        ->from($prefix.'page_views', array('date' => 'view_date', 'view_id', 'value' => 'COUNT(*)'));
    }

    $statsSelect
      ->where('view_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
      ->where('view_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
      ->where('page_id = ?', $page->getIdentity())
      ->group('view_date')
      ->order('view_date ASC');
    
    $rawData = $statsTable->fetchAll($statsSelect);
    
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;
    $data = array();
    $dataLabels = array();
    $cumulative = 0;
    $previous = 0;
    
    do {
      $nextObject->add(1, $chunk);
      
      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp = $nextObject->getTimestamp();

      $data[$currentObjectTimestamp] = $cumulative;

      $currentPeriodCount = 0;
      foreach( $rawData as $rawDatum ) {
        $rawDatumDate = strtotime($rawDatum->date);
        if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
          $currentPeriodCount += $rawDatum->value;
        }
      }

      // Now do stuff with it
      switch( $mode ) {
        default:
        case 'normal':
          $data[$currentObjectTimestamp] = $currentPeriodCount;
          break;
        case 'cumulative':
          $cumulative += $currentPeriodCount;
          $data[$currentObjectTimestamp] = $cumulative;
          break;
        case 'delta':
          $data[$currentObjectTimestamp] = $currentPeriodCount - $previous;
          $previous = $currentPeriodCount;
          break;
      }
      
      $currentObject->add(1, $chunk);
    } while ( $currentObject->getTimestamp() < $endObject->getTimestamp() );
    
    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
    foreach ( $data as $key => $value ) {
      $labelDate->set($key);
      $labelStrings[] = $this->view->locale()->toDate($labelDate, array('size' => 'short')); //date('D M d Y', $key);
    }

    // Let's expand them by 1.1 just for some nice spacing
    $minVal = min($data);
    $maxVal = max($data);
    $minVal = floor($minVal * ($minVal < 0 ? 1.1 : (1 / 1.1)) / 10) * 10;
    $maxVal = ceil($maxVal * ($maxVal > 0 ? 1.1 : (1 / 1.1)) / 10) * 10;

    // Remove some labels if there are too many
    $xlabelsteps = 1;
    if ( count($data) > 10 ) {
      $xlabelsteps = ceil(count($data) / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if ( count($data) > 100 ) {
      $xsteps = ceil(count($data) / 100);
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps( $xlabelsteps );
    $x_axis_labels->set_labels( $labelStrings );

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels( $x_axis_labels );
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal/*, $steps*/);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");
    
    // Make data
    $graph = new OFC_Charts_Line();
    $graph->set_values( array_values($data) );
    $graph->set_colour("#5ba1cd");

    // Make title
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_CORE_PAGE_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
    $title = new OFC_Elements_Title( $titleStr . ': '. $startObject->toString() . ' to ' . $endObject->toString() );
    $title->set_style( "{font-size: 14px;font-weight: bold; margin-bottom: 10px; color: #777777;}" );

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour('#ffffff');

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);
    $chart->add_element($graph);
    $chart->set_title($title);
    
    // Send
    $this->getResponse()->setBody( $chart->toPrettyString() );
	}
	
	public function mapAction()
	{
    /**
     * @var $page Page_Model_Page
     **/
		$page = $this->view->page;
		$format = $this->_getParam('format');
		$p = $this->_getParam('p', 1);
		
		$params = array();
		$this->view->map_items = $visitors = $page->getViewStats($params);
		$this->view->list_items = $list_items = clone $visitors;

		$list_items->setCurrentPageNumber($p);
		$list_items->setItemCountPerPage(20);
		$this->view->total_items = $page->getViewStatsCount($params);
    $this->view->map_items->setItemCountPerPage($this->view->total_items);
		
		if ($format == 'json') {
			$this->view->html = $this->view->render('_countryList.tpl');
		}
	}
}