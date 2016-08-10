<?php

include("html.php"); GAMEHEADER("Bank"); 



$bar = mysql_fetch_array(mysql_query("SELECT trn,bank,money,crack,condom,whore,thug,weed,networth FROM {$tab['pimp']} WHERE id='$id';"));

 ?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">

  <tr>

    <td valign="top"><div align="center"><font color="#ff0000"><img src="/new/BANK-bank.jpg" width="100" height="100" /><br />
          <font size="4">The Bank</font></b></font><font size="4"></font><font size="2"></font><br />
          <strong>Banking</strong> costs 10 turns each time.<br>
        <br>

      Current Money: $

      <?=number_format($bar[2])?>

      <br />

      <br />

      Bank Balance: $

      <?=number_format($bar[1])?>

      <br />

      <br />

      <?php 

$cashtotal = ($bar[2] + $bar[1]);

$max_dep = round($bar[networth] * .25);

$formatmax = round(number_format($max_dep));

$dep_leftt = ($max_dep - $bar[1]);

if($dep_leftt < 0){ $dep_leftt = 0;}

$formatleft = number_format($dep_left);

$maxdraw = $bar[bank];

  if($max_dep < $bar[1]){ $show = "MAXED";}

else{ $show =" $".number_format($max_dep)."";}



//Print "<font color=ff0000>Interest APR: 1.67%</font><br>";

Print "<font color=ff0000>Max Deposit Amount: $show</font><br>";

Print "<font color=ff0000>Already Deposited: $".number_format($bar[1])."</font><br>";

Print "<font color=ff0000>Deposited Left: $".number_format($dep_leftt)."</font><br><Br>";

//Print "<font color=red>You have enough room left in your bank acct for $$dep_left more dollars.</font><br>";

?>

      </div>
      <form method="post" action="bank.php?action=withdraw&amp;tru=<?=$tru?>&amp;auto=yes">

        <div align="center">
          <?php

print "<table><tr><td><input type=submit value=withdraw> <input type=hidden maxlength=15 value=$maxdraw name=with></td>";

?>
          </div>
      </form>

      <form method="post" action="bank.php?action=deposit&amp;tru=<?=$tru?>&amp;auto=yes">

        <div align="center">
          <?php

print "<td><input type=submit value=deposit> <input type=hidden maxlength=10 value=$max_dep name=dep></td></tr></table>";

?>
          </div>
      </form>
      <div align="center"><br />
        OR<br />
      </div>
      <form method="post" action="bank.php?action=withdraw&amp;tru=<?=$tru?>&amp;auto=no">

        <div align="center">
          <?php

print "<table><tr><td><input type=submit value=withdraw> <input type=text maxlength=18 name=with></td></tr></table>";

?>
          </div>
      </form>

      <form method="post" action="bank.php?action=deposit&amp;tru=<?=$tru?>&amp;auto=no">

        <div align="center">
          <?php

print "<table><tr><td><input type=submit value=deposit> <input type=text maxlength=18 name=dep></td></tr></table>";

?>
          </div>
      </form>

      <div align="center">
        <?php

if (($action == withdraw) && ($auto == yes)){



$with = floor($with);

//Added line above. It removes all junk from the supplied input other than the number. It also takes care of any decimals.



	if ($with > $bar[1] || $with <= 0) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You can't withdraw that amount! You Cheatin Bitch!</font></a></td></tr></table>";

	}else if ($bar[0] <= 9) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You need 10 turns to perform this action.</font></a></td></tr></table>";

	}else{

	//log files

$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));

$logpimp = $userlog[0];

$action = "withdrew $with from his bank";

			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");

			  

		mysql_query("update {$tab['pimp']} set money=money+$with, networth=networth+$with, trn=trn-10 where id='$id'");

		mysql_query("update {$tab['pimp']} set bank=bank-$with where id='$id'");

		print "<table class='darkbox'><tr><td align='center'>You withdrew $$with.</font></a></td></tr></table>";

	}

}





