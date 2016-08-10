<?
include("html.php");

$pmp = mysql_fetch_array(mysql_query("SELECT trn,whore,thug,weed,condom,crack,medicine,thappy,whappy,money FROM $tab[pimp] WHERE id='$id';"));
$pimp = mysql_fetch_array(mysql_query("SELECT whappy,thappy,glock,shotgun,uzi,ak47,whore,condom,medicine,crack,thug,hitmen,bodyguards,dealers,bootleggers,hustlers,punks FROM $tab[pimp] WHERE id='$id';"));

$pimppp = mysql_fetch_array(mysql_query("SELECT networth,crew,trn,attout,city,thug,lowrider,crack,weed,condom,whore,medicine,thappy,whappy,money,pimp,glock,shotgun,uzi,ak47,attin,whorek,thugk,status,bank,cashstolen,hitmen,bodyguards FROM $tab[pimp] WHERE id='$id';"));


if($keyboard==enter)
  {
      if (($trn == "") || ($pmp[0] <= 0) || ($trn > $pmp[0]) || ($trn == 0) || (strstr($trn,".")) || (!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $trn)))
         { $error="You don't have enough turns!";$trn = 0; }
    elseif($trn >= 100000){$error="you can only use up to 99,999 turns!";$trn = 0;}
	elseif(!is_numeric($trn) || $trn < 0){$error="You didn't enter a number!";$trn = 0;}
	
    else {

         if($pmp[8]<=55){$happiness=55;}else{$happiness=$pmp[8];}
         $formula=rand(10,30);
         
         //Check what people to use depending on what to produce
         $usePeople = 0;
         if(isset($xID)) $usePeople = $pimp[13];
         else if(isset($xID1)) $usePeople = $pimp[6];
         else if(isset($xID2)) $usePeople = $pimp[15];
         else if(isset($xID3)) $usePeople = $pimp[16];
         
         $getpaid=round(($usePeople*$formula)*$trn);

         //Don't need the game_engine here
         //include("game_engine.php");
 
         //updating
         $hoetl=fixinput($pmp[1]+$hoe-$killbystd-$hoeleft); 
         $thugtl=fixinput($pmp[2]+$thug-$thugleft);
         $cashtl=fixinput($pmp[9]+$cash+$getpaid);
         $turntl=$pmp[0]-$trn;
         if($trn >= 60){$protect=60;}else{$protect=$trn;}
         mysql_query("UPDATE $tab[pimp] SET trn='$turntl', money='$cashtl', whore='$hoetl', thug='$thugtl', protection='$protect', protectstarted='$time' WHERE id='$id'");

//USE THOSE RESOURCES
$usedope=dope($pimppp[8],$pimppp[5],2);
$usecondom=condom($pimppp[9],$pimppp[10],2);
$usecrack=crack($pimppp[7],$pimppp[10],2);
mysql_query("UPDATE $tab[pimp] SET condom=$usecondom, crack=$usecrack, weed=$usedope WHERE id='$id'");

//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "made $cash $getpaid collecting with $trn turns";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");
			  
         //net update
         $networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);
         mysql_query("UPDATE $tab[pimp] SET whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");
         }
}
GAMEHEADER("Get some cash");
?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form method=post action="collect.php?tru=<?=$tru?>">
<font size="3" color="#ffffff"><b>Collect</b></font>
<br><font color="#FFCC00"><?=$error?></font><br>
<?if(($keyboard==enter) && (!$error)){?>
<nobr>

<b><font color="#B0C4DE">using <?=$trn?> turns, your units brought in $<?=commas($cash+$getpaid)?> cash.</font></b>
</nobr>
<br>
<?}?>
<? 
	
?>
<table width="100%" border="0" cellspacing="10">
        <tr>
    <td>
              <div align="center"><b><img src="/pics/cards.jpg" width="131" height="69" /><br />Roll out casino tables</b><br>
              you got <span class="bk"><?=commas($pimp[13])?></span> card dealers<br>
				
                <form method=post action="collect.php?tru=<?=$tru?>" onSubmit="this.carddealers.disabled = true;">use 
                <input name="trn" type="text" class="centered" id="trn" size="5" maxlength="5">

                turns to roll out your casino tables. <br>
                <input type="hidden" name="keyboard" value="enter">
                <input name="carddealers" type="submit" class="button" id="carddealers" value="roll out" onClick="document.getElementById('xID').value=12">
                  	<input id="xID" type="hidden" name="xID" value="0">
					              
            </form></div></td>
    <td>
              <div align="center"><b><img src="/pics/bj15.gif" width="182" height="103" /><br />
              Pimp some hoes</b><br>
                you got <span class="bk"><?=commas($pimp[6])?></span> whores
				<form method=post action="collect.php?tru=<?=$tru?>" onSubmit="this.whores.disabled = true;">

                use 
                <input type="text" class="centered" maxlength="5" size="5" name="trn">
                turns to pimp your hoes.<br>
                <input type="hidden" name="keyboard" value="enter">
                <input name="whores" type="submit" class="button" id="whores" value="pimp" onClick="document.getElementById('xID1').value=12">
                  	<input id="xID1" type="hidden" name="xID1" value="0">
					              
            </form>
	  </div></td>
  </tr>

  <tr>
    <td>
              <div align="center"><b><img src="/pics/bj17.gif" width="165" height="99" /><br />
              Collect loan payments</b><br>
                you got <span class="bk"><?=commas($pimp[15])?></span> hustlers
				<form method=post action="collect.php?tru=<?=$tru?>" onSubmit="this.hustlers.disabled = true;">
                use 
                <input name="trn" type="text" class="centered" id="trn" size="5" maxlength="5">
                turns to collect loan payments. <br>

                <input type="hidden" name="keyboard" value="enter">
                <input name="hustlers" type="submit" class="button" id="hustlers" value="collect"onClick="document.getElementById('xID2').value=12">
                  	<input id="xID2" type="hidden" name="xID2" value="0">
					              
      </form></div></td>
    <td>
              <div align="center"><b><img src="/pics/cards.jpg" width="131" height="69" /></b><br />
                Push people to bet.<br>
			  you got <span class="bk"><?=commas($pimp[16])?></span> punks
				<br><form method=post action="collect.php?tru=<?=$tru?>" onSubmit="this.punks.disabled = true;">

                use 
                <input name="trn" type="text" class="centered" id="trn" size="5" maxlength="5">
                turns to push people to bet. <br>
                <input type="hidden" name="keyboard" value="enter">
                <input name="punks" type="submit" class="button" id="punks" value="hustle"onClick="document.getElementById('xID3').value=87">
                  	<input id="xID3" type="hidden" name="xID3" value="0">
					              
      </form>				</div></td>
  </tr>

</table>
<br>
<br>
</form>
<br>
<?
$pimp = mysql_fetch_array(mysql_query("SELECT whappy,thappy,glock,shotgun,uzi,ak47,whore,condom,medicine,crack,thug FROM $tab[pimp] WHERE id='$id';"));
?>

<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>
<?
GAMEFOOTER();
?>