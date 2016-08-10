<?
#include settings file
require_once("echo_setup.php");

include("html.php");

mysql_query("UPDATE $tab[pimp] SET page='attacking' WHERE id='$id'");

if((!fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$pid';")) || (fetch("SELECT pimp FROM $tab[pimp] WHERE pimp='$pid' AND id='$id';"))){ header("Location: mobster.php?tru=$tru"); }

if(($attack == stealhoes) && ($crack >= 5)){setcookie("stealhoes",$crack);}
if(($attack == stealthugs) && ($weed >= 5)){setcookie("stealthugs",$weed);}

$pimp = mysql_fetch_array(mysql_query("SELECT networth,crew,trn,attout,city,thug,lowrider,crack,weed,condom,whore,medicine,thappy,whappy,money,pimp,glock,shotgun,uzi,ak47,attin,whorek,thugk,status,bank,cashstolen,hitmen,bodyguards,id,plane FROM $tab[pimp] WHERE id='$id';"));

$pmp = mysql_fetch_array(mysql_query("SELECT id,pimp,networth,protection,protectstarted,glock,shotgun,uzi,ak47,thug,money,whore,whappy,thappy,lowrider,attin,city,crew,status,thugk,ip,medicine,bank,cashstolen,hitmen,bodyguards,bootleggers,hustlers,dealers,punks,crack,weed,condom,plane FROM $tab[pimp] WHERE pimp='$pid';"));

$pmpp = mysql_fetch_array(mysql_query("SELECT whore,bootleggers,hustlers,dealers,punks,crack,weed,condom FROM $tab[pimp] WHERE pimp='$pid';"));

$city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pimp[4]';"));


$revg= mysql_fetch_array(mysql_query("SELECT time FROM $tab[mail] WHERE dest='$pimp[28]'AND src='$pmp[0]'"));

$revg2= mysql_fetch_array(mysql_query("SELECT time FROM $tab[mail] WHERE dest='$pmp[0]'AND src='$pimp[28]'"));

$revenge = fetch("SELECT COUNT(inbox) FROM $tab[mail] WHERE dest='$pimp[28]'AND src='$pmp[0]' AND time>=$time-86400 AND inbox='attacks';"); 

$revenge2 = fetch("SELECT COUNT(inbox) FROM $tab[mail] WHERE dest='$pmp[0]'AND src='$pimp[28]' AND time>=$time-86400 AND inbox='attacks';"); 

$gamed = mysql_fetch_array(mysql_query("SELECT attin,attout FROM $tab[game] WHERE round='$tru';"));

$high=round($pimp[0]*4);

$low=round($pimp[0]/2);

//if (($pmp[2] < $low) || ($high < $pmp[2])){ header("Location: mobster.php?pid=$pid&tru=$tru"); }

GAMEHEADER("attacking $pimp[1]");
?>



<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">



 <tr>



  <td align="center" valign="top">



<form method="post" action="hit.php?pid=<?=$pid?>&tru=<?=$tru?><?php if(isset($revenge)) print("&revenge=1");?><?php if(isset($contract_id)) print("&contract_id=".$contract_id);?>">



  <p><br>



      <font color="#FF0000" size="3"><b>make a hit</font>



      <font color="#FF0000"><br>

          Ready To Whack dis Bitch?</b> <br />
          <font size="2"><b>Revenge Attacks Within 24 Hours</b><br />
          
          <?=$pmp[1]?>
hit you <b><font color=red><? echo $revenge;?></b></font></font> <font color="#FF0000">times. || You hit <font size="2">
<?=$pmp[1]?>
</font></font>  <font color="#FF0000"><b><font color=red><?echo $revenge2;?></b> <font size="2">times.</font><br>



      <?



if ($pimp[5]+$pimp[26]+$pimp[27] == 0){?>

      <br>



      <b>you need men to make a hit</b><br>

          <? } else {



if ($hit)



   {



    $protect=$pmp[3]+$pmp[4];







        if(!$attack){?>

          <b>you didn't select an attack type.</b></font> 

          <font color="#FF0000">
          <? }



    elseif($pmp[18] == banned){?>

          <br>

          <b>you cannot attack a Mafioso that is frozen.</b></font> 

          <font color="#FF0000">
          <? }



	elseif($pmp[20] == $REMOTE_ADDR){?>

          <br>

          <b>attack being logged, you cannot attack Mafioso 

          with the same ip address.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($pimp[1] > 0) && ($pmp[17] == $pimp[1])){?>

          <b>you cannot attack Mafioso in your own crew. 

          Dumbass </b></font> 

          <font color="#FF0000">
          <? }



    elseif(($pmp[15] >= $gamed[0]) && ($revenge <=0)){?>

          <b>that Mafioso has been attacked to much today, 

          you to damn late</b></font> 

          <font color="#FF0000">
          <? }



    elseif($pimp[2] < 2){?>

          <b>you dont have enough turns to make a hit.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($pimp[3] >= $gamed[1]) && ($revenge <= 0)){?>

          <b>you have reached your attack limit.</b></font> 

          <font color="#FF0000">
          <? }



    elseif($pmp[16] != $pimp[4]){?>

          <b>you can only hit Mafioso in 

          <?=$city[0]?>

          .</b></font> 

          <font color="#FF0000">
          <? }



    elseif($protect > $time){$protect=$protect-$time;?>

          <b>that Mafioso is protected for 

          <?=$protect?>

          more seconds.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == driveby) && ($pimp[6] == 0)){?>

          <b>you need some rides before you can do a drive-by, 

          duh.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == driveby) && ($pmp[9]+$pmp[24]+$pmp[25]== 0)){?>

          <b>that Mafioso doesn't have any boys to do a drive-by 

          on.</b></font> 

          <font color="#FF0000">
          <? }

    elseif(($attack == bankem) && ($pmp[22] <= 0)){?>

          <b>that Mafioso doesnt have any banked cash to 

          snatch</b></font> 

          <font color="#FF0000">

          <? }

    elseif(($attack == bankem) && ($pmp[9]+$pmp[24]+$pmp[25]!= 0)){?>

          <b>try killing his DUS first</b></font> 

          <font color="#FF0000">
		  
          <? }



	elseif(($attack == jackrides) && ($pmp[14] == 0)){?>

          <b>that Mafioso has no rides to jack.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == jackrides) && ($pimp[6] == 0)){?>

          <b>you need at least 1 S-Class Limo's to jack rides.</b></font> 

          <font color="#FF0000">
          <? }



	elseif(($attack == jackplanes) && ($pmp[33] == 0)){?>

          <b>that Mafioso has no planes to jack.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == jackplanes) && ($pimp[29] == 0)){?>

          <b>you need at least 1 Plane to jack Planes.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == stealhoes) && (!preg_match ('/^[0-9]*$/i', $crack))){?>

          <b>you dont have that much coke to send.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == stealhoes) && ($crack > $pimp[7])){?>

          <b>you dont have that much coke to send.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == stealhoes) && (4 >= $crack)){?>
          <strong>you must send at least 5 crackrocks.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == stealthugs) && (!preg_match ('/^[0-9]*$/i', $weed))){?>

          <b>you dont have that weed to send.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack == stealthugs) && ($weed > $pimp[8])){?>

          <b>you dont have that much weed to send.</b></font> 

          <font color="#FF0000">
          <? }

    elseif(($attack == sabagegunops) && ($pmp[9]+$pmp[24]+$pmp[25]!= 0)){?>

          <b>try killing his DUS first</b></font> 

          <font color="#FF0000">
		  
		  <? }

    elseif(($attack == drugraid) && ($pmp[9]+$pmp[24]+$pmp[25]!= 0)){?>

          <b>try killing his DUS first</b></font> 

          <font color="#FF0000">
		  
          <? }



    elseif(($attack == stealthugs) && (4 >= $weed)){?>

          <b>you must send at least 5 grams of weed.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($high < $pmp[2]) && ($revenge <= 0)){?>

          <b>that Mafioso is out of range.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($pmp[2] < $low) && ($revenge <= 0)){?>

          <b>that Mafioso is out of range.</b></font> 

          <font color="#FF0000">
          <? }



    elseif(($attack==driveby) || ($attack==infest) || ($attack==bankem) || ($attack==invasion) || ($attack==stealhoes) || ($attack==stealthugs) || ($attack==jackrides) || ($attack==bussiness) || ($attack==drugraid) || ($attack==jackplanes))



