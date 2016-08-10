<?
include("html.php");

mysql_query("UPDATE $tab[user] SET currently='Viewing Pimp', online='$time' WHERE id='$id'"); 

$pimp = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,online,whore,thug,lowrider,networth,profile,lastattackby,lastattack,crew,description,city,id,code FROM "."r".$rnd."_".$tab[pimp]." WHERE pimp='$pid';"));
$crew = mysql_fetch_array(mysql_query("SELECT id,name,icon,founder FROM "."r".$rnd."_".$tab[crew]." WHERE id='$pimp[11]';"));
$attacker = mysql_fetch_array(mysql_query("SELECT pimp FROM "."r".$rnd."_".$tab[pimp]." WHERE id='$pimp[9]';"));
$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username FROM $tab[user] WHERE code='$pimp[code]';"));

function timecount ($online){
global $time;

$difference=$time-$online;
$num = $difference/86400;
$days = intval($num);
$num2 = ($num - $days)*24;
$hours = intval($num2);
$num3 = ($num2 - $hours)*60;
$mins = intval($num3);
$num4 = ($num3 - $mins)*60;
$secs = intval($num4);

if($days != 0){echo"$days days, ";}
if($hours != 0){echo"$hours hours, ";}
if($mins != 0){echo"$mins mins, ";}
echo"$secs secs";
}

siteheader();
?>
  <title><?=$site[name]?></title>
<table width="90%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
  <b>Not a member? <a href="signup.php">become one</a>, its free!</b><br>
  <table cellspacing="1" cellpadding="0">
    <tr><td valign="middle">
  <table width="100%" height="100%" border="0" cellspacing="0" cellpadding="3">
   <tr>
    <td align="right">
      <?if($pimp[8]){ $pimp[8]=securepic($pimp[8]); $pro=strrchr($pimp[8],'.');if($pro == ".swf"){?>
      <embed src="<?=$pimp[8]?>" menu="false" quality="high" width="200" height="200" type="application/x-shockwave-flash" pluginspage"=http://www.macromedia.com/go/getflashplayer"></embed>
      <?}else{?>
      <img src="<?=$pimp[8]?>" width="200" height="200">      <?}}?>    </td>
    <td valign="middle">
    <table cellspacing="0" cellpadding=="0"><tr><td><nobr><b><font size="+1"><?if($crew[2]){?><img src="<?=$crew[2]?>" align="absmiddle" border="0"><?}?> <?=$pimp[0]?></b></font></nobr></td></tr></table>
    <nobr>
    ranked <b>
    <?=commas($pimp[1])?>
    </b> in <b>
    <?=$city[0]?>
    </b>, <b>
    <?=commas($pimp[2])?>
    </b> national
    <?if($crew[0] > 0){?>
    <br>
    <?if($crew[3] == $pimp[0]){?>
    founder
    <?}else{?>
    member
    <?}?> 
    of <b>
    <?=$crew[1]?>
    </b>.
    <?}?>
    <br>
    <br>
    
    <?if($pimp[3]>0){?>
    last seen 
    <?=timecount($pimp[3])?> 
    ago.
    <?}else{?>
    this member hasnt logged in yet.
    <?}?>
    <br>
    <br>
    <b>
    <?=commas($pimp[4])?>
    </b> OP's and <b>
    <?=commas($pimp[5])?>
    </b> DU's 
    <?if($pimp[6]>0){?>
    <br>
    with <b>
    <?=commas($pimp[6])?>
    </b> transports 
    <?}?>
    <br>
    worth <b>$
    <?=commas($pimp[7])?>
    </b>
    <br>
    <?if($pimp[9]){?>
    <br>
    last attacked by <a href="pimp.php?pid=<?=$attacker[0]?>&rnd=<?=$rnd?>">
    <?=$attacker[0]?>
    </a><br>
    
    <?=timecount($pimp[10])?> 
    ago.
    <?}?>
    <br>
    <br>
    </nobr> </td>
   </tr>
  </table>
  </td></tr></table>
  <br><?if($pimp[12]){echo"<i>$pimp[12]</i>";}?>

  </td>
 </tr>
</table>
<div align="center">
  <p>&nbsp;</p>
  <p><a href="../pimpprofile.php?pid=<?=$user[username]?>">View this members Master account profile here</a> <br>
  </p>
</div>
<?
sitefooter();
?>