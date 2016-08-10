<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Order.php 24.01.12 14:00 TeaJay $
 * @author     Taalay
 */

/**
 * @category   Application_Extensions
 * @package    Credit
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Credit_Model_Order extends Core_Model_Item_Abstract
{
  protected $_type = 'credit_order';

  protected $_statusChanged;

  /**
   * @return Engine_Db_Table_Rowset
   */
  public function isOrderPending()
  {
    return ($this->status == 'pending') ? true : false;
  }

  public function onPaymentPending()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('initial', 'pending')) ) {
      // Change status
      if( $this->status != 'pending' ) {
        $this->status = 'pending';
        $this->_statusChanged = true;
      }
    }

    $this->save();
    return $this;
 	}

 	public function onPaymentSuccess()
 	{
    $this->_statusChanged = false;

    // Give Credits
    /**
     * @var $api Credit_Api_Core
     */
    $buyer = Engine_Api::_()->getItem('user', $this->user_id);

    $api = Engine_Api::_()->credit();
    $api->buyCredits($buyer, $this->credit, $this->getGatewayTitle());

    // Change status
    if( $this->status != 'completed' ) {
      $this->status = 'completed';
      $this->payment_date = new Zend_Db_Expr('NOW()');
      $this->_statusChanged = true;
    }
    $this->save();

    return $this;
 	}

  public function onPaymentFailure()
	{
    $this->_statusChanged = false;

    // Change status
    if( $this->status != 'failed' ) {
      $this->status = 'failed';
      $this->payment_date = new Zend_Db_Expr('NOW()');
      $this->_statusChanged = true;
    }
    $this->save();

    return $this;
	}

 	public function didStatusChange()
  {
 		return $this->_statusChanged;
 	}

  public function cancel()
  {
    // Cancel this row
    $this->active = false; // Need to do this to prevent clearing the user's session
    $this->onCancel();
    return $this;
  }

  public function onCancel()
  {
    $this->_statusChanged = false;
    if( in_array($this->status, array('pending', 'cancelled')) ) {
      // Change status
      if( $this->status != 'cancelled' ) {
        $this->status = 'cancelled';
        $this->_statusChanged = true;
      }
    }
    $this->save();
    return $this;
  }

  public function isChecked()
  {
    if ( $this->status != 'completed') return false;

    /**
     * @var $table Credit_Model_DbTable_Transactions
     */
    $table = Engine_Api::_()->getItemTable('credit_transaction');
    $select = $table
      ->select()
      ->setIntegrityCheck(false)
      ->from($table->info('name'), 'transaction_id')
      ->where('gateway_transaction_id = ?', $this->gateway_transaction_id)
      ->where('state = ?', 'okay')
    ;

    return (bool) $table->fetchRow($select);
  }

  public function getSource()
  {
    /**
     * @var $table Credit_Model_DbTable_Payments
     */

    $table = Engine_Api::_()->getDbTable('payments', 'credit');
    $select = $table->select()
      ->where('payment_id = ?', $this->payment_id)
      ->limit(1)
    ;
    $row = $table->fetchRow($select);
    return $row;
  }

  public function getUser()
  {
    return Engine_Api::_()->getItem('user', $this->user_id);
  }

  public function getGatewayTitle()
  {
    /**
     * @var $gatewaysTable Payment_Model_DbTable_Gateways
     */
    $gatewaysTable = Engine_Api::_()->getDbTable('gateways', 'payment');
    $select = $gatewaysTable->select()
      ->where('gateway_id = ?', $this->gateway_id)
      ->limit(1);
    return $gatewaysTable->fetchRow($select)->title;
  }
}
