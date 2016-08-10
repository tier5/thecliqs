<?php 
   include("funcs.php");

   //SOL
   //admin@game-script.net aka Dedicated Gaming Network, LLC
   //alert pay integration module, customised for themafia-order.com only
   
    /**/ $test=0; // Test activation 
    /**/ $debug=1; // Change this to 1 for debug mode (Turn off after and delete debug.txt)  
    /**/ $debug_file="debugger.txt"; // Debug file name (chmod to 777) 
    /**/ $encrypted_alerurl_pass="jL9NLV9b0uqHo9Vz"; // Encrypted AlertURL pass.  
    
    // Checking if debug mode is activated 
    if ($debug=="1"){ 
    $seperator="
	***********************************************
	"; // Debug seperator 
    if (!file_exists($debug_file)) {echo "The file $debug_file does not exist. Debug mode aborded."; $debug="0"; 
    }else{ 
    $fh = fopen($debug_file, 'w'); 
    fwrite($fh, "DEBUG MODE IS ACTIVATED!\n$seperator\n"); 
    } 
    } 
     
      
    // IF DEBUG MODE -> START Checking the database connection 
    if ($debug=="1"){fwrite($fh, "Loading database ... ...");} 
     
   
     
    // IF DEBUG MODE -> END ERROR! Database not connecting... 
    if ($debug=="1"){if (mysql_error()){ 
      fwrite($fh, " ERROR! Database did not load!\n".mysql_error()."\n$seperator\n"); 
       
    // IF DEBUG MODE -> END SUCCESS Database connected 
     }else{ 
      fwrite($fh, " Database loaded!\n$seperator\n"); 
    } 
    } 
     
      
    // IF DEBUG MODE -> START Loading POST vars 
    if ($debug=="1"){fwrite($fh, "Loading POST vars ... ...");}  
      
    // All the data is inserted into a string separated by $$$ 
    // It will then be sanitized and exploded 
    // To retrieve the corresponded var use $alertpay_data[0] 
    // Replace 0 by the number beside the $_POST 
      
    // Security code variable 
    $alertpay=$_POST['ap_securitycode']."$$$"; //0 

    // Customer info variables 
    $alertpay.=$_POST['ap_custfirstName']."$$$"; //1 
    $alertpay.=$_POST['ap_custlastName']."$$$"; //2 
    $alertpay.=$_POST['ap_custaddress']."$$$"; //3 
    $alertpay.=$_POST['ap_custcity']."$$$"; //4 
    $alertpay.=$_POST['ap_custcountry']."$$$"; //5 
    $alertpay.=$_POST['ap_custzip']."$$$"; //6 
    $alertpay.=$_POST['ap_custemailaddress']."$$$"; //7 

    // Common transaction variables 
    $alertpay.=$_POST['ap_referencenumber']."$$$"; //8 
    $alertpay.=$_POST['ap_status']."$$$"; //9 
    $alertpay.=$_POST['ap_purchasetype']."$$$"; //10 
    $alertpay.=$_POST['ap_merchant']."$$$"; //11 
    $alertpay.=$_POST['ap_itemname']."$$$"; //12 
    $alertpay.=$_POST['ap_itemcode']."$$$"; //13 
    $alertpay.=$_POST['ap_description']."$$$"; //14 
    $alertpay.=$_POST['ap_quantity']."$$$"; //15 
    $alertpay.=$_POST['ap_amount']."$$$"; //16 
    $alertpay.=$_POST['ap_additionalcharges']."$$$"; //17 
    $alertpay.=$_POST['ap_shippingcharges']."$$$"; //18 
    $alertpay.=$_POST['ap_taxamount']."$$$"; //19 
    $alertpay.=$_POST['ap_discountamount']."$$$"; //20 
    $alertpay.=$_POST['ap_totalamount']."$$$"; //21 
    $alertpay.=$_POST['ap_currency']."$$$"; //22 

    // Custom fields 
    $alertpay.=$_POST['ap_apc_1']."$$$"; //24 
    $alertpay.=$_POST['ap_apc_2']."$$$"; //25 
    $alertpay.=$_POST['ap_apc_3']."$$$"; //26 
    $alertpay.=$_POST['ap_apc_4']."$$$"; //27 
    $alertpay.=$_POST['ap_apc_5']."$$$"; //28 
    $alertpay.=$_POST['ap_apc_6']."$$$"; //29 

    // Subscription variables 
    $alertpay.=$_POST['ap_subscriptionreferencenumber']."$$$"; //30 
    $alertpay.=$_POST['ap_timeunit']."$$$"; //31 
    $alertpay.=$_POST['ap_periodlength']."$$$"; //32 
    $alertpay.=$_POST['ap_periodcount']."$$$"; //33 
    $alertpay.=$_POST['ap_nextrundate']."$$$"; //34 
    $alertpay.=$_POST['ap_trialtimeunit']."$$$"; //35 
    $alertpay.=$_POST['ap_trialperiodlength']."$$$"; //36 
    $alertpay.=$_POST['ap_trialamount']; //37 
     
    // IF DEBUG MODE -> Checking if the security code was returned from AlerPay 
    if ($debug=="1"){if ($_POST['ap_securitycode']==""){ 
      
     // IF DEBUG MODE -> END ERROR no data received 
     fwrite($fh, " ERROR! There was a communication problem between AlertPay and this script. Vars not loaded!\n$seperator\n"); 
     // IF DEBUG MODE -> END Vars all loaded 
     }else{ 
     fwrite($fh, " Vars all loaded.\n$seperator\nYour security code is: $_POST[ap_securitycode]\n$seperator\n"); 
     } 
     
    } 
     
    // SQL Sanitizer 
    function sanitize_sql ( $mValue ) 
    { 
        $mValue = (MAGIC_QUOTES) ? $mValue : addslashes($mValue); 
        $rPattern = "/;/"; 
        return preg_replace($rPattern, '', $mValue); 
    } 
     
    // Sanitizing the string 
    $alertpay = sanitize_sql ( $alertpay ); 
     
    // IF DEBUG MODE -> END Vars are now fully sanitized 
    if ($debug=="1"){fwrite($fh, "All vars and now sanitized ($$$ are normal):\n$alertpay\n$seperator\n");} 
     
    // Exploding the $alertpay var (Use the number next to the var Ex: $alertpay_data[0] for the customer first name) 
    $alertpay_data=explode('$$$', $alertpay); 
    $transaction_data=explode('-', $alertpay_data[12]); 

    // Initialize variable 
    setSecurityCodeVariable(); 
     
    // IF DEBUG MODE -> START Autentificating Alertpay 
    if ($debug=="1"){fwrite($fh, "Autentificating Alertpay ... ... Checking if the security code matches ... ...");} 
     
        // Checking if the AlertPay code is valid 
        if ($alertpay_data[0] != $encrypted_alerurl_pass) // 
        { 
            // The Data is NOT sent by AlertPay. 
            // Take appropriate action  
             
            echo "Unauthorised Access"; 
             
            // IF DEBUG MODE -> END Autentificating of Alertpay failed 
            if ($debug=="1"){fwrite($fh, " ERROR! The security code from alertpay ($alertpay_data[0]) and the one you added in the script ($encrypted_alerurl_pass) does not match!\n$seperator\n");} 
             
        }else{ 
          
         // IF DEBUG MODE -> END Autentificating of Alertpay worked. Checking if in test mode 
         if ($debug=="1"){fwrite($fh, " Success!\n$seperator\nChecking if test mode is activated ... ...");} 
             
            if ($test == "1"){ 
                // Your site is currently being integrated with AlertPay IPN for TESTING PURPOSES 
                // ONLY. Don't store any information in your Production database and don't process 
                // this transaction as a real order. 
                 
				 mysql_query("INSERT into test(field,value,time) VALUES ('test1','$_POST[ap_amount]','$time')");
				 
				 
                // IF DEBUG MODE -> END test mode is activated 
                if ($debug=="1"){fwrite($fh, " Test mode Activated\n$seperator\n");} 
                }else{ 
              

                // IF DEBUG MODE -> END test mode is NOT activated 
               mysql_query("INSERT into test(field,value,time) VALUES ('test1','$_POST[ap_amount]','$time')");
                if ($debug=="1"){fwrite($fh, " Test is NOT Activated\n$seperator\n");} 
              
                // Initialize variables 
                setCustomerInfoVariables(); 
                setCommonTransactionVariables(); 

                // Initialize the custom field variables. 
                setCustomFields(); 

                // If the transaction is subscription-based (recurring payment), initialize the 
                // Subscription variables too. 
                 
                // IF DEBUG MODE -> START Checking is this transaction is a subscription 
                if ($debug=="1"){fwrite($fh, "Checking if this transaction is a subscription ... ...");} 
                if ($ap_PurchaseType == "Subscription") 
                { 
                    // IF DEBUG MODE -> END This ID a subscription 
                    if ($debug=="1"){fwrite($fh, " This IS a subscription!\n$seperator\n");} 
                    setSubscriptionVariables(); 
                }else{ 
                    // IF DEBUG MODE -> END This is NOT a subscription 
                    if ($debug=="1"){fwrite($fh, " REF NO: $_POST[ap_referencenumber] This is NOT a subscription!\n$seperator\n");} 
                } 

                // IF DEBUG MODE -> START checking if ref number is valid 
                if ($debug=="1"){fwrite($fh, "Checking the REF number ... ...");} 
                if (strlen($_POST['ap_referencenumber']) == 0 && $ap_TrialAmount != "0") 
                { 
                    // Invalid reference number. The reference number is invalid because the ap_ReferenceNumber doesn't 
                    // contain a value and the ap_TrialAmount is not equal to 0. 
                     
                    // IF DEBUG MODE -> END REF is NOT valid 
                    if ($debug=="1"){fwrite($fh, " ERROR! REF number is NOT valid!\n$seperator\n");} 
                } 
                else 
                { 
                  // IF DEBUG MODE -> END REF is valid. Checking Transaction status 
                  if ($debug=="1"){fwrite($fh, "Status: $_POST[ap_status]  REF is OK\n$seperator\n Checking transaction status ... ...");} 
                   
                    if ($_POST['ap_status'] == "Success") 
                    { 
                      // IF DEBUG MODE -> END Transaction has ended successfuly 
                      if ($debug=="1"){fwrite($fh, " Transaction has ended successfuly! sub? : $ap_PurchaseType , $_POST[ap_purchasetype] \n$seperator\n");} 
                        // Transaction is complete. It means that the amount was paid successfully. 
                        // Process the order here. 
                        // You can use the $alertpay_data[ to retreive the needed info 


                        // Process non-subscription order. 
                        if ($_POST['ap_purchasetype'] != "subscription") 
                        { 
                            // NOTE: The subscription variables are not applicable here. Don't use them. 
							  if ($debug=="1"){fwrite($fh, "res? $_POST[apc_2] Transaction has ended successfuly!\n$seperator\n");} 
                              
							  if($_POST['apc_2'] == 'subscription')
							    {if ($debug=="1")fwrite($fh, "including subscription handout alertpaysub !\n$seperator\n");
							    include("alertpaysub.php");}
							  else
							    {if ($debug=="1")fwrite($fh, "including credits handout alertpaycredits !\n$seperator\n");
								include("alertpaycredits.php");}
                        } 
                        // Process the subscription order. Use ap_SubscriptionReferenceNumber to uniquely identify 
                        // this particular subscription transaction. 
                        else 
                        { 
						   if ($debug=="1"){fwrite($fh, " Subscription entered!\n$seperator\n");}
                            // Check whether the trial is free or not 
                            if ($ap_TrialAmount == "0") 
                            { 
                                // Process the free trial here. 
                                // NOTE: The ap_ReferenceNumber is always empty for trial periods and therefore you 
                                // should not use it. 
                            } 
                            else 
                            { 
							  if ($debug=="1")fwrite($fh, " Subscription amount : $_POST[ap_amount] for $_POST[apc_1] type $_POST[apc_2]!\n$seperator\n");
							  include("alertpaysub.php");
                                // The is not a free trial and ap_TrialAmount contains some amount and the 
                                // ap_ReferenceNumber contains a valid transaction reference number. 
                            } 
                        } 
                    } 
                    else 
                    { 
                        // IF DEBUG MODE -> END Transaction cancelled 
                        if ($debug=="1"){fwrite($fh, " ERROR Transaction was explicitely cancelled!\n$seperator\n");} 
                        // Transaction cancelled means seller explicitely cancelled the subscription or AlertPay                                  
                        // cancelled or it was cancelled since buyer didnt have enough money after resheduling after two times. 
                        // Take Action appropriately 
                    } 
                } 
            } 
    } 

