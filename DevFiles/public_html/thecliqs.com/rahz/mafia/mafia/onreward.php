<?
include("html.php");

$user = mysql_fetch_array(mysql_query("SELECT username,cash FROM $tab[user] WHERE id='$id';"));



secureheader();
siteheader();

if($user["cash"] >= 250){?>
<center><b>You must spend your GCash below a $250.00 limit before you can complete more offers.  You currently have $<?=$user["cash"]?> Spend it <a href="gcash.php">Here</a><? }else{?>

<center>You currently have $<?=$user["cash"]?> GCash available.  Spend it <a href="gcash.php">Here</a>.<br />
<br />
<iframe src="http://super.kitnmedia.com/super/offers?h=fkulzfeoji.24746862895&uid=<?=$id?>" frameborder="0" width="540" height="2750" scrolling="no"></iframe>
</center>
<? }?><?
sitefooter();
?>