<?php 
include("html.php");

if($ips != ""){
       mysql_query("UPDATE users SET status='banned' WHERE ip='$ips';");
$msg="all users with IP address of $ips has been banned";
}

admin();
$menu='pimp/';
secureheader();
siteheader();
?>
<br /><center><?=$msg?>
  <br />
  <br />
  This will ban all accounts with the ip address you enter. <br />
<form method="post" action="">
  <label>
  <input type="text" name="ips" />
  </label>
  <input type="submit" name="Submit" value="Submit" />
</form></center>
<?
sitefooter();
?>