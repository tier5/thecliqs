<?php 
include("html.php");
ADMINHEADER("Admin Options");

$pimpinfo = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,online,whore,thug,lowrider,networth,profile,lastattackby,lastattack,crew,description,city,id,status,trn,res,whappy,thappy,weed,crack,condom,medicine,glock,shotgun,uzi,ak47,attin,attout,attackin,attackout,thugk,whorek,ip,host,defaultturns,sounds,code,money FROM $tab[pimp] WHERE pimp='$pid';"));
$pimpusrinfo = mysql_fetch_array(mysql_query("SELECT id,status,ip,lastip,host,credits,fullname,username,password,email,age,messager,online,membersince,statusexpire,referredby FROM $tab[user] WHERE code='$pimpinfo[39]';"));
$getgamesplayed = mysql_result(mysql_query("SELECT COUNT(*) FROM $tab[stat] WHERE user='$pimpusrinfo[7]';"),0);
?><head>
<script language="javascript" type="text/javascript">
function limitText(limitField, limitCount, limitNum) {
	if (limitField.value.length > limitNum) {
		limitField.value = limitField.value.substring(0, limitNum);
	} else {
		limitCount.value = limitNum - limitField.value.length;
	}
}
</script>
<style type="text/css">
<!--
.style3 {font-size: 16px}
body,td,th {
	color: #000000;
}
a:link {
	color: #FF0000;
	text-decoration: none;
}
a:visited {
	text-decoration: none;
	color: #FF0000;
}
a:hover {
	text-decoration: none;
	color: #CCCCCC;
}
a:active {
	text-decoration: none;
}
.style5 {font-size: 16px; font-weight: bold; }
-->
</style>
</head>