if (($action == deposit) && ($auto == yes)) {



$dep = floor($dep);

//Added line above. It removes all junk from the supplied input other than the number. It also takes care of any decimals.



	if ($dep > $bar[2] || $dep <= 0) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You dont have that much!</font></a></td></tr></table>";

	}else if ($dep + $bar[1] > $max_dep) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You can only deposit $$max_dep</font></a></td></tr></table>";

	}else if ($bar[0] <= 9) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You need 10 turns to perform this action.</font></a></td></tr></table>";

	}else{

	$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));

$logpimp = $userlog[0];

$action = "deposited $dep to his bank";

			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");

			  

		mysql_query("update {$tab['pimp']} set money=money-$dep, networth=networth-$dep, trn=trn-10 where id='$id'");

		mysql_query("update {$tab['pimp']} set bank=bank+$dep where id='$id'");

		print "You deposited $$dep.";

	}

}

?>      
        <?php

if (($action == withdraw) && ($auto == no)){



$with = floor($with);

//Added line above. It removes all junk from the supplied input other than the number. It also takes care of any decimals.



	if ($with > $bar[1] || $with <= 0) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You can't withdraw that amount! You Cheatin Bitch!</font></a></td></tr></table>";

		}else if ($bar[0] <= 9) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You need 10 turns to perform this action.</font></a></td></tr></table>";

		}else{

	//log files

$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));

$logpimp = $userlog[0];

$action = "withdrew $with from his bank";

			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");

			  

		mysql_query("update {$tab['pimp']} set money=money+$with, networth=networth+$with, trn=trn-10 where id='$id'");

		mysql_query("update {$tab['pimp']} set bank=bank-$with where id='$id'");

		print "<table class='darkbox'><tr><td align='center'>You withdrew $$with.</font></a></td></tr></table>";

	}

}





if (($action == deposit) && ($auto == no)) {



$dep = floor($dep);

//Added line above. It removes all junk from the supplied input other than the number. It also takes care of any decimals.



	if ($dep > $bar[2] || $dep <= 0) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You dont have that much!</font></a></td></tr></table>";

	}else if ($dep + $bar[1] > $max_dep) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You can only deposit $$max_dep</font></a></td></tr></table>";

	}else if ($bar[0] <= 9) {

		print "<table class='darkbox'><tr><td align='center'>ERROR: You need 10 turns to perform this action.</font></a></td></tr></table>";

	}else{

	$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));

$logpimp = $userlog[0];

$action = "deposited $dep to his bank";

			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");

			  

		mysql_query("update {$tab['pimp']} set money=money-$dep, networth=networth-$dep, trn=trn-10 where id='$id'");

		mysql_query("update {$tab['pimp']} set bank=bank+$dep where id='$id'");

		print "You deposited $$dep.";

	}

}

?>
        
        <br />
        
        <br />
    </div></td>
  </tr>
  <tr>
    <td valign="top"><div align="center"></div></td>
  </tr>
  <tr>
    <td valign="top"><div align="center">
      <?

require_once("echo_setup.php");

require_once("hitmen.php");



$cancel_sent = form_table($db1, "r".$tru."_money_transfer", "id", "cancel_sent");

$accept = form_table($db1, "r".$tru."_money_transfer", "id", "accept");

$deny = form_table($db1, "r".$tru."_money_transfer", "id", "deny");



$row['send_to'] == trim($row['send_to']);





