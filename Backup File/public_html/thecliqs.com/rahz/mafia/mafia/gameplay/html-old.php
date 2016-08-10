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

include("funcs.php");


function ADMINHEADER ($title){
global $tab, $tru, $id, $sitename, $userstatus, $userads;
$getgames = mysql_query("SELECT round FROM $tab[game] WHERE starts<$time AND ends>$time ORDER BY round ASC;");
$pimp = mysql_fetch_array(mysql_query("SELECT status FROM $tab[pimp] WHERE id='$id';"));
if($pimp[0] != admin){ echo"<center><b>ACCESS DENIED!</b></center>"; die(); }
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
<html>
<head>
	<title><?=$site[name]?> - <?=$site[slogan]?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta name="keywords" content="<?=$sitekeywords?>" />	
	<meta name="description" content="<?=$sitemetadescription?>" />
	<meta name="robots" content="index, follow" />
	
<link rel="stylesheet" type="text/css" href="styletwo.css"><style type="text/css">

#dropmenudiv{
position:absolute;
border:1px solid black;
border-bottom-width: 0;
font:normal 12px Verdana;
line-height:18px;
z-index:100;
}

#dropmenudiv a{
width: 100%;
display: block;
text-indent: 3px;
border-bottom: 1px solid black;
padding: 1px 0;
text-decoration: none;
font-weight: bold;
}

#dropmenudiv a:hover{ /*hover background color*/
background-color: #222222;
}

</style>


