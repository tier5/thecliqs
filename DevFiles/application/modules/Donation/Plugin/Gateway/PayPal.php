<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 27.07.12
 * Time: 10:20
 * To change this template use File | Settings | File Templates.
 */
class Donation_Plugin_Gateway_PayPal extends Payment_Plugin_Gateway_PayPal
{
  public function onCreateTransaction(Payment_Model_Order $order, array $params = array(), array $amount_data = array())
  {
    // Check that gateways match
    if( $order->gateway_id != $this->_gatewayInfo->gateway_id ) {
      throw new Engine_Payment_Plugin_Exception('Gateways do not match');
    }
    // Get payment state
    $paymentStatus = null;
    $orderStatus = null;
    $status = 'failed';
    $txn_id = '';
    $currency = 'USD';
    $amount = 0;

    if(isset($params['payment_status'])){
      $status = $params['payment_status'];
    }
    elseif(isset($params['st'])){
      $status = $params['st'];
    }

    if(isset($params['txn_id'])){
      $txn_id = $params['txn_id'];
    }
    elseif(isset($params['tx'])){
      $txn_id = $params['tx'];
    }

    if(isset($params['mc_currency'])){
      $currency = $params['mc_currency'];
    }
    elseif(isset($params['cc'])){
      $currency = $params['cc'];
    }

    if(isset($params['mc_gross']) && isset($params['mc_fee'])){
      $amount = $params['mc_gross'] - $params['mc_fee'];
    }
    elseif(isset($params['amt'])){
      $amount = $params['amt'];
    }

    switch(strtolower($status)) {
      case 'created':
      case 'pending':
        $paymentStatus = 'pending';
        $orderStatus = 'complete';
        break;

      case 'completed':
      case 'processed':
      case 'canceled_reversal': // Probably doesn't apply
        $paymentStatus = 'completed';
        $orderStatus = 'complete';
        break;

      default: // No idea what's going on here
        $paymentStatus = 'failed';
        $orderStatus = 'failed'; // This should probably be 'failed'
        break;
    }

    // Update order with profile info and complete status?
    $order->state = $orderStatus;
    $order->gateway_transaction_id = $txn_id;
    $order->save();

    /**
     * @var $user User_Model_User;
     */
    $user = Engine_Api::_()->getItem('user',$order->user_id);

    //check for exists this transaction
    $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'donation');
    $select = $transactionsTable->select();
    $select->where('gateway_id = ?',$order->gateway_id);
    $select->where('gateway_transaction_id = ?', $txn_id);
    $select->limit(1);
    $transaction = $transactionsTable->fetchRow($select);

    if($transaction){
      $transaction->state = $paymentStatus;
      $transaction->save();
    }
    else{
      $transactionsTable->insert(array(
        'order_id' => $order->order_id,
        'user_id' => $order->user_id,
        'name' => (!$order->user_id)?$amount_data['name']:$user->getTitle(),
        'email' => (!$order->user_id)?$amount_data['email']:$user->email,
        'gateway_id' => $this->_gatewayInfo->gateway_id,
        'item_id' => $order->source_id,
        'item_type' => $order->source_type,
        'state' => $paymentStatus,
        'gateway_id' => $order->gateway_id,
        'gateway_transaction_id' => $txn_id,
        'amount' => $amount,
        'currency' => $currency,
        'description' => $amount_data['donation_text'],
        'creation_date' => $order->creation_date,
      ));
    }

    //Update the raised money
    if($paymentStatus == 'completed') {
      // Earning credits for donation
      if (Engine_Api::_()->getDbTable('modules', 'core')->isModuleEnabled('credit')) {
        $user = Engine_Api::_()->getItem('user', $order->user_id);
        $object_id = $order->source_id;
        $creditApi = Engine_Api::_()->getApi('core', 'credit');
        $creditApi->updateDonationCredits($user, $object_id);
      }

      $donation = Engine_Api::_()->getItem($order->source_type,$order->source_id);
      $donation->raised_sum = $donation->raised_sum + $amount;
      $donation->save();
      $parentDonation = $donation->getParent();
      if($parentDonation){
        $parentDonation->raised_sum = $donation->raised_sum + $amount;
        $parentDonation->save();
      }
    }

    return $paymentStatus;
  }
}