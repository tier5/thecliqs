<?php
    
	class paypal implements Payment_Interface
    {
         private $errors ="";
         private $preapprovalKey  = "";// use to verify transaction.
         private $PROXY_HOST = '';
         private $PROXY_PORT = '';
         private $Env = "";
       
        //------------------------------------
        // PayPal API Credentials
        // Replace <API_USERNAME> with your API Username
        // Replace <API_PASSWORD> with your API Password
        // Replace <API_SIGNATURE> with your Signature
        //------------------------------------
         private $API_UserName = "";
         private $API_Password = "";
         private $API_Signature = "";
        // AppID is preset for sandbox use
        //   If your application goes live, you will be assigned a value for the live environment by PayPal as part of the live onboarding process
         private $API_AppID = "";
         private $API_Endpoint = "";
         private $USE_PROXY = false;
         private $ipn_data = array();
         private $ipn_response = "";
        /**
        * set setting for payment 
        * 
        * @param mixed $aSetting
        */
         public function __construct()
         {
            $this->PROXY_HOST = '127.0.0.1';
            $this->PROXY_PORT = '808';
            $this->Env = "sandbox";
            $this->API_UserName = "music2_1298365294_biz_api1.yahoo.com";
            $this->API_Password = "1298365304";
            $this->API_Signature = "AGvofgFr5KfTPLmgHXGvSxdUjiipALiplxLdQuq.GsgTLEvE0yswKLFb";
            $this->API_AppID = "APP-80W284485P519543T";
            $this->API_Endpoint = "";
            $this->USE_PROXY = false;
            $this->preapprovalKey = "";
         }
         public function set($aSetting = array())
         {
            $this->PROXY_HOST = $aSetting['proxy_host'];
            $this->PROXY_PORT = $aSetting['proxy_port'];
            $this->Env = $aSetting['env'];
            $this->API_UserName = $aSetting['api_username'];
            $this->API_Password = $aSetting['api_password'];
            $this->API_Signature = $aSetting['api_signature'];
            $this->API_AppID = $aSetting['api_app_id'];
            //$this->API_Endpoint = $aSetting['api_endpoint'];
            if ($this->Env == "sandbox") 
            {
                $this->API_Endpoint = "https://svcs.sandbox.paypal.com/AdaptivePayments";
            }
            else
            {
                $this->API_Endpoint = "https://svcs.paypal.com/AdaptivePayments";
            }
            $this->USE_PROXY = $aSetting['use_proxy'];
         }
         /**
         * Verify the transaction of site.
         * 
         * @param mixed $params
         */
         public function verify($params = array())
         {
             $returnURL        = "http://localhost";    
             $cancelURL        = "http://localhost";    
             $currencyCode    = Engine_Api::_()->getApi('settings', 'core')->getSetting('YnAuction.currency', 'USD');
             $startingDate    = "2011-02-24T13:00:00";   
             $endingDate        = "2011-02-28T13:00:00"; 
             $maxTotalAmountOfAllPayments    = "2000";   
             $senderEmail                    = "";        
             $maxNumberOfPayments            = "";   
            //            NO_PERIOD_SPECIFIED
            //            DAILY - each day
            //            WEEKLY - each week
            //            BIWEEKLY - every other week
            //            SEMIMONTHLY - twice a month
            //            MONTHLY - each month
            //            ANNUALLY - each year     
             $paymentPeriod                    = "";      
                                                     
             $dateOfMonth                    = ""; 
             $dayOfWeek                        = "";
             $maxAmountPerPayment            = "";  
             $maxNumberOfPaymentsPerPeriod    = ""; 
             $pinType                        = "";  
             $resArray = $this->CallPreapproval ($returnURL, $cancelURL, $currencyCode, $startingDate, $endingDate, $maxTotalAmountOfAllPayments,
                                            $senderEmail, $maxNumberOfPayments, $paymentPeriod, $dateOfMonth, $dayOfWeek,
                                            $maxAmountPerPayment, $maxNumberOfPaymentsPerPeriod, $pinType
                                            );
             $ack = strtoupper($resArray["responseEnvelope.ack"]);
             if($ack=="SUCCESS")
             {
                  $this->preapprovalKey = $resArray["preapprovalKey"];
                  $cmd = "cmd=_ap-preapproval&preapprovalkey=" . urldecode($resArray["preapprovalKey"]);
                  $this->Redirect( $cmd );
                  //return $resArray["preapprovalKey"];
             } 
             else  
             {
                 $ErrorCode = urldecode($resArray["error(0).errorId"]);
                 $ErrorMsg = urldecode($resArray["error(0).message"]);
                 $ErrorDomain = urldecode($resArray["error(0).domain"]);
                 $ErrorSeverity = urldecode($resArray["error(0).severity"]);
                 $ErrorCategory = urldecode($resArray["error(0).category"]);
                 $this->errors = $ErrorCode.':'.$ErrorMsg.' '.$ErrorDomain.' '.$ErrorSeverity.' '.$ErrorCategory;
                 $this->logging('verify Error : '. $this->errors);
                 return false;
             }
         }
         /**
         * Check to send money to the gateway.
         * 
         * @param mixed $params
         */
         public function checkOut($params = array())
         {
             $actionType            = $params['actionType'];;
             $cancelUrl            = $params['cancelUrl'];//"http://localhost";    
             $returnUrl            = $params['returnUrl'];   
             $startingDate    = ""; 
               
             $currencyCode        = $params['currencyCode'];
             $receiverEmailArray = array();
             $receiverAmountArray = array();
             $receiverInvoiceIdArray = array();
             foreach($params['receivers'] as $rc)
             {
                 $receiverEmailArray[] = $rc['email'];
                 $receiverAmountArray[] = $rc['amount'];
                 $receiverInvoiceIdArray[] = $rc['invoice'];
             }
            
            $receiverPrimaryArray = array();
            $senderEmail = $params['sender']; 
            /**
            * feesPayer value {SENDER, PRIMARYRECEIVER, EACHRECEIVER}
            * 
            * @var mixed
            */
            $feesPayer    = $params['feesPayer'];       
            $ipnNotificationUrl = $params['ipnNotificationUrl'];
            $memo = $params['memo'];        
            $pin  = $params['pin'];        
            $preapprovalKey = $params['preapprovalKey'];
            //echo $preapprovalKey;
            $reverseAllParallelPaymentsOnError = $params['reverseAllParallelPaymentsOnError'];   
            $trackingId = $this->generateTrackingID();    
            $resArray = $this->CallPay ($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray,
                                        $receiverAmountArray, $receiverPrimaryArray, $receiverInvoiceIdArray,
                                        $feesPayer, $ipnNotificationUrl, $memo, $pin, $preapprovalKey,
                                        $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId,$startingDate
                                );
                

            
             $ack = strtoupper($resArray["responseEnvelope.ack"]);
                   
             if($ack=="SUCCESS")
             {
                 if ("" == $preapprovalKey)
                 {
                    // redirect for web approval flow
                    $cmd = "cmd=_ap-payment&paykey=" . urldecode($resArray["payKey"]);
                    //$cmd = "cmd=_notify-validate&paykey=" . urldecode($resArray["payKey"]);
                    
                    $this->Redirect($cmd);
                 }
                 else
                 {
                    
                    // payKey is the key that you can use to identify the result from this Pay call
                    $payKey = urldecode($resArray["payKey"]);
                    // paymentExecStatus is the status of the payment
                    $paymentExecStatus = urldecode($resArray["paymentExecStatus"]);
                }    
               
            }
             else
             {
                $ErrorCode = urldecode($resArray["error(0).errorId"]);
                $ErrorMsg = urldecode($resArray["error(0).message"]);
                $ErrorDomain = urldecode($resArray["error(0).domain"]);
                $ErrorSeverity = urldecode($resArray["error(0).severity"]);
                $ErrorCategory = urldecode($resArray["error(0).category"]);
                $this->errors = $ErrorCode.':'.$ErrorMsg.' '.$ErrorDomain.' '.$ErrorSeverity.' '.$ErrorCategory;
                $this->logging('checkOut Error : '. $this->errors);  
                return false;
             } 
             
         }
         /**
         * get Callback Url .
         * 
         */
         public function getCallBackUrl()
         {
             
         }
         /**
         * Log 
         * 
         * @param mixed $log
         */
         public function logging($message = "")
         {
             $log = new Logging();
             $log->lwrite($message);
         }
         
         public function generateCharacter () 
         {
             $possible = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
             $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
             return $char;
         }

         public function generateTrackingID () 
         {
            $GUID = $this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter();
            $GUID .= $this->generateCharacter().$this->generateCharacter().$this->generateCharacter().$this->generateCharacter();
            return $GUID;
         }
         /**
         * Function to perform the API call to PayPal using API signature
         * 
         * @param mixed $methodName is name of API method
         * @param mixed $nvpStr is nvp string
         * @return string : an associative array containing the response from the server
         */
         public function hash_call($methodName, $nvpStr)
         {
             
            //declaring of global variables
            //global $API_Endpoint, $API_UserName, $API_Password, $API_Signature, $API_AppID;
            //global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
            
            $this->API_Endpoint .= "/" . $methodName;
           
            //setting the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$this->API_Endpoint);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);

            //turning off the server and peer verification(TrustManager Concept).
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch, CURLOPT_POST, 1);
            
            // Set the HTTP Headers
            curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
                        'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
                        'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
                        'X-PAYPAL-SECURITY-USERID: ' . $this->API_UserName,
                        'X-PAYPAL-SECURITY-PASSWORD: ' .$this->API_Password,
                        'X-PAYPAL-SECURITY-SIGNATURE: ' . $this->API_Signature,
                        'X-PAYPAL-SERVICE-VERSION: 1.3.0',
                        'X-PAYPAL-APPLICATION-ID: ' . $this->API_AppID
            ));
        
            //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
            //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
            if($this->USE_PROXY)
                curl_setopt ($ch, CURLOPT_PROXY, $this->PROXY_HOST. ":" . $this->PROXY_PORT); 

            // RequestEnvelope fields
            $detailLevel    = urlencode("ReturnAll");    // See DetailLevelCode in the WSDL for valid enumerations
            $errorLanguage    = urlencode("en_US");        // This should be the standard RFC 3066 language identification tag, e.g., en_US

            // NVPRequest for submitting to server
            $nvpreq = "requestEnvelope.errorLanguage=$errorLanguage&requestEnvelope.detailLevel=$detailLevel";
            $nvpreq .= "&$nvpStr";

            //setting the nvpreq as POST FIELD to curl
            curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
          
            //getting response from server
            $response = curl_exec($ch);
           
            //converting NVPResponse to an Associative Array
            $nvpResArray=$this->deformatNVP($response);
            $nvpReqArray=$this->deformatNVP($nvpreq);
            $_SESSION['nvpReqArray']= $nvpReqArray;

            if (curl_errno($ch)) 
            {
                // moving to display page to display curl errors
                  $_SESSION['curl_error_no']=curl_errno($ch) ;
                  $_SESSION['curl_error_msg']=curl_error($ch);

                  //Execute the Error handling module to display errors. 
            } 
            else 
            {
                 //closing the curl
                  curl_close($ch);
            }

            return $nvpResArray;
        }
         
         /**
         * This function will take NVPString and convert it to an Associative Array and it will decode the response.
         * It is usefull to search for a particular key and displaying arrays.
         * @$nvpstr is NVPString.
         * @nvpArray is Associative Array.
         * @par
         */
         public function deformatNVP($nvpstr)
         {
            $intial=0;
            $nvpArray = array();

            while(strlen($nvpstr))
            {
                //postion of Key
                $keypos= strpos($nvpstr,'=');
                //position of value
                $valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

                /*getting the Key and Value values and storing in a Associative Array*/
                $keyval=substr($nvpstr,$intial,$keypos);
                $valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
                //decoding the respose
                $nvpArray[urldecode($keyval)] =urldecode( $valval);
                $nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
             }
            return $nvpArray;
        }
         public function CallPayDirect($actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmail, $amount,$preapprovalKey,$senderEmail,$trackingId)
         {
             $nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=" . urlencode($currencyCode);
             $nvpstr .= "&returnUrl=" . urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);

             if (0 != count($receiverAmountArray))
             {
                reset($receiverAmountArray);
                while (list($key, $value) = each($receiverAmountArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                    }
                }
            }

             if (0 != count($receiverEmailArray))
             {
                reset($receiverEmailArray);
                while (list($key, $value) = each($receiverEmailArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                    }
                }
            }

            if (0 != count($receiverPrimaryArray))
            {
                reset($receiverPrimaryArray);
                while (list($key, $value) = each($receiverPrimaryArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").primary=" . urlencode($value);
                    }
                }
            }

            if (0 != count($receiverInvoiceIdArray))
            {
                reset($receiverInvoiceIdArray);
                while (list($key, $value) = each($receiverInvoiceIdArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").invoiceId=" . urlencode($value);
                    }
                }
            }
        
            // optional fields
           

            if ("" != $preapprovalKey)
            {
                $nvpstr .= "&preapprovalKey=" . urlencode($preapprovalKey);
            }
            if ("" != $senderEmail)
            {
                $nvpstr .= "&senderEmail=" . urlencode($senderEmail);
            }
            if ("" != $trackingId)
            {
                $nvpstr .= "&trackingId=" . urlencode($trackingId);
            }

            /* Make the Pay call to PayPal */
            
            $resArray =$this->hash_call("Pay", $nvpstr);

            /* Return the response array */
            return $resArray;
         }
         /**
         * 
         * Purpose:   Prepares the parameters for the Refund API Call.
         *            The API credentials used in a Pay call can make the Refund call
         *            against a payKey, or a tracking id, or to specific receivers of a payKey or a tracking id
         *            that resulted from the Pay call
         *
         *            A receiver itself with its own API credentials can make a Refund call against the transactionId corresponding to their transaction.
         *            The API credentials used in a Pay call cannot use transactionId to issue a refund
         *            for a transaction for which they themselves were not the receiver
         *
         *            If you do specify specific receivers, keep in mind that you must provide the amounts as well
         *            If you specify a transactionId, then only the receiver of that transactionId is affected therefore
         *            the receiverEmailArray and receiverAmountArray should have 1 entry each if you do want to give a partial refund
         * Inputs:
         *
         * Conditionally Required:
         *        One of the following:  payKey or trackingId or trasactionId or
         *                              (payKey and receiverEmailArray and receiverAmountArray) or
         *                              (trackingId and receiverEmailArray and receiverAmountArray) or
         *                              (transactionId and receiverEmailArray and receiverAmountArray)
         * @param mixed $payKey
         * @param mixed $transactionId
         * @param mixed $trackingId
         * @param mixed $receiverEmailArray
         * @param mixed $receiverAmountArray
         * @return string :The NVP Collection object of the Refund call response.
         */
         public function CallRefund( $payKey, $transactionId, $trackingId, $receiverEmailArray, $receiverAmountArray )
         {
            /* Gather the information to make the Refund call.
                The variable nvpstr holds the name value pairs
            */
            
            $nvpstr = "";
            
            // conditionally required fields
            if ("" != $payKey)
            {
                $nvpstr = "payKey=" . urlencode($payKey);
                if (0 != count($receiverEmailArray))
                {
                    reset($receiverEmailArray);
                    while (list($key, $value) = each($receiverEmailArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                        }
                    }
                }
                if (0 != count($receiverAmountArray))
                {
                    reset($receiverAmountArray);
                    while (list($key, $value) = each($receiverAmountArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                        }
                    }
                }
            }
            elseif ("" != $trackingId)
            {
                $nvpstr = "trackingId=" . urlencode($trackingId);
                if (0 != count($receiverEmailArray))
                {
                    reset($receiverEmailArray);
                    while (list($key, $value) = each($receiverEmailArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                        }
                    }
                }
                if (0 != count($receiverAmountArray))
                {
                    reset($receiverAmountArray);
                    while (list($key, $value) = each($receiverAmountArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                        }
                    }
                }
            }
            elseif ("" != $transactionId)
            {
                $nvpstr = "transactionId=" . urlencode($transactionId);
                // the caller should only have 1 entry in the email and amount arrays
                if (0 != count($receiverEmailArray))
                {
                    reset($receiverEmailArray);
                    while (list($key, $value) = each($receiverEmailArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                        }
                    }
                }
                if (0 != count($receiverAmountArray))
                {
                    reset($receiverAmountArray);
                    while (list($key, $value) = each($receiverAmountArray))
                    {
                        if ("" != $value)
                        {
                            $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                        }
                    }
                }
            }

            /* Make the Refund call to PayPal */
            $resArray = $this->hash_call("Refund", $nvpstr);

            /* Return the response array */
            return $resArray;
        }
         public function CallPaymentDetails( $payKey, $transactionId, $trackingId )
         {
            /* Gather the information to make the PaymentDetails call.
                The variable nvpstr holds the name value pairs
            */
            $nvpstr = "";
            // conditionally required fields
            if ("" != $payKey)
            {
                $nvpstr = "payKey=" . urlencode($payKey);
            }
            elseif ("" != $transactionId)
            {
                $nvpstr = "transactionId=" . urlencode($transactionId);
            }
            elseif ("" != $trackingId)
            {
                $nvpstr = "trackingId=" . urlencode($trackingId);
            }
            /* Make the PaymentDetails call to PayPal */
            $resArray = $this->hash_call("PaymentDetails", $nvpstr);
            /* Return the response array */
            return $resArray;
        }
         public function CallPay( $actionType, $cancelUrl, $returnUrl, $currencyCode, $receiverEmailArray, $receiverAmountArray,
                        $receiverPrimaryArray, $receiverInvoiceIdArray, $feesPayer, $ipnNotificationUrl,
                        $memo, $pin, $preapprovalKey, $reverseAllParallelPaymentsOnError, $senderEmail, $trackingId,$startingDate )
         {
            /* Gather the information to make the Pay call.
                The variable nvpstr holds the name value pairs
            */
            
            // required fields
            $nvpstr = "actionType=" . urlencode($actionType) . "&currencyCode=" . urlencode($currencyCode);
            $nvpstr .= "&returnUrl=" . urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
            if($startingDate !="")
            {
                $nvpstr .="&startingDate=" . urlencode($startingDate);
            }
            if (0 != count($receiverAmountArray))
            {
                reset($receiverAmountArray);
                while (list($key, $value) = each($receiverAmountArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr .= "&receiverList.receiver(" . $key . ").amount=" . urlencode($value);
                    }
                }
            }

            if (0 != count($receiverEmailArray))
            {
                reset($receiverEmailArray);
                while (list($key, $value) = each($receiverEmailArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr .= "&receiverList.receiver(" . $key . ").email=" . urlencode($value);
                    }
                }
            }

            if (0 != count($receiverPrimaryArray))
            {
                reset($receiverPrimaryArray);
                while (list($key, $value) = each($receiverPrimaryArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").primary=" . urlencode($value);
                    }
                }
            }

            if (0 != count($receiverInvoiceIdArray))
            {
                reset($receiverInvoiceIdArray);
                while (list($key, $value) = each($receiverInvoiceIdArray))
                {
                    if ("" != $value)
                    {
                        $nvpstr = $nvpstr . "&receiverList.receiver(" . $key . ").invoiceId=" . urlencode($value);
                    }
                }
            }
        
            // optional fields
            if ("" != $feesPayer)
            {
                $nvpstr .= "&feesPayer=" . urlencode($feesPayer);
            }

            if ("" != $ipnNotificationUrl)
            {
                $nvpstr .= "&ipnNotificationUrl=" . urlencode($ipnNotificationUrl);
            }

            if ("" != $memo)
            {
                $nvpstr .= "&memo=" . urlencode($memo);
            }

            if ("" != $pin)
            {
                $nvpstr .= "&pin=" . urlencode($pin);
            }

            if ("" != $preapprovalKey)
            {
                $nvpstr .= "&preapprovalKey=" . urlencode($preapprovalKey);
            }

            if ("" != $reverseAllParallelPaymentsOnError)
            {
                $nvpstr .= "&reverseAllParallelPaymentsOnError=" . urlencode($reverseAllParallelPaymentsOnError);
            }

            if ("" != $senderEmail)
            {
                $nvpstr .= "&senderEmail=" . urlencode($senderEmail);
            }

            if ("" != $trackingId)
            {
                $nvpstr .= "&trackingId=" . urlencode($trackingId);
            }

            /* Make the Pay call to PayPal */
            
            $resArray =$this->hash_call("Pay", $nvpstr);

            /* Return the response array */
            return $resArray;
        }
         /**
         *  Prepares the parameters for the PaymentDetails API Call.
         *  The PaymentDetails call can be made with either
         *  a payKey, a trackingId, or a transactionId of a previously successful Pay call.
         * 
         * @param mixed $preapprovalKey
         * @return string
         */
         public function CallPreapprovalDetails( $preapprovalKey )
         {
            /* Gather the information to make the PreapprovalDetails call.
                The variable nvpstr holds the name value pairs
            */
 
            // required fields
            $nvpstr = "preapprovalKey=" . urlencode($preapprovalKey);

            /* Make the PreapprovalDetails call to PayPal */
            $resArray = $this->hash_call("PreapprovalDetails", $nvpstr);
            /* Return the response array */
            return $resArray;
        }
         /**
         * Prepares the parameters for the Pay API Call
         * 
         * @param mixed $returnUrl
         * @param mixed $cancelUrl
         * @param mixed $currencyCode
         * @param mixed $startingDate
         * @param mixed $endingDate
         * @param mixed $maxTotalAmountOfAllPayments
         * @param mixed $senderEmail
         * @param mixed $maxNumberOfPayments
         * @param mixed $paymentPeriod
         * @param mixed $dateOfMonth
         * @param mixed $dayOfWeek
         * @param mixed $maxAmountPerPayment
         * @param mixed $maxNumberOfPaymentsPerPeriod
         * @param mixed $pinType
         * @return string
         */
         public function CallPreapproval( $returnUrl, $cancelUrl, $currencyCode, $startingDate, $endingDate, $maxTotalAmountOfAllPayments,
                                $senderEmail, $maxNumberOfPayments, $paymentPeriod, $dateOfMonth, $dayOfWeek,
                                $maxAmountPerPayment, $maxNumberOfPaymentsPerPeriod, $pinType )
         {
            /* Gather the information to make the Preapproval call.
                The variable nvpstr holds the name value pairs
            */
            
            // required fields
            $nvpstr = "returnUrl=" . urlencode($returnUrl) . "&cancelUrl=" . urlencode($cancelUrl);
            $nvpstr .= "&currencyCode=" . urlencode($currencyCode) . "&startingDate=" . urlencode($startingDate);
            $nvpstr .= "&endingDate=" . urlencode($endingDate);
            $nvpstr .= "&maxTotalAmountOfAllPayments=" . urlencode($maxTotalAmountOfAllPayments);
            
            // optional fields
            if ("" != $senderEmail)
            {
                $nvpstr .= "&senderEmail=" . urlencode($senderEmail);
            }

            if ("" != $maxNumberOfPayments)
            {
                $nvpstr .= "&maxNumberOfPayments=" . urlencode($maxNumberOfPayments);
            }
            
            if ("" != $paymentPeriod)
            {
                $nvpstr .= "&paymentPeriod=" . urlencode($paymentPeriod);
            }

            if ("" != $dateOfMonth)
            {
                $nvpstr .= "&dateOfMonth=" . urlencode($dateOfMonth);
            }

            if ("" != $dayOfWeek)
            {
                $nvpstr .= "&dayOfWeek=" . urlencode($dayOfWeek);
            }

            if ("" != $maxAmountPerPayment)
            {
                $nvpstr .= "&maxAmountPerPayment=" . urlencode($maxAmountPerPayment);
            }

            if ("" != $maxNumberOfPaymentsPerPeriod)
            {
                $nvpstr .= "&maxNumberOfPaymentsPerPeriod=" . urlencode($maxNumberOfPaymentsPerPeriod);
            }

            if ("" != $pinType)
            {
                $nvpstr .= "&pinType=" . urlencode($pinType);
            }

            /* Make the Preapproval call to PayPal */
            $resArray = $this->hash_call("Preapproval", $nvpstr);

            /* Return the response array */
            return $resArray;
        }
         /**
         * Redirects to PayPal.com site.
         *           
         * @param mixed $cmd is the querystring
         */
         public function Redirect ( $cmd )
         {
            // Redirect to paypal.com here
            //global $Env;

            $payPalURL = "";
            
            if ($this->Env == "sandbox") 
            {
                $payPalURL = "https://www.sandbox.paypal.com/webscr?" . $cmd;
            }
            else
            {
                $payPalURL = "https://www.paypal.com/webscr?" . $cmd;
            }

            header("Location: ".$payPalURL);
        }
         public function getErrors()
         {
             return $this->errors;
         }
         public function verifyIPN()
         {
                $payPalURL = "";
            
                if ($this->Env == "sandbox") 
                {
                    $payPalURL = "https://www.sandbox.paypal.com/webscr?" . $cmd;
                }
                else
                {
                    $payPalURL = "https://www.paypal.com/webscr?" . $cmd;
                }
                $url_parsed=parse_url($payPalURL);        

              // generate the post string from the _POST vars aswell as load the
              // _POST vars into an arry so we can play with them from the calling
              // script.
              $post_string = '';    
              foreach ($_POST as $field=>$value) { 
                 $this->ipn_data["$field"] = $value;
                 $post_string .= $field.'='.urlencode(stripslashes($value)).'&'; 
              }
              $post_string.="cmd=_notify-validate"; // append ipn command
              $errnum = "";
              $errstr = "";
              // open the connection to paypal
              $fp = fsockopen($url_parsed[host],"80",$errnum,$errstr,30); 
              if(!$fp) {
                  
                 // could not open the connection.  If loggin is on, the error message
                 // will be in the log.
                 $this->errors = "fsockopen error no. $errnum: $errstr";
                 return false;
                 
              } else { 
         
                 // Post the data back to paypal
                 fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
                 fputs($fp, "Host: $url_parsed[host]\r\n"); 
                 fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
                 fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
                 fputs($fp, "Connection: close\r\n\r\n"); 
                 fputs($fp, $post_string . "\r\n\r\n"); 

                 // loop through the response from the server and append to variable
                 while(!feof($fp)) { 
                    $this->ipn_response .= fgets($fp, 1024); 
                 } 

                 fclose($fp); // close connection

              }
              
              if (eregi("VERIFIED",$this->ipn_response)) {
          
                 // Valid IPN transaction.
                 return true;       
                 
              } else {
          
                 // Invalid IPN transaction.  Check the log for details.
                 $this->errors = 'IPN Validation Failed.';
                 return false;
                 
              }
         }
    }

?>
