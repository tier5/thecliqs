<?

include("html.php");



$user1 = mysql_fetch_array(mysql_query("SELECT status,code,subscribe FROM $tab[pimp] WHERE id='$id';"));

$pimp1 = mysql_fetch_array(mysql_query("SELECT status,code,subscribe FROM $tab[user] WHERE code='$user1[1]';"));



mysql_query("UPDATE $tab[pimp] SET status='$pimp1[0]', subscribe='$pimp1[2]' WHERE id='$id';");







//RESERVES TO TURNS HERE updated 2/20/08 Theodore Gaushas Dedicated Gaming Network LLC
$reserves = substr(floor((int)$_POST['reserves']), 0, 5);
$res = mysql_fetch_array(mysql_query("SELECT res FROM $tab[pimp] WHERE id='$id';"));

if(($reserves > 0) && (!preg_match ('[0-9]', $reserves)) && (!strstr($reserves,"+")) && (!strstr($reserves,"-")) && (!strstr($reserves,".")) && (!strstr($reserves,"/")) && (!strstr($reserves,"<")) && (!strstr($reserves,">")) &&  (!strstr($reserves,")")) &&  (!strstr($reserves,"(")) && (!strstr($reserves,"&")) && (!strstr($reserves,"@")) && (!strstr($reserves,"=")) && (!strstr($reserves,"*"))  && ($reserves <= $res[0]))
{
$reserves = substr($reserves, 0, 5);
if ( ctype_digit($reserves) ){
 mysql_query("UPDATE $tab[pimp] SET trn=trn+$reserves, res=res-$reserves WHERE id='$id';");
 //log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "took $reserves from in game reserves";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");
			  }else{
}}
//END RESERVE TURNS



$pmp = mysql_fetch_array(mysql_query("SELECT pimp,rank,nrank,city,networth,money,trn,res,condom,medicine,crack,weed,glock,shotgun,uzi,ak47,whore,thug,whappy,thappy,payout,crew,msg,atk,ivt,lowrider,attin,attout,lastattackby,lastattack,cmsg,bank,tbank,beer,status,subscribe FROM $tab[pimp] WHERE id='$id';"));

$crw = mysql_fetch_array(mysql_query("SELECT name,founder,icon FROM $tab[crew] WHERE id='$pmp[21]';"));

$cty = mysql_fetch_array(mysql_query("SELECT name FROM $tab[city] WHERE id='$pmp[3]';"));

//$reserves = substr($reserves, 0, 5);

// turn info



$turnupdate = @mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='turns';"));



$game = mysql_fetch_array(mysql_query("SELECT speed,maxbuild FROM $tab[game] WHERE round='$tru';"));





if($pmp[subscribe]==0){$gameturnmax=$game[1];}

if($pmp[subscribe]==1){$gameturnmax=3500;}

if($pmp[subscribe]==2){$gameturnmax=4500;}

if($pmp[subscribe]==3){$gameturnmax=8000;}

if($pmp[subscribe]==4){$gameturnmax=10000;}



if($pmp[subscribe]==0){$gamespeed=$game["speed"];}

if($pmp[subscribe]==1){$gamespeed=35;}

if($pmp[subscribe]==2){$gamespeed=45;}

if($pmp[subscribe]==3){$gamespeed=65;}

if($pmp[subscribe]==4){$gamespeed=100;}



  $turnleft=($gameturnmax-$user["turn"]);

  $turnhour=$gamespeed*6;

  $hour=($turnleft/$turnhour);







function sounds(){

global $id, $tab, $site;

$sound = mysql_fetch_array(mysql_query("SELECT sounds,msg,atk FROM $tab[pimp] WHERE id='$id';"));

if($sound[0] != disabled)

  {

      if($sound[2] >= 1){?>



<embed src="<?=$site[img]?>attack.swf" quality="high" width="1" height="1" menu="false" type="application/x-shockwave-flash" pluginpage="http://www.macromedia.com/go/getflashplayer"></embed><? }

  elseif($sound[1] >= 1){?><embed src="<?=$site[img]?>message.swf" quality="high" width="1" height="1" menu="false" type="application/x-shockwave-flash" pluginpage="http://www.macromedia.com/go/getflashplayer"></embed><? }

  }

}