{//ATTACK SCRIPT







$attopt=no;



$cash=money(2);



//USE THOSE RESOURCES



$usedope=dope($pimp[8],$pimp[5],2);



$usecondom=condom($pimp[9],$pimp[10],2);



$usecrack=crack($pimp[7],$pimp[10],2);



mysql_query("UPDATE $tab[pimp] SET condom=$usecondom, crack=$usecrack, weed=$usedope WHERE id='$id'");







$bonus=(($pimp[5]/10000)+1);







//IF THEY DONT HAVE CONDOMS, THEY USE MEDS, OR DIE



$infected=meds($pimp[9],$pimp[11],$pimp[10],2);



$killbystd=nomeds($pimp[9],$pimp[11],$pimp[10],2);                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 







if($pimp[12] < 80)                                                                                                                                  



  {



  $maxleave=round($trn*.75); $leftrand=rand(0, $maxleave);



  $thugleft=round($leftrand*(($bonus/7)+1));



  if($thugleft >= $pimp[5]){$thugleft=$pimp[5]; }



  }







if($pimp[13] < 80)



  {



  $maxleave=round($trn*.75); $leftrand=rand(0, $maxleave);



  $hoeleft=round($leftrand*(($bonus/9)+1));



  if($hoeleft >= $pimp[10]){$hoeleft=$pimp[10]; }



  }











    //FINAL UPGRADE



    $hoetl=fixinput($pimp[10]-$killbystd-$hoeleft); 



    $thugtl=fixinput($pimp[5]-$thugleft);



    $turntl=$pmp[0]-$trn;



    if($trn >= 60){$protect=60;}else{$protect=$trn;}



    mysql_query("UPDATE $tab[pimp] SET whore='$hoetl', thug='$thugtl' WHERE id='$id'");







    if($message){$message=filter($message);$addmsg="<table width=100%><tr><td align=center><small>$pimp[15] left you a note stapled to one of your boys forheads:</small><br><font color=0099cc>  $message </font></td></tr></table><br>";}



    include("attacks/$attack.php");







    $ifuckinghatephp = mysql_fetch_array(mysql_query("SELECT money FROM $tab[pimp] WHERE id='$id';"));



    $cashtl=fixinput($ifuckinghatephp[0]+$cash);



    mysql_query("UPDATE $tab[pimp] SET money='$cashtl' WHERE id='$id'");







    ?>

          <br>



        
          <?



    if(($thugleft > 0) && ($hoeleft > 0)){?>



          <br>
          </font>



          <font color="#FF0000">
          <?=commas($thugleft)?> 
          thug
          <?if($thugleft != 1){echo"s";}?> 
          and 
          <?=commas($hoeleft)?> 
          hoe
          <?if($hoeleft != 1){echo"s";}?> 
          got pissed and ditched you.
          <?}



elseif($thugleft > 0){?>
          <br>
          <?=commas($thugleft)?> 
          thug
          <?if($thugleft != 1){echo"s";}?> 
          got pissed and ditched you.
          <?}



elseif($hoeleft > 0){?>
          <br>
          <?=commas($hoeleft)?> 
          hoe
          <?if($hoeleft != 1){echo"s";}?> 
          got pissed and ditched you.
          <?}











    if($killbystd > 0){?>
          <br>
          </font> 

          <font color="#FF0000">
          <?=commas($killbystd)?>

          hoe 

          <?if($killbystd != 1){echo"s";}?>

          died of multiple STD's, Nasty Bitch. 

          <?}



    if($infected > 0){?>

          <br>

          Your hoes used 
          <?=commas($infected)?> 
          box
          <?if($infected != 1){echo"es";}?> 
          of medicine.
          <?}



    ?>
          <br>

          your hoes returned with them $ 

          <?=commas($cash)?>

          in cash smart bitch<br>



          <input type="hidden" name="hit" value="attack">



          <input type="hidden" name="attack" value="<?=$attack?>">



          <input type="hidden" name="pid" value="<?=$pid?>">



          <input type="hidden" name="crack" value="<?=$crack?>">



          <input type="hidden" name="weed" value="<?=$weed?>">



          <input type="button" class="button" value="go back" onclick="window.location.replace('hit.php?pid=<?=$pid?>&tru=<?=$tru?>')">
&nbsp;&nbsp;&nbsp;
<input type="submit" class="button" value="repeat">



    <br>

          <?







    //UPGRADE THERE NETWORTH



    $networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);



    mysql_query("UPDATE $tab[pimp] SET attackout=attackout+1, whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");



    //UPGRADE THERE NETWORTH



    $networth=net($pmp[0]);$wappy=hoehappy($pmp[0]);$tappy=thughappy($pmp[0]);



    mysql_query("UPDATE $tab[pimp] SET attackin=attackin+1, whappy='$wappy', thappy='$tappy',networth='$networth' WHERE id='$pmp[0]'");







   }else{?>

          <b>you didn't select attack an type.</b></font> 

          <font color="#FF0000">
          <?}



}



