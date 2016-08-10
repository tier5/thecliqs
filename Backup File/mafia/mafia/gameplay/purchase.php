<?
include("html.php");

$pmp = @mysql_fetch_array(mysql_query("SELECT pimp,glock,shotgun,uzi,ak47,lowrider,money,condom,medicine,crack,weed,thug,thappy,plane FROM $tab[pimp] WHERE id='$id';"));

GAMEHEADER("Pimp Shop");
?>
<?

if (($buy) || ($sell))
{
      if ((maxlength($condoms) == bad) ||
          (maxlength($meds) == bad) ||
          (maxlength($thugs) == bad) ||
          (maxlength($glocks) == bad) ||
          (maxlength($shotguns) == bad) ||
          (maxlength($uzis) == bad) ||
          (maxlength($ak47s) == bad) ||
          (maxlength($crack) == bad) ||
          (maxlength($dope) == bad) ||
          (maxlength($lowriders) == bad) ||
          (maxlength($planes) == bad))
          {$error='<font color="#FFCC00">You cannot buy or sell that amount at a time</font>';}
  elseif (($condoms) && (eregi_replace("([0-9]+)","",$condoms)) ||
        ($meds) && (eregi_replace("([0-9]+)","",$meds)) ||
        ($thugs) && (eregi_replace("([0-9]+)","",$thugs)) || 
        ($condoms) && (eregi_replace("([0-9]+)","",$condoms)) || 
        ($crack) && (eregi_replace("([0-9]+)","",$crack)) || 
        ($dope) && (eregi_replace("([0-9]+)","",$dope)) || 
        ($glocks) && (eregi_replace("([0-9]+)","",$glocks)) || 
        ($shotguns) && (eregi_replace("([0-9]+)","",$shotguns)) ||
        ($uzis) && (eregi_replace("([0-9]+)","",$uzis)) ||
        ($ak47s) && (eregi_replace("([0-9]+)","",$ak47s)) ||
        ($crack) && (eregi_replace("([0-9]+)","",$crack)) ||
        ($dope) && (eregi_replace("([0-9]+)","",$dope)) ||
        ($planes) && (eregi_replace("([0-9]+)","",$planes)) ||
        ($lowriders) && (eregi_replace("([0-9]+)","",$lowriders))) {?><font color="#FFCC00">No item selected</font><br><?}
   else{
       if($buy)
         {$ecryptprice1=$condoms*5;
          $ecryptprice2=$meds*20;
          $ecryptprice3=$thugs*1000;
          $ecryptprice4=$glocks*500;
          $ecryptprice5=$shotguns*1000;
          $ecryptprice6=$uzis*2500;
          $ecryptprice7=$ak47s*5000;
          $ecryptprice8=$crack*20;
          $ecryptprice9=$dope*10;
          $ecryptprice10=$lowriders*2500;
          $ecryptprice11=$planes*10000;
         }
       if($sell)
         {$ecryptprice1=$condoms*3;
		  $ecryptprice8=$crack*14;
		  $ecryptprice9=$dope*7;
		  $ecryptprice4=$glocks*375;
          $ecryptprice5=$shotguns*750;
          $ecryptprice6=$uzis*1875;
          $ecryptprice7=$ak47s*3750;
          $ecryptprice10=$lowriders*1875;
          $ecryptprice11=$planes*7500;
         }
       $cost=$ecryptprice1+$ecryptprice2+$ecryptprice3+$ecryptprice4+$ecryptprice5+$ecryptprice6+$ecryptprice7+$ecryptprice8+$ecryptprice9+$ecryptprice10+$ecryptprice11;
       if (($buy) && ($cost > $pmp[6])){?><font color="#FFCC00">You don't have enough cash.</font><br><?}
   elseif (($buy) && ($thugs)){?><font color="#FFCC00">You can't buy thugs.</font><br><?}
   elseif (($sell) && ($meds)){?><font color="#FFCC00">You can't sell meds.</font><br><?}
   elseif (($sell) && ($thugs)){?><font color="#FFCC00">You can't sell thugs.</font><br><?}
   elseif (($sell) && ($condoms > $pmp[7])){?><font color="#FFCC00">You don't have that many alcohol.</font><br><?}
   elseif (($sell) && ($dope > $pmp[10])){?><font color="#FFCC00">You don't have that many weed.</font><br><?}
   elseif (($sell) && ($crack > $pmp[9])){?><font color="#FFCC00">You don't have that many coke.</font><br><?}
   elseif (($sell) && ($glocks > $pmp[1])){?><font color="#FFCC00">You don't have that many glocks.</font><br><?}
   elseif (($sell) && ($shotguns > $pmp[2])){?><font color="#FFCC00">You don't have that many shotguns.</font><br><?}
   elseif (($sell) && ($uzis > $pmp[3])){?><font color="#FFCC00">You don't have that many uzi's.</font><br><?}
   elseif (($sell) && ($ak47s > $pmp[4])){?><font color="#FFCC00">You don't have that many ak-47's.</font><br><?}
   elseif (($sell) && ($lowriders > $pmp[5])){?><font color="#FFCC00">You don't have that many lowriders.</font><br><?}
   elseif (($sell) && ($planes > $pmp[13])){?><font color="#FFCC00">You don't have that many planes.</font><br><?}
       else{
		   if($buy)
             {$pmp[6] -= $cost;
              $pmp[7] += $condoms;
              $pmp[8] += $meds;
              $pmp[9] += $crack;
              $pmp[10] += $dope;
              $pmp[11] += $thugs;
              $pmp[1] += $glocks;
              $pmp[2] += $shotguns;
              $pmp[3] += $uzis;
              $pmp[4] += $ak47s;
              $pmp[5] += $lowriders;
              $pmp[13] += $planes;}
		   if($sell)
             {$pmp[6] += $cost;
              $pmp[7] -= $condoms;
              $pmp[10] -= $dope;
              $pmp[9] -= $crack;
              $pmp[1] -= $glocks;
              $pmp[2] -= $shotguns;
              $pmp[3] -= $uzis;
              $pmp[4] -= $ak47s;
              $pmp[5] -= $lowriders;
              $pmp[13] -= $planes;
             }
           $pmp[1]=fixinput($pmp[1]);
           $pmp[2]=fixinput($pmp[2]);
           $pmp[3]=fixinput($pmp[3]);
           $pmp[4]=fixinput($pmp[4]);
           $pmp[5]=fixinput($pmp[5]);
           $pmp[6]=fixinput($pmp[6]);
           $pmp[7]=fixinput($pmp[7]);
           $pmp[8]=fixinput($pmp[8]);
           $pmp[9]=fixinput($pmp[9]);
           $pmp[10]=fixinput($pmp[10]);
           $pmp[11]=fixinput($pmp[11]);
           $pmp[13]=fixinput($pmp[13]);
           mysql_query("UPDATE $tab[pimp] SET money='$pmp[6]', condom='$pmp[7]', medicine='$pmp[8]', crack='$pmp[9]', weed='$pmp[10]', thug='$pmp[11]', glock='$pmp[1]', shotgun='$pmp[2]', uzi='$pmp[3]', ak47='$pmp[4]', lowrider='$pmp[5]', plane='$pmp[13]' WHERE id='$id'");
           $soldfor=$pmp[6];
           $transaction=completed;
           }
       }
    //UPGRADE THERE NETWORTH
    $networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);
    mysql_query("UPDATE $tab[pimp] SET whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");
}


?>
<?
if($transaction==completed){
           if(($buy) && ($cost > 0)){?><font color="#FFCC00"><b><br />
<br />
Items Purchase:</b></font>
<? }
           if(($sell) && ($cost > 0)){?>
<font color="#FFCC00"><b><br />
Items Sold for <font color="#FFFFFF">$
<?=commas($cost)?>
</font>:</b></font><br>
<? }
           if($condoms == 1){echo "$condoms bottles of alcohol<br>";}elseif($condoms > 1){echo "$condoms bottles of alcohol<br>";}
           if($meds == 1){echo "$meds box of meds<br>";}elseif($meds > 1){echo "$meds boxes of meds<br>";}
           if($crack == 1){echo "$crack grams of coke<br>";}elseif($crack > 1){echo "$crack grams of coke<br>";}
           if($dope == 1){echo "$dope gram of dope<br>";}elseif($dope > 1){echo "$dope grams of dope<br>";}
           if($thugs == 1){echo "$thugs thug<br>";}elseif($thugs > 1){echo "$thugs thugs<br>";}
           if($glocks == 1){echo "$glocks glock<br>";}elseif($glocks > 1){echo "$glocks glocks<br>";}
           if($shotguns == 1){echo "$shotguns shotgun<br>";}elseif($shotguns > 1){echo "$shotguns shotguns<br>";}
           if($uzis == 1){echo "$uzis uzi<br>";}elseif($uzis > 1){echo "$uzis uzi's<br>";}
           if($ak47s == 1){echo "$ak47s ak-47<br>";}elseif($ak47s > 1){echo "$ak47s ak-47's<br>";}
           if($lowriders == 1){echo "$lowriders s-class limo<br>";}elseif($lowriders > 1){echo "$lowriders s-class limos<br>";}
           if($planes == 1){echo "$planes plane<br>";}elseif($planes > 1){echo "$planes planes<br>";}
}

$pimp = @mysql_fetch_array(mysql_query("SELECT whappy,thappy,glock,shotgun,uzi,ak47,whore,condom,medicine,crack,thug,planes FROM $tab[pimp] WHERE id='$id';"));
$total = $pimp[2]+$pimp[3]+$pimp[4]+$pimp[5];
?>

<?
$change=$pmp[6];

$con=round(($pimp[6]*5)-$pimp[7]); if(0 > $con){$con=0;}
$med=round(($pimp[6]*1.5)-$pimp[8]); if(0 > $med){$med=0;}
$cra=round(($pimp[6]*2.5)-$pimp[9]); if(0 > $cra){$cra=0;}

$conprice=$con;
$medprice=$med*20;
$craprice=$cra*10;

if($conprice > $change){ $con=round($change/1); $change=$change-($con*1); }
if($medprice > $change){ $med=round($change/20); $change=$change-($med*20); }
if($craprice > $change){ $cra=round($change/10); $change=$change-($cra*10); }



if ($keyboard==enter)
{
	if ($amount == '') { echo '<b>please enter an amount</b>'; }

	if ($exchange == $exchange_with)
	{
		echo '<b>can not exchange the same gun.</b>';
		exit();
	}

	if ($amount > $pmp[$exchange])
	{
		echo'<b>you dont have that many guns.</b>';
		exit();
	}

	if ($exchange == 'glock')	 $price_exchange = 500;
	if ($exchange == 'shotgun')	 $price_exchange = 1000;
	if ($exchange == 'uzi')	 $price_exchange = 2500;
	if ($exchange == 'ak47')	$price_exchange = 5000;

	if ($exchange_with == 'glock')	 $price_exchange_with = 500;
	if ($exchange_with == 'shotgun')	 $price_exchange_with = 1000;
	if ($exchange_with == 'uzi')	 $price_exchange_with = 2500;
	if ($exchange_with == 'ak47')	$price_exchange_with = 5000;


	$lost_amount = $pmp[$exchange] - $amount;
	
    mysql_query("UPDATE $tab[pimp] SET " . $exchange . " = '$lost_amount' WHERE id='$id'");

	if ($price_exchange > $price_exchange_with)
	{
		$gain_amount = $pmp[$exchange_with] + ($price_exchange / $price_exchange_with);
	}
	else
	{
		$gain_amount = $pmp[$exchange_with] + ($price_exchange_with / $price_exchange);
	}

	echo '<b>You exchanged ' . $amount . ' ' . $exchange . ' for ' . $gain_amount . ' ' . $exchange_with . '.';

	mysql_query("UPDATE $tab[pimp] SET " . $exchange_with . " = '$gain_amount' WHERE id='$id'");

}
?>
<form name="store" method="post" action="purchase.php?tru=<?=$tru?><?if($buying){?>&buying=<?=$buying?><?}elseif($pawn){?>&pawn=<?=$pawn?><?}?>">
<font size="3" color="ffffff"><b>Black Market</b></font>
<br>

<br>
<br>
<a href="purchase.php?sell=sell&glocks=<?=$pimp[2]?>&shotguns=<?=$pimp[3]?>&uzis=<?=$pimp[4]?>&ak47s=<?=$pimp[5]?>&tru=<?=$tru?>">sell all weapons</a>
<?
$change=$pmp[6];

$con=round(($pimp[6]*5)-$pimp[7]); if(0 > $con){$con=0;}
$cra=round(($pimp[6]*2.5)-$pimp[9]); if(0 > $cra){$cra=0;}

$conprice=$con;
$craprice=$cra*10;

if($conprice > $change){ $con=round($change/1); $change=$change-($con*1); }
if($craprice > $change){ $cra=round($change/10); $change=$change-($cra*10); }

?>

<br>
<br><a href="purchase.php?buy=buy&condoms=<?=$con?>&crack=<?=$cra?>&tru=<?=$tru?>">buy needed items</a>
<br />
<br />
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center"><img src="/new/BLACKMARKET-beer.jpg" width="100" height="100" /><br />
        <?=commas($pmp[7])?>
        <br />
      alcohol<br />
      $5<br />
  <input type="text" class="text" maxlength="13" size="12" name="condoms" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.condoms.value=<?=floor($pmp[6]/5)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-weed.jpg" width="100" height="100" /><br />
        <?=commas($pmp[10])?>
        <br />
      weed<br />
      $10<br />
  <input type="text" class="text" maxlength="13" size="12" name="dope" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.dope.value=<?=floor($pmp[6]/10)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-coke.jpg" width="100" height="100" /><br />
        <?=commas($pmp[9])?>
        <br />
      coke<br />
      $20<br />
  <input type="text" class="text" maxlength="13" size="12" name="crack" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.crack.value=<?=floor($pmp[6]/20)?>" value="MAX BUY" />
    </div></td>
  </tr>
</table>
<br />
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center"><img src="/new/BLACKMARKET-glock.jpg" width="100" height="100" /><br />
      
        <?=commas($pmp[1])?>
        <br />
      glock<br />
      $500<br />
  <input name="glocks" type="text" class="centered" id="glock" size="8" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.glocks.value=<?=floor($pmp[6]/500)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-shotgun.jpg" width="100" height="100" /><br />
      
        <?=commas($pmp[2])?>
        <br />
      shotgun<br />
      $1,000<br />
  <input name="shotguns" type="text" class="centered" id="shotgun" size="8" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.shotguns.value=<?=floor($pmp[6]/1000)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-uzi.jpg" width="100" height="100" /><br />
      
        <?=commas($pmp[3])?>
        <br />
      uzi<br />
      $2,500<br />
  <input name="uzis" type="text" class="centered" id="uzi" size="8" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.uzis.value=<?=floor($pmp[6]/2500)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-ak47.jpg" width="100" height="100" /><br />
      
        <?=commas($pmp[4])?>
        <br />
      ak-47<br />
      5,000$<br />
  <input name="ak47s" type="text" class="centered" id="ak47" size="8" />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.ak47s.value=<?=floor($pmp[6]/5000)?>" value="MAX BUY" />
    </div></td>
  </tr>
</table>
<br />
<table width="500" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center"><img src="/new/BLACKMARKET-car.jpg" width="184" height="100" /><br />
      Cars<br />
      Mess em up with a drive-by.<br />
  <?=commas($pmp[5])?>
  <br />
      S-Class Limo<br />
      $2,500<br />
  <input type="text" class="text" maxlength="13" size="12" name="lowriders" />
  <br />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.lowriders.value=<?=floor($pmp[6]/2500)?>" value="MAX BUY" />
    </div></td>
    <td><div align="center"><img src="/new/BLACKMARKET-planes.jpg" width="184" height="100" /><br />
      Planes<br />
      Used for travel .<br />
  <?=commas($pmp[13])?>
  <br />
      Planes<br />
      $10,000<br />
  <input name="planes" type="text" class="text" id="planes" size="12" maxlength="13" />
  <br />
  <br />
  <input name="button" type="button" style="font-size: 10px;" onclick="document.store.planes.value=<?=floor($pmp[6]/10000)?>" value="MAX BUY" />
    </div></td>
  </tr>
</table>
<br />
<input type="submit" name="buy" value="-- - - - - - - P U R C H A S E -- - - - - -" /><input type="submit" name="sell" value="-- - - - - - - S  E  L  L -- - - - - -" />
<br />
</form>
<?=bar($id)?>
<br />
<br />

<?
GAMEFOOTER();
?>