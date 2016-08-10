<?
include("html.php");

$pimp = mysql_fetch_array(mysql_query("SELECT status FROM $tab[pimp] WHERE id='$id';"));

if($r){$prev =$r+10;$next=$r-10;}else{$r=0;}
$prev = $r - 10;
$next = $r + 10;

    if(($post) && ($reply!=submit))
      {
         $floodtime=$time-3600;
         if (fetch("SELECT COUNT(msg) FROM $tab[board] WHERE msg='$msg' AND time>$floodtime;"))
         { }
     elseif($msg == "") { $error="<br><b><font color='#7777CC'>You have entered a blank post</font><br>"; }
       else{
           $msg = filter($msg);
           mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','$msg','no','help');");
           $agree=reset;
           } 
      }

    if(($reply==submit) && ($pimp[0] == admin))
      {
         $floodtime=$time-3600;
         $replyto = mysql_fetch_array(mysql_query("SELECT who FROM $tab[board] WHERE id='$msgid';"));

           if($ignore==yes){ mysql_query("UPDATE $tab[board] SET status='ignored' WHERE id='$msgid';"); }
       elseif(fetch("SELECT COUNT(msg) FROM $tab[board] WHERE msg='$msg' AND who='$replyto[0]' AND board='help' AND time>$floodtime;")){ }
       elseif($msg == "") { $error="<br><b><font color='#7777CC'>You have entered a blank post</font><br>"; }
       else{
           $msg = filter($msg);
           mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board,adminpost) VALUES ('$time','$replyto[0]','$msg','no','help','yes');");
           mysql_query("UPDATE $tab[board] SET status='replied' WHERE id='$msgid';");
           }
      }

GAMEHEADER("admin hangout");
?>
<br>
<B><font color="#FF0000"><?=$site[name]?> Admin</font></B><br>
<font color="red"><b>welcome to the <?=$site[name]?> Admin hangout</b></font>

<br>

<?if($pimp[0]==admin){?>

<br>

<?if($reply==yes){?>

<form method="post" action="godadmins.php?reply=submit&msgid=<?=$msgid?>&tru=<?=$tru?>">

 <b>Post a message</b>

 <br><textarea cols="50" rows="6" name="msg"></textarea>

 <br><input type="submit" class="button" name="post" value="post message"> 

</form>

<?}?>

<table width="90%" cellspacing="1" border="0">

 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="godadmins.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">prev 10</a></td><td align="right"><a href="godadmins.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>

<?

$get = mysql_query("SELECT who,time,msg,id,status FROM $tab[board] WHERE del='no' AND who>0 AND board='help' AND adminpost='no' ORDER BY id DESC LIMIT $r,10;");

while ($msg = @mysql_fetch_array($get))

{

       if($msg[4] == "new") {$rankcolor="#cccccc"; }

   else{$rankcolor="#999999";}



$pmp=mysql_fetch_array(mysql_query("SELECT pimp,crew,city,status,postpriv,rank FROM $tab[pimp] WHERE id='$msg[0]';"));

$crew=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pmp[1]';"));

$city=mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pmp[2]';"));

?>

 <tr>

  <td align="right" valign="top" width="100">

  <nobr><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"><?=$pmp[0]?></a><br><small><?=$pmp[5]?> <font color="#3366CC">in <?=$city[0]?></font></small><br><?if($crew[1]){?><a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crew[1]?>" width="16" height="16"></a><?}?></nobr>

  </td>

  <td align="left" height="100%" valign="top" bgcolor="<?=$rankcolor?>">

   <table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0" class="dark">

    <tr>

     <td valign="top"><?=$msg[2]?><br><br></td>

    </tr>

    <tr><td align="right" height="5"><table width="100%" cellspacing="0" cellpadding="0"><tr><td><font color="red"><small>posted <?=countdown($msg[1])?> ago</small></font></td><td align="right"><small><b><?if($msg[4]==replied){?>This message has been replied to<?}elseif($msg[4]==ignored){?>This message was ignored<?}else{?><a href="godadmins.php?reply=yes&msgid=<?=$msg[3]?>&tru=<?=$tru?>">reply</a> :: <a href="godadmins.php?reply=submit&msgid=<?=$msg[3]?>&ignore=yes&tru=<?=$tru?>">ignore</a><?}?></b></small></td></tr></table></td></tr>

   </table>

  </td>

 </tr>

<?}?>

 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="godadmins.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">prev 10</a></td><td align="right"><a href="godadmins.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>

</table>



<?}else{?>

<form method=post action="godadmins.php?agree=yes&post=yes&tru=<?=$tru?>">

<table width="80%">

 <tr>

  <td>

  Here you can help keep the game clean by reporting <B>Mafioso's</B> who are breaking the game <a href="../rules.php" target="_new">rules</a>. If you post below, only you will be able to see your post and our reply. If you have been banned, and decided to re-join the game, to speak to us here, we will just ignore your post and disable your account.

  <br>

  <br><small><font color="#B0C4DE">Please make sure you include all <font color="red"><b>pimp names</b></font>, please leave us a description and alittle <font color="red"><b>proof</b></font> showing how they are cheating, and also state which rule they are breaking.</font></small>

  <br>

  <br><b>DO NOT ASK US FOR TIPS OR HOW TO PLAY, READ THE <a href="../guide.php">GAME GUIDE</a>!</b>

  <br>

  <br>If you are asking a question, make sure you read our <a href="../support.php">F.A.Q.</a>, Please make sure you have read the posts before posting so we don't have to keep repeating ourselves. This would really help us alot. So read first please, and then if you don't see the answer to your question, then by all means post it.

  <br>

  </td>

 </tr>

</table>

<?if($error){?><?=$error?><?}?>

<br><?if($agree!=yes){?><b>>> <a href="godadmins.php?agree=yes&tru=<?=$tru?>">I have read all of the above, and agree.</a> <<</b><br><br><?}

else{?>

 <b>Post a message</b>

 <br><textarea cols="50" rows="6" name="msg"></textarea>

 <br><input type="submit" class="button" name="post" value="post message">     

</form>

  <?}?>

<table width="90%" cellspacing="1" border="0">

 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="godadmins.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">prev 10</a></td><td align="right"><a href="godadmins.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>

<?

$get = mysql_query("SELECT who,time,msg,id,adminpost FROM $tab[board] WHERE del='no' AND who='$id' AND board='help' ORDER BY id DESC LIMIT $r,10;");

while ($msg = mysql_fetch_array($get))

{

       if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}

   elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}



