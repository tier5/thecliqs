<?php
    if($pmp[9]+$pmp[24]+$pmp[25] != 0){ echo"kill his dus first";}else{
	?><?

$found1=($pmp[5]/25);
$std1=round($found1*.40);

$found2=($pmp[6]/35);
$std2=round($found2*.30);

$found3=($pmp[7]/55);
$std3=round($found3*.20);

$found4=($pmp[8]/72);
$std4=round($found4*.10);


?>
<br>
You sent your DUs out and found: <br>
<font color="#B5CDE6"><?=commas($found1)?> Pistol's</font><br>
<font color="#B5CDE6"><?=commas($found2)?> Shotgun's</font><br>
<font color="#B5CDE6"><?=commas($found3)?> Uzi's</font><br>
<font color="#B5CDE6"><?=commas($found4)?> AK47's</font><br>
of <b><?=$pmp[pimp]?>'s</b> weapons.
<br>
Your DUs managed to to snag:<br>
<font color="#B5CDE6"><?=commas($std1)?></font> Pistol's.<br>
<font color="#B5CDE6"><?=commas($std2)?></font> Shotgun's.<br>
<font color="#B5CDE6"><?=commas($std3)?></font> Uzi's.<br>
<font color="#B5CDE6"><?=commas($std4)?></font> AK47's.<br>
<?
$stdP=commas($std1);
$stdS=commas($std2);
$stdU=commas($std3);
$stdA=commas($std4);

$foundP=commas($found1);
$foundS=commas($found2);
$foundU=commas($found3);
$foundA=commas($found4);

$pistolleft=fixinput($pmp[5]-$std1);
$shotgunleft=fixinput($pmp[6]-$std2);
$uzileft=fixinput($pmp[7]-$std3);
$akleft=fixinput($pmp[8]-$std4);

$pistolstolen=fixinput($pimp[16]+$std1);
$shotgunstolen=fixinput($pimp[17]+$std2);
$uzistolen=fixinput($pimp[18]+$std3);
$akstolen=fixinput($pimp[19]+$std4);

mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> found a bunch of weapons.  A bunch was stolen.','$time','attacks');");                        
mysql_query("UPDATE $tab[pimp] SET attout=attout+1, protection=0 WHERE id='$id'");
mysql_query("UPDATE $tab[pimp] SET atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
mysql_query("UPDATE $tab[pimp] SET glock='$pistolstolen', shotgun='$shotgunstolen', uzi='$uzistolen', ak47='$akstolen' WHERE id='$id'");
mysql_query("UPDATE $tab[pimp] SET glock='$pistolleft', shotgun='$shotgunleft', uzi='$uzileft', ak47='$akleft', lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
}?>