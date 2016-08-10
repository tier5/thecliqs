<?
include("html.php");

$crw = mysql_fetch_array(mysql_query("SELECT crew,status,postpriv FROM $tab[pimp] WHERE id='$id';"));
$src = mysql_fetch_array(mysql_query("SELECT pimp,id FROM $tab[pimp] WHERE pimp='$pid';"));

if ($cc) {
    $cc_arr = explode(",", $cc);
	if (sizeof($cc_arr)) {
	    for ($i = 0; $i <= sizeof($cc_arr)-1; $i++){
			if (trim($cc_arr[$i]) != "") {
			    $cc_list[] = trim($cc_arr[$i]);
			}			
		}
	}
}

if($sendthefucker)
{
  if($src)
    {
        if ($msg == "")
           { }
          if(!$msg){ $attempt=msg; }
    elseif(fetch("SELECT contact FROM $tab[clist] WHERE pimp='$src[1]' AND type='block' AND contact='$id';")){$attempt=ignore;}
     else {
	 
	 	  if ($cc != ""){
		  #check if cc names are in ignore list	
	      $cc_count = 0;
		  $cc_list = array_unique($cc_list);
		foreach ($cc_list as $pers){
		  	   $info = mysql_fetch_array(mysql_query("SELECT pimp,id FROM $tab[pimp] WHERE pimp='$pers';"));
			   if ($info['id']) {
			       if (!fetch("SELECT contact FROM $tab[clist] WHERE pimp='".$info['id']."' AND type='block' AND contact='$id';")) {
			           $cc_count++;
					   $ids[] = $info;
			       }
			   }
		  }
		} 
	 	   $cc_count++;	
           mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE pimp='$pid'");
		   $msgsent = fetch("SELECT msgsent FROM $tab[pimp] WHERE id=$id");
		   $msgsent = $msgsent + $cc_count;
		   mysql_query("UPDATE $tab[pimp] SET msgsent=$msgsent WHERE id='$id'");
           mysql_query("UPDATE $tab[pimp] SET online='$time' WHERE id='$id'");
           $msg=filter($msg);
          if($cc != ""){
		      mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del,crew) VALUES ('$id','$src[1]','CC to  <br>
				    $cc  
					  <br><br>
			   $msg','$time','inbox','no','$crw[0]');");
			   }else{
              mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del,crew) VALUES ('$id','$src[1]','$msg','$time','inbox','no','$crw[0]');");
			   }
			   
           $sent_to = "";
		   if (sizeof($ids)) {
			   foreach ($ids as $k => $to_id){
mysql_query("INSERT INTO $tab[mail] (src,dest,msg,time,inbox,del,crew) VALUES ('$id','".$to_id['id']."','CC to  <br>
				    $cc  
					  <br><br>
				       $msg','$time','inbox','no','$crw[0]');");					   
				   mysql_query("UPDATE $tab[pimp] SET msg=msg+1 WHERE id='".$to_id['id']."'");
				   $sent_to .= $to_id['pimp'];
				   if ($k != sizeof($ids)-1) {
				       $sent_to .= ", ";
				   }
			   }	   
           }
		   $success=yes;
           }
    }
else{ }
}
mysql_query("UPDATE $tab[pimp] SET currently='spamin $pid', online='$time' WHERE id='$id'"); 

GAMEHEADER("send message to $src[0]");
?>
<style type="text/css">
<!--
.style1 {color: #FF0000}
-->
</style>
<table width="90%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
 <form action="out.php?tru=<?=$tru?>" method=post class="style3">
 <input type="hidden" name="pid" value="<?=$pid?>">
 <input type="hidden" name="camefrom" value="<?=$camefrom?>">
 <br>
 <table width="350" height="180" style="<?=$dark?>">
  <tr>
   <td colspan="2" align="center" valign="middle">
   <?if($attempt==ignore){?>
   <span class="style1"><b>This user has you on his ignore list, message not sent!</b></span><br><?}?>
   <?if($success==yes){?>
   <b><?if($id == 0){?>
   <span class="style1"><font color=red>Your message was not sent <b>Bitch</b>.</font></span>
   <span class="style1">
   <?}else{?>
   Your message has been sent to</span>   <span class="style1">
   <?=$src[0]?>
   <?if ($sent_to){?>
   , 
   <?= $sent_to ?>
   <?}?> 
   !</span></b><span class="style1">
   <?}?>
   </span><br>
   <br><a href="mailbox.php?tru=<?=$tru?>">Click here to go back.</span></a>
   <?}else{?>
   <strong><span class="style1">send a message to</span> <font color="#7777CC">
   <?=$src[0]?>
   </font></strong><br>
   CC: 
   <input type="textbox" name="cc" size="60" value="<?= $cc ?>" />
   
Example: NameHere, NameHere (separate with commas)<br />
<textarea cols="50" rows="6" name="msg" onKeyDown="limitText(this.form.msg,this.form.countdown,500" onKeyUp="limitText(this.form.msg,this.form.countdown,500);"></textarea><br>
   <br>
   <br />
   <input type="submit" class="button" name="sendthefucker" value="send message">   </td>
  </tr>
 </table>
</form>
 <span class="style2">
 <?=bar($id)?>
  </span></td>
 </tr>
</table><?}?>
<?
GAMEFOOTER();
?>