<?
include("html.php");
mysql_query("UPDATE $tab[pimp] SET page='produce drugs' WHERE id='$id'");
$pmp = mysql_fetch_array(mysql_query("SELECT trn,whore,thug,weed,condom,crack,medicine,thappy,whappy,money,bootleggers,punks FROM $tab[pimp] WHERE id='$id';"));
//edited
if($keyboard==enter)
  {
    if (($trn == "") || ($pmp[0] <= 0) || ($trn > $pmp[0]) || ($trn == 0))// || (strstr($trn,".")) || (!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $trn)))
         { $error="You don't have enough turns!"; }
    elseif($trn >= 100000){$error="you can only use up to 99,999 turns!";$trn = 0;}
    elseif(!is_numeric($trn) || $trn < 0){$error="You didn't enter a number!";$trn = 0;}
    else {

         if($pmp[7]<=55){$happiness=55;}else{$happiness=$pmp[7];}
         $formula=($happiness*0.6);
         $producedrugs=round((($pmp[2]/100)*$formula)*$trn);
         //bootleggers produce drugs
         $bProducedrugs = round((($pmp[10]/100)*$formula)*$trn);
         if($drug_type == crack){$makecrack=$producedrugs;}
         if($drug_type == dope){$makedope=$producedrugs;}
         //condoms == bootleggers alcohol
         if($drug_type == condom){$makecondom=$bProducedrugs;}
		   
         include("game_engine.php");
 
         $ifuckinghatephp = mysql_fetch_array(mysql_query("SELECT weed,crack,condom FROM $tab[pimp] WHERE id='$id';"));

         //updating
         $hoetl=fixinput($pmp[1]-$killbystd-$hoeleft); 
         $thugtl=fixinput($pmp[2]-$thugleft);
         $cashtl=fixinput($pmp[9]+$cash);
         $cracktl=fixinput($ifuckinghatephp[1]+$makecrack);
         $dopetl=fixinput($ifuckinghatephp[0]+$makedope);
		 $condoml=fixinput($ifuckinghatephp[2]+$makecondom);
         $turntl=$pmp[0]-$trn;
         if($trn >= 60){$protect=60;}else{$protect=$trn;}
         mysql_query("UPDATE $tab[pimp] SET weed='$dopetl', crack='$cracktl', condom='$condoml', trn='$turntl', money='$cashtl', whore='$hoetl', thug='$thugtl', protection='$protect', protectstarted='$time' WHERE id='$id'");

         //net updating
         $networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);
         mysql_query("UPDATE $tab[pimp] SET whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");
         }

  }

GAMEHEADER("Produce");
?>
<?
$pmp = mysql_fetch_array(mysql_query("SELECT trn,whore,thug,weed,condom,crack,medicine,thappy,whappy,money,bootleggers,punks FROM $tab[pimp] WHERE id='$id';"));
?><div align="center">
<font size="3"><b>Produce</b></font>
<br>
<font color="#FFCC00"><?=$error?></font><br>
<?if(($keyboard==enter) && (!$error)){?>
<nobr>

<?if($drug_type==crack){?>using <font color="#3366FF"><?=$trn?></font> turns, your thugs cooked up <font color="#3366FF"><?=commas($makecrack)?></font> coke 
<?
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "made $makecrack crack";
?><?if($makecrack != 1){echo"s";}?>.<?}?>
<?if($drug_type==dope){?>
<br />
using <font color="#3366FF"><?=$trn?></font> turns, your thugs grew up <?
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "made $makedope dope";
?><font color="#3366FF"><?=commas($makedope)?></font> gram<?if($makedope != 1){echo"s";}?> of dope.<?}?>
<?if($drug_type==condom){?>
<br />
using <font color="#3366FF"><?=$trn?></font> turns, your bootleggers brewed up  
<?
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "made $makecondom alcohol";
?>
<font color="#3366FF"><?=commas($makecondom)?></font> bottle<?if($makecondom != 2){echo"s";}?> of alcohol.<?}?>
<br>
your units returned with an amount of $ <font color="#3366FF"><?=commas($cash)?></font> in cash.</b>
</nobr>
<br>
<?}?></div>
<table width="500" align="center" cellspacing="0" cellpadding="12" border="0">
  <tr>
    <td>
<form method="post" action="produce.php?tru=<?=$tru?>" onSubmit="this.coke1.disabled = true;">
  <div align="center"><b><img src="/new/BLACKMARKET-coke.jpg" width="100" height="100" /><br />Produce Coke</b><br>
You've got <span class="bk"><?=commas($pmp[thug])?></span> thugs working for you.<br><br>Use <input type="text" class="text" maxlength="6" size="6" name="trn" value="<?if($trn){echo"$trn";}?>">
      turns producing coke.
        <input type="hidden" value="crack" name="drug_type" checked="<? echo ($drug_type == 'crack') ? "selected" : "false"; ?>" /><br>
<input type="hidden" name="keyboard" value="enter"><input type="submit" class="button" name="produce" value="produce">
</span>
</form></td>
    <td><form method="post" action="produce.php?tru=<?=$tru?>" onSubmit="this.dope1.disabled = true;"><div align="center"><b><img src="/new/BLACKMARKET-weed.jpg" width="100" height="100" /><br />Produce Weed</b><br>
  You've got <span class="bk"><?=commas($pmp[thug])?></span> thugs working for you.<br><br>Use <input type="text" class="text" maxlength="6" size="6" name="trn" value="<?if($trn){echo"$trn";}?>"> 
  turns producing dope.<br><input type="hidden" value="dope" name="drug_type" checked="<? echo ($drug_type == 'dope') ? "selected" : "false"; ?>" /><input type="hidden" name="keyboard" value="enter"><input type="submit" class="button" name="produce" value="produce">
</span>
</form></td>
  </tr>
  <tr>
    <td><form method="post" action="produce.php?tru=<?=$tru?>" onSubmit="this.alcohol1.disabled = true;"><div align="center"><b><img src="/new/BLACKMARKET-beer.jpg" width="100" height="100" /><br />Bootleg Alcohol</b><br>
You've got <span class="bk"><?=commas($pmp[bootleggers])?></span> bootleggers working for you.<br><br>Use  <input type="text" class="text" maxlength="6" size="6" name="trn" value="<?if($trn){echo"$trn";}?>">
           	          turns producing alcohol
           	                <input type="hidden" value="condom" name="drug_type" checked="<? echo ($drug_type == 'condom') ? "selected" : "false"; ?>" /><br>
<input type="hidden" name="keyboard" value="enter"><input type="submit" class="button" name="produce" value="produce">
</span>
</form></td>
    <td><form method="post" action="produce.php?tru=<?=$tru?>" onSubmit="this.cash1.disabled = true;"><div align="center"><b><img src="/pics/cash.jpg" width="131" height="69" /><br />Produce Cash</b><br>You've got <span class="bk"><?=commas($pmp[punks])?></span> punks working for you.<br><br>Use <input type="text" class="text" maxlength="3" size="5" name="trn" value="<?if($trn){echo"$trn";}?>"> 
turns printing fake money.<input type="hidden" value="cash" name="drug_type" checked="<? echo ($drug_type == 'cash') ? "selected" : "false"; ?>" /><br>
<input type="hidden" name="keyboard" value="enter"><input type="submit" class="button" name="produce" value="produce">
</span>
</form></td>
  </tr>
</table>
<br />
<br />
<?=bar($id)?>
<br />
<br />
<?
GAMEFOOTER();
?>