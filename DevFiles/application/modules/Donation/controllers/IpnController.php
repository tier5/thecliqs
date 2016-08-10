<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Ravshanbek
 * Date: 02.08.12
 * Time: 15:29
 * To change this template use File | Settings | File Templates.
 */
class Donation_IpnController extends Core_Controller_Action_Standard
{
  public function __call($method, array $arguments)
  {
    $session = new Zend_Session_Namespace('Donation_Transaction');
    $params = $this->_getAllParams();
    $gatewayId = $params['gateway_id'];
    $orderId = $params['order_id'];
    unset($params['module']);
    unset($params['controller']);
    unset($params['rewrite']);
    unset($params['gateway_id']);
    unset($params['order_id']);

    if($this->validateIpn($params) && $orderId) {
      $order = Engine_Api::_()->getItem('payment_order', $orderId);
      $amount_data = $session->__get('donation_info');
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
      $order->gateway_transaction_id = $txn_id;;
      $order->save();

      //Check exists this transaction
      $transactionsTable = Engine_Api::_()->getDbtable('transactions', 'donation');
      $select = $transactionsTable->select();
      $select->where('gateway_id = ?',$gatewayId);
      $select->where('gateway_transaction_id = ?', $txn_id);
      $select->limit(1);
      $transaction = $transactionsTable->fetchRow($select);
      if($transaction){
        $transaction->state = $paymentStatus;
        $transaction->save();
      }
      else{
        /**
         * @var $user User_Model_User;
         */
        $user = Engine_Api::_()->getItem('user',$order->user_id);

        // Insert transaction
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
      if($paymentStatus == 'completed'){
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
    }

    // Log ipn
    $ipnLogFile = APPLICATION_PATH . '/temporary/log/donation-ipn.log';
    file_put_contents($ipnLogFile,
      date('c') . ': ' .
        print_r($params, true),
      FILE_APPEND);


    // Exit
    echo 'OK';
    exit();
  }

  public function validateIpn($params = array())
  {
    $gateway = Engine_Api::_()->getItem('payment_gateway', 2);
    $gatewayPlugin = $gateway->getGateway();
    $gatewayUrl = $gatewayPlugin->getGatewayUrl();
    $postData="";
    foreach ($params as $key=>$value) $postData.=$key."=".urlencode($value)."&";
    $postData.="cmd=_notify-validate";
    $curl = curl_init($gatewayUrl);
    curl_setopt ($curl, CURLOPT_HEADER, 0);
    curl_setopt ($curl, CURLOPT_POST, 1);
    curl_setopt ($curl, CURLOPT_POSTFIELDS, $postData);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 1);
    $response = curl_exec ($curl);
    curl_close ($curl);
    if ($response != "VERIFIED"){
      return false;
    }

    return true;
  }
}
