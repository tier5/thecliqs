<?
//Good ol' time
$time=time();
$banklimit = 1000000000000000000;

if($ref){ setcookie("ref",$ref); } //grabs the referral code if available.

$site[location] = 'http://'.$_SERVER['SERVER_NAME'].'/';  //your web URL with trailing /
$site[img] = $_SERVER['SERVER_NAME'].'/images/';  //your web url to images directory with trailing /

$full_path = "/home/CPANE_USER/public_html/"; //Full path to your install example: /home/cpanel_user/public_html/  on cpanel this would be the main directory which is optimal for the game script to run sub directorys takes more modifications to the script to run.
$inc_path = "/home/CPANE_USER/public_html/include/"; //full path to folder include example: /home/cpanel_user_name/public_html/include/  with trailing slash

$paypal_email_address = "someone@yoursite.com"; //email address you will be recieving payments to in paypal



//////UPLOAD SETTINGS
$dir="../images/icons/$tru/"; //you will have to generate folders 1 - however many rounds you have.  chmod each to 777 
$arr_allow_ex=array("gif","jpg","png"); //icon types allowed for family icons
$maxupload[sup] = '10000'; //supporter upload in bytes
$maxupload[nor] = '5000'; //normal upload in bytes

//////DATABASE CONFIG, AND CONNECTION
$dbh=mysql_connect ("localhost", "thecliqs_warmafa", "Loki@2002") or die ('I cannot connect to the database because: ' . mysql_error());
mysql_select_db ("DB_NAME"); 

//////DATABASE TABLES
$tab[user] = 'users';
$tab[html] = 'html';
$tab[news] = 'news';
$tab[game] = 'games';
$tab[site] = 'site_details';
$tab[stat] = 'stats';
$tab[censor] = 'censors';
$tab[banned] = 'bans';
$tab[paypal] = 'paypal';
$tab[logs] = 'logs';
$tab[newsletter] = 'newsletter';
$tab[votelinks] = 'votelinks';
$tab[votes] = 'votes';

//Site Details from Database
$get_site = mysql_fetch_array(mysql_query("SELECT sitename, siteslogan, sitedetails, incentivesignupmsg, paypal, metakeywords, metadescription, siteannouncement, adsense, rules, tos, guide, chatroomname FROM $tab[site];"));
$site[name] = "$get_site[0]";
$site[slogan] = "$get_site[1]"; 
$site[details] = "$get_site[2]";
$site[announcement] = "$get_site[7]";
$site[signupincentivemsg] = "$get_site[3]";
$paypal_email_address = "$get_site[4]";
$sitekeywords = "$get_site[5]";
$sitemetadescription = "$get_site[6]";
$site[adsense] = "$get_site[8]";
$site[rules] = "$get_site[9]";
$site[tos] = "$get_site[10]";
$site[guide] = "$get_site[11]";
$site[chatroomname] = "$get_site[12]";


/////GAME DATABASE TABLES
$tab[board] = "board";
$tab[clist] = "contacts";
$tab[city] = "city";
$tab[crew] = "crew";
$tab[invite] = "invites";
$tab[mail] = "mailbox";
$tab[pimp] = "pimp";
$tab[revenge] = "revenges";
$tab[cron] = "cronjobs";
$tab[contracts] = "contracts";

if(($tru) && (!mysql_fetch_row(mysql_query("SELECT round FROM $tab[game] WHERE round='$tru' AND starts<$time AND ends>$time;"))))
  { header("Location: $site[location]play.php"); }

if($tru){
   $tab[board] = "r".$tru."_board";
   $tab[clist] = "r".$tru."_contacts";
   $tab[city] = "r".$tru."_city";
   $tab[crew] = "r".$tru."_crew";
   $tab[invite] = "r".$tru."_invites";
   $tab[mail] = "r".$tru."_mailbox";
   $tab[pimp] = "r".$tru."_$tab[pimp]";
   $tab[revenge] = "r".$tru."_revenges";
   $tab[cron] = "r".$tru."_cronjobs";
   $tab[contracts] = "r".$tru."_contracts";
}

//////CENSORS
$getcensors = mysql_query("SELECT censor FROM $tab[censor];");
$censorwords = array();
while($censor=mysql_fetch_array($getcensors)) {
  array_push($censorwords, $censor[0]);
 
}


//GRAB THEY MASTER AND GAME ID
if($tru){ $id = mysql_fetch_array(mysql_query("SELECT id FROM $tab[pimp] WHERE code='$trupimpn';")); }
    else{ $id = mysql_fetch_array(mysql_query("SELECT id FROM $tab[user] WHERE code='$trupimp';")); }
$id=$id[0];
?>