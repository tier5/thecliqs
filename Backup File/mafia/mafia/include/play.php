<?

include("html.php");
mysql_query("UPDATE $tab[user] SET currently='Viewing Play Page', online='$time' WHERE id='$id'"); 
if($action == login){
$getcode = mysql_fetch_array(mysql_query("SELECT code FROM $tab[user] WHERE id='$id';"));
$chkban = mysql_fetch_array(mysql_query("SELECT status,code FROM "."r".$goto."_".$tab[pimp]." WHERE code='$getcode[0]';"));
  if($chkban[0] == banned){ 
    mysql_query("UPDATE "."r".$goto."_".$tab[pimp]." SET status='banned' WHERE code='$chkban[1]'");
    }
  if($chkban[0] != banned){
    mysql_query("UPDATE "."r".$goto."_".$tab[pimp]." SET status='$chkban[0]' WHERE code='$chkban[1]';");
    }
header("location: tru/dologin.php?code=$trupimp&tru=$goto");
}

if(($pimp) && ($city)){
$user = mysql_fetch_array(mysql_query("SELECT status,code,username,host,email,lvl,flashlink,ingamelayout,battle,subscribe1a,subscribe2b,sub1aexpires,sub2bexpires,maxxaddonall,maxxaddonexpires,subscribe FROM $tab[user] WHERE id='$id';"));
$game = mysql_fetch_array(mysql_query("SELECT type,reserves,resets,credits,name FROM $tab[game] WHERE round='$round';"));
if($user[0] == admin){ $check=admin; }else{ $check=supporter; }
if(($user[maxxaddonall] == 'yes')  && ($game[3]!=1000000)){$gameaddon=$game[1]+$game[3]; }else{ $gameaddon=$game[1]; }
if(($user[maxxaddonall] == 'yes')  && ($game[3]!=1000000)){$gameaddon1=$game[3]; }else{ $gameaddon1='0'; }
    if (($game[0] == supporters) && ($user[0] != $check))
       { $msg="You must become a supporter to join this game"; }
elseif (($game[0] == admin) && ($user[0] != $check))
       { $msg="You must be a admin to join this game"; }
elseif (($game[0] == battle) && ($user[battle] < 1))
       { $msg="You must purchase battle a battle for this round"; }
elseif ((!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $pimp)) || (strstr($pimp,".")))
       { $msg="Pimp name can only have a-Z, 0-9, -_ charactors."; }
elseif (fetch("SELECT pimp FROM "."r".$round."_".$tab[pimp]." WHERE pimp='$pimp';"))
       { $msg="Sorry, that name is taken."; }
elseif (!fetch("SELECT id FROM "."r".$round."_".$tab[city]." WHERE id='$city';"))
       { $msg="Please select a city to start in.";}
