<?
include("html.php");


GAMEHEADER("Mailbox");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?=$site[chatroomname]?> Live Chat</title>
<style type="text/css">
/*<![CDATA[*/
object {
width: 100%;
max-width: 550px;
height: 775px;
overflow: none;
}
</style>
</head>

<body>
<object type="text/html" data="http://www.dedicatedgamerschat.com/chatap/rooms/<?=$site[chatroomname]?>/">

</object>
</body>
</html>
<?
GAMEFOOTER();
?>