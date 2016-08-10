<?
include("html.php");

$user = mysql_fetch_array(mysql_query("SELECT * from $tab[user] WHERE id='$id';"));

secureheader();
?>
<link href="style.css" rel="stylesheet" />
<style type="text/css">
<!--
body {
	background-color: #000000;
}
body,td,th {
	color: #FFFFFF;
}
-->
</style><center>
  <b>Send the link below to recruit new players and get free turns!</b>
<br>
<br>
<input type="text" class="text" size="50" value="<?=$site[location]?>?ref=<?=$user["id"]?>">
<br>
<br><b>top 30 refers</b>
       <table width="60%" cellspacing="1">
        <tr>
          <td width="10%">ID(#)</td>
         <td width="46%"><small>manager</small></td><td width="44%" align="right"><small>credits earned</small></td>
        </tr>
       <?
   $get = mysql_query("SELECT * FROM $tab[user] WHERE referrals>0 ORDER BY refCredits DESC limit 30;");
        while ($referz = mysql_fetch_array($get))
              {
                if($row==0){$color="330000";$row++;}elseif($row==1){$color="";$row--;}
              ?>
              <tr bgcolor="<?=$color?>">
                <td><small>
                  <?=$referz["id"]?>
                </small></td>
               <td><small><?=$referz["username"]?></small></td><td align="right"><small>
			   <?=$referz["refcredits"]?></small></td>
              </tr>
            <?}?>
       </table>
<br>
<br><b>How does this work?</b>
<br>
<table align="center" width="89%">
 <tr>
  <td><font color="#FF9900">Also, for just refering somoeone, you will recieve <b>500 free credits</b> to your account! How simple is that? <small> If you abuse this system you will be caught and banned, so dont try it.</small></font></td>
 </tr>
 <tr>
   </tr>
</table>
</center>
<?
sitefooter();
?>