elseif (fetch("SELECT user FROM $tab[stat] WHERE user='$user[2]' AND round='$round';"))
       { $msg="You have already joined that round.";}
  else {
       mysql_query("INSERT INTO "."r".$round."_".$tab[pimp]." (pimp,bilttrn,res,city,online,code,status,reset,host,email,lvl,profile,layout,subscribe,bank,ak47) VALUES ('$pimp','1500','$gameaddon','$city','$time','$user[1]','$user[0]','0','$user[host]','$user[email]','$user[lvl]','$user[flashlink]','$user[ingamelayout]','$user[subscribe]','0','100000');");
       mysql_query("INSERT INTO $tab[stat] (user,round,credits) VALUES ('$user[2]','$round','$gameaddon1');");
}
}
if (($game[0] == battle) && ($user[battle] >= 1))
       { 
	   mysql_query("UPDATE $tab[user] SET battle=battle-1 WHERE id='$id';");}

 function printround($round,$type){
global $tab, $time, $id, $trupimp;
$fix[0]=$round;
$game = mysql_fetch_array(mysql_query("SELECT speed,reserves,credits,crewmax,starts,ends,maxbuild,name,first,second,third,forthnfifth,sixththrewtenth,tophoekiller,topthugkiller,attin,attout,type,attindown,attoutdown,resets,protection,hitman,produceweapons,crewprize FROM $tab[game] WHERE round='$round';"));
$user = mysql_fetch_array(mysql_query("SELECT status,username FROM $tab[user] WHERE id='$id';"));
$reg = mysql_fetch_array(mysql_query("SELECT count(username) FROM $tab[user] WHERE id>'0';"));
$online = mysql_fetch_array(mysql_query("SELECT COUNT(id) FROM $tab[user] WHERE online>'$idletime';"));
$supporterstat = mysql_fetch_array(mysql_query("SELECT count(status) FROM $tab[user] WHERE status='supporter';"));
?>
<base target="_top" />

<link rel="SHORTCUT ICON" href="/images/favicon.ico" />
<LINK href="styles/cartel_wars2.css" rel="stylesheet" type="text/css">
<meta http-equiv="Content-Language" content="en-us">
<style type="text/css">
<!--
.style1 {
	color: red;
	font-weight: bold;
}
.style2 {color: yellow}
.style3 {color: gray}
-->
</style>

<table width="790" cellspacing="0" cellpadding="0" class="blank">
          <tr>
            <td class="blank"><p align="right">Registered: 
              <?=commas($reg[0])?> 
              || 
            Online: 
            <?=commas($online)?> 
            || 
            Supporters: 
            <?=commas($supporterstat[0])?>&nbsp;&nbsp; 
            </p></td>
          </tr>
</table>
</div>
<table width="790" height="100" border="0" cellpadding="0" cellspacing="0" class="blank"> 

 <tr bordercolor="#CCCCCC">

  <td width="625" align="center" valign="top"><div>

    <table width="550"  border="0" cellspacing="0" cellpadding="0" class="blank">

      <tr>

        <td><img src="images/playpage/playpage_top.gif" width="550" height="19"></td>

      </tr>

      <tr>

        <td align="center" valign="middle" background="images/playpage/playpage_middle.gif"><p><strong><font color="red">
        <?

$pimpsloged = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE rank>0;");
$timer = $time-1800;
$pimpsonline = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE online>$timer;");

$pimpsattacking1 = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE currently='hiring hitman';");

$pimpsattacking2 = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE currently='attacking';");

$pimpsattacking = $pimpsattacking1+$pimpsattacking2;

$pimpsgambling1 = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE currently='playin craps';");

$pimpsgambling2 = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE currently='playin slots';");

$pimpsgambling3 = fetch("SELECT COUNT(id) FROM "."r".$round."_".$tab[pimp]." WHERE currently='viewing casino';");

$pimpsgambling = $pimpsgambling1+$pimpsgambling2+$pimpsgambling3;

?>
        <strong>Cartel Members : <font color="red">
        <?=commas($pimpsloged)?>
        </font> Attacking: <font color="red">
        <?=commas($pimpsattacking)?>
        </font></strong> <strong>Gambling: <font color="red">
        <?=commas($pimpsgambling)?>
        </font></strong> <strong>Online: <font color="red">
        <?=commas($pimpsonline)?>
        </font></strong><br />
        Started: <?=countup($game[4]);?>
<? // Starts: countdown($game[4]); ?>
 ago. <br>
Ends: <?=countdown($game[5]);?> from now.
</font></strong>
            <br />
            <span class="style1"><blink><span class="style2">Bonus: </span><span class="style3">250 on hand turns hourly to those online..</span></blink> </span>
          <table width="450" height="175" class="maintable">
            <tr>
              <td valign="top"><table width="100%" border="0" cellpadding="0" cellspacing="0" class="maintable">
                <?
       $getpimps = mysql_query("SELECT id,pimp,networth,nrank,crew FROM "."r".$round."_".$tab[pimp]." WHERE rank>0 ORDER BY nrank ASC LIMIT 20;");

        while ($pimp = mysql_fetch_array($getpimps))

        {

        $crw = mysql_fetch_array(mysql_query("SELECT icon FROM "."r".$round."_".$tab[crew]." WHERE id='$pimp[4]';"));

             if($rankstart==0){$rankcolor="#333333";$rankstart++;}

         elseif($rankstart==1){$rankcolor="#000000";$rankstart--;}

       ?>
                
                <tr onmouseover="style.backgroundColor='#666666'" onmouseout="style.backgroundColor='<?=$rankcolor?>'" bgcolor="<?=$rankcolor?>">
                  <td width="19" class="maintable"><font size="2">
                    <medium>
                      <?=$pimp[3]?>
                    </medium>
                  </font></td>
                  <td width="291" height="16" class="maintable"><font size="2"><nobr>
                    &nbsp;<?if($crw[0]){?>
                    <img src="<?=$crw[0]?>" width="14" height="14" align="absmiddle" />
                    <?}?>
                    <medium><a href="#">
                      <?=$pimp[1]?>
                    </a></medium>
                  </nobr></font></td>
                  <td width="130" align="right" class="blank"><div align="right"><font size="2" color="">$<?=commas($pimp[2])?>
                  </font></div></td>
                </tr>
                <?}?>
              </table></td>
            </tr>
          </table>

          <div align="center">

            <p><font color="#FFCC00"><strong>

              <?

  if(fetch("SELECT user FROM $tab[stat] WHERE user='$user[1]' AND round='$round';")){

  $player = mysql_fetch_array(mysql_query("SELECT pimp,rank,city,whore,thug,lowrider,msg,attin,attout,bilttrn,res FROM "."r".$round."_".$tab[pimp]." WHERE code='$trupimp';"));

  $playercity = mysql_fetch_array(mysql_query("SELECT name FROM "."r".$round."_".$tab[city]." WHERE id='$player[2]';"));

?>

            </strong></font><strong><strong>--Your Details--</strong></strong></p>

            <table width="550" cellspacing="0" cellpadding="0" class="blank">

              <tr align="center" valign="middle">

                <td width="27%"><strong>Nationaly Ranked</strong></td>

                <td width="10%"><strong>Turns</strong></td>

                <td width="17%"><strong>Reserves</strong></td>

                <td width="22%"><strong>Attacks In/Out </strong></td>

                <td width="24%"><strong>Messages</strong></td>

              </tr>

              <tr align="center" valign="middle">

                <td><font color=""><strong><font color="red">

                  <?=commas($player[1])?>

                  </font> in <font color="red">

                  <?=$playercity[0]?>

                </font></strong></font></td>

                <td><strong><font color=""><font color="red">

                  <?=$player[9]?>

                </font></font></strong></td>

                <td><strong><font color=""><font color="red">

                  <?=commas($player[10])?>

                </font></font></strong></td>

                <td><strong><font color=""><font color="">in:</font><font color="red">

                  <?=$player[7]?>

                  </font>&nbsp;<font color="">out:</font><font color="red">

                  <?=$player[8]?>

                </font></font></strong></td>

                <td><strong><font color="">

                  <?if($player[6] > 0){?>

                  <font color="red">

                  <?=$player[6]?>

                  </font> Messages

                  <?}else{?>

                  </font><span class=""><font color="red">0</font></span></strong>

                    <?}?>

                </td>

              </tr>

            </table>

            <p><strong><a href="play.php?action=login&goto=<?=$round?>"> <span class="">

              <?if($game[4] > $time){?>

    -Hasn't started yet </span>

                    <?}else{?>

    -Click here to play

    <?}?>

              </a>

                  <?

  }else{?>

                  <span class="style27">-Sign Up Bitch </span><br>

                  <?

  $citydb = "r".$round."_".$tab[city];



  if($user[0] == admin){ $check=admin; }else{ $check=supporter; }



  if(($type == supporters) && ($user[0] != $check))

      {?>

              </strong> <br>

              <strong><a href="credits.php"><small>You must be a supporter to join</small><br>

    click here to become one</a></strong> </p>

            <strong>

            <?}

  else{?>

            </strong>

            <table align="center" width="393" class="blank">

              <form method="post" action="play.php">

                <tr>

                  <td align="right"><strong>Cartel Member Name :</strong></td>

                  <td><strong>

                    <input name="pimp" type="text" size="12" maxlength="18">

                </strong><span class="style27">*Max 18 Character</span></td>

                </tr>

                <tr>

                  <td align="right"><strong>start in:</strong></td>

                  <td><strong>

                    <select name="city">

                      <option value="">-select one-</option>

                      <?$get = mysql_query("SELECT id,name FROM $citydb ORDER BY id ASC;");

    while ($city = mysql_fetch_array($get))

    {?>

                      <option value="<?=$city[0]?>">

                      <?=$city[1]?>

                      </option>

                      <?}?>

                    </select>

                  </strong></td>

                </tr>

                <tr>

                  <td></td>

                  <td><strong>

                    <input type="hidden" name="round" value="<?=$round?>">

                    <input type="submit" value="&nbsp;join&nbsp;">

                  </strong></td>

                </tr>

              </form>

            </table>

            <strong>

            <?}

  }?>

          </strong></div></td>

      </tr>

      <tr>

        <td heigth="150"><img src="images/playpage/playpage_bottom.gif" width="550" height="25"></td>

      </tr>

    </table>

    <p><strong><font color="red">

      </font></strong></div></center></td>

  <td width="326" align="center" valign="top">

    <table width="98%"  border="0" cellspacing="0" cellpadding="0" class="blank">

      <tr>

        <td><table width="202"  border="0" cellspacing="0" cellpadding="0" class="blank">

          <tr>

            <td><img src="images/playpage/playpage2_top.gif" width="202" height="19"></td>

          </tr>

          <tr>

            <td align="center" valign="middle" background="images/playpage/playpage2_middle.gif"><center>

              <table width="195"  cellspacing="0" cellpadding="0" class="blank">

              <tr>

                <td><b>Game Details</b></span><font color=red> <br>

                    <?php if($game[17]=='battle'){?>

                </font><span class="style25"><strong>-Requires 1 Battle Credit </strong></span><font color=red>

                <?}?>

                </font><br>

                Type:</font> <font color=red>

                <?=$game[17]?>

                </font><br>

                Name :</font> <font color=red>

                <?=$game[7]?>

                </font><br>

                <font color=>Speed :</font> <font color=red>

                <?=$game[0]?>

.turns/10mins</font><br>

<font color=>Max Build :</font> <font color=red>

<?=$game[6]?>

turns</font><br>

<font color=>Reserves :</font> <font color=red>

<?=$game[1]?>

turns</font><br>

<font color=>Turn Addon : </font>

<?if($game[2]==1000000000){?>

<font color=red>Unlimited addon </font>

<?}else{?>

<font color=red>

<?=$game[2]?>

<?}?>

</font><br>

<font color=>Cartel Max :</font> <font color=red>

<?=$game[3]?>

</font><br>

<font color=>Attacks in/out :</font><font color="red">
<?if($game[15]==1000000){?>
UNLIMITED
<?}else{?>
<?=$game[15]?>
<?}?>
</font>/ <font color="red">
<?if($game[16]==1000000){?>
UNLIMITED
<?}else{?>
<?=$game[16]?>
<?}?>
</font><br>

<font color=>Attacks Down :</font> <font color=red>

</font><span class="style26"></span><font color=red>

<?=$game[18]?> per/hr

</font>
<font color=""><br>

Protection :</font> <font color=red>

<?=$game[protection]?>

Sec. per Turn</font><br><br>

<?php if($game[7] == 'Hustle the Hustler'){?><font color=yellow><b>Bonus </font><br><font color=red>100 Reserves ea/hour <br>Max build 5000</b></font><?}?></td>

              </tr>

            </table></center>              </td>

          </tr>

          <tr>

            <td><img src="images/playpage/playpage2_bottom.gif" width="202" height="25"></td>

          </tr>

        </table></td>

      </tr>

    </table>

    <p>&nbsp;    </p>

  </tr>

</table>

<br>

<br>

<?

}







