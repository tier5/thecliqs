<?

include("html.php");
ADMINHEADER("View info : $pimpinfo[0]'s");

mysql_query("UPDATE $tab[pimp] SET page='view info' WHERE id='$id'");

$pimpinfo = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,online,whore,thug,lowrider,networth,profile,lastattackby,lastattack,crew,description,city,id,status,trn,res,whappy,thappy,weed,crack,condom,medicine,glock,shotgun,uzi,ak47,attin,attout,attackin,attackout,bank,thugk,whorek,ip,host,sounds,code,money FROM $tab[pimp] WHERE id='$pid';"));
$pimpusrinfo = mysql_fetch_array(mysql_query("SELECT id,status,ip,lastip,host,credits,fullname,username,password,email,age,messager,online,membersince,statusexpire,referredby FROM $tab[user] WHERE code='$pimpinfo[38]';"));

$getgamesplayed = mysql_result(mysql_query("SELECT COUNT(*) FROM $tab[stat] WHERE user='$pimpusrinfo[7]';"),0);



if($addcredits > 0)

{

//    $cre = mysql_fetch_array(mysql_query("SELECT credits,username,code FROM $tab[user] WHERE id='$id';"));



      $give_credit=$pimpusrinfo[5]+$addcredits; //give credits to user xfering to

      mysql_query("UPDATE $tab[user] SET credits='$give_credit' WHERE code='$pimpinfo[38]'");



      $adderror="$addcredits credits have been added to $pimpusrinfo[7]";

}



if($changemail==yes){

if(!ereg("^.+@.+\\..+$", $changeemail))

       { $adderror="<br />Invalid password: a-Z 0-9 -_ charactors only.";}

elseif(fetch("SELECT email FROM $tab[user] WHERE email='$changeemail';"))

              { $msg="&#149 That e-mail address has already been used."; $changeemail="";}

else{

	$adderror='<br>email changed!';

	mysql_query("UPDATE $tab[user] SET email='$changeemail' WHERE code='$pimpinfo[38]'");

	}

}



if($changepswd==yes){

//if($pimpusrinfo[1]=="admin"){$adderror="<br />Error: admin pw's must be changed manually.";}

if((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $newpw)) || (strstr($newpw,".")))

       { $adderror="<br />Invalid password: a-Z 0-9 -_ charactors only.";}

elseif((strlen($newpw) <= 2) || (strlen($newpw) >= 13))

       { $adderror="<br />Invalid password: must be at least 3-12 in length.";}

else{

	$adderror='<br>password changed!';

	mysql_query("UPDATE $tab[user] SET password='$newpw' WHERE code='$pimpinfo[38]'");

	}

}



if($changstat==yes){



	if($pimpusrinfo[1]=="admin1" || $newstatus=="admin1") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}

    	else{

      	    if($newstatus=="supporter"){

 	      $getstatexpire = mysql_fetch_array(mysql_query("SELECT statusexpire FROM $tab[user] WHERE code='$pimpinfo[38]';"));



 	      if($getstatexpire[0]>time()){$newstatusexpire=$getstatexpire[0]+$statexp;}

	      else{$newstatusexpire=$getstatexpire[0]+$time+$statexp;}



    	      mysql_query("UPDATE $tab[user] SET statusexpire='$newstatusexpire' WHERE code='$pimpinfo[38]'");

	      }

	   else{mysql_query("UPDATE $tab[user] SET statusexpire='' WHERE code='$pimpinfo[38]'");}



	mysql_query("UPDATE $tab[user] SET status='$newstatus' WHERE code='$pimpinfo[38]'");

	mysql_query("UPDATE $tab[user] SET reason='$banreason' WHERE code='$pimpinfo[38]'");

      

	$adderror="user status changed to <font color=#FF0000>$newstatus</font>";

	}

}



