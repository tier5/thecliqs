<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: StatisticsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_StatisticsController extends Store_Controller_Action_User
{
  protected $_navigation;

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
    Zend_Date::DAY   => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR   => 0,
    ),
    Zend_Date::WEEK  => array(
      Zend_Date::SECOND       => 0,
      Zend_Date::MINUTE       => 0,
      Zend_Date::HOUR         => 0,
      Zend_Date::WEEKDAY_8601 => 1,
    ),
    Zend_Date::MONTH => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR   => 0,
      Zend_Date::DAY    => 1,
    ),
    Zend_Date::YEAR  => array(
      Zend_Date::SECOND => 0,
      Zend_Date::MINUTE => 0,
      Zend_Date::HOUR   => 0,
      Zend_Date::DAY    => 1,
      Zend_Date::MONTH  => 1,
    ),
  );

  public function init()
  {
    /**
     * @var $page Page_Model_Page
     */
    if (null != ($page = Engine_Api::_()->getItem('page', (int)$this->_getParam('page_id', 0)))) {
      Engine_Api::_()->core()->setSubject($page);
    }

    // Set up requires
    $this->_helper->requireSubject('page')->isValid();

    $this->view->page = $page = Engine_Api::_()->core()->getSubject('page');
    $this->view->viewer = $viewer = Engine_Api::_()->user()->getViewer();

    //he@todo check admin settings
    if (
      !$page->isAllowStore() ||
      !$page->isOwner($viewer)
//    !$this->_helper->requireAuth()->setAuthParams($page, null, 'edit')->isValid() ||
    ) {
      $this->_redirectCustom($page->getHref());
    }

    /**
     * @var $api Store_Api_Page
     */
    $api                    = Engine_Api::_()->getApi('page', 'store');
    $this->view->navigation = $api->getNavigation($page, 'statistics');
  }

  public function chartAction()
  {
    $types                  = array('gross'=> '_CORE_ADMIN_STATS_GROSS_',
                                    'items'=> '_CORE_ADMIN_STATS_ITEMS_');
    $this->view->filterForm = $filterForm = new Store_Form_Options_Filter();
    $filterForm->type->setMultiOptions($types);
  }

  public function listAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this->view->page;
    $values                 = array();
    $this->view->searchForm = $searchForm = new Store_Form_Options_Search();
    if ($searchForm->isValid($this->_getAllParams())) {
      $values = $searchForm->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order'           => 'total_amount',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    /**
     * @var $table Store_Model_DbTable_Orderitems
     */
    $table  = Engine_Api::_()->getDbtable('orderitems', 'store');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('o'=> $table->info('name')), array(
      'page_id',
      'item_id',
      'item_type',
      'name',
      'quantity'         => 'SUM(qty)',
      'total_amount'     => 'SUM(total_amt)'))
      ->where('item_type = ?', 'store_product')
      ->where('page_id = ?', $page->getIdentity())
      ->group('item_id');

    $select->order((!empty($values['order']) ? $values['order'] : 'total_amount') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($values['name'])) {
      $select->where('name LIKE ?', '%' . $values['name'] . '%');
    }

    $valuesCopy             = array_filter($values);
    $this->view->formValues = $valuesCopy;

    /**
     * Make paginator
     *
     * @var $paginator Zend_Paginator
     */
    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $this->view->paginator = $paginator->setCurrentPageNumber($this->_getParam('page', 1));
  }

  public function chartDataAction()
  {
    /**
     * @var $page Page_Model_Page
     */
    $page = $this->view->page;

    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    // Get params
    $type        = $this->_getParam('type');
    $start       = $this->_getParam('start');
    $offset      = $this->_getParam('offset', 0);
    $mode        = $this->_getParam('mode');
    $chunk       = $this->_getParam('chunk');
    $period      = $this->_getParam('period');
    $periodCount = $this->_getParam('periodCount', 1);

    // Validate chunk/period
    if (!$chunk || !in_array($chunk, $this->_periods)) {
      $chunk = Zend_Date::DAY;
    }
    if (!$period || !in_array($period, $this->_periods)) {
      $period = Zend_Date::MONTH;
    }
    if (array_search($chunk, $this->_periods) >= array_search($period, $this->_periods)) {
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
    Zend_Date::setOptions(array(
      'extend_month' => true,
    ));

    // Get timezone
    $timezone = $settings->getSetting('core_locale_timezone', 'GMT');
    $viewer   = Engine_Api::_()->user()->getViewer();
    if ($viewer && $viewer->getIdentity() && !empty($viewer->timezone)) {
      $timezone = $viewer->timezone;
    }

    // Make start fit to period?
    $startObject = new Zend_Date($start);
    $startObject->setTimezone($timezone);

    $partMaps = $this->_periodMap[$period];
    foreach ($partMaps as $partType => $partValue) {
      $startObject->set($partValue, $partType);
    }

    // Do offset
    if ($offset != 0) {
      $startObject->add($offset, $period);
    }

    // Get end time
    $endObject = new Zend_Date($startObject->getTimestamp());
    $endObject->setTimezone($timezone);
    $endObject->add($periodCount, $period);
    $endObject->sub(1, Zend_Date::SECOND); // Subtract one second

    // Get data
    /**
     * @var $transTable  Store_Model_DbTable_Transactions
     * @var $statsSelect Zend_Db_Select
     */
    $transTable  = Engine_Api::_()->getDbtable('transactions', 'store');
    $statsSelect = $transTable
      ->select()
      ->setIntegrityCheck(false);
    $prefix      = $transTable->getTablePrefix();

    if ($type == 'items') {
      $statsSelect
        ->from(array('t'=> $transTable->info('name')), array('date'=> 't.timestamp'))
        ->join(array('o'=> $prefix . 'store_orderitems'), 'o.order_id=t.order_id', array('value'=> 'SUM(o.qty)'))
      ;
    } else {
      $statsSelect
        ->from(array('t'=> $transTable->info('name')), array('date' => 't.timestamp'))
        ->join(array('o'=> $prefix . 'store_orderitems'), 'o.order_id=t.order_id', array('value'=> 'SUM((o.total_amt - o.commission_amt)*o.qty)'))
      ;
    }

    $statsSelect
      ->where('t.timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
      ->where('t.timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
      ->where('o.page_id = ?', $page->getIdentity())
      ->group('t.timestamp')
      ->order('t.timestamp ASC');

    $rawData = $transTable->fetchAll($statsSelect);


    // Now create data structure
    $currentObject = clone $startObject;
    $nextObject    = clone $startObject;
    $data          = array();
    $dataLabels    = array();
    $cumulative    = 0;
    $previous      = 0;

    do {
      $nextObject->add(1, $chunk);

      $currentObjectTimestamp = $currentObject->getTimestamp();
      $nextObjectTimestamp    = $nextObject->getTimestamp();

      $data[$currentObjectTimestamp] = $cumulative;

      // Get everything that matches
      $currentPeriodCount = 0;
      foreach ($rawData as $rawDatum) {
        $rawDatumDate = strtotime($rawDatum->date);
        if ($rawDatumDate >= $currentObjectTimestamp && $rawDatumDate < $nextObjectTimestamp) {
          $currentPeriodCount += $rawDatum->value;
        }
      }

      // Now do stuff with it
      switch ($mode) {
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
          $previous                      = $currentPeriodCount;
          break;
      }

      $currentObject->add(1, $chunk);

    } while ($currentObject->getTimestamp() < $endObject->getTimestamp());

    // Reprocess label
    $labelStrings = array();
    $labelDate    = new Zend_Date();
    foreach ($data as $key => $value) {
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
    if (count($data) > 10) {
      $xlabelsteps = ceil(count($data) / 10);
    }

    // Remove some grid lines if there are too many
    $xsteps = 1;
    if (count($data) > 100) {
      $xsteps = ceil(count($data) / 100);
    }

    // Create base chart
    require_once 'OFC/OFC_Chart.php';

    // Make x axis labels
    $x_axis_labels = new OFC_Elements_Axis_X_Label_Set();
    $x_axis_labels->set_steps($xlabelsteps);
    $x_axis_labels->set_labels($labelStrings);

    // Make x axis
    $labels = new OFC_Elements_Axis_X();
    $labels->set_labels($x_axis_labels);
    $labels->set_colour("#416b86");
    $labels->set_grid_colour("#dddddd");
    $labels->set_steps($xsteps);

    // Make y axis
    $yaxis = new OFC_Elements_Axis_Y();
    $yaxis->set_range($minVal, $maxVal /*, $steps*/);
    $yaxis->set_colour("#416b86");
    $yaxis->set_grid_colour("#dddddd");

    // Make data
    $graph = new OFC_Charts_Line();
    $graph->set_values(array_values($data));
    $graph->set_colour("#5ba1cd");

    // Make title
    $locale    = Zend_Registry::get('Locale');
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr  = $translate->_('_CORE_ADMIN_STATS_' . strtoupper($type) . '_');
    $title     = new OFC_Elements_Title($titleStr . ': ' . $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject));
    $title->set_style("{font-size: 14px;font-weight: bold;margin-bottom: 10px; color: #777777;}");

    // Make full chart
    $chart = new OFC_Chart();
    $chart->set_bg_colour('#ffffff');

    $chart->set_x_axis($labels);
    $chart->add_y_axis($yaxis);
    $chart->add_element($graph);
    $chart->set_title($title);

    // Send
    $this->getResponse()->setBody($chart->toPrettyString());
  }
}