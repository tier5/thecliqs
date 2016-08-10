<?
#include settings file


 set_time_limit(0);
if ((($pmp[5] == 0) && ($pmp[6] == 0) && ($pmp[7] == 0) && ($pmp[8] == 0)) || (($pmp[9] == 0) && ($pmp[24] == 0) && ($pmp[25] == 0)))
   {
   
   //Kill hoes
   //function to kill hoes
$totalonee = ($pimp['thug']+$pimp['hitmen']+$pimp['bodyguards'])*.15;
$totaltwoo = ($pimp['thug']+$pimp['hitmen']+$pimp['bodyguards'])*.20;
$totalone = $totalonee/4;
$totaltwo = $totaltwoo/4;

		$killed11 = round(rand($totalone, $totaltwo));
		        if($killed11 > $pmpp[0]){ $killed1 = $pmpp[0];}
			  else{$killed1 = $killed11;}
        $killed22 = round(rand($totalone, $totaltwo));
		        if($killed22 > $pmpp[1]){ $killed2 = $pmpp[1];}
			  else{$killed2 = $killed22;}
        $killed33 = round(rand($totalone, $totaltwo));
		        if($killed33 > $pmpp[2]){ $killed3 = $pmpp[2];}
			  else{$killed3 = $killed33;}
        $killed44 = round(rand($totalone, $totaltwo));
		        if($killed44 > $pmpp[3]){ $killed4 = $pmpp[3];}
			  else{$killed4 = $killed44;}
        $killed55 = round(rand($totalone, $totaltwo));
		        if($killed55 > $pmpp[4]){ $killed5 = $pmpp[4];}
			  else{$killed5 = $killed55;}
		
		
		?>
		<font color="#FF0000"><b>
                       <?=$pmp[1]?>
's</b></font> <font color="#FFCC00"><b><?=fixinput($killed1) ?> whore, <?=fixinput($killed2) ?> bootleggers, <?=fixinput($killed3) ?> hustlers, <?=fixinput($killed4) ?> dealers, <?=fixinput($killed5) ?> punks
  Op's were found dead. </b></font>
<?
 
        $whorekreward=fixinput($pimp[21]+$killed1+$killed2+$killed3+$killed4+$killed5);
        $totalkilledd=fixinput($killed1+$killed2+$killed3+$killed4+$killed5);
        if(0 > $hoesgot){$hoesgot=0;}

         $db1->doSql("UPDATE $tab[pimp] SET whorek='$whorekreward' WHERE id='$id'");
         $db1->doSql("UPDATE $tab[pimp] SET whore=whore-$killed1, bootleggers=bootleggers-$killed2, hustlers=hustlers-$killed3, dealers=dealers-$killed4, punks=punks-$killed5 WHERE id='$pmp[0]'");

                     ?><?
					 
					 
	   $formula=round($pmp[10]*.15);$stole=commas($formula);
	   $cash1 = number_format(($pimp[14]+$formula),0,",",""); 
	   $cash2 = number_format(($pmp[10]-$formula),0,",",""); 
	   $db1->doSql("UPDATE $tab[pimp] SET money='$cash1', attout=attout+1, trn=trn-2 WHERE id='$id'");
	   $db1->doSql("UPDATE $tab[pimp] SET money='$cash2', attin=attin+1, atk=atk+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
	   $db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> raided your crib while your thugs were unarmed and ganked <b><font color=3366FF> $$stole </font> </b> from you. Total OPs Killed of yours $killed1 whores $killed2 bootleggers $killed3 hustlers $killed4 dealers and $killed5 punks','$time','attacks');"); 
	   echo"<br><br>you burst down the door of <b>$pmp[1]'s</b> crib to notice no one was there,<br>so you walked in and stole <b>$$stole</b> from him.<br> ";
	   
	   if(isset($contract_id) && is_numeric($contract_id)){
	   	$query	= sprintf("SELECT amount FROM %s WHERE id = %s", $tab[contracts], $contract_id);
	   	$result	= mysql_query($query);
	   	$amount	= mysql_fetch_array($result);
	   	$amount	= $amount[0];
	   	
	   	$result	= false;
	   	if($amount > 0){
	   		$query	= sprintf("UPDATE %s SET bank=bank+%s WHERE id=%s", $tab[pimp], $amount, $id);
	   		$result	= $db1->doSql($query);
	   	}else{
	   		print("<br><b>Contract already fullfilled!</b>");
	   	}
	   	
	   	if($result){
	   		$query	= sprintf("DELETE FROM %s WHERE id = %s", $tab[contracts], $contract_id);
	   		$result	= $db1->doSql($query);
	   		
	   		print("<br><b>You fullfilled your contract and $amount has been transferred to your bank!</b>");
	   	}
	   }
   }else{
 #Power Constants
#attack
$att_hitmen_ak47s = 0.65;
$att_thug_ak47s   = 0.55;
$att_bodyguard_ak47s   = 0.45;
$att_hitmen_uzis = 0.25;
$att_thug_uzis   = 0.20;
$att_bodyguard_uzis   = 0.15;
$att_hitmen_shotguns = 0.08;
$att_thug_shotguns   = 0.05;
$att_bodyguard_shotguns = 0.10;
$att_hitmen_glocks = 0.03;
$att_thug_glocks   = 0.02;
$att_bodyguard_shotguns = 0.01;

#defence
$def_hitmen_ak47s = 0.60;
$def_thug_ak47s   = 0.50;
$def_bodyguard_ak47s   = 0.40;
$def_hitmen_uzis = 0.20;
$def_thug_uzis   = 0.15;
$def_bodyguard_uzis   = 0.10;
$def_hitmen_shotguns = 0.06;
$def_thug_shotguns   = 0.04;
$def_bodyguard_shotguns   = 0.05;
$def_hitmen_glocks = 0.02;
$def_thug_glocks   = 0.01;
$def_bodyguard_glocks   = 0.01;

#total atackng troups
$thugs   = $pimp['thug'];
$hitmens = $pimp['hitmen'];
$bodyguards = $pimp['bodyguards'];
$punks     = $thugs+$hitmens+$bodyguards;

$thugs_temp = $thugs;
$hitmens_temp = $hitmens;
$bodyguards_temp = $bodyguards;

$glocks   = $pimp['glock'];
$shotguns = $pimp['shotgun'];
$uzis     = $pimp['uzi'];
$ak47s    = $pimp['ak47'];

#atacker - set the waepons per hitmen and thug
$hweapons = array();
$tweapons = array();
$bweapons = array();
list($hweapons['ak47s'], $hitmens, $ak47s)	 = getGuns($hitmens, $ak47s);
list($hweapons['shotguns'], $hitmens, $shotguns)	 = getGuns($hitmens, $shotguns);
list($hweapons['uzis'], $hitmens, $uzis)	 = getGuns($hitmens, $uzis);
list($hweapons['glocks'], $hitmens, $glocks)	 = getGuns($hitmens, $glocks);	 
list($tweapons['ak47s'], $thugs, $ak47s)	 = getGuns($thugs, $ak47s);
list($tweapons['shotguns'], $thugs, $shotguns)	 = getGuns($thugs, $shotguns);
list($tweapons['uzis'], $thugs, $uzis)	 = getGuns($thugs, $uzis);
list($tweapons['glocks'], $thugs, $glocks)	 = getGuns($thugs, $glocks);	
list($bweapons['ak47s'], $bodyguards, $ak47s)	 = getGuns($bodyguards, $ak47s);
list($bweapons['shotguns'], $bodyguards, $shotguns)	 = getGuns($bodyguards, $shotguns);
list($bweapons['uzis'], $bodyguards, $uzis)	 = getGuns($bodyguards, $uzis);
list($bweapons['glocks'], $bodyguards, $glocks)	 = getGuns($bodyguards, $glocks);

#restore hitmens and thugs as we lost them
$thugs   = $thugs_temp;
$hitmens = $hitmens_temp;
$bodyguards = $bodyguards_temp;

#-------------------------------------
#attacked
$glocks1   = $pmp['glock'];
$shotguns1 = $pmp['shotgun'];
$uzis1     = $pmp['uzi'];
$ak47s1    = $pmp['ak47']; 

$hitmens1  = $pmp['hitmen'];
$thugs1    = $pmp['thug'];
$bodyguards1 = $pmp['bodyguards'];

$punks2 = $hitmens1 + $thugs1 + $bodyguards1;

$hweapons1 = array();
$tweapons1 = array();
$bweapons1 = array();

list($hweapons1['ak47s'], $hitmens1, $ak47s1)	 = getGuns($hitmens1, $ak47s1);
list($hweapons1['shotguns'], $hitmens1, $shotguns1)	 = getGuns($hitmens1, $shotguns1);
list($hweapons1['uzis'], $hitmens1, $uzis1)	 = getGuns($hitmens1, $uzis1);
list($hweapons1['glocks'], $hitmens1, $glocks1)	 = getGuns($hitmens1, $glocks1);	 
list($tweapons1['ak47s'], $thugs1, $ak47s1)	 = getGuns($thugs1, $ak47s1);
list($tweapons1['shotguns'], $thugs1, $shotguns1)	 = getGuns($thugs1, $shotguns1);
list($tweapons1['uzis'], $thugs1, $uzis1)	 = getGuns($thugs1, $uzis1);
list($tweapons1['glocks'], $thugs1, $glocks1)	 = getGuns($thugs1, $glocks1);
list($bweapons1['ak47s'], $bodyguards1, $ak47s1)	 = getGuns($bodyguards1, $ak47s1);
list($bweapons1['shotguns'], $bodyguards1, $shotguns1)	 = getGuns($bodyguards1, $shotguns1);
list($bweapons1['uzis'], $bodyguards1, $uzis1)	 = getGuns($bodyguards1, $uzis1);
list($bweapons1['glocks'], $bodyguards1, $glocks1)	 = getGuns($bodyguards1, $glocks1);

$hitmens1  = $pmp['hitmen'];
$thugs1    = $pmp['thug'];
$bodyguards1 = $pmp['bodyguards'];

$bullets=($hweapons['ak47s'] + $tweapons['ak47s'] + $bweapons['ak47s'])*30+($hweapons['uzis'] + $tweapons['uzis'] + $bweapons['uzis'])*20+($hweapons['shotguns'] + $tweapons['shotguns'] + $bweapons['shotguns'])*10+($hweapons['glocks'] + $tweapons['glocks'] + $bweapons['glocks'])*3;
$bullets=round($bullets);

$glock   = ($hweapons['glocks'] * $att_hitmen_glocks) + ($tweapons['glocks'] * $att_thug_glocks) + ($bweapons['glocks'] * $att_bodyguard_glocks);
$uzi     = ($hweapons['uzis'] * $att_hitmen_uzis) + ($tweapons['uzis'] * $att_thug_uzis) + ($bweapons['uzis'] * $att_bodyguard_uzis);
$shotgun = ($hweapons['shotguns'] * $att_hitmen_shotguns) + ($tweapons['shotguns'] * $att_thug_shotguns) + ($bweapons['shotguns'] * $att_bodyguard_shotguns);
$ak47    = ($hweapons['ak47s'] * $att_hitmen_ak47s) + ($tweapons['ak47s'] * $att_thug_ak47s) + ($bweapons['ak47s'] * $att_bodyguard_ak47s);

$glock1   = ($hweapons1['glocks'] * $def_hitmen_glocks) + ($tweapons1['glocks'] * $def_thug_glocks) + ($bweapons1['glocks'] * $def_bodyguard_glocks);
$uzi1     = ($hweapons1['uzis'] * $def_hitmen_uzis) + ($tweapons1['uzis'] * $def_thug_uzis) + ($bweapons1['uzis'] * $def_bodyguard_uzis);
$shotgun1 = ($hweapons1['shotguns'] * $def_hitmen_shotguns) + ($tweapons1['shotguns'] * $def_thug_shotguns) + ($bweapons1['shotguns'] * $def_bodyguard_shotguns);
$ak471    = ($hweapons1['ak47s'] * $def_hitmen_ak47s) + ($tweapons1['ak47s'] * $def_thug_ak47s) + ($bweapons1['ak47s'] * $def_bodyguard_ak47s);


$kill = $glock+$shotgun+$uzi+$ak47;if($kill >= $punks2){$kill=$punks2;}
$kill2 = $glock1+$shotgun1+$uzi1+$ak471;if($kill2 >= $punks){$kill2=$punks;}

$kill = round($kill);
$kill2 = round($kill2);

if ($bodyguards1 >= $kill) {
    $his_bodyguards_dead  = $kill;
	$his_hitmens_dead = 0;
	$his_thugs_dead = 0;
	$remaining_kill = 0;
}else {
	$his_bodyguards_dead = $bodyguards1;
	$remaining_kill = $kill - $his_bodyguards_dead;
}

if ($thugs1 >= $remaining_kill) {
    $his_thugs_dead  = $remaining_kill;
	$his_hitmens_dead = 0;
	$remaining_kill = 0;
}else {
	$his_thugs_dead  = $thugs1;
	$remaining_kill = $remaining_kill - $his_thugs_dead;
}

if (!$his_hitmens_dead) {
    if ($hitmens1 >= $remaining_kill) {
		$his_hitmens_dead = $remaining_kill;
    }else {
		$his_hitmens_dead = $hitmens1;
	}
}
$his_total_dead=fixinput($kill);


if ($bodyguards >= $kill2) {
    $my_bodyguards_dead  = $kill2;
	$my_hitmens_dead = 0;
	$my_thugs_dead = 0;
	$my_remaining_kill = 0;
}else {
	$my_bodyguards_dead = $bodyguards;
	$my_remaining_kill = $kill2 - $my_bodyguards_dead;
}

if ($thugs >= $my_remaining_kill) {
    $my_thugs_dead  = $my_remaining_kill;
	$my_hitmens_dead = 0;
	$my_remaining_kill = 0;
}else {
	$my_thugs_dead  = $thugs;
	$my_remaining_kill = $my_remaining_kill - $my_thugs_dead;
}

if ($my_remaining_kill) {
    if ($hitmens >= $my_remaining_kill) {
        $my_hitmens_dead = $my_remaining_kill;
    }else {
		$my_hitmens_dead = $hitmens;
	}
}
$my_total_dead=fixinput($kill2);

#restore weapons
$your_ak47s    = $hweapons['ak47s'] + $tweapons['ak47s'] + $bweapons['ak47s'];
$your_uzis     = $hweapons['uzis'] + $tweapons['uzis'] + $bweapons['uzis'];
$your_shotguns = $hweapons['shotguns'] + $tweapons['shotguns'] + $bweapons['shotguns'];
$your_glocks   = $hweapons['glocks'] + $tweapons['glocks'] + $bweapons['glocks'];

$his_ak47s2     = $hweapons1['ak47s'] + $tweapons1['ak47s'] + $bweapons1['ak47s'];
$his_uzis2      = $hweapons1['uzis'] + $tweapons1['uzis'] + $bweapons1['uzis'];
$his_shotguns2  = $hweapons1['shotguns'] + $tweapons1['shotguns'] + $bweapons1['shotguns'];
$his_glocks2    = $hweapons1['glocks'] + $tweapons1['glocks'] + $bweapons1['glocks'];   
   

$your_steal=round($pmp[10]*.15);
$his_steal=round($pimp[14]*.15);
 
$his_stealc=(commas($his_steal));$your_stealc=commas($your_steal);

 ?>
<p><br>
  <font color="#FFCC00">
  <?=($pimp[5]+$pimp[26]+$pimp[27]/*$ak47s+$ak47s3+$uzis+$shotguns+$glocks*/)?>
  </font> of your boys (
  <?=commas($pimp[5])?> 
  thugs 
  <?=commas($pimp[26])?> 
  hitmen 
  <?=commas($pimp[27])?> 
  bodyguards ) invaded <b> 
  <?=$pmp[1]?>
  's</b> headquarters. <br>
armed with 
<?if($your_ak47s > 0){?>
<font color="#FFCC00">
<?=$your_ak47s?>
</font> aks
<?if(($your_uzis==0) && ($your_shotguns==0) && ($your_glocks==0)){echo".";}else{echo", ";} }if($your_uzis > 0){?>
<font color="#FFCC00">
<?=$your_uzis?>
</font> uzi's
<?if(($your_shotguns==0) && ($your_glocks==0)){echo".";}else{echo", ";} }if($your_shotguns > 0){?>
<font color="#FFCC00">
<?=$your_shotguns?>
</font> shotguns
<?if($your_glocks==0){echo".";}else{echo", ";} }if($your_glocks > 0){?>
<font color="#FFCC00">
<?=$your_glocks?>
</font> glocks, 
<?}?>
and fired <font color="#FFCC00">
<?=commas($bullets)?>
</font> rounds.
   <br>
   <b>
   <?=($pmp[9]+$pmp[24]+$pmp[25]/*$ak47s2+$uzis2+$shotguns2+$glocks2*/)?> 
   of 
   <?=$pmp[1]?>
   's</b> boys (
  <?=commas($pmp[9])?> 
  thugs  
  <?=commas($pmp[24])?> 
  hitmen  
  <?=commas($pmp[25])?> 
  bodyguards ) welcomed you with
  <?if($his_ak47s2 > 0){?>
   <font color="#FFCC00">
   <?=$his_ak47s2?>
   </font> aks
  <?if(($his_uzis2==0) && ($his_shotguns2==0) && ($his_glocks2==0)){echo".";}else{echo", ";} }if($his_uzis2 > 0){?>
   <font color="#FFCC00">
   <?=$his_uzis2?>
   </font> uzi's
  <?if(($his_shotguns2==0) && ($his_glocks2==0)){echo".";}else{echo", ";} }if($his_shotguns2 > 0){?>
   <font color="#FFCC00">
   <?=$shotguns2?>
   </font> shotguns
  <?if($his_glocks2==0){echo".";}else{echo", ";} }if($his_glocks2 > 0){?>
   <font color="#FFCC00">
   <?=$his_glocks2?>
   </font> glocks.
  <?}?>

   <br>
   <br>
   <b>you killed 
   <?=commas($his_total_dead)?> 
	(
	<?=commas($his_thugs_dead)?> 
	thugs 
	<?=commas($his_hitmens_dead)?> 
	hitmen 
	<?=commas($his_bodyguards_dead)?> 
	bodyguards )
   of 
   <?=$pmp[1]?>
   's boys in the attack.</b>
   <br>
   your 
   <?if ($my_total_dead >= $his_total_dead){?>
   <b><font color="#FFCC00">
   <?}?>
   <?=commas($my_total_dead)?> 
   were killed 
   (
   <?=commas($my_thugs_dead)?> 
   thugs 
   <?=commas($my_hitmens_dead)?> 
   hitmen 
   <?=commas($my_bodyguards_dead)?> 
   bodyguards )
   </font></b>
   <br>
   <br>
   <?if ($my_total_dead >= $his_total_dead){?>
   		<b>
   		<?=$pmp[1]?>
   		's</b> boys pocketed <font color="#FFCC00">$
   		<?=$his_stealc?>
</font> off your dead thugs.</p>
<p><?
		 $u_cash=$pimp[14]-$his_steal;$u_punk=$pimp[5]-$his_kills;
		 $h_cash=$pmp[10]+$his_steal;$h_punk=$pmp[9]-$your_kills;
		 $db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> invaded your crib. <b>$your_kills</b> of your boys were killed, <b>$his_kills</b> of <b>$pimp[15]&#39s</b> died as well. you collected <b>$$his_stealc</b> from the bodies.','$time','attacks');");
   }else{?>
  you ganked <font color="#FFCC00">$
  <?=$your_stealc?>
      </font> from
  <?=$pmp[1]?>
's dead thugs.</p>
<br><? //$pmpp = mysql_fetch_array(mysql_query("SELECT whore,bootleggers,hustlers,dealers,punks FROM $tab[pimp] WHERE pimp='$pid';"));
?>
   <? if ($my_total_dead <= $his_total_dead){
        
$totalonee = ($pimp['thug']+$pimp['hitmen']+$pimp['bodyguards'])*.15;
$totaltwoo = ($pimp['thug']+$pimp['hitmen']+$pimp['bodyguards'])*.20;
$totalone = $totalonee/4;
$totaltwo = $totaltwoo/4;

		$killed11 = round(rand($totalone, $totaltwo));
		        if($killed11 > $pmpp[0]){ $killed1 = $pmpp[0];}
			  else{$killed1 = $killed11;}
        $killed22 = round(rand($totalone, $totaltwo));
		        if($killed22 > $pmpp[1]){ $killed2 = $pmpp[1];}
			  else{$killed2 = $killed22;}
        $killed33 = round(rand($totalone, $totaltwo));
		        if($killed33 > $pmpp[2]){ $killed3 = $pmpp[2];}
			  else{$killed3 = $killed33;}
        $killed44 = round(rand($totalone, $totaltwo));
		        if($killed44 > $pmpp[3]){ $killed4 = $pmpp[3];}
			  else{$killed4 = $killed44;}
        $killed55 = round(rand($totalone, $totaltwo));
		        if($killed55 > $pmpp[4]){ $killed5 = $pmpp[4];}
			  else{$killed5 = $killed55;}
		
				
		?>
		<font color="#FF0000"><b>
                       <?=$pmp[1]?>
's</b></font> <font color="#FFCC00"><b><?=fixinput($killed1) ?> whore, <?=fixinput($killed2) ?> bootleggers, <?=fixinput($killed3) ?> hustlers, <?=fixinput($killed4) ?> dealers, <?=fixinput($killed5) ?> punks
  Op's were found dead. </b></font>
<?

        $whorekreward=fixinput($pimp[21]+$killed1+$killed2+$killed3+$killed4+$killed5);
        $totalkilledd=fixinput($killed1+$killed2+$killed3+$killed4+$killed5);
        if(0 > $hoesgot){$hoesgot=0;}

         $db1->doSql("UPDATE $tab[pimp] SET whorek='$whorekreward' WHERE id='$id'");
         $db1->doSql("UPDATE $tab[pimp] SET whore=whore-$killed1, bootleggers=bootleggers-$killed2, hustlers=hustlers-$killed3, dealers=dealers-$killed4, punks=punks-$killed5 WHERE id='$pmp[0]'");

                     }?>

  
  <?
		 $u_cash=$pimp[14]+$your_steal;
		 $u_punk=$pimp[5]-$my_total_dead;
		 $h_cash=$pmp[10]-$your_steal;
		 $h_punk=$pmp[9]-$his_total_dead;
		 $db1->doSql("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> invaded your crib. <b>$his_total_dead</b> of your boys were killed. <b>$my_total_dead</b> of <b>$pimp[15]&#39s</b> died as well. $pimp[15] stole <b>$$your_stealc</b> from you. Total OPs Killed of yours $killed1 whores $killed2 bootleggers $killed3 hustlers $killed4 dealers and $killed5 punks','$time','attacks');");                        
   }
   $db1->doSql("UPDATE $tab[pimp] SET thug=thug-$my_thugs_dead, hitmen=hitmen-$my_hitmens_dead, bodyguards=bodyguards-$my_bodyguards_dead, money='$u_cash', thugk=thugk+$his_total_dead, hitmenk=hitmenk+$his_hitmens_dead, bodyguardsk=bodyguardsk+$his_bodyguards_dead, attout=attout+1, trn=trn-2 WHERE id='$id'");
   $db1->doSql("UPDATE $tab[pimp] SET thug=thug-$his_thugs_dead, hitmen=hitmen-$his_hitmens_dead, bodyguards=bodyguards-$his_bodyguards_dead, money='$h_cash', thugk=thugk+$my_total_dead, hitmenk=hitmenk+$my_hitmens_dead, bodyguardsk=bodyguardsk+$my_bodyguards_dead, atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
   }

?>
</p>