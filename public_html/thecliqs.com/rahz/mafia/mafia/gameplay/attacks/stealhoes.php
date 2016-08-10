<?
$deadly=fixinput($pmp[11]*20);
$crack=fixinput($crack);
      if($crack >= $deadly)
        {
        $rand1 = fixinput($pmp[11]*.001);$rand2=fixinput($pmp[11]*.02);
        $kill_hoes = fixinput(rand($rand1,$rand2));
        $deadmessage="$kill_hoes hoes overdosed.";
        if($kill_hoes < 0){$kill_hoes="0";}
        }

      if($pmp[12] < 60){$rob=rand(0,($pmp[11]*.040));}
  elseif($pmp[12] < 70){$rob=rand(0,($pmp[11]*.035));}
  elseif($pmp[12] < 80){$rob=rand(0,($pmp[11]*.020));}
  elseif($pmp[12] < 90){$rob=rand(0,($pmp[11]*.015));}
    else{$rob=0;}
       $rob=fixinput($rob);
      if($rob > $pmp[11])
        {
        ?>Impossible... How can you steal that many hoes if he doesn't have that many hoes.<br>Please message <a href="mailto:admin@trupimps.com">Admin</a> and tell him what you did to get this message.<?
        }
   elseif($rob==0)
        {
        ?><br><br><b><?=$pmp[1]?></b> bitches smoked all <font color="#FFCC00"><?=commas($crack)?></font> rocks. Damn!<?
        mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,crew) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> wasted $crack rocks on your hoes and got nothin. $deadmessage','$time','attacks','$pimp[1]');"); 
        }
    else{
        $rob=round($rob);
        ?><br><br><font color="#FFCC00"><?=commas($rob)?></font> hoes left <b><?=$pmp[1]?></b> to smoke your shit instead. Haha Dumbass!<?
        mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,crew) VALUES ('$id','$pmp[0]','$addmsg $rob hoes left you for <b>$pimp[15]</b>! $deadmessage','$time','attacks','$pimp[1]');");
        }
      if(($crack >= $deadly) && ($kill_hoes > 0)){?><br><font color="#FFCC00"><b><?=fixinput(fixinput($kill_hoes))?> overdosed, and were found dead.</b></font><?}
         
        $hoesgot=fixinput($pimp[10]+$rob);
        $hoesleft=fixinput($pmp[11]-$kill_hoes-$rob);
        $crackleft=fixinput($pimp[7]-$crack);
        $whorekreward=fixinput($pimp[21]+$kill_hoes);
        if(0 > $hoesgot){$hoesgot=0;}

        if($pmp[11] > $rob){
         mysql_query("UPDATE $tab[pimp] SET whore='$hoesgot', crack='$crackleft', whorek='$whorekreward', attout=attout+1, trn=trn-2 WHERE id='$id'");
         mysql_query("UPDATE $tab[pimp] SET whore='$hoesleft', attin=attin+1, atk=atk+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
        } 
?>
