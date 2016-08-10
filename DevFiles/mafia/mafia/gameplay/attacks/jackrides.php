<?
$thugshave=round($pimp[6]*2);
if($thugshave > $pimp[5]){$thugshave=$pimp[5];}
$stealride=round($thugshave);

if($stealride > $pmp[14]){$stealride=$pmp[14];}

if(($pmp[9] == 0) && ($stealride > 0))
  {
  ?><br>your boys managed to jack <font color="#FFCC00"><?=commas($stealride)?></font> of <b><?=$pmp[1]?>'s</b> lolos.<?
  $getride=round($pimp[6]+$stealride);
  $loseride=round($pmp[14]-$stealride);
  mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2, lowrider='$getride' WHERE id='$id'");
  mysql_query("UPDATE $tab[pimp] SET attin=attin+1, atk=atk+1, lowrider='$loseride', lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
  mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> jacked $stealride lolo&#39s from you!','$time','attacks');");
  }
else
  {
  echo"<br><b>$pmp[1]ls</b> rides are too well protected. your boys returned with nothing.";
  mysql_query("UPDATE $tab[pimp] SET attout=attout+1, trn=trn-2 WHERE id='$id'");
  }

?>