$tw=$pmp[12]+$pmp[13]+$pmp[14]+$pmp[15];

$figure=$time - $pmp[28];

$count=round($figure / (60*60*24));

if(($alert) && ($pmp[31] == admin)){ mysql_query("UPDATE $tab[pimp] SET alert='$alert', newalert='1' WHERE id>0;"); }



GAMEHEADER("Main Menu");

?> 

<div align="center"><font color="CCCCCC" face="Arial, Helvetica, sans-serif"><strong> 

  </strong></font> </div>

<div align="center"></div>

<div align="center"></div>

<tr>

  <td align="center" valign="top">

    <table width="91%">

      <tr>

        <td valign="bottom"><table cellspacing="0" cellpadding="0">

            <tr>

              <?if($crw[2]){?>

              <td height="32" valign="bottom"><a href="family.php?cid=<?=$pmp[21]?>&tru=<?=$tru?>"><img src="<?=$crw[2]?>" border="0" width="32" height="32"></a>&nbsp;</td>

              <?}?>

              <td><font color="#0099cc"><br>

                  <strong><font color="#990000">currently worth $

                  <?=commas($pmp[4])?>

                  <?if($pmp[21] > 0){?>

                  <br>

                  <?if($pmp[0] == $crw[1]){?>

            boss of

            <?}else{?>

            member of

            <?}?>

            <a href="family.php?cid=<?=$pmp[21]?>&tru=<?=$tru?>">

            <?=$crw[0]?>
            </a>.

            <?}?>
                  </font></strong> </font>                   </td>
            </tr>

        </table></td>

        <td align="right" valign="bottom"><font color="#0099cc">      <a href="mailbox.php?tru=<?=$tru?>"><font color="#990000">Open Mailbox</font></a><font color="#990000"><br>

            <?if($pmp[22] == 1){?>

            you have 1 new message

            

          <?}elseif($pmp[22] > 1){?>

          you have

          <?=$pmp[22]?> 

          new messages</font>

            <font color="#990000">

<?}else{?>

you have no new messages

<?}?>

<?if($pmp[23] == 1){?>

                  <br>

                  <a href="attacks.php?cid=<?=$pmp[23]?>&amp;tru=<?=$tru?>">you have been attacked!</a></font></font>

          <font color="#990000"><a href="attacks.php?cid=<?=$pmp[23]?>&amp;tru=<?=$tru?>">

          <?}elseif($pmp[23] > 1){?>

          </a>

          <br>

          <a href="attacks.php?cid=<?=$pmp[23]?>&tru=<?=$tru?>">you have been attacked

          <?=$pmp[23]?>

      times!</a></font>

            <font color="#990000">

      <?}?>

      <?if($pmp[24] == 1){?>

      <br>

      <a href="invites.php?tru=<?=$tru?>">you have a invitation!</a></font>

            <font color="#990000"><a href="invites.php?tru=<?=$tru?>">

      <?}elseif($pmp[24] > 1){?>

      <br>

      you have

      <?=$pmp[24]?>

      new invitations!

            
            <?}elseif($pmp[30] > 0){?>

            </a>

            <br>

            <a href="cboard.php?cid=<?=$pmp[21]?>&tru=<?=$tru?>">

      <?=$pmp[30]?>

      new crew message</a>

            <?}?>
        </font>          </td>
      </tr>
    </table>

    <?if($takeout == reserves){?>

    <table width="100%">



   <tr>   </tr>
  </table>

    <form method="post" action="index.php?tru=<?=$tru?>">

      <font face="Arial, Helvetica, sans-serif">how many turns would you like 

      to add? &nbsp; 

      <input name="reserves" type="text" class="text" size="7" maxlength="5">

      &nbsp; 

      <input name="submit" type="submit" class="submit" value="apply">
      </font>
</form>

    <?}else{?>

    <?if($user["reserve"] != 0){?>

    <p><br>

      <a href="index.php?takeout=reserves&tru=<?=$tru?>"><font color="#FFFFFF">you 

      also have</font> 

      <?=commas($user["reserve"])?>

      <font color="#FFFFFF">reserve turns</font></a> 

      <?}?>

      <?}?>
    </p>

    <p> <font face="Arial, Helvetica, sans-serif"> 

      <?if($bigman){?>

      <font color="ffffff" size="3"> 

      <?=$bigman?>
      </font><br>

      <?}?>

      </font></p>

    <p><strong><font face="Arial, Helvetica, sans-serif"><font color="#990000" size="+1">current networth:</font></font><font color="#990000" face="Arial, Helvetica, sans-serif"><font size="+1"> 

      $ 

      <?=commas($pmp[networth])?>

      </font></font></strong><font face="Arial, Helvetica, sans-serif"><br>

      <font size="+1"><font color="#990000"> 

      <?=commas($pmp[6])?>

      turns</font> and <font color="#990000">$ 

      <?=commas($pmp[5])?>

 cash</font> on hand.</font></font> <font face="Arial, Helvetica, sans-serif"><br>

 <?if($turnupdate[0]){?>

you will receive <font color="#0099cc"> <font color="#FF0000"> <strong>

<font color="#990000">
<?=$gamespeed?>
</font></strong></font></font> <font color="#990000"><strong>turns</strong></font> in:

<?=countup($turnupdate[0]+600)?>

<br />

<small>you can hold up to <font color="#0099cc"> <font color="#FF0000"> <strong>

<font color="#990033">
<font color="#990000">
<?=commas($gameturnmax)?>
</font></font></strong></font></font> <font color="#990000"><strong>turns</strong></font>.

<?}else{?>

<i><b>Turns will start processing in less then 10 minutes...</b></i>

<?}?>

