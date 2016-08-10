<?
include("html.php");

$menu='pimp/';
secureheader();
siteheader();
//First thing To Do is to check if the user have already logged in, if they did, redirect to main page instead of
// Having to log-in Again


?>
<html>
<head>
<title>Referral Program</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
   <table width="450" height="30%" align="center">
    <tr>
     <td height="12" align="center"> <b>
      <h1><font color="#000000"></font>Referral Guide</font></h1>
<font color="#000000">You each have a referral id now that you may locate in your myaccount link. Just copy and paste it into an email and let all your friends know that it is you sending the email and if your friends</font> <font color="red"><b>sign up</b></font> <font color="#000000">for Trupimps.com, you will receive </font><font color="red">100 points for each referral</font>.<font color="#000000"> Or you may use the form below to send an email to them. I leave it up to you how you send it. There is not a time limit on referrals anymore. So if they sign up six months down the road, you will still get credit for them. If we do find that you are spamming, we will have to take you out of the referral program.</font></b>
<br><br>

<b><font color="#000000">Rules of the contest:</font></b><br>
<font color="#000000">We need your help to make this an ever - growing game! Refer everyone you know. The more you get to play the game the more points you will acquire. <br><br>
These turns will be credited to your master account reserves for each genuine account created from your referral links! </font><font color="red">This turn credit will be given after the accounts have been created and the month is over.</font><font color="#000000"> This rule is in place to help eliminate abuse of the referral program, as well as allow the system ample time to filter bogus/abusive accounts.
<br><br>
You only have to refer your friend once in order to qualify for the bonus credits, but don't forget they must sign up to count as a legitimate referral.  Remind them this is a way for you to receive credits.</font>
<br><br><font color="red">Multi-Accounts will not be allowed and will be banned. </font>
<br><br>
<strong><font color="#000000">The referrals you submit must NOT already be:</font><ul><li>a current member</li><li>sent to the same ip as your own</li><li>A fake person</li><li>Phony submissions - ignored</li></ul></strong>
<br><br>
<font color="#000000">A little advice from our end: Using hotmail and yahoo emails sometimes won't get the effect you are looking for. Alot of users have these accounts for junk email, so when sent it may go straight to their junk mail, or sometimes they will overlook it and just delete it. If you know their actual ISP email, I suggest you use that one to submit their name, they are more likely to read your email. Don't forget to double check the email you are sending to: such as aaaa@yahoo will go nowhere. Make sure the url is correct before you send it. We can't give you credit if it never makes it to the user.</font>

<b><font color="red">Plus, don't forget that once we reach 2500 players the one that has referred the most will win 20,000 credits.</font></b>
</td>
</tr>
</table>

<form action="tellthem.php" method="post" name="adminlogin" id="adminlogin">
  <table width="50%" border="5" align="center" cellpadding="5" cellspacing="0" bordercolor="#000022" style="border-style:inset;">
    <tr bgcolor="red"> 
      <td colspan="2"><div align="center"><font color="#FFFFFF" face="Georgia, Times New Roman, Times, serif"><strong>Tell A 
          Friend: </strong></font></div></td>
    </tr>
    <tr> 
      <td width="47%"><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your Member 
          Name:<br> (ex: trubluepimp)</strong></font></div></td>
      <td width="53%"><input name="yourname" type="text" id="yourname" style="font-weight:bold;width:150;"></td>
    </tr>
    <tr> 
      <td><div align="right"><font face="Georgia, Times New Roman, Times, serif"><strong>Your Referral's 
          Name: </strong></font></div></td>
      <td><input name="frndname" type="password" id="frndname" style="font-weight:bold;width:150;"></td>
    </tr>
    <tr> 
      <td colspan="2"><div align="center"><font face="Georgia, Times New Roman, Times, serif"><strong>
          <input name="Submit" type="submit" id="Submit" style="border:1;border-style:outset;font-weight: bold;color: white;background:#000000;border-color:#99CCFF;" value="Send Email!">
          </strong></font> </div></td>
    </tr>
  </table>
  </form>
</body>
</html>

<?
sitefooter();
?>