<?
include("html.php");

if(($username) && ($password)){

$user = mysql_fetch_array(mysql_query("SELECT id,status,code,email,ip FROM $tab[user] WHERE username='$username' AND password='$password';"));

    if($user[1] == banned){ header("Location: login.php?reason=banned&code=$user[2]"); }
elseif($user[1] == unverified){ header("Location: confirm.php?email=$user[3]"); }
elseif($user)
      {
       $host=gethostbyaddr("$REMOTE_ADDR");
       mysql_query("UPDATE $tab[user] SET online='$time', ip='$REMOTE_ADDR', lastip='$user[4]', host='$host' WHERE id='$user[0]';");

       setcookie("trupimp",$user[2]);

       header("Location: newsandupdates.php");
      }
  else{ header("Location: login.php?reason=invalid"); }
}


if($reason==banned){
$banned = mysql_fetch_array(mysql_query("SELECT reason FROM $tab[user] WHERE code='$code';"));
}


?>
<html>

<TITLE><?=$site[name]?></TITLE>


<link href="css/splash.css" rel="stylesheet" type="text/css">

<body  bgcolor="#000000" text="#FFFFFF" link="#FFFFFF" vlink="#FFFFFF" alink="#0099FF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style="margin: 0; padding: 0;">
<table width="821" border="0" align="center" id="container">
  <tr> 
    <td width="821" align="center" colspan="3"><img src="images/splash.jpg" width="821" height="516"></td>

  </tr>
  <tr>
    <td align="left"> 
      <div id="welcome" style="background: #000 url('images/descbg.jpg') bottom right no-repeat; position:relative; width:450; height:450; z-index:1; left: 5; top: 0; overflow: auto; font: 95% arial, verdana; unicode-bidi:bidi-override; direction:rtl; display:block; overflow:auto; padding:10px; padding-bottom: 0; border:1px solid #ba8; margin:0 auto;"> 
	  <div dir="ltr">
	  <p style="font-size: 120%; color: #ab976e; font-weight: bold;"><?=$site[name]?> Online Game</p>

        <p> <?=$site[details]?></p>
		</div>
      </div> 
	</td>
	<td align="center">
	   <div id="login" style="z-index:3;">
	     <form action="login.php" method="POST">
	   	  <table width="160" cellpadding="0" cellspacing="0">
		  	  <tr>
			  	 <td>
					
				   <span class="login">
				   <br>
				   	 <span style="color: #ab976e; font: 115% arial, verdana; text-transform: uppercase; font-weight: bold; text-align: center;">Login</span>
				   </span>				 	
				 </td>
			  </tr>
  <table width="100%" height="100%">
    <tr>
     <td align="center" valign="top">
     <form method="post" action="login.php">
     <?if($reason==banned){?><b><font color="3399FF"><center>You have been banned from our site for a reason.... Why?</center><br><br></font><?=$banned[0]?>  </b><br><br><?}?>      
     <?if($reason==invalid){?><b><font color="89B93A">Invalid login attempt from </font><?=$REMOTE_ADDR?></b><br><br><?}?> 
     <?if($reason==notlogged){?><b><font color="89B93A">You must login before you can access this page</font></b><br><br><?}?> 
     <?if($reason==idle){?><b><font color="89B93A">You have been logged out for idling</font></b><br><br><?}?> 
     <?if($reason==logout){?><b><font color="89B93A">Thanks for playing truepimpwars.com, come back soon!</font></b><br><br><?}?>
     <table>
      <tr>
       <td align="right"><span style="color: #ab976e; font: 65% arial, verdana; text-transform: uppercase; font-weight: bold;">username:</span></td><td><strong><font color="#FFFFFF">
         <input type="text" id=entry type="input" class="input" name="username" maxlength="18" value="username" onfocus="if(this.value == 'username') this.value = '';">
       </font></strong></td>
      </tr>
      <tr>
       <td align="right"><span style="color: #ab976e; font: 65% arial, verdana; text-transform: uppercase; font-weight: bold;">password:</span></td><td><strong><font color="#FFFFFF">
         <input id=entry type="password" class="input" name="password" maxlength="18" value="password" onFocus="if(this.value == 'password') this.value = '';">
       </font></strong></td>
      </tr>
      <tr>
       <td align="right" colspan="2"><strong><font color="#FFFFFF">
         <input type="submit" name="login" value="login">
       </font></strong></td>
      </tr>
     </table>
     <br>
     <strong>

<img src="images/signup.jpg" alt="signup, it's free." title="signup, it's free.">

<br>
    <span style="color: #ab976e; font: 65% arial, verdana;">
		<a href="lostpass.php">Sign up</a>, it's <span style="text-transform: uppercase; color: #8b3400; font-weight: bold; font-size: 120%;">free!</span>
		<br />
		Lost your password? <a href="lostpass.php">click here</a>.
	</span>
     </strong>
     </form>
     </td>
    </tr>
   </table>		  </table>

		</form>
	   </div>	   
	</td>      
  </tr>
  <tr>
  	<td colspan="3" style="padding-top: 30px;">

  		<p align="center" style="font-size: 10px;">
		    <center>Copyright &copy; <?=$site[name]?>, all rights reserved<br> 
<!-- you can not remove this copyright line without prior athorization from Game-Script.net and a removal fee is paid. -->
<a href="http://www.game-script.net">Script provided by: Game-Script.net</a></center>
		</p>

</body>
</html>