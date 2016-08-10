<?
include("html.php");

/*
Secret: 460bf4c2cf7712c07a55e210595caf52

Postbacks
Every time a user has completed a payment or offer we will send your server a postback. On this
postback we send the following query arguments:
id: a unique identifier for this transaction
new: points user earned by filling out offer “oid”
total: total number of points accumulated by this user on your application
uid: your site's user uid (facebook, myspace, custom, etc)
oid: SuperRewards offer identifier
sig: security hash used to verify the authenticity of the postback
*/
$SECRET = "460bf4c2cf7712c07a55e210595caf52";

$sig = md5($_REQUEST['id'] . ':' . $_REQUEST['new'] . ':' . $_REQUEST['uid'] . ':' .$SECRET);

if(!sig){ die("you do not have access to this page"); }

$uid = $_REQUEST['uid'];
$new = $_REQUEST['new'];

if($sig){
       mysql_query("UPDATE $tab[user] SET cash=cash+$new WHERE id='$uid'");
       echo "1";
}


?>