<script language="JavaScript1.2" src="tmb.js"></script>
   <script langauge="JavaScript">
   <!-- Hide
   function MM_jumpMenu(targ,selObj,restore){ //v3.0
    eval(targ+".location='"+selObj.options[selObj.selectedIndex].value+"'");
    if (restore) selObj.selectedIndex=0;
   }

   function MM_displayStatusMsg(msgStr) { //v1.0
    status=msgStr;
    document.MM_returnValue = true;
   }

   function MM_openBrWindow(theURL,winName,features) { //v2.0
    window.open(theURL,winName,features);
    }

   <?if($protect>0){?>
   function display(){
    rtime=etime-ctime;
    if (rtime>60)
    m=parseInt(rtime/60);
    else{
    m=0;
    }
    s=parseInt(rtime-m*60);
    if(s<10)
    s=""+s
    window.status="You have "+s+" seconds of protection left!"
    window.setTimeout("checktime()",1000)
   }

   function settimes(){
    var time= new Date();
    secs= time.getSeconds();
    etime=secs;
    etime+=<?=$protect?>;
    checktime();
   }

   function checktime(){
    var time= new Date();
    secs= time.getSeconds();
    ctime=secs
    if(ctime>=etime){
    expired();
   }
   else
   display();
   }

   function expired(){ window.status="You have ran out of protection!!!"; }
   <?}?>

   <?if($boot[4] == 1){?>alert ("<?=str_replace('"', '', $boot[5])?>");<? mysql_query("UPDATE $tab[pimp] SET newalert='0' WHERE id='$id'");}?>
    // Done hiding -->
   </script>
<style type="text/css">
<!--
body {
	background-color: #000000;
}
.style4 {color: #FFFFFF}
.style5 {
	color: #FFFFFF;
	font-weight: bold;
}
.style6 {
	font-size: 18px;
	font-weight: bold;
}
.style7 {font-weight: bold}
.style8 {color: #FF0000}
-->
</style></head>
<body>
<table width="961" height="391" border="1" align="center" cellpadding="0" cellspacing="0" bordercolor="#202020" id="Table_01">
	<tr>
		<td height="170" valign="top">		  <table width="957" border="0">
            <tr>
              <td width="659" height="164" valign="top"><div align="center">592 x 162 Banner goes here </div></td>
              <td width="288" valign="top"><p align="center"><span class="style6">Messages</span></p>
                <p><a href="mailbox.php?tru=<?=$tru?>">Open Mailbox</a><br>
                    <?if($pmp[22] == 1){?>
                    <font color="#66CCFF">you have 1 new message</font>
                    <?}elseif($pmp[22] > 1){?>
                    <font color="#66CCFF">you have
                    <?=$pmp[22]?>
  new messages</font>
                    <?}else{?>
  you have no new messages
  <?}?>
  <span class="style4"><br>
  <strong><a href="attacks.php?tru=<?=$tru?>"><br>
  Open Attacks</a></strong></span> &nbsp;
  <?if($pmp[23] == 1){?>
  <br>
  <span class="style4">you have been attacked!
  <?}elseif($pmp[23] > 1){?>
  <br>
  you have been attacked</span><font color="#white">
  <?=$pmp[23]?>
  times
  <?}else{?>
  </font><span class="style4"><br>
  you have not beent attacked !</span>
  <?}?>
  <br>
  <span class="style5"><br>
  <a href="invites.php?tru=<?=$tru?>">Open Invites</a></span>&nbsp;
  <?if($pmp[24] == 1){?>
  <br>
  <font color="#0066CC">you have a invitation!</font>
  <?}elseif($pmp[24] > 1){?>
  <br>
  <font color="#0066CC">you have
  <?=$pmp[24]?>
  new invitations!</font>
  <?}elseif($pmp[30] > 0){?>
  <br>
  <a href="cboard.php?cid=<?=$pmp[21]?>&tru=<?=$tru?>">
  <?=$pmp[30]?>
  new Family message</a>
  <?}?>
                </p></td>
            </tr>
      </table>
	  </td>
	</tr>
	<tr align="center" background="businesses/mbk.gif.jpg">
<tr align="center" background="businesses/mbk.gif.jpg">
	  <td height="26" valign="top"><table width="860" border="0" align="center">
        <tr>
          <? $set = mysql_fetch_array(mysql_query("SELECT ends FROM $tab[game] WHERE round='$tru';"));?>
          <td width="373" height="21"><div align="left"> Round Ends In
                  <?=countup($set[ends])?>
          </div></td>
          <td width="9">&nbsp;</td>
          <td width="464"><b>
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
                            <strong>Progressive Jackpot <br>
<br>
</strong> $<?=commas($balance1);?> 
                            <strong>#1 Rank Credits:</strong> <?=commas($gameee[sup_21]);?> 
          </b></td>
        </tr>
      </table></td>
  </tr></tr>
<tr align="center" background="businesses/mbk.gif">
<td height="30" valign="top"><table align="center" border="0" cellpadding="0" cellspacing="0"><tbody><tr height="30"><td width="16"></td>
		<td colspan="2" background="businesses/mbk.gif">
<table align="center" cellpadding="0" cellspacing="0">
<tr align="center" valign="top">
<td style="padding-left: 12px; padding-right: 12px;"><a href="attack.php?tru=<?=$tru?>"><img src="navtop_r1_c1.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="scout.php?tru=<?=$tru?>"><img src="navtop_r1_c2.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="produce.php?tru=<?=$tru?>"><img src="navtop_r1_c3.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="collect.php?tru=<?=$tru?>"><img src="navtop_r1_c4.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="travel.php?tru=<?=$tru?>"><img src="navtop_r1_c7.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="purchase.php?tru=<?=$tru?>"><img src="navtop_r1_c6.jpg" ></a></td>
<td style="padding-left: 12px; padding-right: 12px;"><a href="bank.php?tru=<?=$tru?>"><img src="navtop_r1_c9.jpg" ></a></a></td>
<td width=16>&nbsp;</td></tr></table>
<br>
</td></tr></table>
  <table border=0 cellpadding=0 cellspacing=0 bgcolor="#000000" width="100%">
<tr align="right" valign="top">

<td>
<table border=0 cellpadding=0 cellspacing=0 bgcolor="#000000" width="100%" >
<Br> 
		<td height="26" valign="top"><table border=0 align="center" cellpadding=0 cellspacing=0 class="ts">
          <tr>
            <td width="40" class="g s p bgg">VIEW</td>
            <td width="70"><a href="index.php?tru=<?=$tru?>" class="boxlink">MainMenu</a></td>
            <td width="90"><a href="mailbox.php?tru=<?=$tru?>" class="boxlink"> Mailbox
                  <?if($pmp[22] > 0){?>
                  <span class="style8">(
                  <?=$pmp[22]?>
      )</span>
                  <?}else{?>
      (0)
      <?}?>
            </a> <font color="#66CCFF"> </font> </td>
            <td width="75"><a href="family.php?cid=<?=$crw[id]?>&tru=<?=$tru?>" class="boxlink">My Family</a></td>
            <td width="80"><a href="mobster.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>" class="boxlink">My&nbsp;Profile</a></td>
            <td width="54"><a href="contacts.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>" class="boxlink">Contacts</a></td>
            <td width="82"><a href="whosonline.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>" class="boxlink">Who's&nbsp;Online</a></td>
            <td width="50"><a href="awards.php?pid=<?=$pmp[0]?>&tru=<?=$tru?>"class="boxlink">Awards</a></td>
            <td width="50"><a href="chat.php?tru=<?=$tru?>"class="boxlink">Live Chat</a></td>
       
          </tr>
        </table></td>
	</tr>
	<tr>
	  <td width="895" height="80" align="center" valign="top" bgcolor="#000000">
	  <script language="JavaScript" type="text/JavaScript">
		function pw() 
		{
				var ww = window.innerWidth != null? window.innerWidth: document.body != null? document.body.clientWidth:null;
				if(ww>950) ww=950;
				return ww;
		}
		document.write('<img src="gfx/-.gif" border=0 width=' + (pw()*.9) + ' height=1>'); // stretches table to 90% of window width
		</script>
        <table align="center" border="0" cellpadding="12" cellspacing="0" width="100%">
          <tbody>
            <tr>
              <td height="60" align="center" valign="top"><b>
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
?>
              </b><br>                
              </td>
            </tr>
          </tbody>
        </table>
        <TABLE 
            style="PADDING-RIGHT: 4px; PADDING-LEFT: 4px; PADDING-BOTTOM: 4px; PADDING-TOP: 4px" 
            cellSpacing=0 cellPadding=0 align=center border=0>
          <TBODY>
            <TR>
              <TD><TABLE class=ts cellSpacing=0 cellPadding=0 border=0>
                  <TBODY>
                    <TR>
                      <TD class="g s p bgg">FIND</TD>
                      <TD><A class=boxlink 
                        href="mobster.php?tru=<?=$tru?>">Mafioso</A></TD>
                      <TD><A class=boxlink 
                        href="family.php?tru=<?=$tru?>">Family</A></TD>
                      <TD><A class=boxlink 
                        href="whosonline.php?tru=<?=$tru?>">Who's&nbsp;Online</A></TD>

                    </TR>
                  </TBODY>
              </TABLE></TD>
              <TD width=15>&nbsp;</TD>
              <TD><table border=0 cellpadding=0 cellspacing=0 class="ts">
                  <tr>
                    <td class="g s p bgg">RANKS</td>
                    <td><a href="ranks.php?tru=<?=$tru?>" class="boxlink">City</a></td>
                    <td><a href="cranks.php?tru=<?=$tru?>" class="boxlink">Family</a></td>
                    <td><a href="granks.php?tru=<?=$tru?>" class="boxlink">Global</a></td>
                    <td><A class=boxlink 
                        href="franks.php?tru=<?=$tru?>">Free</A></td>
                    <td><a href="sranks.php?tru=<?=$tru?>" class="boxlink">Supporters </a></td>
                    <td><a href="../winners.php" class="boxlink" target="_blank">Past</a></td>
                    <td><a href="prizes.php?tru=<?=$tru?>" class="boxlink">Prizes</a> </td>
                  </tr>
              </table></TD>
            </TR>
          </TBODY>
        </TABLE>
      </td>
    </tr>
	<tr align="center" valign="middle" bgcolor="#666666">

<td height="37" colspan="2" background="businesses/mbk.gif">

<table align="center" cellpadding="0" cellspacing="0">

<tr align="center" valign="top">

<td style="padding-left: 15px; padding-right: 16px;"><a href="board.php?tru=<?=$tru?>"><img src="c0.jpg"></a></td>


<td style="padding-left: 15px; padding-right: 15px;"><a href="godadmins.php?tru=<?=$tru?>"><img src="c3.jpg"></a></td>

<td style="padding-left: 15px; padding-right: 16px;"><a href="../credits.php"><img src="c4.jpg" alt="Buy Turns"></a></td>

<td style="padding-left: 15px; padding-right: 15px;"><a href="logout.php"><img src="c5.jpg"></a></td>
  </tr>
</table>
<p align="center"><p>Copyright &copy; <?php echo date("Y") ?> <?=$site[name]?>.<br />
<!--The below provided by must remain otherwise you will break the terms of service for this license-->
		Provided by: Game-Script.net <a href="http://www.game-script.net">URL</a></p>
</body>
</html>
<? } ?>