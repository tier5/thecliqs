<?
include("html.php");


$pimpinfo = mysql_fetch_array(mysql_query("SELECT username,code,id FROM $tab[user] WHERE id='$pid';"));
$pimpusrinfo = mysql_fetch_array(mysql_query("SELECT id,status,ip,lastip,host,credits,fullname,username,password,email,age,messager,online,membersince,statusexpire FROM $tab[user] WHERE id='$pimpinfo[id]';"));
$getgamesplayed = mysql_result(mysql_query("SELECT COUNT(*) FROM $tab[stat] WHERE user='$pimpusrinfo[7]';"),0);

if($addcredits > 0)
{
//    $cre = mysql_fetch_array(mysql_query("SELECT credits,username,code FROM $tab[user] WHERE id='$id';"));

      $give_credit=$pimpusrinfo[5]+$addcredits; //give credits to user xfering to
      mysql_query("UPDATE $tab[user] SET credits='$give_credit' WHERE code='$pimpinfo[1]'");

      $adderror="$addcredits credits have been added to $pimpusrinfo[7]";
}

if($changemail==yes){
if(!ereg("^.+@.+\\..+$", $changeemail))
       { $adderror="<br />Invalid password: a-Z 0-9 -_ charactors only.";}
elseif(fetch("SELECT email FROM $tab[user] WHERE email='$changeemail';"))
              { $msg="&#149 That e-mail address has already been used."; $changeemail="";}
else{
	$adderror='<br>email changed!';
	mysql_query("UPDATE $tab[user] SET email='$changeemail' WHERE code='$pimpinfo[1]'");
	}
}

if($changepswd==yes){
if($pimpusrinfo[1]=="admin"){$adderror="<br />Error: admin pw's must be changed manually.";}
elseif((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $newpw)) || (strstr($newpw,".")))
       { $adderror="<br />Invalid password: a-Z 0-9 -_ charactors only.";}
elseif((strlen($newpw) <= 2) || (strlen($newpw) >= 13))
       { $adderror="<br />Invalid password: must be at least 3-12 in length.";}
else{
	$adderror='<br>password changed!';
	mysql_query("UPDATE $tab[user] SET password='$newpw' WHERE code='$pimpinfo[1]'");
	}
}

