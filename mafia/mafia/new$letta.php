<?php 
require("html.php");
require("mpfunc.inc"); 
admin();
secureheader();
siteheader();
?>
<?
if (!$action) {
?>

<div align="center">
<form action="new$letta.php?action=2" method="post">
<input name="headline" type="text" value="Insert subject here" size="60" maxlength="128" /><br />
<textarea name="newsl" cols="50" rows="15"></textarea><br />
<input type="submit" value="submit" name="go">
</form>
</div>
<?
}
if ($action == "2") {
?>

<?
$getuser = mysql_query("SELECT id,username,email FROM $tab[user] WHERE 1 limit 3"); 
	while ($users = mysql_fetch_array($getuser)){
		if($rankstart==0){$color="#EEEEEE";$rankstart++;}
		elseif($rankstart==1){$color="#FFFFFF";$rankstart--;}
		?>
<?
$myclass = &New NewsLetter;
$letter = ("$newsl");
$username = ("$users[1]");
$email = ("$users[2]");
$fixletter = $myclass->replacenews($letter);
$fixletter_ = ereg_replace("username", "$fullname", $fixletter);
?>
<div align="center">

    <table width="400" align="center" border="0" cellpadding="0" cellspacing="0">
     <tr>
      <td bgcolor="<?=$color?>" align="left"><small>
	<div align="right" style="text-decoration:overline underline">Newsletter send to: <I><?=$users[0]?> - <?=$users[1]?> - <?=$users[2]?></I></div>  
	<div align="center" style="font-size:larger"><? echo ("$headline")?></div><br />
    <div align="center"><? echo nl2br("$fixletter_")?></div>
	  </small></td>
     </tr>
    </table>	
	</div>	
	<?}?>
	
<div align="center">	
<form action="new$letta.php?action=3" method="post">
<!-- <input name="email" type="hidden" value="<?=$email?>" /> -->
<input name="subj" type="hidden" value="<?=$headline?>" />
<input name="enewsletter" type="hidden" value="<?=$fixletter?>" />
<input name="go" type="submit" value="Send the newsletter" />
</form>
<b>This is the point of no return.</b><br>When you click "Send the newsletter" its out of your hands untill all mails are send out.<br>If you need to change anything, use your browsers BACK functions.
</div>	
<?
}
if ($action == "3") {

$efooter = "You are receiving this newsletter because you or someone has signed up with this email address at ".$site[name].".  Based upon sign up and agreement to our terms of service you agreed to receive our news letter.  If you wish not to receive any further news letters please login to your account and go to My Account and update your information to not receive them anymore.  Thanks and we look forward to many years with you as a continued player and your support.";
       mysql_query("INSERT INTO $tab[newsletter] (subject,body,footer,sent) VALUES ('$subj','$enewsletter','$efooter','no');");

?>

<div align="center"><a href="new$letta.php?action=4">Done click here.</a></div>

<?
}
if ($action == "4") {
$menu='pimp/';
?>
<pre>
All mail has been sent!
Thank you for using <?=$site[name]?>'s Newsletter, please
post on our forums to let us know how this worked out
for you!
</pre>
<?
}
?>

<?
sitefooter();
?>