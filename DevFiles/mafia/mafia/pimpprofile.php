<?
include("html.php");

mysql_query("UPDATE $tab[user] SET currently='Viewing member Profile', online='$time' WHERE id='$id'"); 

//used for pimp profile
$pimpprofile = mysql_fetch_array(mysql_query("SELECT username,email,fullname,age,messager,online,ip,host,code,membersince,referredby,aim,yahoo,msn,code,newinfo,membersince,flashlink,status,lvl,pimpmessage,crewname,pimpname,statusexpire,reason,id,referrals FROM $tab[user] WHERE username='$pid';"));
$getgamesplayed = mysql_result(mysql_query("SELECT COUNT(*) FROM $tab[stat] WHERE user='$pimpprofile[username]';"),0);
$getrefferals = $pimpprofile["referrals"];
///admin check
$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username,email,ip,lastip,membersince,flashlink,aim,yahoo,msn,password,newinfo,pimpname,crewname,pimpmessage FROM $tab[user] WHERE id='$id';"));
$cl = mysql_fetch_array(mysql_query("SELECT lvl FROM $tab[user] WHERE id='$id';"));


siteheader();
?><body bgcolor="#000000" text="#990000">
<table width="90%" align="center" cellspacing="0" cellpadding="12" border="0">
 <tr>
  <td align="center" valign="top">
  <b>Not a member? <a href="<?=$site[location]?>signup.php?step=3&amp;refer=<?=$pimpprofile['id']?>">become one</a>, its free!</b>
  <br>
  <br>
  <table bgcolor="" cellspacing="1" cellpadding="0">
    <tr><td valign="middle">
  <table width="100%" height="100%" border="1" cellpadding="3" cellspacing="0" bordercolor="#000000" bgcolor="">
   <tr>
    <td valign="top">      <?if($pimpprofile[flashlink]){ $pimpprofile[flashlink]=($pimpprofile[flashlink]); $pro=strrchr($pimpprofile[flashlink],'.');if($pro == ".swf"){?>
      <embed src="<?=$pimpprofile[flashlink]?>" menu="false" quality="high" width="200" height="200" type="application/x-shockwave-flash" pluginspage"=http://www.macromedia.com/go/getflashplayer"></embed>
      <?}else{?>
      <img src="<?=$pimpprofile[flashlink]?>" width="200" height="200">
      <?}}?></td>
    <td valign="middle">    <table width="100%"  border="0">
	  <tr>
        <td><font color="#990000" size="2"><strong>Member Name: </strong></font></td>
        <td>CENSORED        </td>
      </tr>
      <tr>
        <td><font color="#990000" size="2">&nbsp;</font></td>
        <td><font size="2">&nbsp;</font></td>
      </tr>
      <tr>
        <td><font color="#990000" size="2"><strong>Refferal's:</strong></font></td>
        <td><b>          <font color="" size="2">
          <?= $getrefferals;?>
</font></b></td>
      </tr>
      <tr>
        <td><font color="#990000" size="2">&nbsp;</font></td>
        <td>&nbsp;                  </td>
      </tr>
      <tr>
        <td><font size="2">&nbsp;</font></td>
        <td><font size="2">&nbsp;</font></td>
      </tr>
      <tr>
        <td><font color="#990000" size="2"><strong>Rounds Played: </strong></font></td>
        <td><b><font color="" size="2">
          <?= $getgamesplayed;?>
        </font></b></td>
      </tr>
      <tr>
        <td><font color="#990000" size="2"><b>Member Since:</b></font></td>
        <td><b><font color="" size="2">
          <?=date("M dS, Y", $pimpprofile[membersince])?>
        </font></b></td>
      </tr>
      <tr>
        <td><font size="2">&nbsp;</font></td>
        <td><font size="2">&nbsp;</font></td>
      </tr>
    </table>
    <br>
    <br>
    <b><font color="">    </font></b>    <nobr><br>
    </nobr>
    </td>
   </tr>
  </table>
  </td></tr></table>
  <div align="center"><a href="<?=$_SERVER['SERVER_NAME']?>/signup.php?step=3&refer=<?=$pimpprofile['id']?>" target="_blank"><strong>Sign up here with my Referral Link <br>
  http://<?=$_SERVER['SERVER_NAME']?>/signup.php?step=3&amp;refer=
  <?=$pimpprofile['id']?>
  </strong></a><br>
        <b><font color="">
      </font></b>
        <table width="100%" height="100%"  border="0">
          <tr>
            <td><b><font color="#990000" size="2">Member  Message: </font></b></td>
          </tr>
          <tr>
            <td><b><font color="" size="2">
              <?=$pimpprofile[pimpmessage]?>
            </font></b></td>
          </tr>
        </table>
        <br>
  </div></td>
 </tr>
</table>
  <?
sitefooter();
?>