<?
 set_time_limit(30);
$punks = ($pimp[6]*10);
if($pimp[5]+$pimp[26]+$pimp[27] < $punks){$punks=$pimp[5];}
$glocks = $pimp[16];$shotguns = $pimp[17];$uzis = $pimp[18];$ak47s = $pimp[19];                     
 if($ak47s < $punks){ $punk1=$punks-$ak47s; }elseif($punks <= $ak47s){ $ak47s=$punks;$punk1=0; }
 if($uzis < $punk1){$punk2=$punk1-$uzis;}elseif($punk1 <= $uzis){$uzis=$punk1;$punk2=0;}
 if($shotguns < $punk2){$punk3=$punk2-$shotguns;}elseif($punk2 <= $shotguns){$shotguns=$punk2;$punk3=0;}
 if($glocks < $punk3){$punk4=$punk3-$glocks;}elseif($punk3 <= $glocks){$glocks=$punk3;$punk4=0;}

$punks2 = $pmp[9]+$pmp[24]+$pmp[25];$glocks2 = $pmp[5];$shotguns2 = $pmp[6];$uzis2 = $pmp[7];$ak47s2 = $pmp[8];                     
 if($ak47s2 < $punks2){ $punk1=$punks2-$ak47s2; }elseif($punks2 <= $ak47s2){ $ak47s2=$punks2;$punk1=0; }
 if($uzis2 < $punk1){$punk2=$punk1-$uzis2;}elseif($punk1 <= $uzis2){$uzis2=$punk1;$punk2=0;}
 if($shotguns2 < $punk2){$punk3=$punk2-$shotguns2;}elseif($punk2 <= $shotguns2){$shotguns2=$punk2;$punk3=0;}
 if($glocks2 < $punk3){$punk4=$punk3-$glocks2;}elseif($punk3 <= $glocks2){$glocks2=$punk3;$punk4=0;}

$bullets=$ak47s*30+$uzis*20+$shotguns*10+$glocks*3;
$bullets=round($bullets);

$glock = $glocks*.02;$shotgun = $shotguns*.06;$uzi = $uzis*.20;$ak47 = $ak47s*.60;
$glock2 = $glocks2*.02;$shotgun2 = $shotguns2*.06;$uzi2 = $uzis2*.20;$ak472 = $ak47s2*.60;

$kill = $glock+$shotgun+$uzi+$ak47;//if($kill >= $pmp[9]){$kill=$pmp[9];}
$kill2 = $glock2+$shotgun2+$uzi2+$ak472;//if($kill2 >= $punks){$kill2=$punks;}

$your_kills=round(fixinput($kill));
$his_kills=round(fixinput($kill2));

