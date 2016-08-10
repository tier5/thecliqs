<?
include("html.php");

$pmp = mysql_fetch_array(mysql_query("SELECT trn,whore,thug,weed,condom,crack,medicine,thappy,whappy,money,bootleggers,bodyguards,hitmen,hustlers,dealers,punks FROM $tab[pimp] WHERE id='$id';"));

$nextupdate = mysql_fetch_array(mysql_query("SELECT lastran FROM $tab[cron] WHERE cronjob='ranks';"));
  
  if($keyboard==enter)
  {
      //ADD BONUSES
    //$bonus=(($pmp[2]/10000)+1);
	
	if (($trn == "") || ($pmp[0] <= 0) || ($trn > $pmp[0]) || ($trn == 0) || (strstr($trn,".")) || (!preg_match ('/^[a-z0-9][a-z0-9\.\-_]*$/i', $trn)))
         { $error="You dont have enough turns!"; }
    elseif($area == ""){$error="Please select where you want to recruit!";}
    elseif($trn >= 100000){$error="you can only use up to 99,999 turns!";}
    elseif($trn > $pmp[0]){$error="you dont ahve enough turns dumbass!";}
    elseif(!preg_match ('/^[0-9][0-9\.\-_]*$/i', $trn)){$error="cant use letters Dumbass!";}
    else {

         //if($pmp[7]<=100){$happiness=$pmp[7];}
		 
         $formula=rand(4.2,6.6);
         $formula2=rand(5.2,8.6);
         $formula3=rand(7.5,10.4);
         $formula4=rand(7.5,10.4);
		 
         $hitmen=$formula*$trn;
         $smugglers=$formula2*$trn;
		 
         if($area == 'casino'){$hoe=$hitmen;$thug=$smugglers;}
         if($area == 'cafe'){$hoe=$hitmen;$thug=$smugglers;}
         if($area == 'ghetto'){$hoe=$hitmen;$thug=$smugglers;}
         if($area == 'gym'){$hoe=$hitmen;$thug=$smugglers;}

    //give players their bonuses
    $bonus=(($pmp[2]/10000)+1);
    $hoe=$hoe; $hoe=round($hoe);
    $thug=$thug; $thug=round($thug);

    include("game_engine2.php");
 
    //Updating
    $hoetl=fixinput($hoe); 
    $thugtl=fixinput($thug);
    $cashtl=fixinput($pmp[9]+$cash);
    $turntl=$pmp[0]-$trn;
	if($turnt1 <= 0){ $turnt1=0;}
	
    if($trn >= 60){$protect=60;}else{$protect=$trn;}


    //net update
    $networth=net($id);$wappy=hoehappy($id);$tappy=thughappy($id);
    mysql_query("UPDATE $tab[pimp] SET whappy='$wappy', thappy='$tappy',networth='$networth', online='$time' WHERE id='$id'");

	if ($area == 'casino')
		{
			$unit1 = 'whores';
			$unit2 = 'card dealers';
		  mysql_query("UPDATE $tab[pimp] SET trn='$turntl', whore=whore+$hoetl, dealers=dealers+$thugtl, protection='$protect', protectstarted='$time' WHERE id='$id'");
		
//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "found $hoe whores and $thug dealers using $trn turns";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");}
	elseif ($area == 'cafe')
		{
			$unit1 = 'hitmen';
			$unit2 = 'hustlers';
		  mysql_query("UPDATE $tab[pimp] SET trn='$turntl', hitmen=hitmen+$hoetl, hustlers=hustlers+$thugtl, protection='$protect', protectstarted='$time' WHERE id='$id'");
		//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "found $hoe hitmen and $thug hustlers using $trn turns";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");}
	elseif ($area == 'ghetto')
		{
			$unit1 = 'bootleggers';
			$unit2 = 'punks';
		  mysql_query("UPDATE $tab[pimp] SET trn='$turntl', bootleggers=bootleggers+$hoetl, punks=punks+$thugtl, protection='$protect', protectstarted='$time' WHERE id='$id'");
		//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "found $hoe bootleggers and $thug punks using $trn turns";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");}
	elseif ($area == 'gym')
		{
			$unit1 = 'bodyguards';
			$unit2 = 'thugs';
		  mysql_query("UPDATE $tab[pimp] SET trn='$turntl', bodyguards=bodyguards+$hoetl, thug=thug+$thugtl, protection='$protect', protectstarted='$time' WHERE id='$id'");
		//log files
$userlog = mysql_fetch_array(mysql_query("SELECT pimp FROM $tab[pimp] WHERE id='$id';"));
$logpimp = $userlog[0];
$action = "found $hoe bodyguards and $thug thugs using $trn turns";
			  mysql_query("INSERT INTO $tab[logs] (id,time,round,pimpname,action,ip) VALUES ('','$time','$tru','$logpimp','$action','$REMOTE_ADDR');");}
	else
		{
			echo 'Please select an area.';
		}

    }
}

GAMEHEADER("Hoe Locator");
?><head>
<script>
//copyright Vjekoslav Begovic
//submitted and displayed on A1 JavaScripts www.a1javascripts.com/ 
<!--
timerID = null; 
function init(n){
	if (!document.all) 
		return;
	document.getElementById("drunk").style.filter = 'Wave(Add=0, Freq=0, LightStrength=+'+n+', Phase='+-n+', Strength='+n+')';
	n--;
	if (n<0){stop();return};
	cmd = "init("+n+")";
	timerID = window.setTimeout(cmd,100); 
}
function stop(){
	window.clearTimeout(timerID);
}
-->
</script>
</head>

<table width="500" align="center" cellspacing="0" cellpadding="0" border="0">
 <tr>
  <td align="center" valign="top">
<form method=post action="scout.php?tru=<?=$tru?>">
<font size="3"><b>Hire</b></font>

<font color="#FFCC00"><?=$error?></font><br>
<?if(($keyboard==enter) && (!$error)){?>

<b>using <font color="3366FF"><?=$trn?></font> turns, you found a total of <font color="#3366FF"><?=commas($hoe)?></font> <?= $unit1?> and  found <font color="#3366FF"><?=commas($thug)?></font> <?=$unit2?>. 
</nobr>
<br>
<?}?>
<br>

<b><font color="#B0C4DE">
	<font color="#000000">use 
	<input type="text" class="text" maxlength="5" size="5" name="trn" value="<?if($trn){echo"$trn";}?>">
	turns and get yourself some</font> 
	<select name="area">
		<option value="casino" <?PHP if($area == "casino") print("selected");?>>Casino(Whores and Card Dealers)</option>
		<option value="cafe" <?PHP if($area == "cafe") print("selected");?>>Lil Italy(Hitmen and Hustlers)</option>
		<option value="ghetto" <?PHP if($area == "ghetto") print("selected");?>>Hood(Bootleggers and Punks)</option>
		<option value="gym" <?PHP if($area == "gym") print("selected");?>>Club(Thugs and Bodyguards)</option>
	</select>
	</font></b>
<br>
<input type="hidden" name="keyboard" value="enter"><input type="submit" class="button" name="scout" value="scout">
</form>
<TABLE cellSpacing=6 width="500" border=0>
  <TBODY>
    <TR>
      <TD><DIV align=center><SPAN class=hk align="center">Your Defensive Units</SPAN><BR>
                <SPAN class=xsmall>attack your enemies and protect your businesses</SPAN> 
                <TABLE cellSpacing=1 cellPadding=0 width="100%">
                                    <TBODY>
                                      <TR>
                                        <TD align=middle><div align="center"><img src="/new/HIRE-bodyguard.jpg" width="88" height="150" /> <BR>
                                            <?=commas($pmp[11])?> <br>
                                          Bodyguards<BR>
                                        </div></TD>
                                        <TD align=middle><div align="center"><img src="/new/HIRE-thug.jpg" width="88" height="150" /><BR>
                                          <?=commas($pmp[2])?> 
                                          <br>
                                          Thugs<BR>
                                        </div></TD>
                                        <TD align=middle><div align="center"><img src="/new/HIRE-hitman.jpg" width="88" height="150" /><BR>
                                            <?=commas($pmp[12])?> <br>
                                          Hitmen</div></TD>
                                      </TR>
                                    </TBODY>
                </TABLE>
                                  <BR>
                                  <P class=hk align=center>&nbsp;</P>
      </DIV></TD>
    </TR>
    <TR>
      <TD vAlign=top align=middle colSpan=2><SPAN class=hk 
                        align="center">Your Operatives</SPAN><BR>
          <SPAN 
                        class=xsmall>to work in your businesses</SPAN>
          <TABLE cellSpacing=1 cellPadding=0 width="100%">
            <TBODY>
              <TR align=middle>
                <TD width="20%"><div align="center"><IMG height=150 alt="card dealers" 
                              src="../images/dealer.jpg" 
                              width=88><BR>
                      <?=commas($pmp[14])?> <br>
                      Dealers<BR>
                </div></TD>
                <TD width="20%"><div align="center"><IMG height=150 alt=whores 
                              src="../images/whore.JPG" 
                              width=88><BR>
                      <?=commas($pmp[1])?>
                      <br>
                      Whores<BR>
                </div></TD>
                <TD width="20%"><div align="center"><IMG height=150 alt=bootleggers 
                              src="../images/bootlegger.jpg" 
                              width=88><BR>
                      <?=commas($pmp[10])?> <br>
                      Bootleggers<BR>
                </div></TD>
                <TD width="20%"><div align="center"><IMG height=150 alt=hustlers 
                              src="../images/hustler.jpg" 
                              width=88><BR>
                      <?=commas($pmp[13])?> <br>
                      Hustlers<BR>
                </div></TD>
                <TD width="20%"><div align="center"><IMG height=150 alt=punks 
                              src="../images/punk.jpg" 
                              width=88><BR>
                    <?=commas($pmp[15])?> <br>
                    Punks</div></TD>
              </TR>
            </TBODY>
          </TABLE>
          <BR>
          <SMALL></SMALL></TD>
    </TR>
  </TBODY>
</TABLE>
<br>
<br>
<br>
<?=bar($id)?>
  </td>
 </tr>
</table>
<?
GAMEFOOTER();
?>