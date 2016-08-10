<? 
$thug = 0;
$cash = 0;
$hoe = 0;
$casht1 = 0;
$hoet1 = 0;
$thugt1 = 0;
$turnt1 = 0;
$thugleft = 0;
$hoeleft = 0;
$killbystd = 0;
// Turn off all error reporting 
//error_reporting(0); 
if(!is_numeric($tru)){ 
header("Location: ../play.php"); 
} 
$checktru = substr($tru, 0, 1); 
if($checktru == 0){ 
header("Location: ../play.php"); 
} 
if($r <= 0){ $r == 0;} 
include("funcs.php"); 
function doswitch (){ 
global $tab, $time, $id, $tru; 
$pimp = mysql_fetch_array(mysql_query("SELECT code,status,alert FROM $tab[pimp] WHERE id='$id';")); 
$user = mysql_fetch_array(mysql_query("SELECT username FROM $tab[user] WHERE code='$pimp[0]';")); 
$getgames = mysql_query("SELECT round FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;"); 
while ($game = mysql_fetch_array($getgames)) 
{ 
if(fetch("SELECT user FROM $tab[stat] WHERE user='$user[0]' AND round='$game[0]';")){ 
?><a href="dologin.php?switch=game&tru=<?=$game[0]?>"><?if($tru == $game[0]){?><font color="FF0000"><?}?>Round <?=$game[0]?><?if($tru == $game[0]){?></font><?}?></a>, <? 
} 
} 
} 
function ADMINHEADER ($title){ 
global $tab, $tru, $id; 
$pimp = mysql_fetch_array(mysql_query("SELECT status FROM $tab[pimp] WHERE id='$id';")); 
if($pimp[0] != admin){ echo"<center><b>ACCESS DENIED!</b></center>"; die(); } 
header("Cache-Control: no-cache, must-revalidate"); 
Header("Pragma: no-cache"); 
Header("Expires: Thu, 01 Jan 1970 00:00:00 GMT"); 
?> 
<? 
} 
function GAMEHEADER ($title){ 
global $tab, $site, $id, $masteraccount, $tru; 
$boot = mysql_fetch_array(mysql_query("SELECT online,status,pimp,crew,newalert,alert,rank,nrank,networth,msg,atk,ivt FROM $tab[pimp] WHERE id='$id';")); 
$crw = mysql_fetch_array(mysql_query("SELECT name,founder,icon,id FROM $tab[crew] WHERE id='$boot[3]';")); 
$game = mysql_fetch_array(mysql_query("SELECT speed,reserves,credits,crewmax,starts,ends,gamename FROM $tab[game] WHERE round='$round';")); 
$pmp = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,city,networth,money,trn,res,condom,medicine,crack,weed,glock,shotgun,uzi,ak47,whore,thug,whappy,thappy,payout,crew,msg,atk,ivt,lowrider,attin,attout,lastattackby,lastattack,cmsg,bank,tbank,beer FROM $tab[pimp] WHERE id='$id';")); 
$idle=$time-$boot[0]; 
if (!$boot){ header("Location: ../play.php?msg=select"); } 
elseif ($idle > 3600){ header("Location: ../play.php?msg=idle"); } 
elseif ($boot[1] == banned){ header("Location: ../play.php?msg=disabled"); } 
$protection = mysql_fetch_array(mysql_query("SELECT protection,protectstarted FROM $tab[pimp] WHERE id='$id';")); 
$protect=$protection[0]+$protection[1]-$time; 
if($protect < 0){$protect=0;} 
$bling = mysql_fetch_row(mysql_query("SELECT sum(amount), sum(fee) FROM $tab[paypal];")); 
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

			<a href="#"><img src="images2/ad.gif" alt="Ad" /></a>

		</div>
		<!--  \ HEADER CONTAINER / -->
		
		<!--  / MENU CONTAINER \ -->
		<div id="menuCntr">
          <ul>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="scout.php?tru=<?=$tru?>">Hire</a></li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="produce.php?tru=<?=$tru?>">Drugs </a></li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;<a href="collect.php?tru=<?=$tru?>">Collect</a></li>
            <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="../credits.php" target="_blank">BuyTurns </a></li>
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

						<h2>Actions</h2>

						<ul>
							<li><a href="index.php?tru=<?=$tru?>">Main Menu</a></li>
							<br />
							<li><a href="attack.php?tru=<?=$tru?>">Attack</a></li>
							<li><a href="scout.php?tru=<?=$tru?>">Recruit OPs & DUs</a></li>
							<li><a href="produce.php?tru=<?=$tru?>">Produce Drugs</a></li>
							<li><a href="collect.php?tru=<?=$tru?>">Collect Cash</a></li>
							<li><a href="travel.php?tru=<?=$tru?>">Travel to new city</a></li>
							<li><a href="purchase.php?tru=<?=$tru?>">Black Market</a></li>
							<li><a href="bank.php?tru=<?=$tru?>">Bank & Transfers</a></li>
						</ul>
					
					</div>
				</div>
<!--  \ MENU BOX / -->
<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Personal Info</h2>

						<ul>
							<li><a href="family.php?cid=<?=$crw[id]?>&tru=<?=$tru?>">My Family</a></li>
							<li><a href="options.php?opt=profile&tru=<?=$tru?>">Options</a></li>
							<li><a href="contacts.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>">Contacts</a></li>
							<li><a href="awards.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>">Awards</a></li>
						</ul>
					
					</div>
				</div>
		<!--  \ MENU BOX / -->
<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Staff</h2>

						<ul>&nbsp;&nbsp;<span class="stylestaff">Administrators</span><br />
                              <? 
                                 $admin = mysql_query("select pimp,status from $tab[pimp] where status='admin'");
                                 while ($adm = mysql_fetch_array($admin)) { 
                                 echo "<li><a href=mobster.php?pid=$adm[pimp]&tru=$tru>$adm[pimp]</a></li>";
                                 }
                 			       ?>
<br />
                             &nbsp;&nbsp;<span class="stylestaff">Moderators</span><br>
                              <? 
                                 $moderator = mysql_query("select pimp,status from $tab[pimp] where lvl='2'");
                                 while ($mod = mysql_fetch_array($moderator)) { 
                                 echo "<li><a href=mobster.php?pid=$mod[pimp]&tru=$tru>$mod[pimp]</a></li>";
                                 }
                 			       ?>
<br />
                             &nbsp;&nbsp;<span class="stylestaff">Helpers</span><br>
                              <? 
                                 $helper = mysql_query("select pimp,status from $tab[pimp] where lvl='5'");
                                 while ($help = mysql_fetch_array($helper)) { 
                                 echo "<li><a href=mobster.php?pid=$help[pimp]&tru=$tru>$help[pimp]</a></li>";
                                 }
                 			       ?>
						</ul>
					
					</div>
				</div>
		<!--  \ MENU BOX / -->

			</div>
			<!--  \ LEFT 2 CONTAINER / -->
			
			<!--  / CENTER CONTAINER \ -->
			<div id="centerCntr">
				

				<!--  / TEXT BOX \ -->
				<div class="textBox">
					<div class="top">
						<div class="bottom">
                          <div align="center">
  <? $set = mysql_fetch_array(mysql_query("SELECT speed,reserves,credits,crewmax,starts,ends,gamename FROM $tab[game] WHERE round='$tru';"));?> 
  <?  
$gameee = mysql_fetch_array(mysql_query("SELECT round,gamename,free1,free2,free3,free4,free5,free6,free7,free8,free9,free10,sup_11,sup_12,sup_13,sup_14,sup_15,sup_16,sup_17,sup_18,sup_19,sup_110,sup_21,sup_22,sup_23,sup_24,sup_25,sup_26,sup_27,sup_28,sup_29,sup_210,sup_31,sup_32,sup_33,sup_34,sup_35,sup_36,sup_37,sup_38,sup_39,sup_310,du1,du2,du3,du4,du5,du6,du7,du8,du9,du10,op1,op2,op3,op4,op5,op6,op7,op8,op9,op10,c1,c2,c3,c4,c5,c6,c7,c8,c9,c10,fdu1,fdu2,fdu3,fdu4,fdu5,fdu6,fdu7,fdu8,fdu9,fdu10,starts,ends,cash11,cash22,cash33,starts,ends FROM $tab[game] WHERE round='$tru';")); 
$bling = mysql_fetch_row(mysql_query("SELECT sum(amount), sum(fee) FROM $tab[paypal] WHERE datebought>='$gameee[starts]' AND datebought<='$gameee[ends]';")); 
$balance=($bling[0]-$bling[1]); 
$balan=($bling[0]-$bling[1]); 
if($balance<0){$balance=0;} 
if($balance>0){$balance=$balance;} 
$balance11=($balance*.40); 
$balance1=round($balance11+$gameee[cash22]); 
$balance22=($balance*.51); 
$balance2=round($balance22); 
$balance33=($game[cash11]); 
$balance3=round($balance33); 
?> 
                            <strong>Current Round #</strong>  <?=$tru?> - <?=$set[6]?> 
                            | <strong>Family Max:</strong>   <?=$set[3]?> 
                            | <strong>Turns every 10 mins:</strong>   <?=$set[0]?><br />
                            <strong>Ends in:</strong> <?=countup($set[ends])?> <br />
                            <strong>Progressive Jackpot (offered in main round only-not mini-round, so play in Main for the Cash) <br />
                                       Now At:</strong> $<?=commas($balance1);?> 
                            <strong>#1 Rank Credits:</strong> <?=commas($gameee[sup_21]);?> 
                            </div>
					   </div>
					</div>
				</div>
				<!--  \ TEXT BOX / -->
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
<table width="550" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><div align="center">
	<? } 
