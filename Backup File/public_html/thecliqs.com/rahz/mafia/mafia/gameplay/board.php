<?
include("html.php");

$pmp = mysql_fetch_array(mysql_query("SELECT pimp,postpriv,status,crew,lvl FROM $tab[pimp] WHERE id='$id';"));
$cty = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pnk[1]';"));
$crw = mysql_fetch_array(mysql_query("SELECT name FROM $tab[crew] WHERE id='$pmp[3]';"));

if($r){$prev =$r+5;$next=$r-5;}else{$r=0;}
$prev = $r - 5;
$next = $r + 5;

    if(($post) && ($pmp[2] != disabled))
      {
         $floodtime=$time-60;
         if (fetch("SELECT COUNT(msg) FROM $tab[board] WHERE msg='$msg' AND time>$floodtime;"))
         { }
     elseif($msg == "") { $error="<br><b><font color='#7777CC'>You have entered a blank post</font><br>"; }
       else{
           if(($adminpost==yes) && ($pmp[2] != admin)){ $adminpost=no; }
           mysql_query("INSERT INTO $tab[board] (time,who,msg,del,board,adminpost) VALUES ('$time','$id','$msg','no','$brd','$adminpost');");
           } 
      }

if($brd==recruit){ GAMEHEADER("recruiting board"); }else{ GAMEHEADER("pimp board"); }
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

</head>
<script language="JavaScript" type="text/JavaScript"> 
function launchForums(){
name=window.open("help.php?tru=<?=$tru?>&help=msgboard","","width=640,height=480,top=100,left=100,resizable=yes,scrollbars=no,menubar=no,toolbar=no,status=no,location=no")
}
</script>

<script language="JavaScript" type="text/JavaScript"> 
function sendText(e, text) 
{ 
  e.value += text
} 
</script> 
 
<center>
  <font size="+1"><b>
  <?if($brd==recruit){?>
  recruiting board
  <?}else{?>
  the message board
  <?}?>
  </b></font>
  <br>
  <b><font>
  <?if($brd==recruit){?>
  please post only recruiting related talk
  <?}else{?>
  all messages posted here are seen by all mobsters
  <?}?>
  </font>
  <br>
  <br>
  <?
if($pmp[1] == disabled){?>
  <br>
  <strong><font>Your posting privileges have been disabled.</font></strong><small><br>
  Maybe next time you will <u>think</u> before you <strong>speak</strong>.</small><br>
  <br>
  <?}
