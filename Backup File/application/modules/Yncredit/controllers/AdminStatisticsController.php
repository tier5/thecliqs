<?php
class Yncredit_AdminStatisticsController extends Core_Controller_Action_Admin
{
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
	public function indexAction()
	{
		$this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
			->getNavigation('yncredit_admin_main', array(), 'yncredit_admin_main_statistics');

		$this->view->form = $form = new Yncredit_Form_Admin_SearchStatistics();
		
		$typeTbl = Engine_Api::_()->getDbTable("types", "yncredit");
	    $typeTblName = $typeTbl->info("name");
		$moduleTbl = Engine_Api::_()->getDbTable("modules", "core");
	    $modules = $moduleTbl->select()->where("enabled = ?", 1)->query()->fetchAll();
	    $enabledModules = array();
	    foreach ($modules as $key => $module)
	    {
	    	$enabledModules[] = $module['name']; 
	    }
	    
	    $select = $typeTbl->select() ->distinct() 
			-> from ($typeTblName, "module")
	    	-> where("$typeTblName.module in (?)", $enabledModules)
	    	-> order("$typeTblName.module ASC");
		$modules = $typeTbl -> fetchAll($select);
		$moduleOptions = array('' => 'All Modules');
		$translate = Zend_Registry::get('Zend_Translate');
		foreach($modules as $module)
		{
			$moduleOptions[$module -> module] = ucfirst($translate->translate('YNCREDIT_MODULE_'. strtoupper($module->module)));
		}
		$form -> modu -> setMultiOptions($moduleOptions);
	}
	public function chartDataAction()
  	{
	    // Disable layout and viewrenderer
	    $this->_helper->layout->disableLayout();
	    $this->_helper->viewRenderer->setNoRender(true);
	
	    // Get params
	    $module   = $this->_getParam('modu', '');
	    $start  = $this->_getParam('start');
	    $offset = $this->_getParam('offset', 0);
	    $group   = $this->_getParam('group', 'earn');
	    $mode   = $this->_getParam('mode');
	    $chunk  = $this->_getParam('chunk');
	    $period = $this->_getParam('period');
	    $periodCount = $this->_getParam('periodCount', 1);
	    //$end = $this->_getParam('end');
	
	    // Validate chunk/period
	    if( !$chunk || !in_array($chunk, $this->_periods) ) {
	      $chunk = Zend_Date::DAY;
	    }
	    if( !$period || !in_array($period, $this->_periods) ) {
	      $period = Zend_Date::MONTH;
	    }
	    if( array_search($chunk, $this->_periods) >= array_search($period, $this->_periods) ) {
	      die('whoops');
	      return;
	    }
	
	    // Validate start
	    if( $start && !is_numeric($start) ) {
	      $start = strtotime($start);
	    }
	    if( !$start ) {
	      $start = time();
	    }
	
	    // Fixes issues with month view
	    Zend_Date::setOptions(array(
	      'extend_month' => true,
	    ));
	
	    // Get timezone
	    $timezone = Engine_Api::_()->getApi('settings', 'core')
	        ->getSetting('core_locale_timezone', 'GMT');
	    $viewer = Engine_Api::_()->user()->getViewer();
	    if( $viewer && $viewer->getIdentity() && !empty($viewer->timezone) ) {
	      $timezone = $viewer->timezone;
	    }
	
	    // Make start fit to period?
	    $startObject = new Zend_Date($start);
	    $startObject->setTimezone($timezone);
	    
	    $partMaps = $this->_periodMap[$period];
	    foreach( $partMaps as $partType => $partValue ) {
	      $startObject->set($partValue, $partType);
	    }
	
	    // Do offset
	    if( $offset != 0 ) {
	      $startObject->add($offset, $period);
	    }
	    
	    // Get end time
	    $endObject = new Zend_Date($startObject->getTimestamp());
	    $endObject->setTimezone($timezone);
	    $endObject->add($periodCount, $period);
	    $endObject->sub(1, Zend_Date::SECOND); // Subtract one second
	
	    // Get data
	    $logsTable = Engine_Api::_()->getDbtable('logs', 'yncredit');
		$logName = $logsTable -> info('name');
		
		$typeTable = Engine_Api::_() -> getDbTable('types', 'yncredit');
		$typeName = $typeTable -> info('name');
		
		$select = $logsTable -> select() 
			-> from($logName, "$logName.*") 
			-> joinLeft($typeName, "$logName.type_id = $typeName.type_id", "");
		
		if($module)
		{
			$select ->where('module = ?', $module);
		}
	    $select -> where('`group` = ?', $group)
		 // -> where("$logName.credit <> 0")
	      -> where('creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
	      -> where('creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
	      -> order('creation_date ASC');
	    $rawData = $logsTable->fetchAll($select);
	    
	    // Now create data structure
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
	
	      // Get everything that matches
	      $currentPeriodCount = 0;
	      foreach( $rawData as $rawDatum ) {
	        $rawDatumDate = strtotime($rawDatum->creation_date);
	        if( $rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp ) {
	          $currentPeriodCount += abs($rawDatum->credit);
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
	    } while( $currentObject->getTimestamp() < $endObject->getTimestamp() );
	
	    // Reprocess label
	    $labelStrings = array();
	    $labelDate = new Zend_Date();
	    foreach( $data as $key => $value ) {
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
	    if( count($data) > 10 ) {
	      $xlabelsteps = ceil(count($data) / 10);
	    }
	
	    // Remove some grid lines if there are too many
	    $xsteps = 1;
	    if( count($data) > 100 ) {
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
	    $locale = Zend_Registry::get('Locale');
	    $translate = Zend_Registry::get('Zend_Translate');
	    $titleStr = $translate->_('YNCREDIT_GROUP_TYPE_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $group), '_')));
	    $title = new OFC_Elements_Title( $titleStr . ': '. $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject) );
	    $title->set_style( "{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}" );
	
	    // Make full chart
	    $chart = new OFC_Chart();
	    $chart->set_bg_colour('#ffffff');
	
	    $chart->set_x_axis($labels);
	    $chart->add_y_axis($yaxis);
	    $chart->add_element($graph);
	    $chart->set_title( $title );
	    
	    // Send
	    $this->getResponse()->setBody( $chart->toPrettyString() );
  	}

}