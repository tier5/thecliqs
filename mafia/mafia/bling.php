<?
include("html.php");
$menu='pimp/';
admin();
secureheader();
siteheader();

$bling = mysql_fetch_row(mysql_query("SELECT sum(amount), sum(fee) FROM $tab[paypal];"));
$idlee = $time-86400;
$time = fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idlee;");
$reg = fetch("SELECT COUNT(id) FROM $tab[user];");
$tover = fetch("SELECT COUNT(id) FROM $tab[user] WHERE status='unverified';");
$sup = fetch("SELECT COUNT(id) FROM $tab[user] WHERE status='supporter';");
$balance=($bling[0]-$bling[1]);

?>

<p align="center">Online in the last 24 hours: 
  <?=$time?><br>
  Registered Users: <?=$reg?><br>

Unverified / Banned Users: <?=$tover?><br>
 Supporters: <?=$sup?>
</p>
<p align="center"><font color="red"><b>You currently have a account balance of <font size="+1" color="#000000">$
  <?=number_format($balance, 2, '.', '')?>
  </font></b></font>
  <br>
  <br>
   <b>showing last 30 payments</b>   </p>
  <div align="center">
    <table width="500" align="center" cellspacing="1" cellpadding="3">
      <tr>
        <td bgcolor="#cccccc"><strong><font color="#000000"> member </font></strong></td>
    <td align="center" bgcolor="#cccccc"><strong><font color="#000000"> date </font></strong></td>
    <td align="center" bgcolor="#cccccc"><strong><font color="#000000"> credits </font></strong></td>
    <td align="right" bgcolor="#cccccc"><strong><font color="#000000"> cost </font></strong></td>
   </tr>
  <?
$get = mysql_query("SELECT user,datebought,amount,fee FROM $tab[paypal] ORDER BY datebought DESC limit 30;");
while ($pay = mysql_fetch_array($get)){

    if($pay[2] == "5.00"){ $credits="1000 or silver sub"; }
elseif($pay[2] == "10.00"){ $credits="3000"; }
elseif($pay[2] == "25.00"){ $credits="8000"; }
elseif($pay[2] == "15.00"){ $credits="gold sub"; }
elseif($pay[2] == "30.00"){ $credits="plat. sub"; }
elseif($pay[2] == "50.00"){ $credits="20000"; }
elseif($pay[2] == "100.00"){ $credits="45000"; }
elseif($pay[2] == "200.00"){ $credits="95000"; }
elseif($pay[2] == "400.00"){ $credits="200000"; }
elseif($pay[2] == "600.00"){ $credits="350000"; }
elseif($pay[2] == "1000.00"){ $credits="700000"; }
  else{ $credits="0"; }

$amount=($pay[2]-$pay[3]);

    if($rank==0){$color="#cccccc";$rank++;}
elseif($rank==1){$color="#999999";$rank--;}
?>
      <tr bgcolor="<?=$color?>">
        <td><b><?=$pay[0]?></b></td>
    <td align="center"><?=date("M. j, Y", $pay[1])?></td>
    <td align="center"><?=$credits?></td>
    <td align="right">$<?=number_format($amount, 2, '.', '')?></td>
   </tr>
  <?}?>
    </table>
  </div>
  <?
sitefooter();
?>