<?
include("html2.php");
if(($username) && ($password)){
$user = mysql_fetch_array(mysql_query("SELECT id,status,code,email,ip FROM $tab[user] WHERE username='$username' AND password='$password';"));

   if($user[1] == banned){ header("Location: index.php?reason=banned&code=$user[2]"); }
elseif($user[1] == unverified){ header("Location: confirm.php?email=$user[3]"); }
elseif($user)
      {
       $host=@gethostbyaddr("$REMOTE_ADDR");
       mysql_query("UPDATE $tab[user] SET online='$time', ip='$REMOTE_ADDR', lastip='$user[4]', host='$host' WHERE id='$user[0]';");
       setcookie("trupimp",$user[2]);
       header("Location: index.php");
     }
  else{ header("Location: index.php?reason=invalid"); }
}

if($reason==banned){
$banned = mysql_fetch_array(mysql_query("SELECT reason FROM $tab[user] WHERE code='$code';"));
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>


<!--The below provided by must remain otherwise you will break the terms of service for this license-->
<!--  //  This site script has been provided by Game-Script.net \\  -->
<!--  //  This script can be purchased from Game-Script.net for \\  -->
<!--  //  personal use of an online gaming application and can  \\  -->
<!--  //  NOT be resold inpart or in full without autorization  \\  -->
<!--  //  by Game-Script.net                                    \\  -->
<!--  //           End Heading service statement                \\  -->


	<title><?=$site[name]?> - <?=$site[slogan]?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?=$sitekeywords?>" />	
	<meta name="description" content="<?=$sitemetadescription?>" />
	<meta name="robots" content="index, follow" />
	<link rel="stylesheet" href="stylesheets/global.css" type="text/css" />
	
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
			<!--<a href="#"><img src="images/ad.gif" alt="Ad" /></a>-->

		</div>
		<!--  \ HEADER CONTAINER / -->
		
		<!--  / MENU CONTAINER \ -->
		<div id="menuCntr">

			<ul>
				<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="index.php">Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
				<li>&nbsp;&nbsp;<a href="myaccount.php">Account </a></li>
				<li>&nbsp;&nbsp;&nbsp;<a href="play.php">Play</a></li>
				<li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="credits.php">Buy Turns </a></li>
				<li class="last">&nbsp;&nbsp;<a href="mailto:<?=$paypal_email_address?>">Contact</a></li>
			</ul>

		</div>
		<!--  \ MENU CONTAINER / -->
		
		<!--  / CONTENT BOX \ -->
		<div class="contentBox">

			<!--  / LEFT CONTAINER \ -->
			<div id="leftCntr">

		<? if(!$id){?>
				<!--  / LOGIN BOX \ -->
				<div class="loginBox">
					<div class="bottom">

						<h2>Log in on <?=$site[name]?></h2>

						<fieldset>
<form action="index.php" method="POST">
							<label for="email">Username:</label>
							<input name="username" type="text" class="field" id="username" value="admin" />

							<label for="pass">Password:</label>
							<input name="password" type="password" class="field" id="password" value="admin" />
							<input class="submit" type="submit" value="login" />
							<div class="clear"></div>
</form>
						</fieldset>
					
					</div>
				</div>
				<!--  \ LOGIN BOX / -->
				<? }?>
				<!--  / WELKOM BOX \ -->
				<div class="welkomBox">
					<div class="bottom">
					
						<h2>Welcome to <?=$site[name]?> </h2>

						<p> <?=$site[details]?> </p>
					
					</div>
				</div>
				<!--  \ WELKOM BOX / -->
		<? if(!$id){?>
				<!--  / REG BOX \ -->
				<div class="regBox">
					<div class="bottom">

						<h2>Register on <?=$site[name]?> </h2>

						<p><?=$site[signupincentivemsg]?></p>
						
						<a href="signup.php">REGISTER Today and WHACK a Don! </a>
					
					</div>
				</div>
				<!--  \ REG BOX / -->
            <? }?>
		<? if($id){?>
				<!--  / REG BOX \ -->
				<div class="regBox">
					<div class="bottom">

						<h2>Refer Someone Today! </h2>

						<p>Earn account credits just for sending someone your referral link</p>
						
						<a href="refer.php">Get your referral link here </a>
					
					</div>
				</div>
				<!--  \ REG BOX / -->
            <? }?>
		<? if($id){?>
				<!--  / REG BOX \ -->
				<div class="regBox">
					<div class="bottom">

						<h2>Now that you've logged in... </h2>

						<p>Click the below link and join a round to get in on the action</p>
						
						<a href="play.php">Get to Playing now! </a>
					
					</div>
				</div>
				<!--  \ REG BOX / -->
            <? }?>
			</div>
			<!--  \ LEFT CONTAINER / -->
			
			<!--  / RIGHT CONTAINER \ -->
			<div id="rightCntr">

				<!--  / TEXT BOX \ -->
				<div class="textBox">
					<div class="bottom">

						<h2>Latest News on <?=$site[name]?>! </h2>
                        <div align="center">
                          <?  
    $getnews = mysql_query("SELECT id,news,posted,subject FROM $tab[news] WHERE id>0 ORDER BY posted DESC limit 2;");  
    while ($news = mysql_fetch_array($getnews)){?>
                          <ul class="first">
                            <li> 
                              
                              <table cellpadding="10">
                                <tr>
                                  <td><u><strong><?=$news[3]?></strong></u></td>
           </tr>
                                                      </table>
                            </li>
                          </ul>
                          <table width="450" cellpadding="10">
                            <tr>
                              <td><?=$news[1]?><br /><strong><?=date('F j, Y g:i a', $news[2])?></strong></td>
       </tr>
                                              </table>
                          <br>
                          <br />
                          <? }?>						
                        </div>
                        <div class="archief"><!--archive link here if created--></div>
					
					</div>
				</div>
				<!--  \ TEXT BOX / -->

			</div>
			<!--  \ RIGHT CONTAINER / -->

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


</html>