$pimp=mysql_fetch_array(mysql_query("SELECT pimp,crew,city,status,postpriv,rank FROM $tab[pimp] WHERE id='$msg[0]';"));

$crew=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pimp[1]';"));

$city=mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pimp[2]';"));

?>

 <tr>



  <td align="right" valign="top" width="100">

  <nobr><?if($msg[4] == yes){?>
  <strong><font color="red"><?=$site[name]?></font></strong><br><small><font color="red"> admin</font></small><?}else{?><a href="mobster.php?pid=<?=$pimp[0]?>&tru=<?=$tru?>"><?=$pimp[0]?></a><br>
  <small><?=$pimp[5]?> <font color="red">in <?=$city[0]?></font></small><br><?if($crew[1]){?><a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crew[1]?>" width="16" height="16"></a><?}?><?}?></nobr>  </td>

  <td align="left" height="100%" valign="top" bgcolor="<?=$rankcolor?>">

   <table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0" class="dark">

    <tr>

     <td valign="top">

     <?if($pimp[3] == banned){?><font color="red"><b>This Mafioso has been removed from the game.</b></font><br>
     <br>
     <?}elseif($pimp[4] == disabled){?><font color="red"><b>This Mafioso  posting privledges have been disabled.</b></font><br>
     <br><?}else{?><?if($msg[4] == yes){?><b><?}?><?=$msg[2]?></b><br><br><?}?>

     </td>

    </tr>

    <tr><td align="right" height="5"><table width="100%" cellspacing="0" cellpadding="0"><tr><td><font color="#B0C4DE"><small>posted <?=countdown($msg[1])?> ago</small></font></td><?if($pmp[2] == admin){?><td align="right"><small><b>admin: <a href="godadmins.php?del=<?=$msg[3]?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">delete</a> :: <a href="godadmins.php?poster=<?=$pimp[0]?>&priv=<?if($pimp[4] == enabled){echo"disabled";}else{echo"enabled";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[4] == enabled){echo"freeze";}else{echo"unfreeze";}?> posting</a></b></small></td><?}?></tr></table></td></tr>

   </table>

  </td>

 </tr>

<?}?>

 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="godadmins.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">prev 10</a></td><td align="right"><a href="godadmins.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>

</table>

<br>

<?}?>

<br>

<br><?=bar($id)?>

<br>

<?

GAMEFOOTER();

?>