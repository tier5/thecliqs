<?php
    include_once 'gateway.php';
    $gateway = new gateway();
    $p = $gateway->load('paypal');
    $params = array();
    $aSetting = array(
                    'env' =>'sandbox',
                    'proxy_host' => '127.0.0.1',
                    'proxy_port' => '808',
                    'api_username' =>'music2_1298365294_biz_api1.yahoo.com',
                    'api_password' =>'1298365304',
                    'api_signature' =>'AGvofgFr5KfTPLmgHXGvSxdUjiipALiplxLdQuq.GsgTLEvE0yswKLFb',
                    'api_app_id' =>'APP-80W284485P519543T',
                    'use_proxy' =>false,
                );
    $p->set($aSetting);
    /*$paramsVerify = array(
                'actionType' => '',
                'cancelUrl'  => '',
                'returnUrl'  => '',
                'currencyCode' => '',
                'startingDate' => '',
                'endingDate' => '',
                'maxTotalAmountOfAllPayments' => '',
                'senderEmail' => '',
                'maxNumberOfPayments' => '',
                'paymentPeriod' => '',
                'dateOfMonth' => '',
                'dayOfWeek' => '',
                'maxAmountPerPayment' => '',
                'maxNumberOfPaymentsPerPeriod' => '',
                'pinType' => '',
             );
    $verify_code = $p->verify($paramsVerify);
     */
    $paramsPay = array(
                'actionType' => 'PAY',
                'cancelUrl'  => 'http://localhost',
                'returnUrl'  => 'http://localhost',
                'currencyCode' => Engine_Api::_()->getApi('settings', 'core')->getSetting('YnAuction.currency', 'USD'),
                'sender'=>'',
                'feesPayer'=>'EACHRECEIVER',//feesPayer value {SENDER, PRIMARYRECEIVER, EACHRECEIVER}
                'sender'=>'',
                'ipnNotificationUrl'=> '',
                'memo'=> '',
                'pin'=> '',
                'preapprovalKey'=> '',
                'reverseAllParallelPaymentsOnError'=> '',
                'receivers' => array(
                                array('email' => 'music2_1298365294_biz@yahoo.com','amount' => '11.11','invoice' => '11111'),
                                array('email' => 'music1_1298365241_per@yahoo.com','amount' => '22.22','invoice' => '22222'),
                                
                             )
             );
    $result = $p->checkOut($paramsPay); 
    if($result == false)
    {
        echo $p->getErrors();
    }
  
?>