function GAMEFOOTER (){ 
global $site, $tru, $tab, $id; 
$pimp = mysql_fetch_array(mysql_query("SELECT pimp,attin,attout,trn FROM $tab[pimp] WHERE id='$id';")); 
if(($pimp[1] >= 5000) || ($pimp[2] >= 5000)){ 
mysql_query("UPDATE $tab[pimp] SET attout='0' WHERE id='$id' AND attout>'5000';"); 
mysql_query("UPDATE $tab[pimp] SET attin='0' WHERE id='$id' AND attin>'5000';"); 
} 
if($pimp[3] >= 10000000){ 
mysql_query("UPDATE $tab[pimp] SET trn='0' WHERE id='$id' AND attout>'10000000';"); 
} 
//SHOWING PIMPS ONLINE
$idletime=$time-760;
//$idle2 = $time-604800;
$reg = fetch("SELECT COUNT(id) FROM $tab[user];");
//$real = fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idle2;");
$sups = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='supporter';"));
//$bans = mysql_fetch_row(mysql_query("SELECT count(id) FROM $tab[user] WHERE status='banned';"));

$online=fetch("SELECT COUNT(id) FROM $tab[user] WHERE online>$idletime;");

$pmp = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,city,networth,money,trn,res,condom,medicine,crack,weed,glock,shotgun,uzi,ak47,whore,thug,whappy,thappy,payout,crew,msg,atk,ivt,lowrider,attin,attout,lastattackby,lastattack,cmsg,bank,tbank,beer FROM $tab[pimp] WHERE id='$id';")); 
$boot = mysql_fetch_array(mysql_query("SELECT online,status,pimp,crew,newalert,alert,rank,nrank,networth,msg,atk,ivt FROM $tab[pimp] WHERE id='$id';")); 
$crw = mysql_fetch_array(mysql_query("SELECT name,founder,icon,id FROM $tab[crew] WHERE id='$boot[3]';")); 
$game = mysql_fetch_array(mysql_query("SELECT speed,reserves,credits,crewmax,starts,ends,gamename FROM $tab[game] WHERE round='$round';")); 
?></div> </td>
  </tr>
</table>

						
					
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
							<li><a href="../credits.php">Purchase more Reserves </a></li>
							<li><a href="../credits.php">Purchase a Subscription </a></li>
					  </ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Message Center </h2>
						<ul>
							<li><a href="mailbox.php?tru=<?=$tru?>">Mailbox  <?if($pmp[22] > 0){?> ( <?=$pmp[22]?> ) <?}else{?> ( 0 )  <?}?> </a></li>
							<li><a href="board.php?tru=<?=$tru?>">Message Board</a></li>
							<li><a href="board.php?brd=recruit&tru=<?=$tru?>">Recruit Board</a></li>
							<li><a href="godadmins.php?tru=<?=$tru?>">Support Board</a></li>
							<li><a href="chat.php?tru=<?=$tru?>">Live Chat</a></li>
					  </ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Locate </h2>
						<ul>
							<li><a href="mobster.php?tru=<?=$tru?>" class="boxlink">Mafioso</a></li>
							<li><a href="family.php?tru=<?=$tru?>" class="boxlink">Family</a></li>
					  </ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->
				<!--  / MENU BOX \ -->
				<div class="menuBox">
					<div class="bottom">

						<h2>Ranks </h2>

						<ul>
						<li><a href="ranks.php?tru=<?=$tru?>">City</a></li>
						<li><a href="cranks.php?tru=<?=$tru?>">Family</a></li>
						<li><a href="granks.php?tru=<?=$tru?>">Global</a></li>
						<li><a href="sranks.php?tru=<?=$tru?>">Supporter's</a></li>
						<li><a href="franks.php?tru=<?=$tru?>">Free Player's</a></li>
						<li><a href="mostduskilled.php?tru=<?=$tru?>">Most Feared</a></li>
						<li><a href="prizes.php?tru=<?=$tru?>">Prizes</a></li>
						<li><a href="winnings.php?tru=<?=$tru?>">Winnings</a></li>
						<li><a href="../winners.php">Past</a></li>
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
							<li><a href="whosonline.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>">Online:</a> <span><?=$online[0]?></span></li>
						</ul>
					
					</div>
				</div>
				<!--  \ MENU BOX / -->

			</div>
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