// IF DEBUG MODE -> START Checking the Globals 
if ($debug=="1"){fwrite($fh, "Loading the GOBAL variable code ... ...");} 

    // Security code variable 
    function setSecurityCodeVariable() 
    { 
     GLOBAL $_POST; 
      
     $ap_SecurityCode = $_POST['ap_securitycode']; 
      
     GLOBAL $ap_SecurityCode; 
    } 
     
    // Customer info variables 
    function setCustomerInfoVariables() 
    { 
     GLOBAL $_POST; 
      
      $ap_CustFirstName =$_POST['ap_custfirstname']; 
      $ap_CustLastName = $_POST['ap_custlastname']; 
      $ap_CustAddress = $_POST['ap_custaddress']; 
      $ap_CustCity = $_POST['ap_custcity']; 
      $ap_CustCountry = $_POST['ap_custcountry']; 
      $ap_CustZip = $_POST['ap_custzip']; 
      $ap_CustEmailAddress = $_POST['ap_custemailaddress']; 
      $ap_PurchaseType = $_POST['ap_purchasetype']; 
      $ap_Merchant = $_POST['ap_merchant']; 
       
     GLOBAL $ap_CustFirstName, $ap_CustLastName, $ap_CustAddress, $ap_CustCity, $ap_CustCountry, $ap_CustZip, $ap_CustEmailAddress, $ap_PurchaseType, $ap_Merchant; 
    } 
     
    // Common transaction variables 
    function setCommonTransactionVariables() 
    { 
     GLOBAL $_POST; 
      
     $ap_ItemName = $_POST['ap_itemname']; 
     $ap_Description = $_POST['ap_description']; 
     $ap_Quantity = $_POST['ap_quantity']; 
     $ap_Amount = $_POST['ap_amount']; 
     $ap_AdditionalCharges=$_POST['ap_additionalcharges']; 
     $ap_ShippingCharges=$_POST['ap_shippingcharges']; 
     $ap_TaxAmount=$_POST['ap_taxamount']; 
     $ap_DiscountAmount=$_POST['ap_discountamount']; 
     $ap_TotalAmount = $_POST['ap_totalamount']; 
     $ap_Currency = $_POST['ap_currency']; 
     $ap_ReferenceNumber = $_POST['ap_referencenumber']; 
     $ap_Status = $_POST['ap_status']; 
     $ap_ItemCode = $_POST['ap_itemcode']; 
     $ap_Test = $_POST['ap_test']; 
      
     GLOBAL $ap_ItemName, $ap_Description, $ap_Quantity, $ap_Amount, $ap_AdditionalCharges, $ap_ShippingCharges, $ap_TaxAmount, $ap_DiscountAmount, $ap_TotalAmount, $ap_Currency, $ap_ReferenceNumber, $ap_Status, $ap_ItemCode, $ap_Test; 
    } 
     
    // Subscription variables 
    function setSubscriptionVariables() 
    { 
     GLOBAL $_POST; 
      
     $ap_SubscriptionReferenceNumber = $_POST['ap_subscriptionreferencenumber']; 
     $ap_TimeUnit = $_POST['ap_timeunit']; 
     $ap_PeriodLength=$_POST['ap_periodlength']; 
     $ap_PeriodCount=$_POST['ap_periodcount']; 
     $ap_NextRunDate=$_POST['ap_nextrundate']; 
     $ap_TrialTimeUnit=$_POST['ap_trialtimeunit']; 
     $ap_TrialPeriodLength=$_POST['ap_trialperiodlength']; 
     $ap_TrialAmount=$_POST['ap_trialamount']; 
     
     GLOBAL $ap_SubscriptionReferenceNumber, $ap_TimeUnit, $ap_PeriodLength, $ap_PeriodCount, $ap_NextRunDate, $ap_TrialTimeUnit, $ap_TrialPeriodLength, $ap_TrialAmount; 
    } 

    // Custom fields 
    function setCustomFields() 
    { 
     GLOBAL $_POST; 
      
     $ap_Apc_1 = $_POST['apc_1']; 
     $ap_Apc_2 = $_POST['apc_2']; 
     $ap_Apc_3 = $_POST['apc_3']; 
     $ap_Apc_4 = $_POST['apc_4']; 
     $ap_Apc_5 = $_POST['apc_5']; 
     $ap_Apc_6 = $_POST['apc_6']; 
         
     GLOBAL $ap_Apc_1, $ap_Apc_2, $ap_Apc_3, $ap_Apc_4, $ap_Apc_5, $ap_Apc_6; 
    }    

// IF DEBUG MODE -> END Globals loaded 
if ($debug=="1"){fwrite($fh, " All globals where loaded.\n$seperator\nRepport was sucessfuly ended."); fclose($fh);} 

?>