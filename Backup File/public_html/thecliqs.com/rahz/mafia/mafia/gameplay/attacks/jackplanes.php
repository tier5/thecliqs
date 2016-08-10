<?
$thugshave=round($pimp[29]*2);
if($thugshave > $pimp[5]){$thugshave=$pimp[5];}
$stealride=round($thugshave);

if(($pmp[9] == 0) && ($stealride > 0))
  {
  ?><br>your boys managed to jack <font color="#FFCC00"><?=commas($stealride)?></font> of <b><?=$pmp[1]?>'s</b> planes.<?
  $getride=round($pimp[29]+$stealride);
  $loseride=round($pmp[33]-$stealride);
  mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2, plane='$getride' WHERE id='$id'");
  mysql_query("UPDATE $tab[pimp] SET attin=attin+1, atk=atk+1, plane='$loseride', lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
  mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> jacked $stealride planes from you!','$time','attacks');");
  }
else
  {
?><br>your boys managed to jack <font color="#FFCC00"><?=commas($stealride)?></font> of <b><?=$pmp[1]?>'s</b> planes.<?
  $getride=round($pimp[29]+$stealride);
  $loseride=round($pmp[33]-$stealride);
  mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2, plane='$getride' WHERE id='$id'");
  mysql_query("UPDATE $tab[pimp] SET attin=attin+1, atk=atk+1, plane='$loseride', lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
  mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> jacked $stealride planes from you!','$time','attacks');");
  }

?>