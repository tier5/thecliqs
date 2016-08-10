<?
include("html.php");
$html = mysql_fetch_array(mysql_query("SELECT rules,tos FROM $tab[html];"));
function randomkeys($length)
{
$pattern = "1234567890";
for($i=0;$i<$length;$i++)
{
$key .= $pattern{rand(0,9)};
}
return $key;
}
$captcha = randomkeys(6);
if($ref){ setcookie("refer",$refer, time()+7776000); }
if($ref){ setcookie("ref",$ref); }
if(!$step){$step=1;}
if(($step1 == 1) && ($agree == yes)){ header("Location: ?step=2"); }
elseif(($step1 == 1) && ($agree != yes)){ header("Location: ?warn=yes"); }
elseif(($step2 == 1) && ($agree == yes)){ header("Location: ?step=3"); }
elseif(($step2 == 1) && ($agree != yes)){ header("Location: ?step=2&warn=yes"); }
if($signup)
{
$host=gethostbyaddr("$REMOTE_ADDR");
$code = md5($username.trucode.$password);
$pin = md5($email.trucode);
if ((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $username)) || (strstr($username,".")))
{ $msg="&#149 Invalid username: a-Z 0-9 -_ charactors only."; $username=""; }
elseif ((strlen($username) <= 2) || (strlen($username) >= 19))
{ $msg="&#149 Invalid username: must be at least 3-18 in length."; $username=""; }
elseif (fetch("SELECT username FROM $tab[user] WHERE username='$username';"))
{ $msg="&#149 Invalid username: already taken."; $username="";}
elseif ((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $password)) || (strstr($password,".")))
{ $msg="&#149 Invalid password: a-Z 0-9 -_ charactors only."; $password=""; $cpassword=""; }
elseif ((strlen($password) <= 2) || (strlen($password) >= 13))
{ $msg="&#149 Invalid password: must be at least 3-12 in length."; $password=""; $cpassword=""; }
elseif ($password != $cpassword)
{ $msg="&#149 Invalid password: your passwords do not match."; $password=""; $cpassword=""; }
elseif (!ereg("^.+@.+\\..+$", $email))
{ $msg="&#149 Invalid email: that is not a valid e-mail address."; $email=""; }
elseif (fetch("SELECT email FROM $tab[user] WHERE email='$email';"))
{ $msg="&#149 That e-mail address has already been used."; $email="";}
elseif ((!preg_match ('/^[a-z]*$/i', $first)) || (strstr($first,".")))
{ $msg="&#149 Invalid first name: a-Z charactors only."; $first=""; }
elseif ((!preg_match ('/^[a-z]*$/i', $last)) || (strstr($last,".")))
{ $msg="&#149 Invalid last name: a-Z charactors only."; $last=""; }
elseif (($age <= 13) || ($age >= 100))
{ $msg="&#149 Invalid age: you must be 14 years or older to play."; $age=""; }
elseif (($messager == AIM) && (!preg_match ('/^[a-z0-9][a-z0-9]*$/i', $messager_id)))
{ $msg="&#149 Invalid screen name: sould only contain a-Z 0-9 charactors, no spaces."; $messager=""; $messager_id=""; }
elseif (($messager == MSN) && (!ereg("^.+@.+\\..+$", $messager_id)))
{ $msg="&#149 Invalid msn email: that is not a valid e-mail address.."; $messager=""; $messager_id=""; }
elseif (($messager == YaHoO) && (!preg_match ('/^[a-z0-9][a-z0-9]*$/i', $messager_id)))
{ $msg="&#149 Invalid screen name: sould only contain a-Z 0-9 charactors, no spaces."; $messager=""; $messager_id=""; }
elseif ($savedcaptcha != $captchastring)
{ $msg="&#149 Invalid number verification code"; }
else {
//echo "signups are disabled in demo version";
mysql_query("UPDATE $tab[user] SET credits=credits+1000 WHERE id=$refer");
mysql_query("UPDATE $tab[user] SET referrals=referrals+1 WHERE id=$refer");
mysql_query("UPDATE $tab[user] SET refcredits=refcredits+1000 WHERE id=$refer");
mysql_query("INSERT INTO $tab[user] (username,password,email,fullname,age,messager,online,ip,host,code,membersince,referredby,refcredits,status) VALUES ('$username','$password','$email','$first $last','$age','$messager: $messager_id','$time','$REMOTE_ADDR','$host','$code','$time','$refer','$refcredits','normal');");
mail_1("Welcome to yoursite","\nWelcome to $site[name]!\n\nYour account information:\n   Username: $username\n   Password: $password\n   Pin: $pin\n\nBefore you can login, you must verify your email address.\n\nTo confirm this email address click on the bottom link, or copy and paste it to your browser.\n$site[location]confirm.php?verify=yes&email=$email&pin=$pin&referer=$refer\n\nIf that link doesnt work, go to $site[location], login and enter in this pin:\n\n $pin\n\n-Admin\n\n\n-----------------------------\n-----------------------------\nAlso upon signup you were added to our Mailing list automatically! If you would like to be removed from this list please wait until you recieve the first letter and use the link at the botttom of the page to unsubscribe yourself!\n","$email");
//mail_2("Welcome to $site[name]!","\nWelcome to the yoursite!\n\nYour account information:\n   Username: $username\n   Password: $password\n   Pin: $pin\n\nBefore you can login, you must verify your email address.\n\nTo confirm this email address click on the bottom link, or copy and paste it to your browser.\n$site[location]confirm.php?verify=yes&email=$email&pin=$pin&referer=$refer\n\nIf that link doesnt work, go to $site[location], login and enter in this pin:\n\n $pin\n\n-Admin\n\n\n-----------------------------\n-----------------------------\nAlso upon signup you were added to our Mailing list automatically! If you would like to be removed from this list please wait until you recieve the first letter and use the link at the botttom of the page to unsubscribe yourself but doing this you wont be first to hear our new news!\n","$email");
header("Location: signup.php?step=4&email=$email&referer=$refer");
}
}
siteheader();
//LAMER CHECK//////////////
$host=gethostbyaddr("$REMOTE_ADDR");
$getbans =mysql_query("SELECT banned FROM $tab[banned];");
$bans = array();
while($ban=mysql_fetch_array($getbans)) {
array_push($bans, $ban[0]);
}
foreach ($bans as $correct){
if(strstr($host,"$correct")){
$banreason = mysql_fetch_array(mysql_query("SELECT reason FROM $tab[banned] WHERE banned='$correct';"));
?>
<table width="99%" height="100%">
<tr>
<td valign="top">
<br>
<b>Your account has been permently banned from yoursite.com.!! bitch!.
<br>
Here is our stated reason:</b><br>
<br><font color="red"><?=$banreason[0]?></font>
</td>
</tr>
</table>
<?
$lamerstop=bitch;
}
}
////////////////////////////
if($lamerstop!=bitch){
?>
<table width="100%" class="maintxt" height="100%">
<tr>
<td height="12"><b>Signing up: <font color="red">Step <?=$step?></font></b></td>
</tr>
<tr>
<td valign="top">
<?
if($step==4){?>
<br>         
THANKS FOR JOINING <?=$site[name]?>! You can now login..  <a href="index.php">Click Here
</a><br>
<br><a href="confirm.php?referer=<?=$referer?>">Click here to enter your pin number!</a><br />
<br>
Didnt receive your pin number? Our Bad!
<br><a href="resend.php">Request it again</a>!
<br>
<br>Still having problems?
<br><a href="support.php">GET HELP</a>!
<br>
<?}elseif($step==3){?>
<form method="post" action="signup.php?step=3">
<?if($msg){?><center><b><font color="#FFCC00"><?=$msg?></font></b></center><?}?>
<table align="center" cellspacing="2" cellpadding="2" class="maintxt">
<tr>
<td colspan="2">&nbsp;</td>
</tr>
<tr >
<td colspan="2" class="border"><b>Login Info:</b> Required</td>
</tr>
<tr>
<td align="right">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr >
<td align="right" >Username:</td>
<td>
  <input type="text" name="username" maxlength="18" value="<?=$username?>"> 
  <font size="1">3-18 length, a-Z 0-9 Charactors.</font></td>
</tr>
<tr>
<td align="right" >Password:</td>
<td>
  <input type="password" name="password" maxlength="18" value="<?=$password?>"> 
  <font size="1">3-12 length, a-Z 0-9 Charactors.</font></td>
</tr>
<tr>
<td align="right" >Confirm Password:</td>
<td>  <input type="password" name="cpassword" maxlength="18" value="<?=$cpassword?>"></td>
</tr>
<tr>
<td align="right" >E-mail Address:</td>
<td>
  <input type="text" name="email" maxlength="100" value="<?=$email?>"></td>
</tr>
<tr >
<td align="right" >&nbsp;</td>
<td>*hotmail, yahoo, aol, and other free email services check spam folders. </td>
</tr>
<tr >
<td colspan="2"  class="border"><b>Personal:</b> Required</td>
</tr>
<tr >
<td align="right" >&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr >
<td align="right" >First Name:</td>
<td>  <input type="text" name="first" maxlength="25" value="<?=$first?>"></td>
</tr>
<tr >
<td align="right" >Last Name:</td>
<td>  <input type="text" name="last" maxlength="25" value="<?=$last?>"></td>
</tr>
<tr >
<td align="right" >Age:</td>
<td>
  <input type="text" name="age" size="4" maxlength="2" value="<?=$age?>">
  <font size="1">18+ only.</font></td>
</tr>
<tr >
<td align="right" >&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr >
<td colspan="2" class="border"><b>Misc:</b> Optional</td>
</tr>
<tr >
<td align="right">&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr bgcolor="none">
<td align="right">instant messager:</td>
<td> <select  name="messager"><option class="maintxt" <?if($messager==""){echo"selected";}?>>N/A</option><option value="AIM" <?if($messager=="AIM"){echo"selected";}?>>AIM</option><option value="MSN" <?if($messager=="MSN"){echo"selected";}?>>MSN</option><option value="YaHoO" <?if($messager=="YaHoO"){echo"selected";}?>>YaHoO</option></select> <input type="text" name="messager_id" size="14" maxlength="50" value="<?=$messager_id?>"> <font size="1">so we may contact you if important.</font></td>
</tr>
<tr>
<td align="right"><strong>Referred By: ( If Referred )</strong></td><td> &nbsp;<? if($refer){ echo"<b>$refer</b><input type=hidden name=refer value=$refer>"; } ?></td>
</tr>
<tr bgcolor="none">
<td colspan="2" align="center"><table width="50%" border="0" cellspacing="0" cellpadding="0">
<tr  class="maintxt">
<td align="right"><span class="style1">Enter the Code </span></td>
<td><table cellpadding="0" cellspacing="0">
<tr>
<input type="hidden" name="savedcaptcha" value="<?echo $captcha;?>" />
<td class="style3"><input type="text" name="captchastring" size="10" maxlength="6" /></td>
<td class="style3"><font color="red">&nbsp;&nbsp;<span class="style2">&nbsp;<span class="style5"><?echo $captcha;?></span></span></font></td>
</tr>
</table></td>
</tr>
</table>
<br>
<b>Your ip address is being logged as: <font color="#red">
<?=$REMOTE_ADDR?>
</font></b><br>
Note: when signing up yahoo and aol emails dont work to well to 
get the verify email so id use something else.<br></td>
</tr>
<?php
$sagain = fetch("SELECT ip FROM $tab[user] WHERE ip='$REMOTE_ADDR'");
?>
<?if($sagain){ ?>
<tr bgcolor="none">
<td colspan="2" align="center"><strong><font color="red">This "<?=$REMOTE_ADDR?>" ip has already been used, the admins will be alerted as soon as you sign up!</strong></font><br>
<br></td>
</tr>
<? } ?>
<tr bgcolor="none">
<td colspan="2" align="center"><input type="submit" name="signup" value="signup"> &nbsp; &nbsp; &nbsp; <input type="reset" value="cancel"></td>
</tr>
</table>
<br>
<input type="hidden" name="hash" value="f7610358ffcc3db6558310ea4a166bcb">
</form>
<?}elseif($step==2){?>
<form method="post" action="signup.php">
  <div align="center">
    <?if($warn==yes){?>
    <b> 
  <?}?>
      In order to proceed, you must agree with the following Game Rules, also <br />

  <a href="<?=$site[location]?>rules.php" target="_blank">posted 
    at this link</a>. Please take a minute to read them over and if you have <br />

      questions, ask the staff after you join the game:</b> 
  </div>
  <table width="90%">
<tr>
<td align="center" valign="middle"><input type="checkbox" name="agree" value="yes" style="background: #CCCCCC;"> <input type="hidden" name="step2" value="1"> <strong class="maintxt">I have read, and agree to abide by the Game Rules.</strong></td>
<td align="center" valign="middle"><input type="submit" value="next &raquo;&raquo;"></td>
</tr>
</table>
</form>
<?}else{?>
<form method="post" action="signup.php">
  <div align="center">
    <?if($warn==yes){?>
    <b> 
  <?}?>
      In order to proceed, you must agree with the following Terms of Service, <br />

  <a href="<?=$site[location]?>terms.php" target="_blank">posted 
    at this link. Please take a minute to review them thoroughly:</a></b> 
  </div>
  <table width="90%">
<tr>
<td align="center" valign="middle"><input type="checkbox" name="agree" value="yes" style="background: #CCCCCC;"> <input type="hidden" name="step1" value="1">
<strong class="maintxt">I have read, and agree to abide by the Terms of Service.</strong></td>
<td align="center" valign="middle"><input type="submit" value="next &raquo;&raquo;"></td>
</tr>
</table>
</form>
<?}?>
</td>
</tr>
</table>
<?}
sitefooter();

?>