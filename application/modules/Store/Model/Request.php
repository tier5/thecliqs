<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    Id: Request.php 5/8/12 6:13 PM mt.uulu $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Store
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Store_Model_Request extends Store_Model_Item_Abstract
{
  protected $_owner_type = 'page';

  protected $_type = 'store_request';

  protected $_searchTriggers = false;

  public function cancel()
  {
    if ($this->status != 'waiting') {
      return false;
    }

    $this->status = 'cancelled';
    if ($this->save()) {
      /**
       * @var $balance Store_Model_Balance
       */
      $balance = Engine_Api::_()->getApi('page', 'store')->getBalance($this->page_id);
      $balance->decreaseRequested($this->amt);
      return $balance->increase($this->amt);
    }

    return false;
  }

  public function setActive($active, $status = 'success')
  {
    if (!$active) {
      switch ($status) {
        case 'success':
          $this->onSuccess();
          break;
        case 'pending':
          $this->onPending();
          break;
      }
    }
    $this->save();

    parent::setActive($active, $status);
  }

  public function onSuccess()
  {
    if ($this->status != 'completed') {
      /**
       * @var $pageApi Store_Api_Page
       * @var $balance Store_Model_Balance
       */
      $pageApi = Engine_Api::_()->getApi('page', 'store');
      $balance = $pageApi->getBalance($this->page_id);
      if ($this->status == 'pending') {
        $balance->decreasePending($this->amt);
      } else {
        $balance->decreaseRequested($this->amt);
      }
      $balance->increaseTransfer($this->amt - $this->getGatewayFee());

      $this->status        = 'completed';
      $this->response_date = new Zend_Db_Expr('NOW()');
      $this->save();

      try {
        if (null != ($order = Engine_Api::_()->getItem('store_order', (int)$this->order_id))) {
          $user = $order->getOwner();
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_request_complete', array(
            'request_details'       => $this->getDetails(),
            'request_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }
      } catch (Exception $e) {
        print_log($e, 'mail');
      }

      try {
        $owner = $this->getOwner()->getOwner();
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'store_owner_request_complete', array(
          'request_details'       => $this->getDetails(),
          'request_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      } catch (Exception $e) {
        print_log($e, 'mail');
      }
    }
  }

  public function onPending()
  {
    if (!in_array($this->status, array('completed', 'pending'))) {
      /**
       * @var $pageApi Store_Api_Page
       * @var $balance Store_Model_Balance
       */
      $pageApi = Engine_Api::_()->getApi('page', 'store');
      $balance = $pageApi->getBalance($this->page_id);
      $balance->decreaseRequested($this->amt);
      $balance->increasePending($this->amt);

      $this->status        = 'pending';
      $this->response_date = new Zend_Db_Expr('NOW()');
      $this->save();

      try {
        if (null != ($order = Engine_Api::_()->getItem('store_order', (int)$this->order_id))) {
          $user = $order->getOwner();
          Engine_Api::_()->getApi('mail', 'core')->sendSystem($user, 'store_request_pending', array(
            'request_details'       => $this->getDetails(),
            'request_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
              Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
          ));
        }
      } catch (Exception $e) {
        print_log($e, 'mail');
      }

      try {
        $owner = $this->getOwner()->getOwner();
        Engine_Api::_()->getApi('mail', 'core')->sendSystem($owner, 'store_owner_request_pending', array(
          'request_details'       => $this->getDetails(),
          'request_link'          => 'http://' . $_SERVER['HTTP_HOST'] .
            Zend_Controller_Front::getInstance()->getRouter()->assemble(array(), 'user_login', true),
        ));
      } catch (Exception $e) {
        print_log($e, 'mail');
      }
    }
  }

  public function onDeny()
  {
    if ($this->status != 'completed') {
      $this->status        = 'denied';
      $this->response_date = new Zend_Db_Expr('NOW()');
      $this->save();
    }
  }

  public function getDetails()
  {
    $details = "";
    /**
     * @var $page  Page_Model_Page
     * @var $owner User_Model_User
     */
    $v = Zend_Registry::get('Zend_View');
    if (null != ($page = $this->getOwner())) {
      $href  = $page->getHref();
      $title = $page->getTitle();

      if (!$href) {
        $name = $title;
      } else {
        $name = '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] . $href . '">' . $title . '</a>';
      }

      $details .= $v->translate('STORE_Store Name') . ": " . $name . "
";

      if (null != ($owner = $page->getOwner())) {
        $href  = $owner->getHref();
        $title = $owner->getTitle();

        if (!$href) {
          $name = $title;
        } else {
          $name = '<a href="' . 'http://' . $_SERVER['HTTP_HOST'] . $href . '">' . $title . '</a>';
        }

        $details .= $v->translate('STORE_Owner Name') . ": " . $name . "
";
      }
    }

    $details .= $v->translate('Requested Amount') . ": " . $v->toCurrency($this->amt) . "
";
    $details .= $v->translate('Requested Date') . ": " . $v->locale()->toDateTime($this->amt) . "
";

    return $details;
  }

  public function getOrderId()
  {
    /**
     * @var $table Store_Model_DbTable_Orders
     */
    $table = Engine_Api::_()->getDbTable('orders', 'store');
    return $table->fetchRow(array('item_type = ?' => 'store_request', 'item_id = ?' => $this->getIdentity()));
  }

  public function getGatewayFee()
  {
    /**
     * @var $table Store_Model_DbTable_Transactions
     */
    $table = Engine_Api::_()->getDbTable('transactions', 'store');
    $transaction = $table->fetchRow(array('item_type = ?' => $this->getType(), 'item_id = ?' => $this->getIdentity()));
    if (!$transaction) {
      return 0;
    }
    return (double) $transaction->gateway_fee;
  }
}
