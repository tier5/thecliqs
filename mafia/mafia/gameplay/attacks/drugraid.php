<?php
    if($pmp[9]+$pmp[24]+$pmp[25] != 0){ echo"kill his dus first";}else{
	?>
<?
$foundcrack=($pmpp[5]/40);
$foundweed=($pmpp[6]/40);
$foundalcohol=($pmpp[7]/40);

$stealcrack=round($foundcrack*.10);
$stealweed=round($foundweed*.10);
$stealalcohol=round($foundalcohol*.10);

?>
<br>
You sent your DU's out and found <font color="#B5CDE6"><?=commas($foundcrack)?> crack rocks & <?=commas($foundweed)?> Blunts & <?=commas($foundalcohol)?> Alcohol</font> in <b><?=$pmp[1]?>'s</b> crib.
<br>
Your DUs managed to snatch <font color="#B5CDE6"><?=commas($stealcrack)?></font> in crack & <font color="#B5CDE6"><?=commas($stealweed)?></font> in weed & <font color="#B5CDE6"><?=commas($stealalcohol)?></font> in Alcohol.
<?
$stdC=commas($stealcrack);
$stdW=commas($stealweed);
$stdA=commas($stealalcohol);
$foundC=commas($foundcrack);
$foundW=commas($foundweed);
$foundAA=commas($foundalcohol);

$crackleft=fixinput($pmpp[5]-$stealcrack);
$weedleft=fixinput($pmpp[6]-$stealweed);
$alcoholleft=fixinput($pmpp[7]-$stealalcohol);

$crackstolen=fixinput($pimp[7]+$stealcrack);
$weedstolen=fixinput($pimp[8]+$stealweed);
$alcoholstolen=fixinput($pimp[9]+$stealalcohol);

mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> found <font color=#B5CDE6>$foundC</font> Crack & <font color=#B5CDE6>$foundW</font> Weed in your crib. Your shit was raided and <font color=#B5CDE6>$stdC crack</font> <font color=#B5CDE6>$stdW weed</font> <font color=#B5CDE6>$stdA alcohol</font> was stolen.','$time','attacks');");                        
mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2, protection=0 WHERE id='$id'");
mysql_query("UPDATE $tab[pimp] SET crack='$crackleft', weed='$weedleft', condom='$alcoholleft', atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
mysql_query("UPDATE $tab[pimp] SET crack='$crackstolen', weed='$weedstolen', condom='$alcoholstolen' WHERE id='$id'");
}?>