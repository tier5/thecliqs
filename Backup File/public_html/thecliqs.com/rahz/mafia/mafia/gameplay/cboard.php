<?
include("html.php");

mysql_query("UPDATE $tab[pimp] SET cmsg='0' WHERE id='$id'");

$pmp = mysql_fetch_array(mysql_query("SELECT pimp,postpriv,status,crew FROM $tab[pimp] WHERE id='$id';"));
$cty = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pmp[1]';"));
$crw = mysql_fetch_array(mysql_query("SELECT name,founder,cofounder FROM $tab[crew] WHERE id='$cid';"));

if($cid != $pmp[3]){ header("Location: index.php?tru=$tru"); }

if($r){$prev =$r+10;$next=$r-10;}else{$r=0;}
$prev = $r - 10;
$next = $r + 10;

    if($post)
      {
         $floodtime=$time-60;
         if (fetch("SELECT COUNT(msg) FROM $tab[board] WHERE msg='$msg' AND time>$floodtime;"))
         { }
     elseif($msg == "") { $error="<br><b><font color='#7777CC'>You have entered a blank post</font><br>"; }
       else{
           $msg = filter($msg);
           mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board) VALUES ('$time','$id','$msg','no','$cid');");
           mysql_query("UPDATE $tab[pimp] SET cmsg=cmsg+1 WHERE crew='$cid' AND pimp!='$pmp[0]'");
           } 
      }

GAMEHEADER("$crw[0]'s alliance board"); 
?>
<br><font size="+1"><b><?=$crw[0]?> alliance board</b></font>
<br><b><font color="#7777CC">Post smack here for other <small>mafioso</small> in your alliance to see.</font>
<br>
<br>
<?
if($make==post){?>
 <form method=post action="cboard.php?cid=<?=$cid?>&tru=<?=$tru?>">
 <b>Post a message</b>
 <br><textarea cols="50" rows="6" name="msg"></textarea>
 <br><input type="submit" class="button" name="post" value="post message">     
</form>
<?}else{?><a href="cboard.php?cid=<?=$cid?>&make=post&tru=<?=$tru?>"><font color="#FFFFFF">Click</font> here <font color="#FFFFFF">to post a message.</font></a><?}?>
<?
    if((($pmp[0] == $crw[1]) || ($pmp[0] == $crw[2])) && ($del)){
      ?><br><b><font color="#7777CC">Post has been deleted.</font><br><?
      mysql_query("UPDATE $tab[board] SET del='yes' WHERE board='$cid' AND id='$del'");
      }
?>
<table width="90%" cellspacing="1" border="0">
 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="cboard.php?cid=<?=$cid?>&r=<?=$prev?>&tru=<?=$tru?>">prev 10</a></td><td align="right"><a href="cboard.php?cid=<?=$cid?>&r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>
<?
$get = mysql_query("SELECT who,time,msg,id,board FROM $tab[board] WHERE del='no' AND who>0 AND board='$cid' ORDER BY id DESC LIMIT $r,10;");
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
  <nobr><a href="mobster.php?pid=<?=$pimp[0]?>&tru=<?=$tru?>"><?=$pimp[0]?></a><br><small><?=$pimp[5]?> <font color="#3366CC">in <?=$city[0]?></font></small><br><?if($crew[1]){?><a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crew[1]?>" width="16" height="16"></a><?}?></nobr>
  </td>
  <td align="left" height="100%" valign="top" bgcolor="<?=$rankcolor?>">
   <table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0" class="dark">
    <tr>
     <td valign="top">
     <?if($pimp[3] == banned){?><font color="#FFCC00"><b>This <small><B>mafioso</B></small> has been removed from the game.</b></font><br>
     <br><?}else{?><?if($msg[5] == yes){?><b><?}?><?=$msg[2]?><br><br><?}?>
     </td>
    </tr>
    <tr><td align="right" height="5"><table width="100%" cellspacing="0" cellpadding="0"><tr><td><font color="#7777CC"><small>posted <?=countdown($msg[1])?> ago</small></font></td><?if(($pmp[0] == $crw[1]) || ($pmp[0] == $crw[2])){?><td align="right"><small><b>founder / cofounder: <a href="cboard.php?cid=<?=$cid?>&del=<?=$msg[3]?>&tru=<?=$tru?>">delete</a></b></small></td><?}?></tr></table></td></tr>
   </table>
  </td>
 </tr>
<?
}

?>
 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="cboard.php?cid=<?=$cid?>&r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">prev 10</a></td><td align="right"><a href="cboard.php?cid=<?=$cid?>&r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">next 10</a></td></tr></table></td></tr>
</table>
<br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>



