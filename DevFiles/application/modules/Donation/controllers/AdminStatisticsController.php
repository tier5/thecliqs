<?php
/**
 * Created by JetBrains PhpStorm.
 * User: adilet
 * Date: 24.08.12
 * Time: 13:21
 * To change this template use File | Settings | File Templates.
 */
class Donation_AdminStatisticsController  extends Core_Controller_Action_Admin
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
    $this->view->navigation = Engine_Api::_()->getApi('menus', 'core')
      ->getNavigation('donation_admin_main', array(), 'donation_admin_main_statistics');
  }

  public function indexAction()
  {
    $this->view->filterForm = $filterForm = new Donation_Form_Options_Filter();
  }

  public function listAction()
  {
    /**
     * @var $table Donation_Model_DbTable_Transactions
     */
    $this->view->form = $form = new Donation_Form_Transaction_Filter();

    if ($form->isValid($this->_getAllParams())) {
      $values = $form->getValues();
    } else {
      $values = array();
    }
    if (empty($values['order'])) {
      $values['order'] = 'transaction_id';
    }
    if (empty($values['direction'])) {
      $values['direction'] = 'DESC';
    }

    $this->view->values = $values;
    $this->view->order = $values['order'];
    $this->view->direction = $values['direction'];


    $table = Engine_Api::_()->getDbTable('transactions', 'donation');
    $prefix = $table->getTablePrefix();
    $select = $table->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $table->info('name')))
      ->join(array('g' => $prefix . 'payment_gateways'), 'g.gateway_id = t.gateway_id', array('gateway' => 'title'))
    ;

    if (!empty($values['name'])) {
      $select
        ->where('name LIKE ?', '%' . $values['name'] . '%');
    }

    if ($values["type"]) {
        $select->join(array('d' => 'engine4_donation_donations'), 'd.donation_id = t.item_id', array('d.type'))
          ->where('d.type = ?', $values['type']);
    }

    if (!empty($values['order'])) {
      if (empty($values['direction'])) {
        $values['direction'] = 'DESC';
      }
      $select->order($values['order'] . ' ' . $values['direction']);
    }


    $this->view->paginator = $paginator = Zend_Paginator::factory($select);
    $paginator->setCurrentPageNumber($this->_getParam('page', 1));
    $this->view->currency = Engine_Api::_()->getDbTable('settings', 'core')->getSetting('payment.currency', 'USD');

    $userIds = array();
    $orderIds = array();
    $donationIds = array();

    foreach ($paginator as $item) {
      if (!empty($item->user_id)) {
        $userIds[] = $item->user_id;
      }

      if (!empty($item->order_id)) {
        $orderIds[] = $item->order_id;
      }
      if (!empty($item->item_id)) {
        $donationIds[] = $item->item_id;
      }
    }
    $userIds = array_unique($userIds);
    $orderIds = array_unique($orderIds);
    $donationIds = array_unique($donationIds);

    // Preload users
    $users = array();
    if (!empty($userIds)) {
      foreach (Engine_Api::_()->getItemTable('user')->find($userIds) as $user) {
        $users[$user->user_id] = $user;
      }
    }
    $this->view->users = $users;


    // Preload orders
    $orders = array();
    if (!empty($orderIds)) {
      foreach (Engine_Api::_()->getItemTable('transaction')->find($orderIds) as $order) {
        $orders[$order->order_id] = $order;
      }
    }
    $this->view->orders = $orders;

    $donations = array();
    if (!empty($donationIds)) {
      foreach (Engine_Api::_()->getItemTable('donation')->find($donationIds) as $donation) {
        if ($donation == null) continue;
        $donations[$donation->donation_id] = $donation;
      }
    }
    $this->view->donations = $donations;

  }

  public function detailAction()
  {
    $item_id = $this->_getParam('transaction_id');

   if (null == ($item = Engine_Api::_()->getItem('transaction', $item_id))) {
      $this->_forward('success', 'utility', 'core', array(
        'redirect' => $this->view->url(array(
          'controller' => 'transactions',
          'action' => 'list',
          'donation_id' => $this->_getParam('donation_id'), 'default', true
        )),
        'redirectTime'   => 1000,
        'messages'       => $this->view->translate("DONATION_No order found with the provided id.")
      ));
    }

    $this->view->item = $item;
    $this->view->user = Engine_Api::_()->getItem('user', $item->user_id);

    $this->view->donation = Engine_Api::_()->getItem('donation', $item->item_id);

    //Transaction details
    $table = Engine_Api::_()->getDbTable('transactions', 'donation');
    $select = $table
      ->select()
      ->where('gateway_id = ?', $item->gateway_id)
      ->where('gateway_transaction_id = ?', $item->gateway_transaction_id);

    if (null == ($transaction = $table->fetchRow($select))) {
      $this->_forward('success', 'utility', 'core', array(
        'redirect' => $this->view->url(array(
          'controller' => 'transactions',
          'action' => 'list',
          'donation_id' => $this->_getParam('donation_id'), 'default', true
        )),
        'redirectTime'   => 1000,
        'messages'       => $this->view->translate("DONATION_No order found with the provided id.")
      ));
    }

    $this->view->transaction = $transaction;
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
    $type = $this->_getParam('type');
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

    // Get data
    /**
     * @var $transTable  Store_Model_DbTable_Transactions
     * @var $statsSelect Donation_Model_DbTable_Transactions
     */

    $transTable = Engine_Api::_()->getDbtable('transactions', 'donation');
    $statsSelect = $transTable->select()
      ->setIntegrityCheck(false)
      ->from(array('t' => $transTable->info('name')), array('value' => 'SUM(t.amount)', 'date' => 't.creation_date'))
      ->where('t.creation_date >= ?', gmdate('Y-m-d H:i:s', $startObject->getTimestamp()))
      ->where('t.creation_date < ?', gmdate('Y-m-d H:i:s', $endObject->getTimestamp()))
      ->group('t.creation_date')
      ->order('t.creation_date ASC');

    if ($type != '0') {
      $statsSelect->join(array('d' => 'engine4_donation_donations'), "d.donation_id = t.item_id AND d.type = '$type'" , array('d.type'));
    }
    $rawData = $transTable->fetchAll($statsSelect);

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
    $title = new OFC_Elements_Title($this->view->locale()->toDateTime($startObject) . ' to ' . $this->view->locale()->toDateTime($endObject));
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
