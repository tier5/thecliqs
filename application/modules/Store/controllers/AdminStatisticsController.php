<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: AdminSettingsController.php 2011-08-19 17:22:12 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_AdminStatisticsController extends Core_Controller_Action_Admin
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
    $this->view->menu = $action = $this->_getParam('action');
    $this->view->isPageEnabled = $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');
    if ($action == 'store-list' && !$isPageEnabled) {
      $this->_redirectCustom(
        $this->view->url(
          array(
            'module' => 'store',
            'controller' => 'statistics',
            'action' => 'chart'
          ), 'admin_default', true
        )
      );
    }
    $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('store_admin_main', array(), 'store_admin_main_statistics');
  }

  public function chartAction()
  {
    $types = array('gross' => '_CORE_ADMIN_STATS_GROSS_', 'items' => '_CORE_ADMIN_STATS_ITEMS_');

    $this->view->filterForm = $filterForm = new Store_Form_Admin_Statistics_Filter();
    $filterForm->type->setMultiOptions($types);
  }

  public function listAction()
  {
    /**
     * @var $table Store_Model_DbTable_Orderitems
     * @var $settings Core_Model_DbTable_Settings
     */

    $values = array();
    $this->view->searchForm = $searchForm = new Store_Form_Admin_Products_Search();

    if ($searchForm->isValid($this->_getAllParams())) {
      $values = $searchForm->getValues();
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'total_amount',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $table = Engine_Api::_()->getDbtable('orderitems', 'store');

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(
        array('o' => $table->info('name')),
        array('page_id', 'item_id', 'item_type', 'name', 'quantity' => 'SUM(qty)', 'total_amount' => 'SUM(total_amt)')
      )
      ->where('item_type = ?', 'store_product')
      ->group('item_id');

    $select->order((!empty($values['order']) ? $values['order'] : 'total_amount') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($values['name'])) {
      $select->where('name LIKE ?', '%' . $values['name'] . '%');
    }

    $this->view->isPageEnabled = $isPageEnabled = Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('page');

    if ($isPageEnabled && !empty($values['store_title'])) {
      $select
        ->joinLeft('engine4_page_pages', 'engine4_page_pages.page_id = o.page_id', array())
        ->where('engine4_page_pages.title LIKE ?', '%' . $values['store_title'] . '%');
    }

    $valuesCopy = array_filter($values);
    $this->view->formValues = $valuesCopy;

    /**
     * Make paginator
     *
     * @var $paginator Zend_Paginator
     */

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    if ($isPageEnabled) {
      /**
       * Preload Pages
       *
       * @var $item Store_Model_Orderitem
       */
      $pages = array();
      foreach ($paginator as $item) {
        if (null !== ($page = Engine_Api::_()->getItem('page', (int)$item->page_id)))
          $pages[$item->page_id] = $page;
      }
      $this->view->pages = $pages;
    }
  }

  public function commissionChartAction()
  {
    $this->view->filterForm = $filterForm = new Store_Form_Admin_Statistics_Filter();
    $filterForm->removeElement('type');
  }

  public function commissionListAction()
  {
    /**
     * @var $table Store_Model_DbTable_Orderitems
     * @var $settings Core_Model_DbTable_Settings
     */
    $table = Engine_Api::_()->getDbtable('orderitems', 'store');
    $settings = Engine_Api::_()->getDbTable('settings', 'core');

    $values = array();
    $this->view->searchForm = $searchForm = new Store_Form_Admin_Products_Search();
    $type = new Zend_Form_Element_Select('type');
    $type
      ->setMultiOptions(array('item_id' => 'By Product', 'page_id' => 'By Store'))
      ->setLabel('Filter By')
      ->setOrder(3)
      ->clearDecorators()
      ->addDecorator('ViewHelper')
      ->addDecorator('Label', array('tag' => null,
      'placement' => 'PREPEND'))
      ->addDecorator('HtmlTag', array('tag' => 'div'));
    $searchForm->addElement($type);

    if ($searchForm->isValid($this->_getAllParams())) {
      $values = $searchForm->getValues();
    }

    $values['type'] = empty($values['type']) ? 'item_id' : $values['type'];

    if ($values['type'] == 'page_id') {
      $searchForm->removeElement('name');
    }

    foreach ($values as $key => $value) {
      if (null === $value) {
        unset($values[$key]);
      }
    }

    $values = array_merge(array(
      'order' => 'total_amount',
      'order_direction' => 'DESC',
    ), $values);

    $this->view->assign($values);

    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('o' => $table->info('name')), array(
      'page_id',
      'item_id',
      'item_type',
      'name',
      'quantity' => 'SUM(qty)',
      'total_amount' => 'SUM(commission_amt*qty)'))
      ->where('item_type = ?', 'store_product')
      ->where('page_id <> 0')
      ->group($values['type']);

    $select->order((!empty($values['order']) ? $values['order'] : 'total_amount') . ' ' . (!empty($values['order_direction']) ? $values['order_direction'] : 'DESC'));

    if (!empty($values['name'])) {
      $select->where('name LIKE ?', '%' . $values['name'] . '%');
    }

    if (!empty($values['store_title'])) {
      $select
        ->joinLeft('engine4_page_pages', 'engine4_page_pages.page_id = o.page_id', array())
        ->where('engine4_page_pages.title LIKE ?', '%' . $values['store_title'] . '%');
    }

    $valuesCopy = array_filter($values);
    $this->view->formValues = $valuesCopy;

    /**
     * Make paginator
     *
     * @var $paginator Zend_Paginator
     */

    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));

    /**
     * Preload Pages
     *
     * @var $item Store_Model_Orderitem
     */
    $pages = array();
    foreach ($paginator as $item) {
      if (null !== ($page = Engine_Api::_()->getItem('page', (int)$item->page_id)))
        $pages[$item->page_id] = $page;
    }
    $this->view->pages = $pages;
  }

  public function chartDataAction()
  {
    // Disable layout and viewrenderer
    $this->_helper->layout->disableLayout();
    $this->_helper->viewRenderer->setNoRender(true);

    // Get params
    $type = $this->_getParam('type');
    $start = $this->_getParam('start');
    $offset = $this->_getParam('offset', 0);
    $mode = $this->_getParam('mode');
    $chunk = $this->_getParam('chunk');
    $period = $this->_getParam('period');
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
    $timezone = Engine_Api::_()->getDbTable('settings', 'core')
      ->getSetting('core_locale_timezone', 'GMT');
    $viewer = Engine_Api::_()->user()->getViewer();
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

    if ($this->_getParam('chart', 1) === 1) {
      // Get data
      /**
       * @var $transTable  Store_Model_DbTable_Transactions
       * @var $statsSelect Zend_Db_Select
       */
      $transTable = Engine_Api::_()->getDbtable('transactions', 'store');
      $statsSelect = $transTable
        ->select()
        ->setIntegrityCheck(false);
      $prefix = $transTable->getTablePrefix();

      if ($type == 'items') {
        $statsSelect
          ->from(array('t' => $transTable->info('name')), array('date' => 't.timestamp'))
          ->join(array('o' => $prefix . 'store_orderitems'), 'o.order_id=t.order_id', array('value' => 'SUM(o.qty)'))
        ;
      } else {
        $statsSelect
          ->from(array('t' => $transTable->info('name')), array('value' => 'SUM(t.amt)', 'date' => 't.timestamp'))
        ;
      }

      $statsSelect
        ->where('t.timestamp >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
        ->where('t.timestamp < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
        ->group('t.timestamp')
        ->order('t.timestamp ASC');

      $rawData = $transTable->fetchAll($statsSelect);
    } else {
      // Get data
      /**
       * @var $orderTable  Store_Model_DbTable_Orderitems
       * @var $statsSelect Zend_Db_Select
       */
      $orderTable = Engine_Api::_()->getDbtable('orderitems', 'store');
      $statsSelect = $orderTable
        ->select()
        ->setIntegrityCheck(false);
      $prefix = $orderTable->getTablePrefix();

      $statsSelect
        ->from(array('oi' => $orderTable->info('name')), array('value' => 'SUM(oi.qty*oi.commission_amt)'))
        ->join(array('o' => $prefix . 'store_orders'), 'o.order_id=oi.order_id', array('date' => 'o.payment_date'))
        ->where('oi.page_id <> 0');

      $statsSelect
        ->where('o.payment_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
        ->where('o.payment_date  < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
        ->group('o.payment_date')
        ->order('o.payment_date ASC');

      $rawData = $orderTable->fetchAll($statsSelect);
    }

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
          $previous = $currentPeriodCount;
          break;
      }

      $currentObject->add(1, $chunk);

    } while ($currentObject->getTimestamp() < $endObject->getTimestamp());

    // Reprocess label
    $labelStrings = array();
    $labelDate = new Zend_Date();
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
    $locale = Zend_Registry::get('Locale');
    $translate = Zend_Registry::get('Zend_Translate');
    $titleStr = $translate->_('_CORE_ADMIN_STATS_' . strtoupper($type) . '_');
    $title = new OFC_Elements_Title($titleStr . ': ' . $this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject));
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