$menu='pimp/';

secureheader();

siteheader();

?>
<table cellspacing="0" cellpadding="0">
  <tr>
    <td align="right"><div align="left"> <font color="white">Announcement  Aug 29, 2007 </font></div></td>
  </tr>
  <tr>
    <td valign="top"><font color="white"><div align="left"> We are sad to say that the site will close after next round.&nbsp; We are  offering to sell the site in full with script, domain, and existing  players and database of members all in tact so you may contact old and  new players.<br />
            <br />
      can be sent to Cartel- here or emailed to <a href="mailto:listreports@dedicatedgamingnetwork.com?subject=Cartel-Wars Sale">listreports@dedicatedgamingnetwork.com</a><br />
      <br />
      This  is not something we had planed on doing however with lack of time to  promote the site and to be here more for the site and the players.&nbsp; We  are left no choice.<br />
      <br />
      If you are interested in purchasing the site please PM me here or best way of communication is by email to <a href="mailto:listreports@dedicatedgamingnetwork.com?subject=Cartel-Wars%20Sale">listreports@dedicatedgamingnetwork.com</a><br />
      <br />
      Sorry to all,<br />
      -Cartel-God</div></font></td>
  </tr>
</table>
   <table width="100%" height="100%">

    <tr>

     <td height="12"><b>Choose a game!</b></td>

    </tr>

    <tr>

    </tr>

    <tr>

     <td align="center" valign="top">

     <?

          if($msg == disabled){?><br><font color="red"><b>Your charactor has been frozen</b></font><br><?}

          if($msg == select){?><br><font color="red"><b>Please select a game.</b></font><br><?}

      elseif($msg){?><br><font color="red"><b><?=$msg?></b></font><br><?}?>

     <?

     $get = mysql_query("SELECT round,type FROM $tab[game] WHERE ends>'$time' ORDER BY round ASC;");

     while ($game = mysql_fetch_array($get)){ printround($game[0],$game[1]); }

     ?>

     </td>

    </tr>

   </table>

<?

sitefooter();

?>