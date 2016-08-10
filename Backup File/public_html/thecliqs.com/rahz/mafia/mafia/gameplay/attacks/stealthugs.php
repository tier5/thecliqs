<?
      if($pmp[13] < 60){$rob=rand(0,($pmp[9]*.040));}
  elseif($pmp[13] < 70){$rob=rand(0,($pmp[9]*.035));}
  elseif($pmp[13] < 80){$rob=rand(0,($pmp[9]*.020));}
  elseif($pmp[13] < 90){$rob=rand(0,($pmp[9]*.015));}
    else{$rob=0;}

      $rob=fixinput($rob);

      if($rob > $pmp[9])
        {
        ?>How can you steal that many thugs, if he doesn't have that many thugs?<br>Please message <a href="mailto:admin@trupimps.com">Admin</a> and tell him what you did to get this message.<?
        }
   elseif($rob == 0)
        {
        ?><br><br><b><?=$pmp[1]?></b> boys smoked your <font color="#FFCC00"><?=commas($weed)?></font> grams of chronic. Fuckers!<?
        mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,crew) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> wasted $weed grams of chronic on your boys and got nothin.','$time','attacks','$pimp[1]');"); 
        }
    else{
        $rob=round($rob);
        $rob=fixinput($rob);
        ?><br><br><font color="#FFCC00"><?=commas($rob)?></font> of <b><?=$pmp[1]?></b> boys liked your chronic better so they followed you home instead. Haha Dumbass!<?
        mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,crew) VALUES ('$id','$pmp[0]','$addmsg $rob boys left you for <b>$pimp[15]</b>!','$time','attacks','$pimp[1]');");
        }

        $thugsgot=fixinput($pimp[5]+$rob);
        $thugsleft=fixinput($pmp[9]-$rob);
        $weedleft=fixinput($pimp[8]-$weed);

        if($pmp[9] > $rob){ 
         mysql_query("UPDATE $tab[pimp] SET thug='$thugsgot', weed='$weedleft', attout=attout+1, trn=trn-2 WHERE id='$id'");
         mysql_query("UPDATE $tab[pimp] SET thug='$thugsleft', attin=attin+1, atk=atk+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
        }
?>
