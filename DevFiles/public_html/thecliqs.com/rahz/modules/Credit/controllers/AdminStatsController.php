<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminStatsController.php 31.01.12 15:05 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_AdminStatsController extends Core_Controller_Action_Admin
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

  public function init()
  {
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('credit_admin_main', array(), 'credit_admin_main_stats');
  }

  public function indexAction()
  {
    // Get types
    $statsTable = Engine_Api::_()->getDbtable('actionTypes', 'credit');
    $select = new Zend_Db_Select($statsTable->getAdapter());
    $select
      ->from($statsTable->info('name'), 'group_type')
      ->distinct(true)
      ;

    $data = $select->query()->fetchAll();
    $types = array();
    foreach( $data as $datum ) {
      $type = $datum['group_type'];
      $fancyType = '_CREDIT_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_'));
      $types[$type] = $fancyType;
    }

    $this->view->filterForm = $filterForm = new Credit_Form_Admin_StatsFilter();
    $filterForm->type->setMultiOptions($types);
  }

  public function chartDataAction()
  {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $type   = $this->_getParam('type');
    $start  = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
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
    /**
     * @var $statsTable Credit_Model_DbTable_Logs
     * @var $actionsTbl Credit_Model_DbTable_ActionTypes
     */

    $statsTable = Engine_Api::_()->getDbtable('logs', 'credit');
    $actionsTbl = Engine_Api::_()->getDbtable('actionTypes', 'credit');
    $statsSelect = $statsTable->select()
      ->setIntegrityCheck(false)
      ->from(array('c' => $statsTable->info('name')), array('c.*', 'credit' => new Zend_Db_Expr('IF (c.credit > 0, c.credit, (-1)*c.credit)')))
      ->joinLeft(array('a' => $actionsTbl->info('name')), 'c.action_id = a.action_id', array())
      ->where('c.creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
      ->where('c.creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
      ->where('a.group_type = ?', $type)
      ->order('c.creation_date ASC')
    ;

    $rawData = $statsTable->fetchAll($statsSelect);

    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject = clone $startObject;
    $data = array();
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
          $currentPeriodCount += $rawDatum->credit;
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
    $titleStr = $translate->_('_CREDIT_ADMIN_STATS_' . strtoupper(trim(preg_replace('/[^a-zA-Z0-9]+/', '_', $type), '_')));
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