if ($cancel_sent) {

    $row = $db1->getRow("SELECT t.sender_id,

							    t.receiver_id,

								t.amount,

								p.pimp as receiver

						   FROM r".$tru."_money_transfer t,

						   	    r".$tru."_pimp p

						  WHERE t.id = $cancel_sent

						    AND t.receiver_id = p.id");

						  

	if ($row['sender_id'] == $id) {	    

		list($my_money, $my_networth) = $db1->getRow("SELECT money, networth FROM ".$tab['pimp']." WHERE id = $id");

		$db1->updateFields($tab['pimp'], "id", $id, array("money", "networth"), array($my_money+$row['amount'], $my_networth+$row['amount']));

		$name = $db1->getField($tab['pimp'], "id", $id, "pimp");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('$id','".$row['receiver_id']."','<b>$name</b> has just canceled a money transfer to you', '$time','transfers','no')");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('".$row['receiver_id']."','$id','<b>$name</b> has just canceled a money transfer to you', '$time','senttransfers','no')");

		mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='".$row['receiver_id']."'");

		$echo = "I have just canceled a money transfer to $name <br> <a href=\"bank.php?tru=$tru\">Click Here to Refresh the list</a>"; 

		$db1->deleteRow("r".$tru."_money_transfer", "id", $cancel_sent);

		$db1->insertFields($tab['logs'], array("time", "round", "pimpname", "action", "ip"), array(time("U"), $tru, $name, $name." canceled money transfer of ".$row['amount'] . " to ".$row['receiver'], $REMOTE_ADDR));	

	}					  

}



if ($deny) {

    $row = $db1->getRow("SELECT t.sender_id,

							    t.receiver_id,

								t.amount,

								p.pimp as sender

						   FROM r".$tru."_money_transfer t,

						   	    r".$tru."_pimp p

						  WHERE t.id = $deny

						    AND t.sender_id = p.id");

						  

	if ($row['receiver_id'] == $id) {	    

		list($his_money, $his_networth) = $db1->getRow("SELECT money, networth FROM ".$tab['pimp']." WHERE id = ".$row['sender_id']);

		$db1->updateFields($tab['pimp'], "id", $row['sender_id'], array("money", "networth"), array($his_money+$row['amount'], $his_networth+$row['amount']));

		$name = $db1->getField($tab['pimp'], "id", $id, "pimp");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('$id','".$row['sender_id']."','<b>$name</b> has just denied your money transfer', '$time','transfers','no')");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('".$row['sender_id']."','$id','<b>$name</b> has just denied your money transfer', '$time','senttransfers','no')");

		mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='".$row['sender_id']."'");

		$echo = "You have denied the money request <br> <a href=\"bank.php?tru=$tru\">Click Here to Refresh the list</a>"; 

		$db1->deleteRow("r".$tru."_money_transfer", "id", $deny);

		$db1->insertFields($tab['logs'], array("time", "round", "pimpname", "action", "ip"), array(time("U"), $tru, $name, $name." denied money transfer of ".$row['amount'] . " from ".$row['sender'], $REMOTE_ADDR));	

	}					  

}



if ($accept) {

    $row = $db1->getRow("SELECT t.sender_id,

							    t.receiver_id,

								t.amount,

								p.pimp as sender

						   FROM r".$tru."_money_transfer t,

						   	    r".$tru."_pimp p

						  WHERE t.id = $accept

						    AND t.sender_id = p.id");

						  	//$to_funds  = $db1->getField($tab['pimp'], "id", $receiver_id);



	if ($row['receiver_id'] == $id) {	    

		list($my_money, $my_networth) = $db1->getRow("SELECT money, networth FROM ".$tab['pimp']." WHERE id = $id");

		$db1->updateFields($tab['pimp'], "id", $id, array("money", "networth"), array($my_money+$row['amount'], $my_networth+$row['amount']));

		$name = $db1->getField($tab['pimp'], "id", $id, "pimp");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('$id','".$row['sender_id']."','<b>$name</b> has just accepted your money transfer', '$time','transfers','no')");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('".$row['sender_id']."','$id','<b>$name</b> has just accepted your money transfer', '$time','senttransfers','no')");

		$db1->deleteRow("r".$tru."_money_transfer", "id", $accept);

		mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='".$row['sender_id']."'");

	    $echo = "You have accepted the money request <br> <a href=\"bank.php?tru=$tru\">Click Here to Refresh the list</a>"; 

		$db1->insertFields($tab['logs'], array("time", "round", "pimpname", "action", "ip"), array(time("U"), $tru, $name, $name." accepted money transfer of ".$row['amount'] . " from ".$row['sender'], $REMOTE_ADDR));	

	}					  

}



