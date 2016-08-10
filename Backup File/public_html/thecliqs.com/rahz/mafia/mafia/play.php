<?
include("html.php");

if($action == login){

$getcode = mysql_fetch_array(mysql_query("SELECT code FROM $tab[user] WHERE id='$id';"));
$chkban = mysql_fetch_array(mysql_query("SELECT status,code FROM "."r".$goto."_".$tab[pimp]." WHERE code='$getcode[0]';"));

  if($chkban[0] == banned){ 
    mysql_query("UPDATE "."r".$goto."_".$tab[pimp]." SET status='banned' WHERE code='$chkban[1]'");
    }
  if($chkban[0] != banned){
    mysql_query("UPDATE "."r".$goto."_".$tab[pimp]." SET status='$chkban[0]' WHERE code='$chkban[1]';");
    }

header("location: gameplay/dologin.php?code=$trupimp&tru=$goto");

}

if(($pimp) && ($city)){

$user = mysql_fetch_array(mysql_query("SELECT status,code,username FROM $tab[user] WHERE id='$id';"));
$game = mysql_fetch_array(mysql_query("SELECT type,reserves FROM $tab[game] WHERE round='$round';"));

if($user[0] == admin){ $check=admin; }else{ $check=supporter; }

    if (($game[0] == supporters) && ($user[0] != $check))
       { $msg="You must become a supporter to join this game"; }
elseif ((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $pimp)) || (strstr($pimp,".")))
       { $msg="Pimp name can only have a-Z, 0-9, -_ charactors."; }
elseif (fetch("SELECT pimp FROM "."r".$round."_".$tab[pimp]." WHERE pimp='$pimp';"))
       { $msg="Sorry, that pimpname is taken."; }
elseif (!fetch("SELECT id FROM "."r".$round."_".$tab[city]." WHERE id='$city';"))
       { $msg="Please select a city to start in.";}
elseif (fetch("SELECT user FROM $tab[stat] WHERE user='$user[2]' AND round='$round';"))
       { $msg="You have already joined that round.";}
  else {

       mysql_query("INSERT INTO "."r".$round."_".$tab[pimp]." (pimp,trn,res,city,online,code,status) VALUES ('$pimp','3000','$game[1]','$city','$time','$user[1]','$user[0]');");
       mysql_query("INSERT INTO $tab[stat] (user,round) VALUES ('$user[2]','$round');");
	   
       }

}

