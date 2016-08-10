<?
// Turn off all error reporting
error_reporting(0);
include("funcs.php");



function admin(){

global $id, $tab;

$user = mysql_fetch_array(mysql_query("SELECT status FROM {$tab['user']} WHERE id='$id';"));

if($user[0] != admin){ die("You are no fucking admin, stop fucking around, get the fuck out of here!!"); }

}



function secureheader(){

global $id, $tab, $time;

  $user = mysql_fetch_array(mysql_query("SELECT online,status,code FROM $tab[user] WHERE id='$id';"));

  $idle=$time-$user[0];



      if (!$user){ setcookie("trupimp",NODATA); header("Location: index.php?reason=notlogged"); }

  elseif ($idle >3600){ setcookie("trupimp",NODATA); header("Location: index.php?reason=idle"); }

  elseif ($user[1] == banned){ setcookie("trupimp",NODATA); header("Location: index.php?reason=banned&code=$user[2]"); }



  mysql_query("UPDATE $tab[user] SET online='$time' WHERE id='$id';");

}



function siteheader(){

global $site, $tab, $menu, $time, $id;



//SHOWING PIMPS ONLINE

$idletime=$time-760;

$idle2 = $time-604800;

$reg = fetch("SELECT COUNT(id) FROM $tab[user];");

$real = fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idle2;");

$sups = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='supporter';"));

$bans = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='banned';"));



$online=fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idletime;");



$newest_user = mysql_result(mysql_query('SELECT username FROM users ORDER BY id DESC LIMIT 1'), 0);



$query = mysql_query("SELECT * from users WHERE username = '$newest_user'");



$getgames = mysql_query("SELECT round FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");

while ($game = mysql_fetch_array($getgames)){

$onlinepimps=fetch("SELECT COUNT(id) FROM r$game[0]_$tab[pimp] WHERE online>$idletime;");

$pimpin=$pimpin+$onlinepimps;

}



$getgames = mysql_query("SELECT round FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");

while ($game = mysql_fetch_array($getgames)){

$pimpshitting=fetch("SELECT COUNT(id) FROM r$game[0]_$tab[pimp] WHERE lastattack>$idletime;");

$attackin=$attackin+$pimpshitting;

}





?>

<!--<?=$real?>!-->

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
 <head>
<title><?=$site[name]?> - <?=$site[slogan]?></title>
<meta http-equiv="pragma" CONTENT="no-cache">
<script language="JavaScript1.2" src="tru/truepimpz.js"></script>
  <link rel="stylesheet" href="style.css">

<!-- Script has been licensed by Game-Script.net a business of 
Dedicated Gaming Network, LLC  www.dedicatedgamingnetwork.com  
You can not remove any part of the copyright or this notice 
without prior authorization from game-script.net or Theodore Gaushas -->


<style type="text/css">

<!--

.style2 {color: #eeeeee}

.style5 {font-size: 18px; }

-->

</style>

<style type="text/css">

<!--

.style1 {color: #FF0000}



div#navcontainer

{

background: #242424 url('tru/gfx/rd.gif');

border-top: solid 1px #000;

border-bottom: solid 1px #000;

}



div#navcontainer ul

{

font-family: Arial, Helvetica, sans-serif;

font-weight: bold;

color: white;

text-align: center;

margin: 0;

padding-bottom: 5px;

padding-top: 5px;

}



div#navcontainer ul li

{

display: inline;

margin-left: -4px;

}



div#navcontainer ul li a

{

padding: 5px 10px 5px 10px;

color: white;

text-decoration: none;

border-right: 1px solid #000;

}



div#navcontainer ul li a:hover

{

background-color: #500a0a;

color: white;

}



#active a { border-left: 1px solid #000; }



-->

</style></head>

<body bgcolor="#666666" background="tru/gfx/background.jpg" text="#FFFFFF" leftmargin="8" topmargin="8" marginwidth="8" marginheight="8" style="background-repeat: repeat;">

<style type="text/css">

img

{

 border: 0px;

}



.style2 {

	font-size: 14px;

	font-weight: bold;

	font-style: italic;

}



.style4 {font-size: 16px}
</style>
 <br>
</head>



 <table width="900" height="100%" align="center" cellspacing="0" style="border: 2px solid #868686;">





   <tr>

   <td height="161">

   <table width="100%" height="100%" cellspacing="0" cellpadding="0">

   <tr>

     <td align="left" valign="top"><b><div style="width:100%; filter:Glow(color=#B82121, strength=2)"></small></b></td></div> 

    </tr>  

 </table>



<center>

  <tr bgcolor="000000" text="BLACK">

   <td height="20" align="center"> <!-- Start of NavBar definition -->

<center>

	<table border=0 cellpadding=0 cellspacing=0 width="100%" align="center"><tr height=36><td style="border-top: 1px solid #868686; border-left: 1px solid #000; border-right: 1px solid #000" background="tru/gfx/rd.gif">

	<center>

		<table border=0 cellpadding=0 cellspacing=0 align="center"><tr height=26><td width=10>&nbsp;</td>









		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td><a href="play.php"><img src="images/play.jpg" border=0></a></td>

		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td width=13>&nbsp;</td>



				<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td><a href="myaccount.php"><img src="images/account.jpg" border=0></a></td>

		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td width=13>&nbsp;</td>



				<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td><a href="credits.php"><img src="images/turns.jpg" border=0></a></td>

		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td width=13>&nbsp;</td>



		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td><a href="logout.php"><img src="images/logout.jpg" border=0></a></td>

		<td width=5><img src="tru/gfx/-.gif" width=5 height=1></td><td width=13>&nbsp;</td>



		<td width=15>&nbsp;</td></tr></table>
	</td></tr></table>
<!-- End of NavBar definition -->
<br />
  <tr bgcolor="000000">
   <td height="3" align="center"></td> </td>
  </tr>
    <tr bgcolor="000000">
   <td align="left" valign="top">
<?
}
function sitefooter(){
global $site, $tab, $id;
$user = @mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));
?>
  </td>
  </tr>
  <?if($user[0] == admin){?>
  <tr>
   <td height="12" align="center" bgcolor="000000"><small><b><font color="ff0000">Admins:</font></b> <a href="in$taLl.php">Install Round</a> &bull; <a href="eDiT$hit.php">Edit Site</a> &bull; <a href="bling.php">Profits</a> &bull; <a href="po$tNeW$.php">Post News</a>&bull; <a href="referers2.php">Check refers</a> &bull; <a href="mrefered.php">New Signups </a> &bull; <a href="/table/">Table Manager</a> &bull; <a href="banbyip.php">Ban by IP </a><br>
   &bull; <a href="cheaterZ.php">IP Check</a> &bull; <a href="pass.php">Pass Check</a> &bull; <a href="new$letta.php">Newsletter</a> &bull; <a href="admins.php">More Tools</a> &bull; <a href="time.php" target="_blank">View Linux Time </a> <br>
<a href="cronjob1234567891.php" target="_blank">AUTO -=||=- AUTO </a>&bull; <a href="mrefered.php">Newest Signed up w/referred </a>&bull; <a href="survey.php">Survey's</a></small></td>
  </tr>
  <?}?>
</table>
 <br>
<center>Copyright &copy; <?=$site[name]?>, all rights reserved<br> 
<!-- you can not remove this copyright line without prior athorization from Game-Script.net and a removal fee is paid. -->
<a href="http://www.game-script.net">Script provided by: Game-Script.net</a></center>
</body>
 </html>
<? }?>