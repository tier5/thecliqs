<?
$custom = $_POST[apc_1];
$payment_gross = $_POST[ap_amount];
$payment_fee = $_POST['ap_additionalcharges']+$_POST['ap_shippingcharges']+$_POST['ap_taxamount'];;
$txn_id = $_POST[ap_referencenumber];
$item_number = $_POST[apc_3];


    if($payment_gross == "5.00"){$turns="1000";}
elseif($payment_gross == "10.00"){$turns="3000";}
elseif($payment_gross == "25.00"){$turns="8000";}
elseif($payment_gross == "50.00"){$turns="20000";}
elseif($payment_gross == "100.00"){$turns="45000";}
elseif($payment_gross == "200.00"){$turns="95000";}
elseif($payment_gross == "400.00"){$turns="200000";}
elseif($payment_gross == "600.00"){$turns="350000";}
elseif($payment_gross == "1000.00"){$turns="700000";}
else{$turns="0";}


$totalcredits = $turns;

   
$expires=$time+864000;
$total=$_POST['payment_gross']-$_POST['payment_fee'];

   

    mysql_query("UPDATE $tab[user] SET status='supporter', statusexpire='$expires', credits=credits+$totalcredits WHERE username='$custom'");

    mysql_query("INSERT INTO $tab[paypal] (tranid,amount,fee,user,datebought) VALUES ('$txn_id','$payment_gross','$payment_fee','$custom','$time');");
    
mail_2("Credits have been purchased!","\nDear  Admin,\n\nYou just received a payment from $custom for $turns credits. \n\nCost: $payment_gross\nFee: $payment_fee\n----------\nTotal: $$total","$paypal_email_address");
    


?>