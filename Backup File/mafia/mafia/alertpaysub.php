<?
if($_POST[ap_amount] == "5.00"){$turns="1"; $turd="1"; $sexpires=10; $expires=$time+864000;}
    elseif($_POST[ap_amount] == "15.00"){$turns="2"; $turd="2"; $sexpires=10; $expires=$time+864000;}
    elseif($_POST[ap_amount] == "30.00"){$turns="3"; $turd="3"; $sexpires=10; $expires=$time+864000;}
    elseif($_POST[ap_amount] == "45.00"){$turns="4"; $turd="4"; $sexpires=10; $expires=$time+864000;}
    else{$turns="0";}



$totalcredits = $turns;
	$payment_fee = $_POST['ap_additionalcharges']+$_POST['ap_shippingcharges']+$_POST['ap_taxamount'];;
    $total=$HTTP_POST_VARS['payment_gross']-$HTTP_POST_VARS['payment_fee'];
   
    mysql_query("UPDATE $tab[pimp] SET status='supporter'"); 
    mysql_query("UPDATE $tab[user] SET status='supporter', subexpires='$sexpires', statusexpire='$expires', subscribe='$turd', credits=credits+$turns WHERE username='$_POST[apc_1]'");
	
    mysql_query("INSERT INTO $tab[paypal] (tranid,amount,fee,user,datebought) VALUES ('$_POST[ap_referencenumber]','$_POST[ap_amount]','$payment_fee','$_POST[apc_1]','$time');");
    mail_2("You have a new subscriber!","\nDear  admin,\n\nYou just received a subscription payment from $_POST[apc_1] for subscription $turns \n\nCost: $$_POST[ap_amount]\nFee: $$payment_fee\n----------\nTotal: $$total","$paypal_email_address");
    


?>