else{

if(($make==post) && ($pmp[2] != disabled)){ ?>
  </div>
</center>
<form name="form1" form method=post action="board.php?tru=<?=$tru?>&action=post<?if($brd==recruit){echo"&brd=recruit";}?>">
  <div align="center"><b>Post a message <font color="ff0000">(NO SPAM!)</b></font><br>
      <br>
    <textarea cols=50 rows=10 name=msg onKeyDown="limitText(this.form.msg,this.form.countdown,500);" onKeyUp="limitText(this.form.msg,this.form.countdown,300);"></textarea>
    <br>
    <center><small>< b ><b>BOLD</b>< / b > < u ><u>UNDERLINE</u>< / u > < i ><i>ITALICS</i>< / i > <br>(To do so there cant be any spaces)</small></center>  
<?if($pmp[2] == admin){?>
    <br>
    post as a admin? 
    <input type="radio" name="adminpost" value="no" checked> 
    no 
    <input type="radio" name="adminpost" value="yes"> 
    yes
    <?}?>
    <br>
    You have 
    <input readonly type="text" name="countdown" size="3" value="300" style="border: 0;background-color: #000000;color: #FFCC00;font-weight: bold;font-size:8pt;">
    characters left.
    <br>

    <br>
    <input type="submit" class="button" name="post" value="post message">
  </div>
</form>
<?}else{?><a href="board.php?make=post<?if($brd==recruit){echo"&brd=recruit";}?>&tru=<?=$tru?>"><font color="red">Click</font> here <font color="red">to post a message.</font></a><?}?>
<?
    if(($pmp[2] == admin) && ($del))
      {
      ?><br><b><font color="#B0C4DE">Post has been deleted.</font><br><?
      mysql_query("DELETE FROM $tab[board] WHERE id='$del'");
      }
elseif(($pmp[2] == admin) && ($poster))
      {
      ?><br><b><font color="#3366FF"><?=$poster?>'s posting privileges have been <?=$priv?>.</font><br><?
      mysql_query("UPDATE $tab[pimp] SET postpriv='$priv' WHERE pimp='$poster'");
      }
elseif(($pmp[2] == admin) && ($posterr))
      {
      ?><br><b><font color="#3366FF"><?=$posterr?>'s has been <?=$privv?>.</font><br><?
      mysql_query("UPDATE $tab[pimp] SET status='$privv' WHERE pimp='$posterr'");
      }
	  
	  //moderator
if(($pmp[4] == 2) && ($del))
      {
      ?><br><b><font color="#B0C4DE">Post has been deleted.</font><br><?
      mysql_query("DELETE FROM $tab[board] WHERE id='$del'");
      }
elseif(($pmp[4] == 2) && ($poster))
      {
      ?><br><b><font color="#3366FF"><?=$poster?>'s posting privileges have been <?=$priv?>.</font><br><?
      mysql_query("UPDATE $tab[pimp] SET postpriv='$priv' WHERE pimp='$poster'");
      }
elseif(($pmp[4] == 2) && ($posterr))
      {
      ?><br><b><font color="#3366FF"><?=$posterr?>'s has been <?=$privv?>.</font><br><?
      mysql_query("UPDATE $tab[pimp] SET status='$privv' WHERE pimp='$posterr'");
      }
	  
	  //helper
 if(($pmp[4] == 5) && ($del))
      {
      ?><br><b><font color="#B0C4DE">Post has been deleted.</font><br><?
      mysql_query("DELETE FROM $tab[board] WHERE id='$del'");
      }
elseif(($pmp[4] == 5) && ($poster))
      {
      ?><br><b><font color="#3366FF"><?=$poster?>'s posting privileges have been <?=$priv?>.</font><br><?
      mysql_query("UPDATE $tab[pimp] SET postpriv='$priv' WHERE pimp='$poster'");
      }
     ?>
<table width="100%" cellspacing="1" style="border: 1px solid #3d3d3d;">
 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="board.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">&laquo; Previous</a></td><td align="right"><a href="board.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">&raquo; Next</a></td></tr></table></td></tr>
<?
if($brd != recruit){ $brd=""; }

$get = mysql_query("SELECT who,time,msg,id,board,adminpost FROM $tab[board] WHERE del='no' AND who>0 AND board='$brd' ORDER BY id DESC LIMIT $r,10;");
while ($msg = @mysql_fetch_array($get))
{
       if($rankstart==0){$rankcolor="#cccccc";$rankstart++;}
   elseif($rankstart==1){$rankcolor="#999999";$rankstart--;}

$pimp=mysql_fetch_array(mysql_query("SELECT pimp,crew,city,status,postpriv,rank FROM $tab[pimp] WHERE id='$msg[0]';"));
$crew=mysql_fetch_array(mysql_query("SELECT id,icon FROM $tab[crew] WHERE id='$pimp[1]';"));
$city=mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pimp[2]';"));
?>
 <tr style="border: 1px solid #3d3d3d; margin: 5px;">

  <td align="left" valign="top" width="100">
  <nobr><?if($msg[5] == yes){?><span class="style1">Admin </span><br>
  <?}else{?><a href="mobster.php?pid=<?=$pimp[0]?>&tru=<?=$tru?>"><?=$pimp[0]?></a><br>
  <small>Located <font color="#941f00">in <?=$city[0]?></font></small><br> <font color="red" size="-2"><i><strong><?=$pimp[3]?> <font color="red" size="-2"><?=$status[0]?></font></strong></i></font><br><?if($crew[1]){?><a href="family.php?cid=<?=$crew[0]?>&tru=<?=$tru?>"><img border="0" align="absmiddle" src="<?=$crew[1]?>" width="20" height="20"></a><?}?><?}?></nobr>  </td>
  <td align="left" height="100%" valign="top" bgcolor="<?=$rankcolor?>">
   <table cellspacing="0" cellpadding="0" width="100%" height="100%" border="0" class="dark">
    <tr>
     <td valign="top">
     <? if($pimp[3] == banned){?><font color="#990000"><b>This mobster may not play this game.</b></font><br>
     <br><? }elseif($pimp[4] == disabled){?><font color="#990000"><b>This mobsters posting privileges have been disabled.</b></font><br>
     <br><? }else{?><? if($msg[5] == yes){?><b><? }?><?=securemsg($msg[2])?></b><br><br><?}?>
     </td>
    </tr>
    <tr><td align="right" height="5"><table width="100%" cellspacing="0" cellpadding="0"><tr><td><font color="#941f00"><small>posted <b> <?=countdown($msg[1])?> </b>ago<br />
      <br />
      <br />
    </small></font></td><?if($pmp[2] == admin){?><td align="right"><small><b>admin: <a href="board.php?del=<?=$msg[3]?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">delete</a> :: <a href="board.php?poster=<?=$pimp[0]?>&priv=<?if($pimp[4] == enabled){echo"disabled";}else{echo"enabled";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[4] == enabled){echo"freeze";}else{echo"unfreeze";}?> posting</a>   :: <a href="board.php?posterr=<?=$pimp[0]?>&privv=<?if($pimp[3] != banned){echo"banned";}else{echo"normal";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[3] != banned){echo"bann";}else{echo"unbann";}?> status</a></b></small></td><?}?>
	<? //Helper ?>
	<?if($pmp[4] == 5){?><td align="right"><small><b>Helper: <a href="board.php?del=<?=$msg[3]?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">delete</a> :: <a href="board.php?poster=<?=$pimp[0]?>&priv=<?if($pimp[4] == enabled){echo"disabled";}else{echo"enabled";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[4] == enabled){echo"freeze";}else{echo"unfreeze";}?> posting</a></b></small></td><?}?>
	<? //moderator  ?>
	<?if($pmp[4] == 2){?><td align="right"><small><b>Moderator: <a href="board.php?del=<?=$msg[3]?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">delete</a> :: <a href="board.php?poster=<?=$pimp[0]?>&priv=<?if($pimp[4] == enabled){echo"disabled";}else{echo"enabled";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[4] == enabled){echo"freeze";}else{echo"unfreeze";}?> posting</a>  :: <a href="board.php?posterr=<?=$pimp[0]?>&privv=<?if($pimp[3] != banned){echo"banned";}else{echo"normal";}?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>"><?if($pimp[3] != banned){echo"bann";}else{echo"unbann";}?> status</a></b></small></td><?}?></tr></table></td></tr>
   </table>
  </td>
 </tr>
<?
}

?>
 <tr><td>&nbsp;</td><td><table width="100%"><tr><td><a href="board.php?r=<?=$prev?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">&laquo; Previous</a></td><td align="right"><a href="board.php?r=<?=$next?>&tru=<?=$tru?><?if($brd==recruit){echo"&brd=recruit";}?>">&raquo; Next</a></td></tr></table></td></tr>
</table>
<?}?>
<br>
<?=bar($id)?>
<br>
<?
GAMEFOOTER();
?>