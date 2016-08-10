<?
include("html.php");

$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username,email,ip,lastip,membersince,cash FROM $tab[user] WHERE id='$id';"));

$menu='pimp/';
secureheader();
siteheader();
?>
	    <div align="center">
	    <table width="500">
	      <tr>
	        <td height="12" align="center">Welcome <b> 
	          <?=$user[0]?>
	          </b>
  <br>
  <a href="changepassword.php">Change Password</a><br><a href="changeemail.php">Change Email</a></td>
          </tr>
	      <tr>
	        <td align="center" valign="top">
	          <br><b>Your account is currently holding <font size="+1"><?=commas($user[1])?></font> turns!</b>
	          <br><b>Your account is currently holding <font size="+1"><?=commas($user[8])?></font> cash!</b>
	          <br>
	          <a href="credits.php">Click here to add turns to your account</a>
	          <br>
			  <a href="cash.php">Click here to convert cash to credits</a>
	          <br>
	          <table>
	            <tr><td align="right">Account Status:</td><td><?=$user[2]?></td></tr>
	            <tr><td align="right">Username:</td><td><?=$user[3]?></td></tr>
	            <tr><td align="right">E-mail address:</td><td><?=$user[4]?></td></tr>
	            <tr><td align="center" colspan="2">&nbsp;</td></tr>
	            <tr><td align="right">Logged in from:</td><td><?=$user[5]?></td></tr>
	            <tr><td align="right">Last logged in from:</td><td><?=$user[6]?></td></tr>
	            <tr><td align="center" colspan="2">&nbsp;</td></tr>
	            <tr><td align="right">Games played:</td><td>0</td></tr>
	            <tr><td align="right">Logged in:</td><td>0 times</td></tr>
	            <tr><td align="right">Strikes Left <small>(for cheating)</small>:</td><td>5</td></tr>
              </table>
       <br>
	          <br><b>You've been a Mobster since <?=date("M dS, Y", $user[7])?></b>
            </td>
   	      </tr>
        </table></div>
<?
sitefooter();
?>