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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">


<!--The below provided by must remain otherwise you will break the terms of service for this license-->
<!--  //  This site script has been provided by Game-Script.net \\  -->
<!--  //  This script can be purchased from Game-Script.net for \\  -->
<!--  //  personal use of an online gaming application and can  \\  -->
<!--  //  NOT be resold inpart or in full without autorization  \\  -->
<!--  //  by Game-Script.net                                    \\  -->
<!--  //           End Heading service statement                \\  -->


<head>
	<title><?=$site[name]?> - <?=$site[slogan]?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?=$sitekeywords?>" />	
	<meta name="description" content="<?=$sitemetadescription?>" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" href="stylesheets/global2.css" type="text/css" />
	
	<!--[if lte IE 6]>
		<link rel="stylesheet" href="stylesheets/ie6.css" type="text/css" />
	<![endif]-->
</head>
<body>

	<!--  / MAIN CONTAINER \ -->
	<div id="mainCntr">
		
		<!--  / HEADER CONTAINER \ -->
		<div id="headerCntr">
           <center>
			<font size="+3" color="#FFFFFF"><?=$site[name]?></font><br />
            <font size="+1" color="#FFFFFF"><?=$site[slogan]?></font>
		</center>
            <!--<a href="#"><img src="images2/ad.gif" alt="Ad" /></a>-->
		</div>
		<!--  \ HEADER CONTAINER / -->
		
		<!--  / MENU CONTAINER \ -->
		<div id="menuCntr">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
            <li>&nbsp;&nbsp;<a href="myaccount.php">Account </a></li>
            <li>&nbsp;&nbsp;&nbsp;<a href="play.php">Play</a></li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="credits.php">Buy Turns </a></li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="onreward.php">GCash </a></li>
            <li class="last">&nbsp;&nbsp;&nbsp;<a href="logout.php">Logout</a></li>
          </ul>
        </div>
		<!--  \ MENU CONTAINER / -->
        <!--  / CONTENT BOX \ -->
<div class="contentBox">

			<!--  / LEFT 2 CONTAINER \ -->
			<div id="left2Cntr">

				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Main Menu </h2>

						<ul>
							<li><a href="index.php">Home</a></li>
							<li><a href="newsandupdates.php">News</a></li>
							<li><a href="play.php">Play Now!</a></li>
							<li><a href="mailto:<?=$paypal_email_address?>">Contact</a></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				
				<?php $user99 = @mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));
				if($user99[0] == admin){?>
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">
					
						<h2>Administration</h2>

						<ul>
							<li><a href="in$taLl.php">Install New Round</a></li>
							<li><a href="dbcleanup.php">Clean Database Tables</a></li>
							<li><a href="sitesettings.php">Site Settings</a></li>
							<li><a href="logs.php">Tracking logs</a></li>
							<li><a href="bling.php">Site Profits</a></li>
							<li><a href="po$tNeW$.php">Post News</a></li>
							<li><a href="/table/" target="_blank">Fast DB Admin</a></li>
							<li><a href="banbyip.php">Ban by IP</a></li>
							<li><a href="cheaterZ.php">Multi ip check</a></li>
							<li><a href="new$letta.php">Newsletter</a></li>
							<li><a href="time.php">Linux Time</a></li>
							<li><a href="cronjob1234567891.php" target="_blank">Hand Out Prizes</a></li>
							<li><a href="mrefered.php">Latest signups</a></li>
							<li><a href="survey.php">Surveys</a></li>
							<li><a href="admins.php">Add Staff / Misc.</a></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
			<? }?>
			
				<?php if($id){?>
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Personal Info</h2>

						<ul>
							<li><a href="myaccount.php">My Account</a></li>
							<li><a href="changeemail.php">Change password</a></li>
							<li><a href="changepassword.php">Change email</a></li>
							<li><a href="refer.php">My Referral Link</a></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
			<? }?>
			</div>
			<!--  \ LEFT 2 CONTAINER / -->
			
			<!--  / CENTER CONTAINER \ -->
			<div id="centerCntr">
				
				<!--  / TEXT BOX \ -->
				<div class="textBox">
					<div class="top">
						<div class="bottom">

						<span><?=$site[announcement]?></span>
					
						</div>
					</div>
				</div>
				<!--  \ TEXT BOX / -->

				<!--  / TEXT BOX \ -->
				<div class="textBox">
					<div class="top">
						<div class="bottom">

						<?
}
function sitefooter(){
global $site, $tab, $id;
$user = @mysql_fetch_array(mysql_query("SELECT status FROM $tab[user] WHERE id='$id';"));

//SHOWING PIMPS ONLINE
$idletime=$time-760;
//$idle2 = $time-604800;
$reg = fetch("SELECT COUNT(id) FROM $tab[user];");
//$real = fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idle2;");
$sups = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='supporter';"));
//$bans = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='banned';"));

$online=fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idletime;");
?>
					
					</div>
				</div>
			</div>
		</div>
			<!--  \ CENTER CONTAINER / -->
			
			<!--  / RIGHT 2 CONTAINER \ -->
			<div id="right2Cntr">

				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Purchase Items </h2>

						<ul>
							<li><a href="credits.php">Purchase more Reserves </a></li>
							<li><a href="credits.php">Purchase a Subscription </a></li>
                            <li><a href="onreward.php">GCash </a></li>
                        </ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Information</h2>

						<ul><li><a href="guide.php">Guide</a></li>
					    	<li><a href="rules.php">Rules</a></li>
						    <li><a href="terms.php">Terms of Service</a></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Site Stats</h2>

						<ul><li>Members: <span><?=$reg?></span></li>
						    <li>Supporters: <span><?=$sups[0]?></span></li>
							<li>Online: <span><?=$online[0]?></span></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->				
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>More Links</h2>

						<ul><!--  / Google Adsense box 120 x 240 \  -->
<center><script type="text/javascript"><!--
google_ad_client = "<?=$site[adsense]?>";
/* 120x240, created 10/28/08 */
google_ad_slot = "5485830650";
google_ad_width = 120;
google_ad_height = 240;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script></center>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->			</div>
			<!--  \ RIGHT 2 CONTAINER / -->

	  </div>
		<!--  \ CONTENT BOX / -->

	</div>
	<!--  \ MAIN CONTAINER / -->
	
	<!--  / FOOTER CONTAINER \ -->
	<div id="footerCntr">

		<p>Copyright &copy; <?php echo date("Y") ?> <?=$site[name]?>.<br />
<!--The below provided by must remain otherwise you will break the terms of service for this license-->
		Provided by: Game-Script.net <a href="http://www.game-script.net">URL</a></p>

</div>
	<!--  \ FOOTER CONTAINER / -->
	
</body>
</html><? }?>