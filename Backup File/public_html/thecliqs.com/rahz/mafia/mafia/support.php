<?
include("html.php");
siteheader();
$cansend = $_POST["canSend"];
if($cansend == 1)
{
$mailfrom = $_POST["txtMailFrom"];
$mailsubject = $_POST["txtSubject"];
$mailbody = $_POST["txtBody"];
$mailsto = $_POST["txtMailTo"];
$mailarray = split(",",$mailsto);
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers .= "From: $mailfrom\r\n";
foreach($mailarray as $mailto)
{
	echo $mailto."<br>";

	if(mail($mailto,$mailsubject,$mailbody,$headers))
	{
		$status = 1;
	}
	else
	{
		$status = 2;
		$problemid .= $mailto;
	}
}
if($status == 1)
{
	echo "<center><font color=blue><b>Successfully Sent</b></font>.</center>";
}
else
{
	echo "<center><font color=blue><b>Problem in sending mail for $problemid</b></font></center>";
}
}
?>
<link href="style.css" rel="stylesheet" type="text/css">
<link href="css/main.css" rel="stylesheet" type="text/css">
<link href="css/common.css" rel="stylesheet" type="text/css">
<style type="text/css">
<!--
.style1 {
	font-size: xx-small;
	font-weight: bold;
	color: #ooccff;
}
.style3 {font-size: xx-small}
.style5 {font-size: xx-small; color: #FFFFFF; }
-->
</style>

<center>
<table border=0 cellpadding=5 cellspacing=2 width=90% align=center>
<tr>
  <td colspan=2 align=center><span class="style1"><img src="http://img491.imageshack.us/img491/9755/mail17yw.gif"></span></td>
</tr>
<tr>
  <td colspan=2 align=center class="style1"><a href="#emails" class="style3">CONTACT ADMIN</a></td>
</tr>
<tr>
  <td colspan=2 align=center>&nbsp;</td>
</tr>
<form name=form1 method=post action=<?=$_SERVER['PHP_SELF'];?> onsubmit="this.canSend.value=1;return true;">
<tr>
<td class="style1"><div align="center">From:</div></td>
<td><p>
          <input name=txtMailFrom type=text class="login_input" style="width: 250px" value="<?php echo $mailfrom;?>">
        </p>
      </td>
</tr>

<tr>
<td class="style1"><div align="center">To:</div></td>
<td><p>
          <input name=txtMailTo type=text class="login_input" style="width: 250px">
        </p>
      </td>
</tr>

<tr>
<td class="style1"><div align="center">Subject:</div></td>
<td><input name=txtSubject type=text class="login_input" style="width: 250px" value="<?php echo $mailsubject;?>"></td>
</tr>

<tr>
<td valign=top class="style1"><div align="center">Message:</div></td>
<td><textarea name=txtBody cols=50 rows=10 class="login_input"></textarea></td>
</tr>
<tr>
<td colspan=2 align=center>
<input type=submit class="login_input" value="Send me a message">
</td>
<input type=hidden name=canSend value=0>
<tr>
  <td colspan=2 align=center>&nbsp;</td>
<tr>
  <td colspan=2 align=center>&nbsp;</td>
<tr>
  <td colspan=2 align=center>&nbsp;</td>
<tr>
  <td colspan=2 align=center><div align="left" class="style1">
    <div align="center">Easy to use mail system. Just copy & paste my e-mail from below for any questions.</font> <br>
      <a name="emails"></a><br>
      <span class="style5"><?=$paypal_email_address?></span><br>
    </div>
  </div></td>
</form>
</table>

<p align="right">&nbsp;</p>
<? sitefooter();?>