$thughs 		= array("u_kills" => 0, "h_kills" => 0);
 $hitmans 		= array("u_kills" => 0, "h_kills" => 0);
 $bodyguards 	= array("u_kills" => 0, "h_kills" => 0);
 
 srand(microtime()*time());

 $thughs 		= array("u_kills" => 0, "h_kills" => 0);
 $bodyguards 	= array("u_kills" => 0, "h_kills" => 0);
 $hitmans 		= array("u_kills" => 0, "h_kills" => 0);
 $his_kills>$punks ? $tempKills=$punks : $tempKills = $his_kills;;
 while($tempKills > 0){
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


 ?><br>
<font color="#FFCC00"><?=($ak47s+$uzis+$shotguns+$glocks)?></font> of your boys, riding in <font color="#FFCC00">
<?php
if($pimp[5]+$pimp[26]+$pimp[27] < ($pimp[6]*10)){
	echo commas(ceil(($pimp[5]+$pimp[26]+$pimp[27])/10));
}else{
	echo commas($pimp[6]);
}
?></font> S-Class Limo's
, equipped with
 <? if($ak47s > 0){?><font color="#FFCC00"><?=$ak47s?></font> aks<? if(($uzis==0) && ($shotguns==0) && ($glocks==0)){echo".";}else{echo", ";} }if($uzis > 0){?><font color="#FFCC00"><?=$uzis?></font> uzi's<? if(($shotguns==0) && ($glocks==0)){echo".";}else{echo", ";} }if($shotguns > 0){?><font color="#FFCC00"><?=$shotguns?></font> shotguns<? if($glocks2==0){echo".";}else{echo", ";} }if($glocks > 0){?><font color="#FFCC00"><?=$glocks?></font> glocks. <? }?>
 <br>
 fired <font color="#FFCC00"><?=commas($bullets)?></font> shots across the streets of <?=$city[0]?>.
 <?if(($ak47s2+$uzis2+$shotguns2+$glocks2) != 0){?><br>
 <b><?=$pmp[1]?></b>'s <font color="#FFCC00"><?=($pmp[9]+$pmp[24]+$pmp[25]/*$ak47s2+$uzis2+$shotguns2+$glocks2*/)?></font> boys (
<?=commas($pmp[9])?> thugs  
<?=commas($pmp[24])?> hitmen  
<?=commas($pmp[25])?> bodyguards ) replied with <? if($ak47s2 > 0){?><font color="#FFCC00"><?=$ak47s2?></font> aks<? if(($uzis2==0) && ($shotguns2==0) && ($glocks2==0)){echo".";}else{echo", ";} }if($uzis2 > 0){?><font color="#FFCC00"><?=$uzis2?></font> uzi's<? if(($shotguns2==0) && ($glocks2==0)){echo".";}else{echo", ";} }if($shotguns2 > 0){?><font color="#FFCC00"><?=$shotguns2?></font> shotguns<? if($glocks2==0){echo".";}else{echo", ";} }if($glocks2 > 0){?><font color="#FFCC00"><?=$glocks2?></font> glocks. <? }?><?}?>
 <br>
 <br><b><?=commas($your_kills)?> of <b><?=$pmp[1]?>'s</b> boys 
 (<?=commas($thughs["h_kills"])?> thugs <?=commas($hitmans["h_kills"])?> hitmen <?=commas($bodyguards["h_kills"])?> bodyguards )
 are now speedbumps.</b>
 <? if($his_kills != 0){?><br><? if ($his_kills >= $your_kills){?><b><font color="#FFCC00"><? }?><?=commas($his_kills)?> of your thugs 
 (<?=commas($thughs["u_kills"])?> thugs <?=commas($hitmans["u_kills"])?> hitmen <?=commas($bodyguards["u_kills"])?> bodyguards )
 were killed as well.</font></b><?}?>
 <?
   $u_punk=fixinput($pimp[26]-$his_kills);
   $h_punk=fixinput($pmp[9]-$your_kills);
   $u_thugkreward=fixinput($pimp[22]+$your_kills);
   $h_thugkreward=fixinput($pmp[19]+$his_kills);
   $u_netaward=fixinput($pmp[2]+$your_kills);
   $h_netaward=fixinput($pimp[0]+$his_kills);

 mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('$id','$pmp[0]','$addmsg <b>$pimp[15]</b> took a short cut through your street in $pimp[6] lolos. <b>$your_kills</b> of your boys became speedbumps. </b>$his_kills</b> of $pimp[15]&#39s thugs were also killed.','$time','attacks');");
 mysql_query("UPDATE $tab[pimp] SET thug=thug-$thughs[u_kills], hitmen=hitmen-$hitmans[u_kills], bodyguards=bodyguards-$bodyguards[u_kills], networth='$u_netaward', thugk='$u_thugkreward', attout=attout+1, trn=trn-2 WHERE id='$id'");
 mysql_query("UPDATE $tab[pimp] SET thug=thug-$thughs[h_kills], hitmen=hitmen-$hitmans[h_kills], bodyguards=bodyguards-$bodyguards[h_kills], networth='$h_netaward', thugk='$h_thugkreward', atk=atk+1, attin=attin+1, lastattack='$time', lastattackby='$id' WHERE id='$pmp[0]'");
?>