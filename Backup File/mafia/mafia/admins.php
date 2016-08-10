<?php 
include("html.php");

$menu='pimp/';
admin();
secureheader();
siteheader();
?>
<?php
$stat = mysql_fetch_array(mysql_query("select * from users where id='$id'"));
if ($stat[status] != admin) {
	print "You're not an admin.";
	exit;
}
?>

<p>&nbsp;</p>
<p>Welcome to the admin panel. What will you do?</p>
<ul>
	<li><a href=admins.php?view=del>Delete Member</a>
	<li><a href=admins.php?view=add>Add Staff</a>
  <li><a href=admins.php?view=ban>Ban From Game</a>   
  <li><a href=admins.php?view=search>Player Search</a>  
  <li><a href=admins.php?add=credits>Hand out Credits</a> 
  <li><a href=admins.php?mass=credits>Mass handout</a> 
</ul>
<p>&nbsp;</p>
<?php if ($mass == credits) { ?>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">Handing out some credits? (<a href="admins.php?mass=credits">back</a>)</div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><?php 
	if ($now == doit) { 
		if(!is_numeric($howmanycreds)){
		echo "<div align=center>How many credits?</div>";
		print "<div align=center><a href=admins.php?mass=credits>Back</a></div>";
		exit;
		}else{
		print "<div align=center>you have given ".commas($howmanycreds)." credits to everyone</div>";
		mysql_query("UPDATE $tab[user] SET credits=credits+$howmanycreds");
		}
	}
	?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
        <form action="admins.php?mass=credits&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">credits:</div></td>
              <td><input type="text" name="howmanycreds"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<?
exit;
} 
?>
<?php
if ($view == del) {
	print "<form method=post action=admins.php?view=del&step=del>Delete ID <input type=text name=did>. <input type=submit value=Delete></form>";
	if ($step == del) {
		if ($did != admin) {
			mysql_query("delete from $tab[user] where username=$did");
			print "You deleted user $did.";
		} else {
			print "You cannot delete the owner.";
		}
}
}

if ($view == ban) {
	print "<form method=post action=admins.php?view=ban&step=add>ID <input type=text name=aid> Should be <select name=banned><option value=banned>Banned</option><option value=tempban>Temp Ban</option><option value=normal>Unbanned</option></select><br />For reason: <input type=text name=rid> <input type=submit value=Add></form>";
	if ($step == add) {
     	if ($aid != 1) {
			mysql_query("update $tab[user] set status='$banned' where username=$aid");
			mysql_query("UPDATE $tab[user] SET reason='$rid' WHERE username=$rid");
			print "<br />You $banned $aid<br />For: $rid";
			} else {
				print "You cannot ban the owner.";
				}
		}
	}
if ($view == search) {
	print "<form method=post action=admins.php?view=search&step=show>Value<input type=text name=lookup> Search By <select name=sby><option value=email>e-mail</option><option value=username>username</option><option value=ip>ip</option><option value=password>password</option><option value=id>ID</option></select> <input type=submit value=show></form>";
	if ($step == show) {
     
			$get = mysql_query("select * from $tab[user] where $sby='$lookup'");
 while ($list = mysql_fetch_array($get)) {
 $wallet = number_format($list[credits]);
 echo "
    <table width=100%>
    <tr>
    <td bgcolor=#CCCCCC><font color=white></font><a href=otheraccount.php?pid=$list[id]>$list[username]</a> ID # $list[id]</td>
    <td bgcolor=#CCCCCC><font color=white size=1>$list[email]</font></td>
    <td bgcolor=#CCCCCC><font color=white></font>$list[password]</td>
    <td bgcolor=#CCCCCC><font color=white></font>$list[ip]</td>

    
   </tr>";
} 
echo '</table>';

		}
	}

if ($view == add) {
	print "<form method=post action=admins.php?view=add&step=add>Add master account username: <input type=text name=aid> as an <select name=status><option value=0>normal</option><option value=5>helper</option><option value=2>moderator</option></select>. <input type=submit value=Add></form>";
	if ($step == add) {
		if ($aid != 'admin') {
			mysql_query("update $tab[user] set lvl='$status' where username='$aid'");
			print "You added $aid as a level $status.";
			} else {
				print "You cannot change the status of the owner";
				}
		}
	}

if ($view == addnewuser) {
	print "<form method=post action=admins.php?view=addnewuser&step=add>username <input type=text name=user2><br>email <input type=text name=email2><br>Password<input type=text name=pass2><br>

 <input type=submit value=Add></form>";
	if ($step == add) {
			mysql_query("insert into quickuser (user, email, pass) values('$user2','$email2','$pass2')");
			print "You added the user $user with the email $email and the password $pass.";
	}
}
?>
<?php if ($add == credits) { ?>
<table width="60%"  border="0" align="center" cellpadding="0" cellspacing="1">
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">Handing out some credits? (<a href="admins.php?add=credits">back</a>)</div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><?php 
	if ($now == doit) { 
		if (!$uid) {
			print "<div align=center>Who's gonna get the credits?</div>";
			print "<div align=center><a href=admins.php?add=credits>Back</a></div>";
			exit;
			}
		print "<div align=center>$uid has been given ".commas($howmanycreds)." credits</div>";
		mysql_query("UPDATE $tab[user] SET credits=credits+$howmanycreds WHERE username='$uid'");
	}
	?>
    </td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
        <form action="admins.php?add=credits&now=doit" method="post">
          <table width="60%"  border="0" cellspacing="1" cellpadding="0">
            <tr>
              <td><div align="right">Username:</div></td>
              <td><input type="text" name="uid"></td>
            </tr>
            <tr>
              <td><div align="right">credits:</div></td>
              <td><input type="text" name="howmanycreds"></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td><input type="submit" name="Submit" value="Submit"></td>
            </tr>
          </table>
        </form>
    </div></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td><div align="center">
        <table width="100%"  border="0" cellspacing="1" cellpadding="0">
          <tr>
            <td><div align="justify"><span class="wepon">NOTE:</span> <span class="style2">when handing out credits you have to remember that people usually pay for these, so dont get gay and start handing them out to everyone, it will have no purpose to it, please be smart and dont do stupid things.</span></div></td>
          </tr>
        </table>
    </div></td>
  </tr>
</table>
<?
exit;
} 
?>
<?php
sitefooter();
?>