if($attopt != no){?>
          </font></p>



  <table cellspacing="10">



 <tr>



  <td align="right">



  <font size="3"><b>attacking <font color="#FF0000"><?=$pmp[1]?></font></b></font>



  <br>



  <font color="#FF0000">
  <? if(($pimp[3]>0) || ($pimp[20]>0))



    {



    ?>
  <small><b>your attacks</b></small></font><small><b><br>
  <?



    if($pimp[20]>0){?><?=$pimp[20]?> in <img src="<?=$site[img]?>/percentage/thug.png" width="<?=$pimp[20]?>" vspace="2" border="1" height="2"><br><? }



    if($pimp[3]>0){?><?=$pimp[3]?> out <img src="<?=$site[img]?>/percentage/hoe.png" width="<?=$pimp[3]?>" vspace="2" border="1" height="2"><br><? }



    ?></b></small><?



    }



  ?>



  <br><input type="submit" class="button" name="hit" value="attack">



  </td>



  <td><input type="radio" name="attack" value="driveby"> <? if(($pimp[6]*10) > $pimp[5]+$pimp[26]+$pimp[27]){$man=$pimp[5];}else{$man=$pimp[6]*10;}echo"$man";?> man drive-by.



  <br>
  <input type="radio" name="attack" value="invasion">
              Run up in <b> 
              <?=$pmp[1]?>
              's</b> headquarters&amp; steal weapons. <br>
              <input type="radio" name="attack" value="bussiness" />
Run up in <b>
<?=$pmp[1]?>
's</b> business.
<br />
<input type="radio" name="attack" value="drugraid" />
Run up in <b>
<?=$pmp[1]?>
's</b> Drug Stash.
<? if(!isset($contract_id) && $pmp[14] > 0){?>
              <br>
              <input type="radio" name="attack" value="jackrides">
              Rob the bitchez rides. 
              <? } ?>
<? if(!isset($contract_id) && $pmp[33] > 0){?>
              <br>
              <input type="radio" name="attack" value="jackplanes">
              Rob the bitchez planes. 
              <? } ?>
              <? if(!isset($contract_id) && ($pmp[11] > 0) && ($pimp[7] > 0)){?>
              
              <? /*<br><input type="radio" name="attack" value="stealhoes">
              T hrow 
              <input type="text" class="text" size="8" name="crack" value="<?=$stealhoes?>"> rocks at the hoes.*/?><?}?>


<? /*
  <? if(!isset($contract_id) && ($pmp[9] > 0) && ($pimp[8] > 0)){?><br><input type="radio" name="attack" value="stealthugs">
              T hrow 
              <input type="text" class="text" size="8" name="weed" value="<?=$stealthugs?>"> grams at the thugs.<?}?>*/?>



  </td>



 </tr>



</table>



        <div align="left"><b>leave a message at the scene?</b><br>

          <input type="text" class="text" size="50" name="message" maxlength="200">

          <br>

          <small><b><font color="#FF0000">200 charactor max</font></b></small>        </div>

      </form>



<?}}?>



<br>



<?=bar($id)?>



  </td>



 </tr>



</table>







<?



GAMEFOOTER();



?>