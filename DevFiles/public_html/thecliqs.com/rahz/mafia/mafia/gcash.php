<?
include("html.php");
$user = mysql_fetch_array(mysql_query("SELECT username,cash FROM $tab[user] WHERE id='$id';"));

if($cost){
	is_numeric($cost);
}

if($credits){
	is_numeric($credits);
}

//200.00 buy
if ($buy200 == yes && $user[1] >= $cost)
{
	$credits = 90000;
	$cost = 200;
       mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}

//100.00 buy
if ($buy100 == yes && $user[1] >= $cost)
{
	$credits = 45000;
	$cost = 100;
	mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}

//50.00 buy
if ($buy50 == yes && $user[1] >= $cost)
{
	$credits = 20000;
	$cost = 50;
	mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}

//25.00 buy
if ($buy25 == yes && $user[1] >= $cost)
{
	$credits = 8000;
	$cost = 25;
	mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}

//10.00 buy
if ($buy10 == yes && $user[1] >= $cost)
{
	$credits = 3000;
	$cost = 10;
	mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}

//5.00 buy
if ($buy5 == yes && $user[1] >= $cost)
{
	$credits = 1000;
	$cost = 5;
	mysql_query("UPDATE $tab[user] SET credits=credits+$credits, cash=cash-$cost WHERE id='$id'");
       $error="You have purchased $credits credits with $cash gcash and the credits have been added to your account now.";

       	   //log files
$logpimp = $user[0];
$action = "purchased $credits credits with $cash gcash";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','<font color=GREEN>GCASH</font>','$logpimp','$action','$REMOTE_ADDR');");
}



$usert = mysql_fetch_array(mysql_query("SELECT username,cash FROM $tab[user] WHERE id='$id';"));

$menu='pimp/';
secureheader();
siteheader();
?>
   <style type="text/css">

<!--
.hjk {
	font-weight: bold;
}
.gyui {
	text-align: center;
}
.tty {
	font-size: 16px;
}

-->

   </style>

<body class="jjj"><span class="jjj"></span>
<table border=0 cellpadding=0 cellspacing=0 width="100%">

	<tr valign="top">

	<td>

	<table border=0 cellpadding=0 cellspacing=0 width="100%" >

	<tr align="center" valign="top"><td><table width="100%" height="100%">

	    <tr>

	      <td align="center" valign="top">

	        <table cellspacing="5">

	        <tr>

	          <td>

  <?if($error){echo"<b><Font color=FFCC00>$error</font></b><br><br>";}?>
    
      <br />
      <br />
      <span class="gyui">You currently have $<?=commas($usert["cash"])?> GCash available for use.  Get more completing offers, or purchase with your mobile phone, home phone, etc. <a href="onreward.php">here</a><br /><br />

Purchase with GCash Below and it will automatically add them to your account:</span>
<table width="88%" border="0" align="center" cellpadding="10" cellspacing="0">
  
  <tr class="hjk">

    <td width="175" class="style1"><small>price</small></td>

      <td width="258" class="style1"><small>credits </small></td>

      <td width="154" align="center"><small>GCash purchase</small></td>
      </tr>

  <tr  >
    <td class="style1"><span class="tty">$200.00</span></td>
    <td class="style1"><span class="tty">90,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 200){?>
      <a href="?buy200=yes&credits=90000&cost=200">Purchase now</a><?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>

  <tr  >
    <td class="style1"><span class="tty">$100.00</span></td>
    <td class="style1"><span class="tty">45,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 100){?>
      <a href="?buy100=yes&credits=45000&cost=100">Purchase now</a>
      <?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>

  <tr  >
    <td class="style1"><span class="tty">$50.00</span></td>
    <td class="style1"><span class="tty">20,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 50){?>
      <a href="?buy50=yes&credits=20000&cost=50">Purchase now</a>
      <?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>

  <tr  >
    <td class="style1"><span class="tty">$25.00</span></td>
    <td class="style1"><span class="tty">8,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 25){?>
      <a href="?buy25=yes&credits=8000&cost=25">Purchase now</a>
      <?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>


  <tr  >
    <td class="style1"><span class="tty">$10.00</span></td>
    <td class="style1"><span class="tty">3,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 10){?>
      <a href="?buy10=yes&credits=3000&cost=10">Purchase now</a>
      <?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>


  <tr>
    <td class="style1"><span class="tty">$5.00</span></td>
    <td class="style1"><span class="tty">1,000</span></td>
    <td align="center"><span class="tty">
      <? if($usert["cash"] >= 5){?>
      <a href="?buy5=yes&credits=1000&cost=5">Purchase now</a>
      <?}else{?>
      <a href="onreward.php">Earn more now</a>
      <?}?>
    </span></td>
    </tr>
  </table>

<p><br />

     </td>

    </tr>

   </table>     </td>

    </tr>

   </table>     </td>

    </tr>

   </table>     </td>

    </tr>

   </table>

<?

sitefooter();

?>