if($changpimpstat==yes){

	if($pimpusrinfo[1]=="admin1" || $newstatus=="admin1") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}

    	else{



	mysql_query("UPDATE $tab[pimp] SET status='$newstatus' WHERE code='$pimpinfo[38]'");

      

	$adderror="pimp status changed to <font color=#FF0000>$newstatus</font>";

	}

}





if($addturn || $addres || $addhoe || $addthug || $addcondom || $addmedicine || $addweed || $addcrack || $addglock || $addshotgun || $adduzi || $addak47 || $addlowtruer || $addcash || $addbank){

	if($addturn){

	   $give_turns=$pimpinfo[16]+$addturn;

	   mysql_query("UPDATE $tab[pimp] SET trn='$give_turns' WHERE code='$pimpinfo[38]'");

	}

	if($addres){

	   $give_res=$pimpinfo[17]+$addres;

	   mysql_query("UPDATE $tab[pimp] SET res='$give_res' WHERE code='$pimpinfo[38]'");

	}

	if($addhoe){

	   $give_hoe=$pimpinfo[4]+$addhoe;

	   mysql_query("UPDATE $tab[pimp] SET whore='$give_hoe' WHERE code='$pimpinfo[38]'");

	}

	if($addthug){

	   $give_thug=$pimpinfo[5]+$addthug;

	   mysql_query("UPDATE $tab[pimp] SET thug='$give_thug' WHERE code='$pimpinfo[38]'");

	}

	if($addcondom){

	   $give_condoms=$pimpinfo[22]+$addcondom;

	   mysql_query("UPDATE $tab[pimp] SET condom='$give_condoms' WHERE code='$pimpinfo[38]'");

	}

	if($addmedicine){

	   $give_meds=$pimpinfo[23]+$addmedicine;

	   mysql_query("UPDATE $tab[pimp] SET medicine='$give_meds' WHERE code='$pimpinfo[38]'");

	}

	if($addweed){

	   $give_weed=$pimpinfo[20]+$addweed;

	   mysql_query("UPDATE $tab[pimp] SET weed='$give_weed' WHERE code='$pimpinfo[38]'");

	}

	if($addcrack){

	   $give_crack=$pimpinfo[21]+$addcrack;

	   mysql_query("UPDATE $tab[pimp] SET crack='$give_crack' WHERE code='$pimpinfo[38]'");

	}

	if($addglock){

	   $give_glock=$pimpinfo[24]+$addglock;

	   mysql_query("UPDATE $tab[pimp] SET glock='$give_glock' WHERE code='$pimpinfo[38]'");

	}

	if($addshotgun){

	   $give_shotgun=$pimpinfo[25]+$addshotgun;

	   mysql_query("UPDATE $tab[pimp] SET shotgun='$give_shotgun' WHERE code='$pimpinfo[38]'");

	}

	if($adduzi){

	   $give_uzi=$pimpinfo[26]+$adduzi;

	   mysql_query("UPDATE $tab[pimp] SET uzi='$give_uzi' WHERE code='$pimpinfo[38]'");

	}

	if($addak47){

	   $give_ak47=$pimpinfo[27]+$addak47;

	   mysql_query("UPDATE $tab[pimp] SET ak47='$give_ak47' WHERE code='$pimpinfo[38]'");

	}

	if($addlowtruer){

	   $give_lowtruer=$pimpinfo[6]+$addlowtruer;

	   mysql_query("UPDATE $tab[pimp] SET lowtruer='$give_lowtruer' WHERE code='$pimpinfo[38]'");

	}

	if($addcash){

	   $give_cash=$pimpinfo[39]+$addcash;

	   mysql_query("UPDATE $tab[pimp] SET money='$give_cash' WHERE code='$pimpinfo[38]'");

	}

	if($addbank){

	   $give_cash=$pimpinfo[32]+$addbank;

	   mysql_query("UPDATE $tab[pimp] SET bank='$give_cash' WHERE code='$pimpinfo[38]'");

	}





	if($addturn || $addres){$adderror="$addturn turns and $addres reserves have been added";}

	if($addhoe || $addthug){$adderror="$addhoe hoes and $addthug thugs have been added";}

	if($addcondom){$adderror="$addcondom condoms have been added";}

	if($addmedicine){$adderror="$addmedicine med packs have been added";}

	if($addweed){$adderror="$addweed grams of weed have been added";}

	if($addcrack){$adderror="$addcrack rocks have been added";}

	if($addglock){$adderror="$addglock glocks have been added";}

	if($addshotgun){$adderror="$addshotgun shutguns have been added";}

	if($adduzi){$adderror="$adduzi uzi's have been added";}

	if($addak47){$adderror="$addak47 ak47's have been added";}

	if($addlowtruer){$adderror="$addlowtruer lowtruer's have been added";}

	if($addcash){$adderror="$$addcash cash has been added";}

	if($addbank){$adderror="$$addbank cash has been added";}

}




