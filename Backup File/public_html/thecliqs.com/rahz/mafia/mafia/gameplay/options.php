<?
include("html.php");


if($setturns)
  {
   $setturns = strip_tags($setturns,"");
$pimp = mysql_fetch_array(mysql_query("SELECT user,pass,profile,pimp,trn,attout,defaultturns FROM $tab[pimp] WHERE id='$id';"));   
$user = mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));
$game = mysql_fetch_array(mysql_query("SELECT type,reserves FROM $tab[game] WHERE round='$round';"));

if($user[0] == admin){ $check=admin; }else{ $check=supporter; }

	if ($setturns>=301)
       { $error="Default turns may not be more than 300."; }
elseif ($check) 
       { mysql_query("UPDATE $tab[pimp] SET defaultturns='$setturns' WHERE id='$id'");
    	$error='Default Turns Set'; }       
  else 
       { mysql_query("UPDATE $tab[pimp] SET defaultturns='$setturns', trn=trn-50 WHERE id='$id'");
    	$error='Default Turns Set'; }        
 

}

if($resetattk)
  {
   $resetattk = strip_tags($resetattk,"");
$pimp = mysql_fetch_array(mysql_query("SELECT user,pass,profile,pimp,trn,attout,defaultturns FROM $tab[pimp] WHERE id='$id';"));
$user = mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));
$game = mysql_fetch_array(mysql_query("SELECT type,reserves FROM $tab[game] WHERE round='$round';"));

if($user[0] == admin){ $check=admin; }else{ $check=supporter; }

    if ($pimp[5] <= 0 )
       { $error='You do not have any attacks to reset.'; }
elseif (($check) && ($pimp[4] < 250))
       { $error='You do not have enough turns.'; }
elseif ($check)
	   {mysql_query("UPDATE $tab[pimp] SET attout='0',trn=trn-250 WHERE id='$id'");
    	$error='Attacks Reset'; }
elseif ($pimp[4] < 500)
       { $error='You do not have enough turns.'; }  
else
       { mysql_query("UPDATE $tab[pimp] SET attout='0',trn=trn-500 WHERE id='$id'");
    	$error='Attacks Reset'; } 
	
}


if($pimpname)
  {
   $pimpname = strip_tags($pimpname,"");
$pimp = mysql_fetch_array(mysql_query("SELECT user,pass,profile,pimp,trn,attout,defaultturns FROM $tab[pimp] WHERE id='$id';"));   
$user = mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));
$game = mysql_fetch_array(mysql_query("SELECT type,reserves FROM $tab[game] WHERE round='$round';"));

if($user[0] == admin){ $check=admin; }else{ $check=supporter; }

	if ((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $pimpname)) || (strstr($pimpname,".")))
       { $error="Pimp name can only have a-Z, 0-9, -_ characters."; }
elseif (fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$pimpname';"))
       { $error="Sorry, that pimpname is taken."; }
elseif ($check)
       { mysql_query("UPDATE $tab[pimp] SET pimp='$pimpname' WHERE id='$id'");
    	$error='Pimp Name Upgraded'; }
elseif ($pimp[4] < 250)
       { $error="You do not have enough turns."; }       
else   { mysql_query("UPDATE $tab[pimp] SET pimp='$pimpname', trn=trn-250 WHERE id='$id'");
    	$error='Pimp Name Upgraded'; }        

}

if($mediaurl)
  {
   $mediaurl = strip_tags($mediaurl,"");
    if(!strstr($mediaurl,"http://")){$error='You must enter a full url including http://';}
elseif(strstr($mediaurl,"java")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"php")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"cgi")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"html")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"jsp")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"options")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"account")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"bank")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"purchase")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"?")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"transfer")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,"index")){$error='Exploit found and fixed, your account has been logged';}
elseif(strstr($mediaurl,">")){$error='Please do not use < > in your url.';}
elseif(strstr($mediaurl,"<")){$error='Please do not use < > in your url.';}
elseif(strstr($mediaurl,"width")){$error='You cant set width.';}
elseif(strstr($mediaurl,"heigth")){$error='you cant set size features';}
else{
    mysql_query("UPDATE $tab[pimp] SET profile='$mediaurl' WHERE id='$id'");
    $error='Profile Media Upgraded';
    }
  }

if($remove==media){mysql_query("UPDATE $tab[pimp] SET profile='' WHERE id='$id'");}

if($description){$description=filter($description); $error='<br>Profile Description Updated';mysql_query("UPDATE $tab[pimp] SET description='$description' WHERE id='$id'");}

$pimp = mysql_fetch_array(mysql_query("SELECT user,pass,profile,pimp,trn,attout FROM $tab[pimp] WHERE id='$id';"));



