<?
include("html.php");
siteheader();
$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username,email,ip,lastip,membersince FROM $tab[user] WHERE id='$id';"));
?>
<?
if ($newemail || $veremail){

	$newemail=trim($newemail);

	if ($newemail == $veremail){
		if (strlen($newemail) > 2){
			if ($newemail == $login){
				$message = "Email has not been changed!";

			}else{
				#ok!
				
				mysql_query("UPDATE users SET email='$newemail' WHERE id=$id");
				
				if (mysql_error())
					print "<B>Error: </B>".mysql_error();
				else
					print "<center>Email has been changed Successfully</center>";
				exit;

			}
		}else{
			$message = "Email must be no longer than 100 characters in length";
		}	
	}else{
		$message = "New Email and Verified Email Must match";
	}
}


?><HTML>
<HEAD>
</HEAD>
<BODY>
<p align="center">
Changing Email for Account <B><?=$user[0]?></B>
<P>
<? if ($message)
	print "<P><center><B>Error:</B> $message</center>";
?>
<FORM>
<TABLE align="center">
<TR><TD align="right">New Email:</TD><TD><input type=text name=newemail></TD></TR>
<TR>
  <TD align="right">Verify Email: </TD>
  <TD><input name=veremail type=text id="veremail"></TD>
</TR>
</TABLE>
<p align="center">
<input type=hidden name=login value="<? echo $login ?>">

<input name="" type="submit"></FORM>
</body>
</html>

<?
sitefooter();
?>