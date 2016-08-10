<?
include("html.php");
$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username,email,ip,lastip,membersince,id FROM $tab[user] WHERE id='$id';"));
siteheader();
?>
<?
if ($newpass || $verpass){

	$newpass=trim($newpass);

	if ($newpass == $verpass){
		if (strlen($newpass) > 2){
			if ($newpass == $login){
				$message = "Password cannot be the same as Login!";

			}else{
				#ok!
				
				mysql_query("UPDATE users SET Password='$newpass' WHERE id=$id");
				
				if (mysql_error())
					print "<B>Error: </B>".mysql_error();
				else
					print "<center>Password changed Successfully</center>";
				exit;

			}
		}else{
			$message = "Password must be no longer than 12 characters in length";
		}	
	}else{
		$message = "New Password and Verified Password Must match";
	}
}


?><HTML>
<HEAD>
</HEAD>
<BODY>
<p align="center">
Changing Password for Account <B><?=$user[0] ?></B>
<P>
<? if ($message)
	print "<P><center><B>Error:</B> $message</center>";
?>
<FORM>
<TABLE align="center">
<TR><TD align="right">New Password:</TD><TD><input type=text name=newpass></TD><TD align="right">Verify Password:</TD><TD><input type=text name=verpass></TD></TR>
</TABLE>
<p align="center">
<input type=hidden name=login value="<?=$user[0] ?>">
<input name="" type="submit">
</FORM>
</body>
</html>

<?
sitefooter();
?>