<?
$found=rand(($pmp[11]/10),$pmp[11]/2);
$std=round(rand(0,($found*.50)));
?>
<br>You sent your thugs out and found <font color="#B5CDE6"><?=commas($found)?></font> of <b><?=$pmp[1]?>'s</b> hoes.
<br>Your thugs managed to spread <font color="#B5CDE6"><?=commas($std)?></font> multiple std's.
<?
$stdC=commas($std);
$foundC=commas($found);
$meds=fixinput($pmp[21]-$std);
mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> found <font color=#B5CDE6>$foundC</font> of your hoes and spreaded multiple std&#39s. Your hoes used up <font color=#B5CDE6>$stdC</font> boxes of medicine.','$time','attacks');");                        
mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2 WHERE id='$id'");
mysql_query("UPDATE $tab[pimp] SET medicine='$meds' atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
?>