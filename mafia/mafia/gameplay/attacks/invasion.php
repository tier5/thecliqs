<?
 set_time_limit(0);
if ((($pmp[5] == 0) && ($pmp[6] == 0) && ($pmp[7] == 0) && ($pmp[8] == 0)) || (($pmp[9] == 0) && ($pmp[24] == 0) && ($pmp[25] == 0)))
   {
	   $formula=round($pmp[10]*.15);$stole=commas($formula);
	   $cash1 = number_format(($pimp[14]+$formula),0,",",""); 
	   $cash2 = number_format(($pmp[10]-$formula),0,",",""); 
	   mysql_query("UPDATE $tab[pimp] SET money='$cash1', attout=attout+1, trn=trn-2 WHERE id='$id'");
	   mysql_query("UPDATE $tab[pimp] SET money='$cash2', attin=attin+1, atk=atk+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
	   mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> raided your crib while your thugs were unarmed and ganked <b><font color=3366FF> $$stole </font> </b> from you.','$time','attacks');"); 
	   echo"<br><br>you burst down the door of <b>$pmp[1]'s</b> crib to notice no one was there,<br>so you walked in and stole <b>$$stole</b> from him.<br>";
	   require_once("sabagegunops.php");
	   
	   if(isset($contract_id) && is_numeric($contract_id)){
	   	$query	= sprintf("SELECT amount FROM %s WHERE id = %s", $tab[contracts], $contract_id);
	   	$result	= mysql_query($query);
	   	$amount	= mysql_fetch_array($result);
	   	$amount	= $amount[0];
	   	
	   	$result	= false;
	   	if($amount > 0){
	   		$query	= sprintf("UPDATE %s SET bank=bank+%s WHERE id=%s", $tab[pimp], $amount, $id);
	   		$result	= mysql_query($query);
	   	}else{
	   		print("<br><b>Contract already fullfilled!</b>");
	   	}
	   	
	   	if($result){
	   		$query	= sprintf("DELETE FROM %s WHERE id = %s", $tab[contracts], $contract_id);
	   		$result	= mysql_query($query);
	   		
	   		print("<br><b>You fullfilled your contract and $amount has been transferred to your bank!</b>");
	   	}
	   }
   }else{
 $punks = $pimp[5]+$pimp[26]+$pimp[27];$glocks = $pimp[16];$shotguns = $pimp[17];$uzis = $pimp[18];$ak47s = $pimp[19];                     
	if($ak47s < $punks){ $punk1=$punks-$ak47s; }elseif($punks <= $ak47s){ $ak47s=$punks;$punk1=0; }
	if($uzis < $punk1){$punk2=$punk1-$uzis;}elseif($punk1 <= $uzis){$uzis=$punk1;$punk2=0;}
	if($shotguns < $punk2){$punk3=$punk2-$shotguns;}elseif($punk2 <= $shotguns){$shotguns=$punk2;$punk3=0;}
	if($glocks < $punk3){$punk4=$punk3-$glocks;}elseif($punk3 <= $glocks){$glocks=$punk3;$punk4=0;}


 $punks2 = $pmp[9]+$pmp[24]+$pmp[25];$glocks2 = $pmp[5];$shotguns2 = $pmp[6];$uzis2 = $pmp[7];$ak47s2 = $pmp[8];                     
	if($ak47s2 < $punks2) { $punk1=$punks2-$ak47s2; }elseif($punks2 <= $ak47s2){ $ak47s2=$punks2;$punk1=0; }
	if($uzis2 < $punk1){$punk2=$punk1-$uzis2;}elseif($punk1 <= $uzis2){$uzis2=$punk1;$punk2=0;}
	if($shotguns2 < $punk2){$punk3=$punk2-$shotguns2;}elseif($punk2 <= $shotguns2){$shotguns2=$punk2;$punk3=0;}
	if($glocks2 < $punk3){$punk4=$punk3-$glocks2;}elseif($punk3 <= $glocks2){$glocks2=$punk3;$punk4=0;}
	

 $bullets=$ak47s*15+$uzis*10+$shotguns*5+$glocks*1.5;
 $bullets=round($bullets);

 $glock = $glocks*.01;$shotgun = $shotguns*.03;$uzi = $uzis*.10;$ak47 = $ak47s*.30;
 $glock2 = $glocks2*.01;$shotgun2 = $shotguns2*.03;$uzi2 = $uzis2*.10;$ak472 = $ak47s2*.30;

 

 $kill = $glock+$shotgun+$uzi+$ak47;//if($kill >= $pmp[9]){$kill=$pmp[9];}
 $kill2 = $glock2+$shotgun2+$uzi2+$ak472;//if($kill2 >= $pimp[5]){$kill2=$pimp[5];}


 $your_kills=round($kill);$your_steal=round($pmp[10]*.15);
 $his_kills=round($kill2);$his_steal=round($pimp[14]*.15);
 
 $thughs 		= array("u_kills" => 0, "h_kills" => 0);
 $hitmans 		= array("u_kills" => 0, "h_kills" => 0);
 $bodyguards 	= array("u_kills" => 0, "h_kills" => 0);
 
 srand(microtime()*time());

 $thughs 		= array("u_kills" => 0, "h_kills" => 0);
 $bodyguards 	= array("u_kills" => 0, "h_kills" => 0);
 $hitmans 		= array("u_kills" => 0, "h_kills" => 0);
 $tempKills = $his_kills;
 
$limit = 0;
      while($tempKills > 0 && $limit < 20){
      $limit++;
	      
 	$bigNum	= (($pimp[5]-$thughs["u_kills"]) >= $tempKills) ? $tempKills : ($pimp[5]-$thughs["u_kills"]);
 	$temp 	= rand(round($bigNum*0.5), $bigNum);
 	$thughs["u_kills"] 	+= $temp;
 	$tempKills			-= $temp;
 	
 	$bigNum	= (($pimp[26]-$hitmans["u_kills"]) >= $tempKills) ? $tempKills : ($pimp[26]-$hitmans["u_kills"]);
 	$temp 	= rand(round($bigNum*0.7), $bigNum);
 	$hitmans["u_kills"] 	+= $temp;
 	$tempKills				-= $temp;
 	
 	$bigNum	= (($pimp[27]-$bodyguards["u_kills"]) >= $tempKills) ? $tempKills : ($pimp[27]-$bodyguards["u_kills"]);
 	$temp 	= rand(round($bigNum*0.9), $bigNum);
 	$bodyguards["u_kills"] 	+= $temp;
 	$tempKills				-= $temp;
 	
	if((($pimp[5]+$pimp[26]+$pimp[27]) - ($thughs["u_kills"]+$hitmans["u_kills"]+$bodyguards["u_kills"])) < 1){
 		$tempKills = 0;
 	}
 }
 /*
 while($tempKills > 0){
 	$temp 	= rand(round($tempKills*0.3), $tempKills);
 	if($temp >= $pimp[5]) { $thughs["u_kills"] += $pimp[5]; $temp = $pimp[5]; }
 	else {$thughs["u_kills"] += $temp;}
 	$tempKills -= $temp;
 	
 	$temp	= rand(round($tempKills*0.3), $tempKills);
 	if($temp >= $pimp[26]) {$hitmans["u_kills"] += $pimp[26]; $temp = $pimp[26]; }
 	else {$hitmans["u_kills"] += $temp;}
 	$tempKills -= $temp;
 	
 	$temp	= $tempKills;
 	if($temp >= $pimp[27]) {$bodyguards["u_kills"] += $pimp[27]; $temp = $pimp[27]; }
 	else {$bodyguards["u_kills"] += $temp; }
 	$tempKills -= $temp;
 	
 	if((($pimp[5]+$pimp[26]+$pimp[27]) - ($thughs["u_kills"]+$hitmans["u_kills"]+$bodyguards["u_kills"])) < 1){
 		$tempKills = 0;
 	}
 	
 }
 */
 //echo $thughs["u_kills"]."::".$hitmans["u_kills"]."::".$bodyguards["u_kills"]."<br>";
 
 $his_kills = $thughs["u_kills"]+$hitmans["u_kills"]+$bodyguards["u_kills"];
 
 $tempKills = $your_kills;
 
 while($tempKills > 0){
 	$bigNum	= (($pmp[9]-$thughs["h_kills"]) >= $tempKills) ? $tempKills : ($pmp[9]-$thughs["h_kills"]);
 	$temp 	= rand(round($bigNum*0.5), $bigNum);
 	$thughs["h_kills"] 	+= $temp;
 	$tempKills			-= $temp;
 	
 	$bigNum	= (($pmp[24]-$hitmans["h_kills"]) >= $tempKills) ? $tempKills : ($pmp[24]-$hitmans["h_kills"]);
 	$temp 	= rand(round($bigNum*0.7), $bigNum);
 	$hitmans["h_kills"] 	+= $temp;
 	$tempKills				-= $temp;
 	
 	$bigNum	= (($pmp[25]-$bodyguards["h_kills"]) >= $tempKills) ? $tempKills : ($pmp[25]-$bodyguards["h_kills"]);
 	$temp 	= rand(round($bigNum*0.9), $bigNum);
 	$bodyguards["h_kills"] 	+= $temp;
 	$tempKills				-= $temp;
 	
	if((($pmp[9]+$pmp[24]+$pmp[25]) - ($thughs["h_kills"]+$hitmans["h_kills"]+$bodyguards["h_kills"])) < 1){
 		$tempKills = 0;
 	}
 }
 /*
 while($tempKills > 0){
 	$temp 	= rand(0, $tempKills);
 	if($temp >= $pmp[9]) { $thughs["h_kills"] += $pmp[9]; $temp = $pmp[9]; }
 	else {$thughs["h_kills"] += $temp;}
 	$tempKills -= $temp;
 	
 	$temp	= rand(0, $tempKills);
 	if($temp >= $pmp[24]) {$hitmans["h_kills"] += $pmp[24]; $temp = $pmp[24]; }
 	else {$hitmans["h_kills"] += $temp;}
 	$tempKills -= $temp;
 	
 	$temp	= $tempKills;
 	if($temp >= $pmp[25]) {$bodyguards["h_kills"] += $pmp[25]; $temp = $pmp[25]; }
 	else {$bodyguards["h_kills"] += $temp; }
 	$tempKills -= $temp;
 	
 	if((($pmp[9]+$pmp[24]+$pmp[25]) - ($thughs["h_kills"]+$hitmans["h_kills"]+$bodyguards["h_kills"])) < 1){
 		$tempKills = 0;
 	}
 }
 */
 //echo $thughs["h_kills"]."::".$hitmans["h_kills"]."::".$bodyguards["h_kills"]."<br>";
 
 $your_kills = $thughs["h_kills"]+$hitmans["h_kills"]+$bodyguards["h_kills"];
 //echo $your_kills."::".$his_kills;

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
<?if($ak47s > 0){?>
<font color="#FFCC00">
<?=$ak47s?>
</font> aks
<?if(($uzis==0) && ($shotguns==0) && ($glocks==0)){echo".";}else{echo", ";} }if($uzis > 0){?>
<font color="#FFCC00">
<?=$uzis?>
</font> uzi's
<?if(($shotguns==0) && ($glocks==0)){echo".";}else{echo", ";} }if($shotguns > 0){?>
<font color="#FFCC00">
<?=$shotguns?>
</font> shotguns
<?if($glocks2==0){echo".";}else{echo", ";} }if($glocks > 0){?>
<font color="#FFCC00">
<?=$glocks?>
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
  <?if($ak47s2 > 0){?>
   <font color="#FFCC00">
   <?=$ak47s2?>
   </font> aks
  <?if(($uzis2==0) && ($shotguns2==0) && ($glocks2==0)){echo".";}else{echo", ";} }if($uzis2 > 0){?>
   <font color="#FFCC00">
   <?=$uzis2?>
   </font> uzi's
  <?if(($shotguns2==0) && ($glocks2==0)){echo".";}else{echo", ";} }if($shotguns2 > 0){?>
   <font color="#FFCC00">
   <?=$shotguns2?>
   </font> shotguns
  <?if($glocks2==0){echo".";}else{echo", ";} }if($glocks2 > 0){?>
   <font color="#FFCC00">
   <?=$glocks2?>
   </font> glocks.
  <?}?>

   <br>
   <br>
   <b>you killed 
   <?=commas($your_kills)?> 
	(
	<?=commas($thughs["h_kills"])?> 
	thugs 
	<?=commas($hitmans["h_kills"])?> 
	hitmen 
	<?=commas($bodyguards["h_kills"])?> 
	bodyguards )
   of 
   <?=$pmp[1]?>
   's boys in the attack.</b>
   <br>
   your 
   <?if ($his_kills >= $your_kills){?>
   <b><font color="#FFCC00">
   <?}?>
   <?=commas($his_kills)?> 
   were killed 
   (
   <?=commas($thughs["u_kills"])?> 
   thugs 
   <?=commas($hitmans["u_kills"])?> 
   hitmen 
   <?=commas($bodyguards["u_kills"])?> 
   bodyguards )
   </font></b>
   <br>
   <br>
   <?if ($his_kills >= $your_kills){?>
   <b>
   <?=$pmp[1]?>
   's</b> boys pocketed <font color="#FFCC00">$
   <?=$his_stealc?>
</font> off your dead thugs.</p>
<p><?
		 $u_cash=$pimp[14]-$his_steal;$u_punk=$pimp[5]-$his_kills;
		 $h_cash=$pmp[10]+$his_steal;$h_punk=$pmp[9]-$your_kills;
		 mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> invaded your crib. <b>$your_kills</b> of your boys were killed, <b>$his_kills</b> of <b>$pimp[15]&#39s</b> died as well. you collected <b>$$his_stealc</b> from the bodies.','$time','attacks');");
   }else{?>
  you ganked <font color="#FFCC00">$
  <?=$your_stealc?>
      </font> from
  <?=$pmp[1]?>
  's dead thugs.<br><br>
  <? require_once("sabagegunops.php");?>
  <?
		 $u_cash=$pimp[14]+$your_steal;$u_punk=$pimp[5]-$his_kills;
		 $h_cash=$pmp[10]-$your_steal;$h_punk=$pmp[9]-$your_kills;
		 mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> invaded your crib. <b>$your_kills</b> of your boys were killed. <b>$his_kills</b> of <b>$pimp[15]&#39s</b> died as well. $pimp[15] stole <b>$$your_stealc</b> from you.','$time','attacks');");                        
   }
   mysql_query("UPDATE $tab[pimp] SET thug=thug-$thughs[u_kills], hitmen=hitmen-$hitmans[u_kills], bodyguards=bodyguards-$bodyguards[u_kills], money='$u_cash', thugk=thugk+$your_kills, hitmenk=hitmenk+$hitmans[u_kills], bodyguardsk=bodyguardsk+$bodyguards[u_kills], attout=attout+1, trn=trn-2 WHERE id='$id'");
   mysql_query("UPDATE $tab[pimp] SET thug=thug-$thughs[h_kills], hitmen=hitmen-$hitmans[h_kills], bodyguards=bodyguards-$bodyguards[h_kills], money='$h_cash', thugk=thugk+$his_kills, hitmenk=hitmenk+$hitmans[h_kills], bodyguardsk=bodyguardsk+$bodyguards[h_kills], atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
   }

?>
</p>