if($changstat==yes){

	if($pimpusrinfo[1]=="admin" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{
      	    if($newstatus=="supporter"){
 	      $getstatexpire = mysql_fetch_array(mysql_query("SELECT statusexpire FROM $tab[user] WHERE code='$pimpinfo[1]';"));

 	      if($getstatexpire[0]>time()){$newstatusexpire=$getstatexpire[0]+$statexp;}
	      else{$newstatusexpire=$getstatexpire[0]+$time+$statexp;}

    	      mysql_query("UPDATE $tab[user] SET statusexpire='$newstatusexpire' WHERE code='$pimpinfo[1]'");
	      }
	   else{mysql_query("UPDATE $tab[user] SET statusexpire='' WHERE code='$pimpinfo[1]'");}

	mysql_query("UPDATE $tab[user] SET status='$newstatus' WHERE code='$pimpinfo[1]'");
	mysql_query("UPDATE $tab[user] SET reason='$banreason' WHERE code='$pimpinfo[1]'");
      
	$adderror="user status changed to <font color=#FF0000>$newstatus</font>";
	}
}

if($changpimpstat==yes){
	if($pimpusrinfo[1]=="admin" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{

	mysql_query("UPDATE $tab[pimp] SET status='$newstatus' WHERE code='$pimpinfo[1]'");
      
	$adderror="pimp status changed to <font color=#FF0000>$newstatus</font>";
	}
}

if($changereason){
	
	mysql_query("UPDATE $tab[user] SET reason='$changereason' WHERE code='$pimpinfo[1]'");
	$adderror='<br>Reason applied!';}

if($changsubscriber1a==yes){

	if($pimpusrinfo[1]=="admin1" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{
      	    if($newstatus=="yes"){
 	      $getstatexpire = mysql_fetch_array(mysql_query("SELECT sub1aexpires FROM $tab[user] WHERE code='$pimpinfo[1]';"));

 	      if($getstatexpire[0]>time()){$newstatusexpire=$getstatexpire[0]+$statexp;}
	      else{$newstatusexpire=$getstatexpire[0]+$time+$statexp;}

    	      mysql_query("UPDATE $tab[user] SET sub1aexpires='$newstatusexpire' WHERE code='$pimpinfo[1]'");
	      }
	   else{mysql_query("UPDATE $tab[user] SET sub1aexpires='' WHERE code='$pimpinfo[1]'");}

	mysql_query("UPDATE $tab[user] SET subscribe1a='$newstatus' WHERE code='$pimpinfo[1]'");
      
	$adderror="user silver subscriber status changed to <font color=#FF0000>$newstatus</font>";
	}
}
if($changpimpsubscriber1a==yes){
	if($pimpusrinfo[1]=="admin" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{

	mysql_query("UPDATE $tab[pimp] SET subscribe1a='$newstatus' WHERE code='$pimpinfo[1]'");
      
	$adderror="pimp silver subscription status changed to <font color=#FF0000>$newstatus</font>";
	}
}

if($changsubscriber2b==yes){

	if($pimpusrinfo[1]=="admin1" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{
      	    if($newstatus=="yes"){
 	      $getstatexpire = mysql_fetch_array(mysql_query("SELECT sub2bexpires FROM $tab[user] WHERE code='$pimpinfo[1]';"));

 	      if($getstatexpire[0]>time()){$newstatusexpire=$getstatexpire[0]+$statexp;}
	      else{$newstatusexpire=$getstatexpire[0]+$time+$statexp;}

    	      mysql_query("UPDATE $tab[user] SET sub2bexpires='$newstatusexpire' WHERE code='$pimpinfo[1]'");
	      }
	   else{mysql_query("UPDATE $tab[user] SET sub2bexpires='' WHERE code='$pimpinfo[1]'");}

	mysql_query("UPDATE $tab[user] SET subscribe2b='$newstatus' WHERE code='$pimpinfo[1]'");
	      
	$adderror="user gold subscriber status changed to <font color=#FF0000>$newstatus</font>";
	}
}
if($changpimpsubscrier2b==yes){
	if($pimpusrinfo[1]=="admin" || $newstatus=="admin") {$adderror="admin status <font color=#FF0000>MUST</font> be changed manually!";}
    	else{

	mysql_query("UPDATE $tab[pimp] SET subscribe2b='$newstatus' WHERE code='$pimpinfo[1]'");
      
	$adderror="pimp gold subscription status changed to <font color=#FF0000>$newstatus</font>";
	}
}
admin();
secureheader();
siteheader();
?><body bgcolor="#000000" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF">
<table width="100%" height="100%">
 <tr>
  <td align="center" valign="top">
  <br><font color="#FFFFFF"><b>::| admin tools¹ |::</b></font><br/><small><a href="?pid=<?=$pid?>&acctinfo=yes&addcred=yes">open all</a> - <a href="?pid=<?=$pid?>">refresh</a> - <a href="?pid=<?=$pid?>">close all</a></small><br/><br/><?if($adderror){echo "<small>".$adderror."<br/><br/></small>";}?>

  <table width="95%" cellspacing="1">
      <? if(!$_GET['acctinfo']){?>
      <td align="center" class="whitetext" colspan="3"><small>[ <a href="?pid=<?=$pimpusrinfo[0]?>&acctinfo=yes">Account Information</a> ]</small></td>
    </tr>
      <?}else{?>
      <td align="center" class="whitetext" colspan="3"><small><a href="viewuserinfo.php?pid=<?=$pimpusrinfo[0]?>">Account Information</a></small></td>
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
      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]=="admin"){echo "<i>- hidden -</i>";}else{?><a href="?pid=<?=$pid?>&acctinfo=yes&changepw=yes"><?echo $pimpusrinfo[8];}?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?id=<?=$id?>&acctinfo=yes"><?if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[8];}else{echo "<i>- hidden -</i>";}?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Password To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changepswd=yes">
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
      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]=="admin"){echo $pimpusrinfo[1];}else{?><a href="?pid=<?=$pid?>&acctinfo=yes&changestatus=yes"><?echo $pimpusrinfo[1];}?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo $pimpusrinfo[1];?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Status To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changstat=yes">
      <td align="left" class="whitetext"><small>
	<table border="0" cellpadding="0" cellspacing="0">
	 <tr>
	  <td class="whitetext">
	    <select name="newstatus" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="normal">normal</option>
 	      <option value="supporter">supporter</option>
		  <option value="admin">admin</option>
 	      <option value="banned">banned</option>
 	      <option value="disabled">disabled</option>
 	      <option value="inactive">inactive</option>
 	      <option value="unverified">unverified</option>
            </select> 
	    <select name="statexp" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="15552000">180 days</option>
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
      <td align="left" class="whitetext"><small><? countdown($pimpusrinfo[14])?></small></td>
    </tr>
    <?}?>
	    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Silver Subscriber Status:</b></small>
      <td width="5">&nbsp;</td>
      <? if(!$_GET['changesubscriber1a']){?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes&changesubscriber1a=yes"><?php echo $pimpusrinfo[17];?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo $pimpusrinfo[17];?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Silver Subscriber To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changsubscriber1a=yes">
      <td align="left" class="whitetext"><small>
	<table border="0" cellpadding="0" cellspacing="0">
	 <tr>
	  <td class="whitetext">
	    <select name="newstatus" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="yes">yes</option>
 	      <option value="no">no</option>
            </select> 
	    <select name="statexp" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="15552000">180 days</option>
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
    <?if($pimpusrinfo[17]=="yes"){?>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Silver Subscription Expires:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><? countdown($pimpusrinfo[18])?></small></td>
    </tr>
    <?}?>
	    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Gold Subscriber Status:</b></small>
      <td width="5">&nbsp;</td>
      <? if(!$_GET['changesubscriber2b']){?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes&changesubscriber2b=yes"><?php echo $pimpusrinfo[19];?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo $pimpusrinfo[19];?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Gold Subscriber To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changsubscriber2b=yes">
      <td align="left" class="whitetext"><small>
	<table border="0" cellpadding="0" cellspacing="0">
	 <tr>
	  <td class="whitetext">
	    <select name="newstatus" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="yes">yes</option>
 	      <option value="no">no</option>
            </select> 
	    <select name="statexp" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000">
 	      <option value="15552000">180 days</option>
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
    <?if($pimpusrinfo[19]=="yes"){?>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Gold Subscription Expires:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><? countdown($pimpusrinfo[20])?></small></td>
    </tr>
    <?}?>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Email Address:</b></small>
      <td width="5">&nbsp;</td>
      <? if(!$_GET['updtemail']){?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes&updtemail=yes"><?echo $pimpusrinfo[9];?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo $pimpusrinfo[9];?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Email To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changemail=yes">
      <td align="left" class="whitetext"><small>
	<table border="0" cellpadding="0" cellspacing="0">
	 <tr>
	  <td class="whitetext"><input type="text" class="text" name="changeemail" value="" size="15" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"/> <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>
	</td>
	 </tr><?}?>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Reason for ban:</b></small>
      <td width="5">&nbsp;</td>
      <? if(!$_GET['changereason']){?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes&changereason=yes">Click here ... <?echo $pimpusrinfo[reason];?>  </a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo $pimpusrinfo[reason];?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Change Reason To:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&changereason=yes">
      <td align="left" class="whitetext"><small>
	<table border="0" cellpadding="0" cellspacing="0">
	 <tr>
	  <td class="whitetext"><input type="text" class="text" name="changereason" value="" size="15" style="background:#ffffff; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); font-family: verdana, arial, tahoma, helvetica; font-size:10px; color:#000000"/> <input type="submit" class="button" value="Update" style="background:#ffffff; font-family: Arial, Tahoma, Helvetica; font-size:10px; height=15; width=50; border-left: 1px solid rgb(0,0,0); border-right: 1px solid rgb(0,0,0); border-top: 1px solid rgb(0,0,0); border-bottom: 1px solid rgb(0,0,0); color:#000000"/>
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
      <td align="left" class="whitetext"><small><?echo $pimpusrinfo[6];?></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Signup Date:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><?echo date("M dS, Y", $pimpusrinfo[13]);?></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Credits:</b></small>
      <td width="5">&nbsp;</td>
      <? if(!$_GET['addcred']){?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes&addcred=yes"><?echo commas($pimpusrinfo[5]);?></a></small></td>
    </tr>
      <?}else{?>
      <td align="left" class="whitetext"><small><a href="?pid=<?=$pid?>&acctinfo=yes"><?echo commas($pimpusrinfo[5]);?></a></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Add How Many?:</b></small>
      <td width="5">&nbsp;</td>
      <form method="post" action="viewuserinfo.php?pid=<?=$pid?>&acctinfo=yes">
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
      <td align="left" class="whitetext"><small><?echo $getgamesplayed;?></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Signup IP:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[2];}else{echo "<i>- hidden -</i>";}?></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Last IP:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[3];}else{echo "<i>- hidden -</i>";}?></small></td>
    </tr>
    <tr>
      <td width="150" align="right" class="whitetext"><small><b>Host Name:</b></small>
      <td width="5">&nbsp;</td>
      <td align="left" class="whitetext"><small><?if($pimpusrinfo[1]!="admin"){echo $pimpusrinfo[4];}else{echo "<i>- hidden -</i>";}?></small></td>
    </tr>
    <?}?>

  </table>
  </td>
 </tr>
</table>
<?
sitefooter();
?>