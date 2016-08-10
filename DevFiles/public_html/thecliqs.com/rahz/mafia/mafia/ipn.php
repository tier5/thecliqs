<?

include("funcs.php");

// read the post from PayPal system and add 'cmd'

$req = 'cmd=_notify-validate';



foreach ($_POST as $key => $value) {

  $value = urlencode(stripslashes($value));

  $req .= "&$key=$value";

}



// post back to PayPal system to validate

$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";

$header .= "Content-Type: application/x-www-form-urlencoded\r\n";

$header .= 'Content-Length: ' . strlen($req) . "\r\n\r\n";

$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);



// assign posted variables to local variables

// note: additional IPN variables also available -- see IPN documentation

$item_name = $_POST['item_name'];

$receiver_email = $_POST['receiver_email'];

$item_number = $_POST['item_number'];

$invoice = $_POST['invoice'];

$payment_status = $_POST['payment_status'];

$payment_gross = $_POST['payment_gross'];

$txn_id = $_POST['txn_id'];

$payer_email = $_POST['payer_email'];



if (!$fp) {

	$req .="&ERROR";

  // ERROR

  echo "$errstr ($errno)";

} else {

  fputs ($fp, $header . $req);

  while (!feof($fp)) {

    $res = fgets ($fp, 1024);

    if (strcmp ($res, "VERIFIED") == 0) {

				$req .="&FAILED";

        echo "<pre>";

        print_r($_POST);

        if($_POST[payment_status]=="Completed") {



if ((!fetch("SELECT tranid FROM $tab[paypal] WHERE tranid='$txn_id';")) && ($business == "$paypal_email_address"))

   {





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



        $expires=$time+2592000;//120 days

        $total=$payment_gross-$payment_fee;

//update database to add turns to user in game they bought for

        mysql_query("UPDATE $tab[user] SET status='supporter', statusexpire='$expires', credits=credits+$turns WHERE username='$custom'");

//insert into database paypal information

        mysql_query("INSERT INTO $tab[paypal] (tranid,amount,fee,user,datebought) VALUES ('$txn_id','$payment_gross','$payment_fee','$custom','$time');");

//send email to admin about transaction from paypal--ADMIN-check database to make sure transaction went through to game

        mail_2("$turns credits were bought!","\nDear  ".$site[name].",\n\nYou just received a payment from $custom for $turns credits\n\nCost: $$payment_gross\nFee: $$payment_fee\n----------\nTotal: $$total","$paypal_email_address");



   }else{echo"Cannot refresh transaction!";}



        }

      // check the payment_status is Completed

      // check that txn_id has not been previously processed

      // check that receiver_email is an email address in your PayPal account

      // process payment

      }

      else if (strcmp ($res, "INVALID") == 0) {

      	$req .="&FAILURE";

				// log for manual investigation

      }

  }

  fclose ($fp);

}
?>