</font><br>

      <br><a href="?takeout=reserves&tru=<?=$tru?>">You have <?=commas($pmp[7])?> reserve turns you can use.</a><br />
    <table width="450" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td>				<!--  / TEXT BOX \ -->
				<div class="textBox">
					<div class="bottom">

						<h2>Latest News on <?=$site[name]?>! </h2>
                        <div align="center">
                          <?  
    $getnews = mysql_query("SELECT id,news,posted,subject FROM $tab[news] WHERE id>0 ORDER BY posted DESC limit 2;");  
    while ($news = mysql_fetch_array($getnews)){?>
                          <ul class="first">
                            <li> 
                              
                              <table cellpadding="10">
                                <tr>
                                  <td><u><strong><?=$news[3]?></strong></u></td>
           </tr>
                              </table>
                            </li>
                          </ul>
                          <table width="450" cellpadding="10">
                            <tr>
                              <td><?=$news[1]?><br /><strong><?=date('F j, Y g:i a', $news[2])?></strong></td>
       </tr>
                          </table>
                          <br>
                          <br />
                          <? }?>						
                        </div>
                        <div class="archief"><!--archive link here if created--></div>
					</div>
				</div>
				<!--  \ TEXT BOX / --></td>
      </tr>
    </table>
    <?if($turnupdate[0]){?>
you will receive <font color="#0099cc"> <font color="#FF0000"> <strong>
<font color="#990000">
<?=$gamespeed?>
</font></strong></font></font> <font color="#990000"><strong>turns</strong></font> in:
<?=countup($turnupdate[0]+600)?>
<br>

        you can hold up to <font color="#0099cc">

        <font color="#FF0000">

        <strong>

        <font color="#990000">
        <?=commas($gameturnmax)?>
        </font></strong></font></font> <font color="#990000"><strong>turns</strong></font>.

        <?}else{?>

        <i><b>Turns will start processing in less then 10 minutes...</b></i>

        <?}?>

        <?=sounds()?>

        <br />

      <font size="2">

        <? if($pmp[status] == admin){?>

        <br />

    <b><font color="#990000">Admin Panel: </font></b><font color="#FF0000"><b><a href="adminonly.php?tru=<?=$tru?>">Options</a></b></font></font><font size="2">

        <?}?>

        </font><br />

  <?

GAMEFOOTER();

?>