$money_sent = $db1->getAllRows("SELECT * 

								 FROM r".$tru."_money_transfer

								WHERE sender_id = $id

								  AND status = 'Pending'");



$money_received = $db1->getAllRows("SELECT * 

								 FROM r".$tru."_money_transfer

								WHERE receiver_id = $id

								  AND status = 'Pending'");							  

							  

if (form_isset("submitted")):



    $row = array();

    $error = array();

    

	$row['send_to'] = form_text("send_to");

	$row['amount'] = form_digits("amount");

	

	if (!$row['send_to'] || $row['send_to'] == '') {

	    $error['send_to'] = "This field is required.";

	}

	

	if (!$row['amount'] || $row['amount'] == '') {

	    $error['amount'] = "This field is required. You need to enter an amount bigger than 0.";

	}

	

	$receiver_id = $db1->getField($tab['pimp'], "pimp", $row['send_to'], "id");

	if (!$receiver_id) {

	    $error['send_to'] = "Mobster was not found.";

	}

	

	$available_cash = $db1->getField($tab['pimp'], "id", $id, "money");

	if ($available_cash < $row['amount']) {

	    $error['amount'] = "Not enough money to transfer this amount.";

	}

	

    $available_turns = $db1->getField($tab['pimp'], "id", $id, "trn");

#	if ($available_turns < 10) {

#	    $error['amount'] = "Not enough turns to transfer this amount. Need at least 10 turns";

#	}

	

	if (strlen($row['amount']) > '15') {

	    $error['amount'] = "Please enter an amount with max 15 digits.";

	}

	

	$to_status = $db1->getField($tab['pimp'], "id", $receiver_id, "status");

	//$to_transfered = $db1->getField($tab['pimp'], "id", $receiver_id, "transfered");

	//$to_funds  = $db1->getField($tab['pimp'], "id", $receiver_id, "crewfunds");

	

	$pending = $db1->getRow("SELECT SUM(amount) as pending FROM r".$tru."_money_transfer WHERE receiver_id = $receiver_id AND status = 'Pending'");

	$pending_amount = $pending['pending'];

	

	if (!sizeof($error)) {

		

		#add money into safe deposit and update sender's data + send message		

		$db1->insertFields("r".$tru."_money_transfer", array("sender_id", "receiver_id", "amount"), array($id, $receiver_id, $row['amount']));

		$networth = $db1->getField($tab['pimp'], "id", $id, "networth");

		$db1->updateFields($tab['pimp'], "id", $id, array("money", "networth"), array($available_cash-$row['amount'], $networth-$row['amount']));

		#$turns = $db1->getField($tab['pimp'], "id", $id, "trn");

		#$db1->updateFields($tab['pimp'], "id", $id, array("trn"), array($turns-10));

		

		$name = $db1->getField($tab['pimp'], "id", $id, "pimp");

		$to = $db1->getField($tab['pimp'], "id", $receiver_id, "pimp");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('$id','$receiver_id','<b>$name</b> has just sent you ".$row['amount']." <a href=\"bank.php?tru=$tru\"><blink>Accept / Deny</blink></a>', '$time','transfers','no')");

		$db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del) VALUES ('$id','$id','<b>$to</b> has just been sent ".$row['amount']." from you', '$time','senttransfers','no')");

		mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='$receiver_id'");

		//mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='$id'");



		$echo = "You have just sent $to ".$row['amount']." <br> <a href=\"bank.php?tru=$tru\">Click Here to Refresh the list</a>";

		$db1->insertFields($tab['logs'], array("time", "round", "pimpname", "action", "ip"), array(time("U"), $tru, $name, $name." sent ".$row['amount']." to $to", $REMOTE_ADDR));	

	}

	

endif;

?>
      <img src="/new/BANK-transfers.jpg" width="100" height="100" /><br />
      Money Transfer
      <br />
        <br />
    </div>
      <form method="post" action="bank.php?tru=<?=$tru?>">
        <div align="center">
          <input type="hidden" name="submitted" value="1" />
          <b>
            <?=$echo?>
          </b><br />
          <br />
          <table cellpadding="0" cellspacing="0" width="90%">
            <tr>
              <td width="60">Send to: </td>
              <td><? /*<input type="text" name="send_to" value="<?= $row['send_to'] ?>" />*/

		$p     = $db1->getRow("SELECT city FROM $tab[pimp] WHERE id=".$db1->escapeSql($id));

$pimps = $db1->getAllRows("SELECT id, 

                                  pimp 

                             FROM $tab[pimp] 

                            WHERE id <> $id

                         ORDER BY pimp");

						 ?>
                <select name="send_to" style="width: 142px;">
                  <? foreach ($pimps as $pimp): ?>
                  <option value="<?=$pimp['pimp'] ?>" <? if ($pimp['pimp'] == $row['send_to']): ?>selected<? endif; ?>>
                    <?= $pimp['pimp'] ?>
                  </option>
                  <? endforeach; ?>
                  </select>
                <? if ($error['send_to']): ?>
                <br />
                <span class="error">
                  <?= $error['send_to'] ?>
                </span>
                <? endif; ?></td>
            </tr>
            <tr>
              <td>Amount: </td>
              <td><input type="text" name="amount" value="<?= $row['amount'] ?>" />
                <? if ($error['amount']): ?>
                <br />
                <span class="error">
                  <?= $error['amount'] ?>
                </span>
                <? endif; ?></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input name="submit" type="submit" value="Send" /></td>
            </tr>
          </table>
        </div>
      </form>
      <div align="center">
        <? if (sizeof($money_sent)) { ?>
      </div>
      <p align="center">&nbsp;</p>
      <h2 align="center">Sent Money</h2>
      <div align="center">
        <table cellpadding="0" cellspacing="0" width="90%">
          <tr>
            <td width="175"><strong>To</strong></td>
            <td width="175"><strong>Amount</strong></td>
            <td>&nbsp;</td>
          </tr>
          <? foreach ($money_sent as $money): 

		$to = $db1->getField($tab['pimp'], 'id', $money['receiver_id'], "pimp");

		?>
          <tr>
            <td><?= $to ?></td>
            <td><?= number_format($money['amount']) ?></td>
            <td><a href="bank.php?tru=<?= $tru ?>&amp;cancel_sent=<?= $money['id'] ?>">cancel</a></td>
          </tr>
          <? endforeach; ?>
        </table>
        <? } ?>
        <? if (sizeof($money_received)) { ?>
      </div>
      <p align="center">&nbsp;</p>
      <h2 align="center">Received Money</h2>
      <div align="center">
        <table cellpadding="0" cellspacing="0" width="90%">
          <tr>
            <td width="175"><strong>From</strong></td>
            <td width="175"><strong>Amount</strong></td>
            <td>&nbsp;</td>
          </tr>
          <? foreach ($money_received as $money): 



		$from = $db1->getField($tab['pimp'], 'id', $money['sender_id'], "pimp");

		?>
          <tr>
            <td><?= $from ?></td>
            <td><?= number_format($money['amount']) ?></td>
            <td><a href="bank.php?tru=<?= $tru ?>&amp;accept=<?= $money['id'] ?>">accept</a> / <a href="bank.php?tru=<?= $tru ?>&amp;deny=<?= $money['id'] ?>">deny</a></td>
          </tr>
          <? endforeach; ?>
        </table>
        <? } ?>
    </div></td>
  </tr>
</table>

<br>

<?=bar($id)?>

<br>



<?

GAMEFOOTER();

?>