function printround($round,$type){
global $tab, $time, $id, $trupimp, $jackpot, $prize, $creditaddon;

$fix[0]=$round;

$game = mysql_fetch_array(mysql_query("SELECT speed,reserves,credits,crewmax,starts,ends,gamename,round FROM $tab[game] WHERE round='$round';"));
$user = mysql_fetch_array(mysql_query("SELECT status,username FROM $tab[user] WHERE id='$id';"));

?>
<b>Round: <?=$round?> | <a href="credits.php">

Credit Addon:
<?if($game[2] >= 5000000) { ?>
Unlimited
<? } else { ?>
<?=commas($game[2])?></a></b>
<?}?>

</b>
<table width="95%" height="100"> 
 <tr>
  <td align="center" width="50%">
  <?
  if(fetch("SELECT user FROM $tab[stat] WHERE user='$user[1]' AND round='$round';")){
  $player = mysql_fetch_array(mysql_query("SELECT pimp,rank,city,whore,thug,lowrider,msg FROM "."r".$round."_".$tab[pimp]." WHERE code='$trupimp';"));
  $playercity = mysql_fetch_array(mysql_query("SELECT name FROM "."r".$round."_".$tab[city]." WHERE id='$player[2]';"));
?>
  <br><?if($game[6] == pause){?>[Round Disabled]<?}else{?>
  <a href="play.php?action=login&goto=<?=$round?>">
  <font size="+1"><?=$player[0]?></font>
  <br>Ranked <?=commas($player[1])?> in <?=$playercity[0]?>
  <br />
  <br><?if($player[6] > 0){?>You have <?=$player[6]?> new messages<?}else{?>You have no new messages<?}?>
  <br>
  <br> [ </font><?if($game[4] > $time){?>Round hasnt started yet<?}else{?>Click Here To Join The Round!<?}?> ] 
  </a>
  <?}?>
  <?
  }else{?>
  <br><?if($game[6] == pause){?>[Round Disabled]<?}else{?>

  <font size="+1"><?=$type?> round
  <br><b>Speed:</b></font> <?=$game[0]?> turns every 10 mins
  <br><b>Reserves:</b></font> <?=$game[1]?> turns reserved
  <br><b>Turn / Credits Addon:</b>
 <?if($game[2] >= 5000000) { ?>
Unlimited
<? } else { ?>
<?=commas($game[2])?> credits only</a></b>
<?}?>
  <br>
  <br>

  <?
  $citydb = "r".$round."_".$tab[city];

  if($user[0] == admin){ $check=admin; }else{ $check=supporter; }

  if(($type == supporters) && ($user[0] != $check))
      {?><center><a href="credits.php"><font color="#FFFFFF">You must be a supporter to join<br></font> Click here to become one</a></center><?}
  else{?>
  <table align="center"><form method="post" action="play.php">
   <tr><td align="right">Pimp Name:</td><td><input type="text" name="pimp" size="12"></td></tr>
   <tr><td align="right">Starting Location:</td>
   <td><select name="city"><option value="">-select one-</option>
   <?$get = mysql_query("SELECT id,name FROM $citydb ORDER BY id ASC;");
    while ($city = mysql_fetch_array($get))
    {?><option value="<?=$city[0]?>"><?=$city[1]?></option><?}?></select>
   </td>
   </tr>
   <tr><td></td><td><input type="hidden" name="round" value="<?=$round?>"><input type="submit" value="Join!"></td></tr>
  </form></table>
  <?}
  }?>
  <?}?>
  <br>
       <table width="300" cellspacing="1" cellpadding="1">
         <br>
         <tr bgcolor="<?=$rankcolor?>">
          <td width=1>Rank</td>
          <td height="16" width="60%"><nobr>Pimp Name</nobr></td>
          <td align=right>Worth</td>
         </tr>
       <?
       $pmpstatus = mysql_fetch_array(mysql_query("SELECT code FROM $tab[user] WHERE status='banned';"));
       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew FROM "."r".$round."_".$tab[pimp]." WHERE rank>0 AND code!='$pmpstatus[0]' ORDER BY nrank ASC LIMIT 10;");
        while ($pimp = mysql_fetch_array($getpimps))
        {
        $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$round."_".$tab[crew]." WHERE id='$pimp[4]';"));
             if($rankstart==0){$rankcolor="#CCCCCC";$rankstart++;}
         elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}
       ?>

         <tr bgcolor="<?=$rankcolor?>">
          <td width=1><?=$pimp[3]?>.</td>
          <td height="16" width="60%"><nobr><?if($crw[0]){?><img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle"><?}?><a href="pimp.php?pid=<?=$pimp[1]?>&rnd=<?=$round?>"><?=$pimp[1]?></a></nobr></td>
          <td align=right>$<?=commas($pimp[2])?></td>
         </tr>
       <?}?>
       </table>

  </td>
 </tr>
</table>
<?if ($game[4] > $time){?>Starts in <?countdown($game[4]);}else{?>Ends in <?countdown($game[5]);}?>
<br>
<br>
<?
}

$menu='pimp/';
secureheader();
siteheader();
?>
<br />
<?php require_once("surveyy.php");?>
<br />
<table width="100%" height="100%">
    <tr>
     <td height="12"><b>Choose a game!</b></td>
    </tr>
    <tr>
     
    </tr>
    <tr>
     <td align="center" valign="top">
     <?
          if($msg == disabled){?><br><b>Your charactor has been frozen</b><br><?}
          if($msg == select){?><br><b>Please select a game.</b><br><?}
      elseif($msg){?><br><b><?=$msg?></b><br><?}?>
     <?
     $get = mysql_query("SELECT round,type FROM $tab[game] WHERE ends>'$time' ORDER BY round ASC;");
     while ($game = mysql_fetch_array($get)){ printround($game[0],$game[1]); }
     ?>
     </td>
    </tr>
</table>
<?
sitefooter();
?>