if($opt==profile){GAMEHEADER("options: edit profile");/// Page to edit profile and description
?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form method="post" action="options.php?opt=profile&tru=<?=$tru?>">
<font size="+1"><b><a href="options.php?tru=<?=$tru?>">Options</font></a></b>
<?if($error){?><br><font color="#7777CC"><b><?=$error?></b></font><?}?>
<br>
<br>
<tr>
<td align="center"><a href="mobster.php?pid=<?=$pimp[3]?>&tru=<?=$tru?>">My Profile</a>
<table>
 <tr>
  <td><b>Change Media:</b> <?if($pimp[2]){?><br><small><b><font color="#7777CC"><?=$pimp[2]?></font> (<a href="options.php?opt=profile&remove=media&tru=<?=$tru?>">Remove</a>)</b></small><?}else{?><small><font color="7777CC"><b>200x200 pixels, swf, jpg, gif, png supported.</b></font></small><?}?></td>
 </tr>
 <tr>
  <td><input type="text" class="text" name="mediaurl" size="50"><br><br></td>
 </tr>
 <tr>
  <td><b>Edit Description:</b></td>
 <tr>
 <tr>
  <td><textarea class="text" name="description" cols="50" rows="5"></textarea>
 </tr>
 <tr>
  <td align="center"><input type="submit" class="button" value="apply"></td>
 </tr>
</table>
<br>
<br>
</form>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>

<?}elseif($opt==pimpnamenow){GAMEHEADER("options: Change Pimp Name");/// Page to change pimp name
?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form method="post" action="options.php?opt=pimpname&tru=<?=$tru?>">
<font size="+1"><b><a href="options.php?tru=<?=$tru?>">Options</font></a></b>
<?if($error){?><br><font color="#7777CC"><b><?=$error?></b></font><?}?>
<br>
<br>
<table>
 <tr>
  <td align="center"><b>Change Pimp Name:</b> <?if($pimp[3]){?><br><br><small><b><font color="#7777CC"><?=$pimp[3]?></font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><input type="text" class="text" name="pimpname" size="20"><br><br></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><b>Supporters may use this option for free</b><?if($pimp[3]){?><br><small><b><font color="#7777CC">Others will have 250 credits deducted from this round</font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><input type="submit" class="button" value="apply"></td>
 </tr>
</table>
<br>
<br>
</form>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>

<?}elseif($opt==attacks){GAMEHEADER("options: Reset Attacks");/// Page to Reset Attacks
?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form method="post" action="options.php?opt=attacks&tru=<?=$tru?>">
<font size="+1"><b><a href="options.php?tru=<?=$tru?>">Options</font></a></b>
<?if($error){?><br><font color="#7777CC"><b><?=$error?></b></font><?}?>
<br>
<br>
<table>
 <tr>
  <td align="center"><b>Reset Attacks:</b> <?if($pimp[5]){?><br><br><small><b><font color="#7777CC"><?=$pimp[5]?> attacks out</font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><?if($pimp[4]){?><b>Supporters may reset attacks for 250 turns</b><br><small><b><font color="#7777CC">Others will have 500 credits deducted from this round</font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><input type="submit" class="button" name="resetattk" value="Reset"></td>
 </tr>
</table>
<br>
<br>
</form>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>

<?}elseif($opt==setturns){GAMEHEADER("options: Set Default Turns");/// Page to set default turns
?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form method="post" action="options.php?opt=setturns&tru=<?=$tru?>">
<font size="+1"><b><a href="options.php?tru=<?=$tru?>">Options</font></a></b>
<?if($error){?><br><font color="#7777CC"><b><?=$error?></b></font><?}?>
<br>
<br>
<table>
 <tr>
  <td align="center"><b>Set Default Turns:</b> <?if($pimp[6]){?><br><br><small><b><font color="#7777CC"><?=$pimp[6]?></font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><input type="text" class="text" name="setturns" size="8"><br><br></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><b>Supporters may use this option for free</b><?if($pimp[3]){?><br><small><b><font color="#7777CC">Others will have 50 credits deducted from this round<br>Press the spacebar to erase your current default</font></small><?}?></td>
 </tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
 <tr>
  <td align="center"><input type="submit" class="button" value="apply"></td>
 </tr>
</table>
<br>
<br>
</form>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>

<?}else{GAMEHEADER("options");?>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
<form>
<b><font size="+1">Options</font></b>
<br>
  <table width="100%" align="center">
   <tr><td align="center"><br></td></tr>
   <tr>
   <tr>
   <tr>
   <tr>
   <tr>
    <td align="center" width="100%"><small><a href="options.php?opt=profile&tru=<?=$tru?>"><img src="<?=$site[img]?>/options.gif" height="32" width="32" border=0><br><nobr>Edit Profile & Description</nobr></a></td>
   </tr>
   </tr>
   <tr><td align="center"><br></td></tr>
   <tr>
   <tr>
   <tr>
   <tr>
   </tr>
   <tr><td align="center"><br></td></tr>
     <tr>
     <tr>
     <tr>
     <tr>
     <tr>
     <td align="center" width="100%"><small><a href="options.php?opt=attacks&tru=<?=$tru?>"><img src="<?=$site[img]?>/options.gif" height="32" width="32" border=0><br><nobr>Reset Attacks</nobr></a></td>
   </tr>
   </tr>
   <tr><td align="center"><br></td></tr>
     <tr>
     <tr>
     <tr>
     <tr>
     <tr>
     <td align="center" width="100%"><small><a href="options.php?opt=setturns&tru=<?=$tru?>"><img src="<?=$site[img]?>/options.gif" height="32" width="32" border=0><br><nobr>Set Default Turns</nobr></a></td>
   </tr>
   </tr>
   <tr><td align="center"><br></td></tr>
     <tr>
     <tr>
     <tr>
     <tr>
     <tr>
  </table>
<br>
<?=bar($id)?>
<br>
  </td>
 </tr>
</table>

<?}?>
<?
GAMEFOOTER();
?>