<?php if ($action == resetlotto) { ?></div></td>
  </tr>
</table>
<?
exit;
} 
?>
<?php if ($action == addstaff) { ?>
<style type="text/css">
<!--
.style4 {color: #999999}
-->
</style>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Changing a Players Status? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">
	<?php 
	if ($now == doit) { 
		if (!$uid) {
			print "<div align=center>Who's Status do you want to change?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=addstaff>Back</a></div>";
			exit;
			}
		if ($uid == 1) {
			print "<div align=center>You cant change the status of the Owner</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=addstaff>Back</a></div>";
			exit;
			}
		$getstatexpire = mysql_fetch_array(mysql_query("SELECT statusexpire FROM $tab[user] WHERE code='$pimpinfo[39]';"));

 	      if($getstatexpire[0]>time()){$newstatusexpire=$getstatexpire[0]+$statexp;}
	      else{$newstatusexpire=$getstatexpire[0]+$time+$statexp;}
		$time = time();
		print "<div align=center>$uid's status was changed to $status<br>It will expire on ".date("M dS,Y", $time+$expires)."</div>";
		mysql_query("UPDATE $tab[user] SET status='$status', statusexpire=$time+$expires WHERE username='$uid'");
	}
	?>	</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
      <form action="adminonly.php?tru=<?=$tru?>&action=addstaff&now=doit" method="post">
	  <table width="60%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><div align="right">Username:</div></td>
          <td><input type="text" name="uid"></td>
        </tr>
        <tr>
          <td><div align="right">status:</div></td>
          <td>
		  <select name="status">
		  	<option value="normal"<? if($pimpusrinfo[1]=="normal"){echo " selected='selected'";}?>>normal</option>
 	        <option value="supporter"<? if($pimpusrinfo[1]=="supporter"){echo " selected='selected'";}?>>supporter</option>
		    <option value="admin"<? if($pimpusrinfo[1]=="admin"){echo " selected='selected'";}?>>admin</option>
 	        <option value="banned"<? if($pimpusrinfo[1]=="banned"){echo " selected='selected'";}?>>banned</option>
 	        <option value="disabled"<? if($pimpusrinfo[1]=="disabled"){echo " selected='selected'";}?>>disabled</option>
          </select>		  </td>
        </tr>
        <tr>
          <td><div align="right">Expires:</div></td>
          <td>
		  <select name="expires">
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
			  <option value="">Never</option>
          </select>		  </td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" value="Submit"></td>
        </tr>
      </table>
	  </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
      <table width="100%"  border="0" cellspacing="1" cellpadding="0">
        <tr>
          <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
        </tr>
      </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == handcredits) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Handing out some reserves? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><?php 
	if ($now == doit) { 
		if (!$uid) {
			print "<div align=center>Who's gonna get the reserves?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=handcredits>Back</a></div>";
			exit;
			}
		print "<div align=center>$uid has been given ".commas($howmanycreds)." reserves</div>";
		mysql_query("UPDATE $tab[pimp] SET res=res+$howmanycreds WHERE pimp='$uid'");
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=handcredits&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">Username:</div></td>
              <td><input type="text" name="uid"></td>
            </tr>
            <tr>
              <td><div align="right">credits:</div></td>
              <td><input type="text" name="howmanycreds"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == masshandout) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Handing out some reservers? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><?php 
	if ($now == doit) { 
		if (!$howmanycreds) {
			print "<div align=center>How many reserves you wanna give away?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=masshandout>Back</a></div>";
			exit;
			}
		print "<div align=center>".commas($howmanycreds)." Reserves have been given out to everyone</div>";
		mysql_query("UPDATE $tab[pimp] SET res=res+$howmanycreds");
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=masshandout&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">reserves:</div></td>
              <td><input type="text" name="howmanycreds"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == massmessage) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Sending a mass message? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><?php 
	if ($now == doit) { 
		if (!$msg) {
			print "<div align=center>Whats the mass message gonna say?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=massmessage>Back</a></div>";
			exit;
			}
		$get = mysql_query("select * from $tab[pimp]"); 
                while ($list = mysql_fetch_array($get)) {
		mysql_query("UPDATE $tab[pimp] SET msg=msg+1, msgsent=msgsent+$list[id] WHERE id='$id'");
		mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox) VALUES ('0','$list[id]','$msg','$time','inbox');") or die("Could not send mail."); 
		}
		print "<table><tr class=td><td>";
		print "<div align=center>$msg</div>";
		print "</td></tr><tr><td>&nbsp;</td></tr><tr><td>";
		print "has been sent";
		print "</td></tr></table>";
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=massmessage&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="center">
                <textarea name="msg" cols="40" rows="10" onKeyDown="limitText(this.form.msg,this.form.countdown,500000);" onKeyUp="limitText(this.form.msg,this.form.countdown,500000);"></textarea>
              </div></td>
            </tr>
            <tr>
              <td>You have <input readonly type="text" name="countdown" size="8" value="50000" style="border: 0;background-color: #EEEEEE;color: #000000;font-weight: bold;">
              characters left.</td>
            </tr>
            <tr>
              <td><div align="center">
                <input type="submit" name="Submit" value="Submit">
              </div></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == massemail) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Sending a mass message? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><?php 
	if ($now == doit) { 
		if (!$msg) {
			print "<div align=center>Whats the mass message gonna say?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=massemail>Back</a></div>";
			exit;
			}
		$sql=mysql_query("SELECT * FROM $tab[user]"); 
		 while ($user=mysql_fetch_array($sql)){ 
		 // Change email content below
		 $subject = "$subject"; 
		 $message = "$msg"; 
		 $headers  = "MIME-Version: 1.0\r\n";
		 $headers .= "Content-type: text/html;";
		 $headers .= " charset=iso-8859-1\r\n";
		 $headers .= "Cc: admin@yoursite.com \r\n";
		 mail ($user['email'],$subject,$message,"From: ~Admin"); 
		}
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=massemail&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="center">
                subject: 
                <input type="text" name="textfield">
              </div></td>
            </tr>
            <tr>
              <td><div align="center">
                <textarea name="msg" cols="40" rows="10" onKeyDown="limitText(this.form.msg,this.form.countdown,500000);" onKeyUp="limitText(this.form.msg,this.form.countdown,500000);"></textarea>
              </div></td>
            </tr>
            <tr>
              <td>You have <input readonly type="text" name="countdown" size="8" value="500000" style="border: 0;background-color: #EEEEEE;color: #000000;font-weight: bold;">
              characters left.</td>
            </tr>
            <tr>
              <td><div align="center">
                <input type="submit" name="Submit" value="Submit">
              </div></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == takeaway) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Taking away reserves? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">
	<?php 
	$gone = mysql_query("SELECT res FROM $tab[pimp] WHERE pimp='$uid'");
	if ($now == doit) { 
		if (!$uid) {
			print "<div align=center>Who's gonna get reserves taken from them?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=takeaway>Back</a></div>";
			exit;
			}
		if($gone[0] <= 0) {
			print "<div align=center>That pimp either doesnt have that many reserves or he is the admin</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=takeaway>Back</a></div>";
			exit;
			}
		print "<div align=center>(#)$uid has had ".commas($howmanycreds)." reserves taken from his account!</div>";
		mysql_query("UPDATE $tab[pimp] SET res=res-$howmanycreds WHERE pimp='$uid'");
		exit;
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=takeaway&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">Pimp:</div></td>
              <td><input type="text" name="uid"></td>
            </tr>
            <tr>
              <td><div align="right">reserves:</div></td>
              <td><input type="text" name="howmanycreds"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == delete) { ?>
</span>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">Deleting a Player ? (<a href="adminonly.php?tru=<?=$tru?>">back</a>)</div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">
	<?php 
	if ($now == doit) { 
		
		if (!$uid) {
			print "<div align=center>Who's gonna get deleted?</div>";
			print "<div align=center><a href=adminonly.php?tru=$tru&action=delete>Back</a></div>";
			exit;
			}
		print "<div align=center>$uid has been deleted</div>";
		mysql_query("DELETE FROM users WHERE username='$uid'");
		exit;
	}
	?>    </td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <form action="adminonly.php?tru=<?=$tru?>&action=delete&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">Username:</div></td>
              <td><input type="text" name="uid"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style4">when resetting the lotto numbers you have to keep in mind that the players probably have already purchased a ticket. This will make it so they dont have a ticket which means there money last time went to waste. So be smart, and make some money!</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<span class="style3">
<?
exit;
} 
?>
<?php if ($action == search) { ?>
</span>
<table width="100%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top" class="style3">
<form method=post action="adminonly.php?tru=<?=$tru?>&action=search">
<b>Find Info </b>
(<a href="adminonly.php?tru=<?=$tru?>">back</a>)<br>
<b><font color="#7777CC">the easy admin directory!</font></b>
<br>
<br>
<?
  if($restart==true){?>There are no pimps matching "<b><font color="#7777CC">
<?=$find?>
</font></b>"<br><?}
  
  if(($search==containing) && ($find != ""))
    {?>
    Pimps containing "<b><font color="#7777CC">
    <?=$find?>
    </font></b>"
    <table width="95%" cellspacing="1" cellpadding="2">
     <tr>
        <td><div align="center"><strong>id</strong></div></td>
        <td><div align="center"><strong>pimp</strong></div></td>
        <td><div align="center"><strong>turns</strong></div></td>
        <td><div align="center"><strong>reserves</strong></div></td>
        <td><div align="center"><strong>glock</strong></div></td>
        <td><div align="center"><strong>shotgun</strong></div></td>
        <td><div align="center"><strong>uzi</strong></div></td>
        <td><div align="center"><strong>ak-47</strong></div></td>
        <td><div align="center"><strong>ip</strong></div></td>
        <td><div align="center"><strong>bank</strong></div></td>
      </tr>
    <?
     $get = mysql_query("SELECT id,pimp,bilttrn,res,glock,shotgun,uzi,ak47,ip,bank FROM $tab[pimp] WHERE pimp LIKE '%$find%' ORDER BY nrank asc limit 20;");
     while ($results = mysql_fetch_array($get))
           {
           $online=$time-$results[6];
           if ($online < 600){$on="<img src=$site[img]online.gif width=16 height=16 align=absmiddle>";}else{$on='';}

               if($id == $results[0]){$rankcolor = "#CCCCCC";}
           elseif($rankstart==0){$rankcolor="#EEEEEE";$rankstart++;}
           elseif($rankstart==1){$rankcolor="#FFFFFF";$rankstart--;}

           $city = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$results[4]';"));
           $icn = mysql_fetch_array(mysql_query("SELECT icon FROM $tab[crew] WHERE id='$results[9]';"));
           ?>
       <tr bgcolor="<?=$rankcolor?>">
        <td>(#)<?=$results[0]?></td>
        <td><a href="mobster.php?pid=<?=$results[1]?>&tru=<?=$tru?>">
          <?=$results[1]?>
        </a></td>
        <td><?=commas($results[2])?></td>
        <td><?=commas($results[3])?></td>
        <td><?=commas($results[4])?></td>
        <td><?=commas($results[5])?></td>
        <td><?=commas($results[6])?></td>
        <td><?=commas($results[7])?></td>
        <td><?=$results[8]?></td>
        <td><?=commas($results[9])?></td>
      </tr>
      <?}?>
	</table>
    <br>
	<? } ?>
    <br>
    <table bgcolor="#FFFFFF" cellspacing="1"><tr><td valign="middle">
    <table class="td" bgcolor="#eeeeee" width="100%" height="100%" border="0" cellspacing="0" cellpadding="3">
     <tr>
      <td align="center" width="500">
search for pimps: <b><input type="radio" name="search" value="containing" <?if($search==containing){echo"checked";}?>> 
containing</b>
&nbsp;
<b>
<input type="radio" name="search" value="containing" <?if($search==ipaddress){echo"checked";}?>> 
ip</b> </td>
     </tr>
     <tr>
       <td align="center"><input type="input" class="text" name="find"></td>
     </tr>
     <tr>
       <td align="center"><input type="submit" class="button" name="do_search" value="find da bitch"></td>
     </tr>
    </table>
    </td></tr></table>
    <table width="450">
     <tr>
      <td align="center" width="150"><b>searches:</b></td><td align="center" width="150"><b>bitches:</b></td><td align="center" width="150"><b>contacts:</b></td>
     </tr>
    </table>

<br>
<br>
</form>  </td>
 </tr>
</table>
<span class="style3">
<?
exit;
}
?>
</span>
<table width="50%"  border="0" align="center" cellpadding="5" cellspacing="1">
  <tr>
    <td class="style3">&nbsp;</td>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
    <td class="style3">&nbsp;</td>
  </tr>
  <tr>
    <td class="style3"><div align="right"><strong>Hand out Reserves: </strong></div></td>
    <td class="style3"><a href="adminonly.php?tru=<?=$tru?>&action=handcredits">Handout</a></td>
  </tr>
  <tr>
    <td class="style3"><div align="right"><strong>Mass Reserve Handout: </strong></div></td>
    <td class="style3"><a href="adminonly.php?tru=<?=$tru?>&action=masshandout">Handout</a></td>
  </tr>
  <tr>
    <td class="style3"><div align="right"><strong>Take away Reserves: </strong></div></td>
    <td class="style3"><a href="adminonly.php?tru=<?=$tru?>&action=takeaway">Takeaway</a></td>
  </tr>
  <tr>
    <td class="style3"><div align="right"><strong>sends from id#0 (NO ONE)---Mass Game Message: </strong></div></td>
    <td class="style3"><a href="adminonly.php?tru=<?=$tru?>&action=massmessage">game-mail</a></td>
  </tr>
  <tr>
    <td align="right" class="style5">Back to the Main game </td>
    <td class="style3"><a href="index.php?tru=<?=$tru?>">Click Here </a></td>
  </tr>
  <tr>
    <td class="style3">&nbsp;</td>
    <td class="style3">&nbsp;</td>
  </tr>
</table>