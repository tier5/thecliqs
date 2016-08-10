<?php

class Ynsocialads_PaymentController extends Core_Controller_Action_Standard
{
  public function indexAction()
  {
    $this->view->someVar = 'someVal';
  }
  
  public function payLaterAction()
  {
  	$ad = Engine_Api::_() -> getItem('ynsocialads_ad', $this-> _getParam('id'));
	$transactionTable =  Engine_Api::_() -> getDbTable('transactions', 'ynsocialads');
	$db = $transactionTable->getAdapter();
    $db->beginTransaction();

    try {
    	
      $transAd = $transactionTable->createRow();
	  $transAd -> start_date = $ad -> start_date;
	  $transAd -> status = 'initialized';
	  $transAd -> payment_method = 'Pay Later';
	  $transAd -> amount = $ad->getPackage()->price;
	  $transAd -> currency = $ad->getPackage()->currency;
	  $transAd -> ad_id = $ad -> getIdentity();
	  $transAd -> user_id = $ad -> user_id;
      $transAd->save();
      // Commit
      $db->commit();
    }
	 catch( Exception $e ) {
      $db->rollBack();
      throw $e;
    }
	 exit(1);
  }
}
