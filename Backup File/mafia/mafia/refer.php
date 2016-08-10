<?
include("html.php");

$user = mysql_fetch_array(mysql_query("SELECT fullname,credits,status,username,email,ip,lastip,membersince,id,referredby,statusexpire,password,referrals,refcredits,id FROM $tab[user] WHERE id='$id';"));
if($user[1] < 0){
	mysql_query("UPDATE $tab[user] SET credits='0' WHERE id='$id'");
	}
$menu='pimp/';
secureheader();
siteheader();
?>    
<div align="center">        
<table width="500"  border="0" align="center">
          <tr>
            <td ><div align="center"><strong>Referral Program</strong></div></td>
        </tr>
          <tr >
            <td><div align="center">How does the Referral Program work?<br />
              <br />
              
              Are you a Mafia Don? Why don't you refer some friends to the game!<br>
              Simply pass the referral link to one of your buddies and you will get a price for it.<br />
              <br />
              </div></td>
        </tr>
          <tr>
            <td ><div align="center"><font color="red"><strong>You will get:1,000</strong></font> credits.</div></td>
        </tr>
              <tr>
                <td  style="padding-top: 20px;"><div align="center"><strong>Your Referral Link</strong></div></td>
              </tr>
              <tr>
                <td><div align="center">
                  <a href="<?=$site[location]?>signup.php?step=3&amp;refer=<?=$user[14]?>"><?=$site[location]?>signup.php?step=3&amp;refer=<?=$user[14]?></a>
				  <br />
                  Referral Account Balance <font color="red"><strong>$0</strong></font><br>
                  <br>
                  Referral Account Credits <font color="red"><strong>
                  <?=$user[13]?> 
                  turns</strong></font><br>
                  <br>
                  Your have referred <font color="red"><strong>
                  <?=$user[12]?> 
                  Mobsters</strong></font>! 
                  </div></td>
              </tr>
              </table>
</div>
<?
sitefooter();
?>