?><body bgcolor="#000000" text="#FFFFFF">

<table width="100%" height="100%" bgcolor="000000">

 <tr>

  <td align="center" valign="top">

  <br><font color="#FFFFFF"><b>::| admin tools¹ |::</b></font><br/><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&acctinfo=yes&addcred=yes">open all</a> - <a href="?pid=<?=$pid?>&tru=<?=$tru?>">refresh</a> - <a href="?pid=<?=$pid?>&tru=<?=$tru?>">close all</a></small><br/><br/><?if($adderror){echo "<small>".$adderror."<br/><br/></small>";}?>



  <table width="95%" cellspacing="1">

    <tr>

      <? if(!$_GET['pimpinfo']){?>

      <td align="center" class="whitetext" colspan="3"><small>[ <a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">Pimp Information</a> ]</small></td>

    </tr>

      <?}else{?>

      <td align="center" class="whitetext" colspan="3"><small><a href="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>">Pimp Information</a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Pimp Name:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?echo $pimpinfo[0];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Pimp Status:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['changepimpstatus']){?>

      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]=="admin"){echo $pimpusrinfo[1];}else{?><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&changepimpstatus=yes"><?echo $pimpinfo[15];}?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&changepimpstatus=yes"><?echo $pimpinfo[15];?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Change Status To:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&changpimpstat=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext">

	    <select name="newstatus" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">

 	      <option value="normal"<?if($pimpusrinfo[1]=="normal"){echo " selected='selected'";}?>>normal</option>
		  
		  <option value="helper"<?if($pimpusrinfo[1]=="helper"){echo " selected='selected'";}?>>helper</option>

 	      <option value="supporter"<?if($pimpusrinfo[1]=="supporter"){echo " selected='selected'";}?>>supporter</option>

 	      <option value="admin"<?if($pimpusrinfo[1]=="admin"){echo " selected='selected'";}?>>admin</option>

		  <option value="banned"<?if($pimpusrinfo[1]=="banned"){echo " selected='selected'";}?>>banned</option>

 	      <option value="disabled"<?if($pimpusrinfo[1]=="disabled"){echo " selected='selected'";}?>>disabled</option>

            </select> 

	    <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Turns / Reserves:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addtrnres']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addtrnres=yes"><?echo $pimpinfo[16]." / ".$pimpinfo[17];?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo $pimpinfo[16]." / ".$pimpinfo[17];?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addturn" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> turns and <input type="text" class="text" name="addres" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> reserves <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Rank / National:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?echo $pimpinfo[1]." / ".$pimpinfo[2];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Last Online:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?echo countdown($pimpinfo[3]);?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Cash:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addcsh']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addcsh=yes"><?echo "$".commas($pimpinfo[39]);?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo "$".commas($pimpinfo[40]);?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Much?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add $<input type="text" class="text" name="addcash" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> cash <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

	<tr>

      <td width="150" align="right" class="whitetext"><small><b>Bank:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addbnk']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addbnk=yes"><?echo "$".commas($pimpinfo[32]);?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo "$".commas($pimpinfo[32]);?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Much?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add $<input type="text" class="text" name="addbank" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> cash <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Hoes / Thugs:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addhoethug']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addhoethug=yes"><?echo commas($pimpinfo[4])." / ".commas($pimpinfo[5]);?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[4])." / ".commas($pimpinfo[5]);?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addhoe" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> hoes and <input type="text" class="text" name="addthug" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> thugs <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Condoms:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addcond']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addcond=yes"><?echo commas($pimpinfo[22]);?></a> condoms</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[22]);?></a> condoms</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addcondom" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> condoms <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

	<tr>

      <td width="150" align="right" class="whitetext"><small><b>Medicine:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addmeds']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addmeds=yes"><?echo commas($pimpinfo[23]);?></a> med packs</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[23]);?></a> med packs</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addmedicine" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> condoms <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Weed:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addwed']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addwed=yes"><?echo commas($pimpinfo[20]);?></a> grams</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[20]);?></a> grams</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Much?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addweed" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> condoms <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Crack:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addcrk']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addcrk=yes"><?echo commas($pimpinfo[21]);?></a> rocks</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[21]);?></a> rocks</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addcrack" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> condoms <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Glocks:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addglk']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addglk=yes"><?echo commas($pimpinfo[24]);?></a> glocks</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[24]);?></a> glocks</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addglock" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> condoms <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Shotguns:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addshtgn']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addshtgn=yes"><?echo commas($pimpinfo[25]);?></a> shotguns</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[25]);?></a> shotguns</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addshotgun" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> shotguns <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Uzi:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['adduzi']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&adduzi=yes"><?echo commas($pimpinfo[26]);?></a> uzi's</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[26]);?></a> uzi's</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="adduzi" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> uzi's <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Ak47:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addak']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addak=yes"><?echo commas($pimpinfo[27]);?></a> ak47's</small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo commas($pimpinfo[27]);?></a> ak47's</small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addak47" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> ak47's <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Lowtruers:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addlowrdr']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes&addlowrdr=yes"><?echo $pimpinfo[6];?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes"><?echo $pimpinfo[6];?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&pimpinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addlowtruer" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> lowtruer's <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Net Worth:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo "$".commas($pimpinfo[7]);?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Attack In / Out:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $pimpinfo[28]." / ".$pimpinfo[29];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Thug / Hoe Kills:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $pimpinfo[33]." / ".$pimpinfo[34];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Default Turns:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $pimpinfo[37];?></small></td>

    </tr>

	<tr>

      <td width="150" align="right" class="whitetext"><small><b>Lotto Number:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $pimpinfo[41];?></small></td>

    </tr>

    <?}?>

    <tr>

      <td height="10" colspan="3">&nbsp;</td>

    </tr>

    <tr>

      <? if(!$_GET['acctinfo']){?>

      <td align="center" class="whitetext" colspan="3"><small>[ <a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes">Account Information</a> ]</small></td>

    </tr>

      <?}else{?>

      <td align="center" class="whitetext" colspan="3"><small><a href="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>">Account Information</a></small></td>

    </tr>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Userame:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?echo $pimpusrinfo[7]." (#".$pimpusrinfo[0].")";?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Password:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['changepw']){?>

      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]=="admin"){echo "<i>- hidden -</i>";}else{?><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes&changepw=yes"><?echo $pimpusrinfo[8];}?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes"><?if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[8];}else{echo "<i>- hidden -</i>";}?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Change Password To:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&changepswd=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><input type="text" class="text" name="newpw" maxlength="12" value="" size="15" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"/> <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Account Status:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['changestatus']){?>

      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]=="admin"){echo $pimpusrinfo[1];}else{?><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes&changestatus=yes"><?echo $pimpusrinfo[1];}?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes"><?echo $pimpusrinfo[1];?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Change Status To:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&changstat=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext">

	    <select name="newstatus" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">

 	      <option value="normal"<?if($pimpusrinfo[1]=="normal"){echo " selected='selected'";}?>>normal</option>

 	      <option value="supporter"<?if($pimpusrinfo[1]=="supporter"){echo " selected='selected'";}?>>supporter</option>

		  <option value="admin"<?if($pimpusrinfo[1]=="admin"){echo " selected='selected'";}?>>admin</option>

 	      <option value="banned"<?if($pimpusrinfo[1]=="banned"){echo " selected='selected'";}?>>banned</option>

 	      <option value="disabled"<?if($pimpusrinfo[1]=="disabled"){echo " selected='selected'";}?>>disabled</option>

            </select> 

	    <select name="statexp" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">

 	      <option value="7776000">90 days</option>

 	      <option value="5184000">60 days</option>

 	      <option value="3888000">45 days</option>

 	      <option value="2592000">30 days</option>

 	      <option value="1809000">21 days</option>

 	      <option value="1206000">14 days</option>

 	      <option value="603000">7 days</option>

 	      <option value="516600">6 days</option>

 	      <option value="430200">5 days</option>

 	      <option value="345600">4 days</option>

 	      <option value="259200">3 days</option>

 	      <option value="172800">2 days</option>

 	      <option value="86400">1 days</option>

            </select> 

	    <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    </tr>

    <?if($pimpusrinfo[1]=="supporter"){?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Status Expires:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?if($pimpusrinfo[14]!="0"){echo countup($pimpusrinfo[14]);}else{echo "n/a";}?></small></td>

    </tr>

    <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Email Address:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['updtemail']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes&updtemail=yes"><?echo $pimpusrinfo[9];?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes"><?echo $pimpusrinfo[9];?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Change Email To:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&changemail=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><input type="text" class="text" name="changeemail" value="" size="15" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"/> <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Full Name:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?php echo $pimpusrinfo[6];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Signup Date:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><?php echo date("M dS, Y", $pimpusrinfo[13]);?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Credits:</b></small>

      <td width="5">&nbsp;</td>

      <? if(!$_GET['addcred']){?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes&addcred=yes"><?echo commas($pimpusrinfo[5]);?></a></small></td>

    </tr>

      <?}else{?>

      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes"><?echo commas($pimpusrinfo[5]);?></a></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>

      <td width="5">&nbsp;</td>

      <form method="post" action="viewinfo69.php?pid=<?=$pid?>&tru=<?=$tru?>&acctinfo=yes">

      <td align="left" class="whitetext"><small>

	<table border="0" cellpadding="0" cellspacing="0">

	 <tr>

	  <td class="whitetext"><small>Add <input type="text" class="text" name="addcredits" value="0" size="5" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"> credits <input type="submit" class="button" value="Add" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=30; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000">

	</td>

	 </tr>

	</table>

	</small>

      </td>

      </form>

    </tr>

      <?}?>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Rounds Played:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $getgamesplayed;?></small></td>

    </tr>

	<tr>

      <td width="150" align="right" class="whitetext"><small><b>Logins:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? echo $pimpusrinfo[16];?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Signup IP:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[2];}else{echo "<i>- hidden -</i>";}?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Last IP:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[3];}else{echo "<i>- hidden -</i>";}?></small></td>

    </tr>

    <tr>

      <td width="150" align="right" class="whitetext"><small><b>Host Name:</b></small>

      <td width="5">&nbsp;</td>

      <td align="left" class="whitetext"><small><? if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[4];}else{echo "<i>- hidden -</i>";}?></small></td>

    </tr>

	<tr>

      <td width="150" align="right" class="whitetext"><small><b>Referred by:</b></small>

      <td width="5">&nbsp;</td>

      <? if($pimpusrinfo[15] != '') { ?><td align="left" class="whitetext"><small><? echo "(#)$pimpusrinfo[15]";?></small></td>

	  <? } else { ?><td align="left" class="whitetext"><small><? echo "$pimpusrinfo[15]";?></small></td><? } ?>

    </tr>

	</small>

      </td>

      </form>

    </tr>

    <?}?>



  </table>



  </td>

 </tr>

</table>